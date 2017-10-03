<?php
/**
 * Copyright © 2015 Pay.nl All rights reserved.
 */

namespace CDS\CCPayment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Checkout\Model\Cart;
use CDS\CCPayment\Model\CCPayment as CDS_CCPayment;

class CCPaymentConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCodes = [
        'ccpayment',
    ];

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];
    
    /**
     * @var \Openpay\Cards\Model\Payment
     */
    protected $payment ;

    protected $cart;


    /**     
     * @param PaymentHelper $paymentHelper
     * @param OpenpayPayment $payment
     */
    public function __construct(PaymentHelper $paymentHelper,CDS_CCPayment $payment, Cart $cart) {
        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
        $this->cart = $cart;
        $this->payment = $payment;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {                
        $config = [];
        
        foreach ($this->methodCodes as $code) {
//            $config['textd'] = "for method codes";
            if ($this->methods[$code]->isAvailable()) {
 //               $config['textdd'] = "for is aviable";
                //$config['payment']['openpay_credentials'] = array("merchant_id" => $this->payment->getMerchantId(), "public_key" => $this->payment->getPublicKey(), "is_sandbox"  => $this->payment->isSanbox());                 
                $config['payment']['months_interest_free'] = $this->payment->getMonthsInterestFree();
                $config['payment']['total'] = $this->cart->getQuote()->getGrandTotal()+$this->cart->getQuote()->getShippingAmount();
                $config['payment']['minimum_amount'] = $this->payment->getMinimumAmount();
                $config['payment']['ccform']["availableTypes"][$code] = array("AE" => "American Express", "VI" => "Visa", "MC" => "MasterCard"); 
                $config['payment']['ccform']["hasVerification"][$code] = true;
                $config['payment']['ccform']["hasSsCardType"][$code] = false;
                $config['payment']['ccform']["months"][$code] = $this->getMonths();
                $config['payment']['ccform']["years"][$code] = $this->getYears();
                $config['payment']['ccform']["cvvImageUrl"][$code] = "http:/\/".$_SERVER['SERVER_NAME']."/pub/static/frontend/Magento/luma/en_US/Magento_Checkout/cvv.png";
                $config['payment']['ccform']["ssStartYears"][$code] = $this->getStartYears();
                $config['payment']['bines'] = $this->getBines();
            }
        }
        return $config;
    }
    
    public function getMonths(){
        return array(
            "1" => "01 - Enero",
            "2" => "02 - Febrero",
            "3" => "03 - Marzo",
            "4" => "04 - Abril",
            "5" => "05 - Mayo",
            "6" => "06 - Junio",
            "7" => "07 - Julio",
            "8" => "08 - Agosto",
            "9" => "09 - Septiembre",
            "10"=> "10 - Octubre",
            "11"=> "11 - Noviembre",
            "12"=> "12 - Diciembre"
        );
    }
    
    public function getYears(){
        $years = array();
        for($i=0; $i<=10; $i++){
            $year = (string)($i+date('Y'));
            $years[$year] = $year;
        }
        return $years;
    }
    
    public function getStartYears(){
        $years = array();
        for($i=5; $i>=0; $i--){
            $year = (string)(date('Y')-$i);
            $years[$year] = $year;
        }
        return $years;
    }
    public function getBines(){
        $bines = array(
                        array('MRCCOD' => 'AE','TIPCOD'=>'CRE','BIN'=>'370240','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'AE','TIPCOD'=>'CRE','BIN'=>'370241','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'AE','TIPCOD'=>'CRE','BIN'=>'370242','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'541854','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'545504','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'518443','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'547807','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'541376','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'454856','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'496601','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'520057','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'Y'),	
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'410144','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'410145','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'451990','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'451989','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'450563','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'431846','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'553166','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'553620','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'531485','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'515811','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'546466','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'515831','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'550232','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),	
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'523763','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),		
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'414326','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),		
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'469735','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'401601','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'401602','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'404931','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'406359','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'510606','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'MC','TIPCOD'=>'CRE','BIN'=>'548083','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'464126','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'464125','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'434796','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'434797','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N'),
                        array('MRCCOD' => 'VI','TIPCOD'=>'CRE','BIN'=>'402912','6MONTH' =>'Y','12MONTH'=>'Y','18MONTH' =>'Y','24MONTH' =>'N')
                    );
        return $bines;
    }
 	   
}
