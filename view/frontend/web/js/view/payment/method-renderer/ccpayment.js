define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component,
              rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'ccpayment',
                component: 'CDS_TDCPayment/payment/ccpayment'
            }
        );
        return Component.extend({});
    }
);