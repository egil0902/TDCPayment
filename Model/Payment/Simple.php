<?php


namespace CDS\TDCPayment\Model\Payment;


class Simple extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'simple';
}