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
        
        
        $infoInstance->setAdditionalInformation('interest_free',
            isset($additionalData['interest_free']) ? $additionalData['interest_free'] : null
        );
        $infoInstance->setAdditionalInformation('cc_number',
            isset($additionalData['cc_number']) ? $additionalData['cc_number'] : null
        );
        return $this;
    }
 
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
	$order = $payment->getOrder();

        /** @var \Magento\Sales\Model\Order\Address $billing */
        $billing = $order->getBillingAddress();
       
        
        $key = 'YdV27NXEB4TzCjK79GPTVf7Y4S2b3RtN'; //poner como parametro en el admin
        $key_id = '4896565';//poner como parametro en el admin
        $type = 'sale';
        $orderid = $order->getIncrementId();
        $time = time();
        $hash = md5($orderid."|".$amount."|".$time."|".$key."|".$key_id);
        $redirect = '';
        $ccnumber = $this->getInfoInstance()->getAdditionalInformation('cc_number');
        //$ccnumber;
        //$ccexp
        //$checkname
        //$cvv
        //$email
        //$phone
        //$address1 =$order->getBillingAddress();
        //$ipaddress
        //throw new \Magento\Framework\Exception\LocalizedException(__('The capture action is not available.'));        
	$this->_logger->debug('Error capturado por eduardo gil '.$ccnumber);
	throw new \Magento\Framework\Validator\Exception(__('Error de mierda'));
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
