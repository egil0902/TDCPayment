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
        
        return $this;
    }
 
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();
        
    }
 
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $transactionId = $payment->getParentTransactionId();
 
        
 
        $payment
            ->setTransactionId($transactionId . '-' . \Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND)
            ->setParentTransactionId($transactionId)
            ->setIsTransactionClosed(1)
            ->setShouldCloseParentTransaction(1);
 
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
}
