<?php
 
namespace CDS\CCPayment\Model;
 
/**
 * Pay In Store payment method model
 */
class CCPayment extends \Magento\Payment\Model\Method\AbstractMethod
{
 
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'ccpayment';
}