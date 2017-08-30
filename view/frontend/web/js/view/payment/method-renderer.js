define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'ccpayment',
                component: 'CDS_CCPayment/js/view/payment/method-renderer/cc-form'
            }
        );
        return Component.extend({});
    }
);