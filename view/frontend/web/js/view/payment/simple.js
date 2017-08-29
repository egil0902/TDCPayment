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
                type: 'simple',
                component: 'CDS_TDCPayment/js/view/payment/method-renderer/simple-method'
            }
        );
        return Component.extend({});
    }
);