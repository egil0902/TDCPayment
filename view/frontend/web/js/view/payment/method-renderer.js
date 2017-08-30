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
                type: 'testpayment',
                component: 'CDS_CCPayment/js/view/payment/method-renderer/ccpayment'
            }
        );
        return Component.extend({});
    }
);