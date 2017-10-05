<?php
 
namespace CDS\CCPayment\Model;
 
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Payment\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class CCPayment extends \Magento\Payment\Model\Method\Cc
{
 
    /**
     * Payment code
     *
     * @var string
     */
    
    const METHOD_CODE                       = 'ccpayment';
 
    protected $_code                    	= self::METHOD_CODE;
 
 
    protected $_isGateway                   = true;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = true;
    protected $_canRefund                   = true;
    
    protected $months_interest_free;   
    protected $minimum_amount;
 
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        ModuleListInterface $moduleList,
        TimezoneInterface $localeDate,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            null,
            null,
            $data
        );
 
        $this->_code = 'ccpayment';
        
 	$this->months_interest_free = $this->getConfigData('interest_free');
        $this->minimum_amount = $this->getConfigData('minimum_amount');
    }
 
    public function assignData(\Magento\Framework\DataObject $data) {
        parent::assignData($data);
                
        $infoInstance = $this->getInfoInstance();
        $additionalData = ($data->getData('additional_data') != null) ? $data->getData('additional_data') : $data->getData();
        
        $infoInstance->setAdditionalInformation('cc_cid',
            isset($additionalData['cc_cid']) ? $additionalData['cc_cid'] : null
        );
        $infoInstance->setAdditionalInformation('cc_type',
            isset($additionalData['cc_type']) ? $additionalData['cc_type'] : null
        );
        $infoInstance->setAdditionalInformation('cc_exp_year',
            isset($additionalData['cc_exp_year']) ? $additionalData['cc_exp_year'] : null
        );
        $infoInstance->setAdditionalInformation('cc_exp_month',
            isset($additionalData['cc_exp_month']) ? $additionalData['cc_exp_month'] : null
        );

        $infoInstance->setAdditionalInformation('cc_number',
            isset($additionalData['cc_number']) ? $additionalData['cc_number'] : null
        );
        $infoInstance->setAdditionalInformation('interest_free',
            isset($additionalData['interest_free']) ? $additionalData['interest_free'] : null
        );
        return $this;
    }
 
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
	/*$order = $payment->getOrder();

        /** @var \Magento\Sales\Model\Order\Address $billing */
        /*$billing = $order->getBillingAddress();
	$time = time();
        $key = 'YdV27NXEB4TzCjK79GPTVf7Y4S2b3RtN';
        $key_id = '4896565';
        $orderid = $order->getIncrementId();
	$month = $this->getInfoInstance()->getAdditionalInformation('cc_exp_month');
	if($month<10){
		$month='0'.$month;
	}
	$year=substr($this->getInfoInstance()->getAdditionalInformation('cc_exp_year'),-2);
        $post_data['Key'] = $key; //poner como parametro en el admin
        $post_data['type'] = 'sale';
	$post_data['time'] = $time;
        $post_data['orderid'] = $orderid;
	$post_data['amount'] = number_format((float)$amount, 2, '.', '');
	$post_data['key_id'] = $key_id;
	$this->_logger->debug('Hash Data: '.$orderid."|".number_format((float)$amount, 2, '.', '')."|".$time."|".$key);
        $post_data['hash'] = md5($orderid."|".number_format((float)$amount, 2, '.', '')."|".$time."|".$key);
        $post_data['redirect'] = '192.168.18.85/recepcion_pago.php';
        $post_data['ccnumber'] = $this->getInfoInstance()->getAdditionalInformation('cc_number');
        $post_data['ccexp'] = $month.$year;
        $post_data['checkname'] = $billing->getFirstname().' '.$billing->getLastname();
        $post_data['cvv'] = $this->getInfoInstance()->getAdditionalInformation('cc_cid');
	$post_data['email'] = 'egil0902@gmail.com';
	$post_data['phone'] = '66234016';
	$post_data['address1'] = 'Panama';
	$post_data['ipaddress'] = $_SERVER['REMOTE_ADDR'];
        $url='https://credomatic.compassmerchantsolutions.com/api/transact.php';
        $curl_connection = curl_init($url);
        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl_connection, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl_connection, CURLOPT_HEADER, TRUE);
        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, TRUE);

        foreach ( $post_data as $key => $value) {
            $post_items[] = $key . '=' . $value;
        }
        $post_string = implode ('&', $post_items);
	$this->_logger->debug('Se definieron las variables: '.$post_string);
        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
        $result = curl_exec($curl_connection);
	$header_size = curl_getinfo($curl_connection,CURLINFO_HEADER_SIZE);
	$body = substr( $result, $header_size );
	$this->_logger->debug('Body del resultado: '.$body);
	curl_close($curl_connection);
	$response = explode('|',$body);
	$this->_logger->debug('Resultado del CURL retorno: '.$response[0]);
	$resp = array();
	foreach($response as $response_line){
		$aux = explode('=',$response_line);
		$resp[$aux[0]] = $aux[1];
	}
	$this->_logger->debug('responsetext: '.$resp['responsetext']);
	if($resp['response']!=1){
		//si no paso entonces capturamos la excepcion
		throw new \Magento\Framework\Validator\Exception(__($resp['responsetext']));
	}*/
        return $this;
    }

    public function getMonthsInterestFree() {
        $months = explode(',', $this->months_interest_free);
        if(!in_array('1', $months)) {
            array_unshift($months, '1');
        }        
        return $months;
    }
    public function getMinimumAmount() {
        return $this->minimum_amount;
    }
public function error($e) {
        /* 6001 el webhook ya existe */
        switch ($e->getErrorCode()) {
            case '1000':
            case '1004':
            case '1005':
                $msg = 'Servicio no disponible.';
                break;
            /* ERRORES TARJETA */
            case '3001':
            case '3004':
            case '3005':
            case '3007':
                $msg = 'La tarjeta fue rechazada.';
                break;
            case '3002':
                $msg = 'La tarjeta ha expirado.';
                break;
            case '3003':
                $msg = 'La tarjeta no tiene fondos suficientes.';
                break;
            case '3006':
                $msg = 'La operación no esta permitida para este cliente o esta transacción.';
                break;
            case '3008':
                $msg = 'La tarjeta no es soportada en transacciones en línea.';
                break;
            case '3009':
                $msg = 'La tarjeta fue reportada como perdida.';
                break;
            case '3010':
                $msg = 'El banco ha restringido la tarjeta.';
                break;
            case '3011':
                $msg = 'El banco ha solicitado que la tarjeta sea retenida. Contacte al banco.';
                break;
            case '3012':
                $msg = 'Se requiere solicitar al banco autorización para realizar este pago.';
                break;
            default: /* Demás errores 400 */
                $msg = 'La petición no pudo ser procesada.';
                break;
        }

        return 'ERROR '.$e->getErrorCode().'. '.$msg;
    }
}
