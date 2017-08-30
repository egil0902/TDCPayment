<?php
namespace CDS\CCPayment\Model\Source;
class Cctype extends \Magento\Payment\Model\Source\Cctype{
    public function getAllowedTypes()
    {
        return array('VI', 'MC', 'AE');
    }
}