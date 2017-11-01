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

$orderObject = $objectManager->create('\Magento\Sales\Model\Order');
$invoiceObject = $objectManager->create('\Magento\Sales\Model\Order\Invoice');

$response = $_REQUEST['response'];
$dataResponse = explode('|', $response);
$dataOrder = explode('=', $dataResponse[7]);
$orderId = $dataOrder[1];

//$order = $orderObject->load($orderId);
$order = $orderObject->getCollection()->addAttributeToFilter('quote_id', $orderId)->getFirstItem();
$addData = $order->getPayment()->getAdditionalInformation();

// *** Ocultar datos de la TDC y guarda la respuesta del banco en sales_order_payment *** //
$newAddData = Array();
$newAddData[cc_cid] = '***';
$newAddData[cc_type] = $addData[cc_type];
$newAddData[cc_exp_year] = $addData[cc_exp_year];
$newAddData[cc_exp_month] = $addData[cc_exp_month];
$newAddData[cc_number] = substr($addData[cc_number], 0, 6).'******'.substr($addData[cc_number], 12, 16);
$newAddData[cc_name] = $addData[cc_name];
$newAddData[interest_free] = $addData[interest_free];
$newAddData[paymentResponse] = $response;
$newAddData[method_title] = $addData[method_title];

//print_r($newAddData).'<br>';

$order->getPayment()->setAdditionalInformation($newAddData);
$order->getPayment()->save();
// ************************************************************************************* //

// *** Se verifica la respuesta y se cambia el estatus a la orden y factura *** //
$dataResp = explode('|', $response);
$bankResp = explode('=', $dataResp[0]);
$responseId = $bankResp[1];

if ($responseId != '1') {
	if ($order->hasInvoices()) {
        foreach ($order->getInvoiceCollection() as $invoiceCollect) {
            $invoiceId = $invoiceCollect->getId();
            $invoice = $invoiceObject->load($invoiceId);
            $invoice->setState(3); //canceled
            $invoice->cancel();
            $invoice->save();
        }
    }
    $order->setState('canceled')->setStatus('canceled');
    $order->cancel();
    $order->save();
} else if($responseId == '1') {
	if ($order->hasInvoices()) {
        foreach ($order->getInvoiceCollection() as $invoiceCollect) {
            $invoiceId = $invoiceCollect->getId();
            $invoice = $invoiceObject->load($invoiceId);
            $invoice->setState(2); //paid
            $invoice->save();
        }
    }
    $order->setState('processing')->setStatus('processing');
    $order->save();
}
// ***************************************************************************** //
