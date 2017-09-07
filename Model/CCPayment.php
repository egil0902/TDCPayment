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
    public function getBines(){
        $bines = array(
                        array('MRCCOD' => 'AE','TIPCOD'=>'CRE','BIN'=>370240,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'AE','TIPCOD'=>'CRE','BIN'=>370241,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'AE','TIPCOD'=>'CRE','BIN'=>370242,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>541854,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>545504,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>518443,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>547807,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>541376,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>454856,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>496601,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>520057,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>Y),	
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>410144,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>410145,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>451990,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>451989,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>450563,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>431846,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>553166,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>553620,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>531485,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>515811,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>546466,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>515831,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>550232,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>523763,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),		
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>414326,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),		
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>469735,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>401601,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>401602,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>404931,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>406359,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>510606,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>548083,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>464126,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>464125,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>434796,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>434797,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>402912,'6MONTH' =>Y,'12MONTH'=>Y,'18MONTH' =>Y,'24MONTH' =>N)
                    );
        return $bines;
    }
            
}
