<?php
    use Magento\Framework\App\Bootstrap;

    require __DIR__ . '/app/bootstrap.php';

    $bootstrap = Bootstrap::create(BP, $_SERVER);
    $obj = $bootstrap->getObjectManager();
    $state = $obj->get('Magento\Framework\App\State');
    $state->setAreaCode('frontend');
    $registry = $obj->get('\Magento\Framework\Registry');
    $registry->register('isSecureArea', true);

    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    $paymentObject = $objectManager->create('\Magento\Sales\Model\Order\Payment');

    $ordersPayment = $paymentObject->getCollection()->addAttributeToSelect('*')->load();

    echo 'BEGIN SCRIPT. Updating Additional Information...<br>';
    foreach ($ordersPayment as $payment) {
        $addData = $payment->getAdditionalInformation();

        // *** Ocultar datos de la TDC y guarda la respuesta del banco en sales_order_payment *** //
        $newAddData = Array();
        (array_key_exists("cc_cid", $addData) ? $newAddData["cc_cid"] = '***' : $newAddData["cc_cid"] = '***');
        (array_key_exists("cc_type", $addData) ? $newAddData["cc_type"] = $addData["cc_type"] : $newAddData["cc_type"] = '');
        (array_key_exists("cc_exp_year", $addData) ? $newAddData["cc_exp_year"] = $addData["cc_exp_year"] : $newAddData["cc_exp_year"] = '');
        (array_key_exists("cc_exp_month", $addData) ? $newAddData["cc_exp_month"] = $addData["cc_exp_month"] : $newAddData["cc_exp_month"] =  '');
        (array_key_exists("cc_number", $addData) ? $newAddData["cc_number"] = substr($addData["cc_number"], 0, 6) . '******' . substr($addData["cc_number"], 12, 16) : $newAddData["cc_number"] =  '****************');
        (array_key_exists("cc_name", $addData) ? $newAddData["cc_name"] = $addData["cc_name"] : $newAddData["cc_name"] =  '');
        (array_key_exists("interest_free", $addData) ? $newAddData["interest_free"] = $addData["interest_free"] : $newAddData["interest_free"] =  '');
        (array_key_exists("paymentResponse", $addData) ? $newAddData["paymentResponse"] = $addData["paymentResponse"] : $newAddData["paymentResponse"] =  '');
        (array_key_exists("method_title", $addData) ? $newAddData["method_title"] = $addData["method_title"] : $newAddData["method_title"] =  '');

        $payment->setAdditionalInformation($newAddData);
        $payment->save();
    }
    echo 'END SCRIPT';
