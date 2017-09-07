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
                $config['payment']['total'] = $this->cart->getQuote()->getGrandTotal();
                $config['payment']['minimum_amount'] = $this->payment->getMinimumAmount();
                $config['payment']['ccform']["availableTypes"][$code] = array("AE" => "American Express", "VI" => "Visa", "MC" => "MasterCard"); 
                $config['payment']['ccform']["hasVerification"][$code] = true;
                $config['payment']['ccform']["hasSsCardType"][$code] = false;
                $config['payment']['ccform']["months"][$code] = $this->getMonths();
                $config['payment']['ccform']["years"][$code] = $this->getYears();
                $config['payment']['ccform']["cvvImageUrl"][$code] = "http:/\/".$_SERVER['SERVER_NAME']."/pub/static/frontend/Magento/luma/en_US/Magento_Checkout/cvv.png";
                //$config['payment']['ccform']["ssStartYears"][$code] = $this->getStartYears();
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
    
}
