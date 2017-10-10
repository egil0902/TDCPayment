/**
 * Openpay_Cards Magento JS component
 *
 * @category    CCPayment
 * @package     CDS_CCPayment
 * @author      Eduardo Gil
 * @license     http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Payment/js/model/credit-card-validation/validator',
	'Magento_Checkout/js/model/payment/additional-validators',
	'Magento_Checkout/js/model/full-screen-loader',
	'Magento_Checkout/js/action/redirect-on-success'	
    ],
    function (Component, $, quote, customer,validator,additionalValidators,fullScreenLoader,redirectOnSuccessAction) {
        'use strict';
        var customerData = quote.billingAddress._latestValue;  
        //var total = window.checkoutConfig.payment.total;
	var total = quote.totals()['base_grand_total'];
	console.log(total);
	
        console.log(customer);
	var response;
        console.log(customerData);
        
        $(document).on("change", "#interest_free", function() {        
            var monthly_payment = 0;
            var months = parseInt($(this).val());     

            if (months > 1) {
                $("#total-monthly-payment").css("display", "inline");
            } else {
                $("#total-monthly-payment").css("display", "none");
            }

            monthly_payment = total/months;
            monthly_payment = monthly_payment.toFixed(2);            
            
            $("#monthly-payment").text(monthly_payment);
        });
        $(document).on("change", "cc_number", function() {        
            getMonthsInterestFree();
        });

        return Component.extend({
	    
	    redirectAfterPlaceOrder: true,
            paymentResponse: null,

            defaults: {
                template: 'CDS_CCPayment/payment/ccpayment-form'
            },

            getCode: function() {
                return 'ccpayment';
            },

            isActive: function() {
                return true;
            },
            
            getMonthsInterestFree: function() {
                var monthsAux = window.checkoutConfig.payment.months_interest_free;
                var bines = window.checkoutConfig.payment.bines;
                var months = new Array();
                for(var i = 0; i < bines.length; i++){
                        if(this.creditCardNumber().includes(bines[i]['BIN'])>0){
                                months[0] = monthsAux[0];
                                if(bines[i]['6MONTH']=='Y'){
                                        months[1]=monthsAux[1];
                                }
                                if(bines[i]['12MONTH']=='Y'){
                                        months[2]=monthsAux[2];
                                }
                                if(bines[i]['18MONTH']=='Y'){
                                        months[3]=monthsAux[3];
                                }
                                if(bines[i]['24MONTH']=='Y'){
                                        months[4]=monthsAux[4];
                                }
                        }
                }
                return months;
            },
            
            showMonthsInterestFree: function() {
                var self = this;
                var months = this.getMonthsInterestFree();
                var minimum_amount = window.checkoutConfig.payment.minimum_amount;         
                var total = window.checkoutConfig.payment.total;
                total = parseInt(total);
                
                return (months.length > 1 && total >= minimum_amount) ? true : false;
		return false;
            },
            getProcessorID : function (months){
                console.log(months)
                if(months==='6'){ return 'panafototc6';}
                if(months==='12'){ return 'panafototc12';}
                if(months==='18'){ return 'panafototc18';}
                if(months==='24'){ return 'panafototc24';}
                return 'panafotocn01';

            },

            /**
             * Prepare and process payment information
	             */
            preparePayment: function (p_type,p_transactionid) {
                //update customer data
                customerData = quote.billingAddress._latestValue;
                console.log("Update customer data");
                console.log(customerData);
                console.log("Update total");
                console.log(quote.totals()['base_grand_total']);

		var type = p_type;		
                var $form = $('#' + this.getCode() + '-form');

                if($form.validation() && $form.validation('isValid')){
                    this.messageContainer.clear();
                    console.log(this.getCustomerFullName());
                    var holder_name = this.getCustomerFullName();
                    var card = $('#ccpayment_cc_number').val();
                    var cvc = $('#ccpayment_cc_cid').val();
                    var year = this.creditCardExpYear().toString().substring(2, 4);
                    var month = this.creditCardExpMonth();
		    month = ("0" + month).slice (-2);
                    var data = {
                        holder_name: holder_name,
                        card_number: card.replace(/ /g, ''),
                        cvv2: cvc
                    };

                    if(this.validateAddress() !== false){
                        data["address"] = this.validateAddress();
                    }else{
			return this.validateAddress();
		    }
		    
		    var param = {'orderid' : quote.getQuoteId(),
				 'amount' : quote.totals()['base_grand_total'],
				 'ccnumber' : card.replace(/ /g, ''),
				 'ccexp' : month.concat(year),
				 'cvv' : cvc,
				 'checkname' : holder_name,
				 'firstname' : customerData.firstname, 
				 'lastname' : customerData.lastname,
				 'phone' : customerData.telephone,
				 'address1' : this.validateAddress(),
				 'type' : type
				};
		var transactionid='undefined';
                if(p_transactionid!=='undefined'){
                        transactionid=p_transactionid;
			param['transactionid']=p_transactionid;
                }
		if($('#interest_free').val()!==''){
                        param['processor_id']=this.getProcessorID($('#interest_free').val());
                }

		console.log(param);
                    
                    this.OpenWindowWithPost("https:/\/www.panafoto.com/metodo_pago.php", type, "NewFile",param);
                  
                }else{
                    return $form.validation() && $form.validation('isValid');
                }
            },

	    OpenWindowWithPost: function(url, type, name, params){
		var self = this;
		self.isPlaceOrderActionAllowed(false);
                fullScreenLoader.startLoader();
		var iframe = document.getElementById("iframeBAC");
		var form = document.createElement("form");
		form.setAttribute("method", "post");
		form.setAttribute("action", url);
		form.setAttribute("target", "iframeBAC");
		for (var i in params)
		{
			if (params.hasOwnProperty(i))
			{
				var input = document.createElement('input');
				input.type = 'hidden';
				input.name = i;
				input.value = params[i];
				form.appendChild(input);
   			}
		}
		var doc = iframe.contentDocument || iframe.contentWindow.document;
		doc.body.appendChild(form);
		require(
        [
            'jquery',
            'Magento_Ui/js/modal/modal'
        ],
        function(
            $,
            modal
        ) {
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: 'Procesando tarjeta de crédito. Por favor no cierre ni actualice el navegador',
                buttons: [{
                    text: $.mage.__('Continue'),
                    class: '',
                    click: function () {
                        this.closeModal();
                    }
                }]
            };

            var popup = modal(options, $('#popup-modal'));

            $('#popup-modal').modal('openModal');
        }
    );
		form.submit();
		var counter = 0;
		var time = 1000;
		$('#response').val('');		
		var i = setInterval(function(){
                		if($('#response').val()!==''){
					self.isPlaceOrderActionAllowed(true);
			                fullScreenLoader.stopLoader();
					clearInterval(i);
					
                        		response = $('#response').val().split("|");
					console.log(response);
                        		var result = response[0].split("=");
                                        if(result[1]!="1"){
						$('#popup-modal').modal('closeModal');						
						require(['Magento_Ui/js/modal/alert'], 
                                                    function(alert) {  
                                                           alert({
                                                               title: 'Resultado de la Transaccion',
                                                               content: '*** Fallida ***',
                                                               actions: {
                                                                   always: function(){}
                                                               }});
                                                       });
                        		}else{
						
						self.placeOrder();
                        		}
                		}
                		counter++;
                		if(counter > 30){
                        		clearInterval(i);
                		}
        		}, time);


	    },
            /**
             * @override
             */
	    placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
		    response = $('#response').val().split("|");
                    var result = response[3].split("=");
		    console.log("En place order");
                    console.log(response);
                    console.log(result[1]);
		     
                    
		    this.getPlaceOrderDeferredObject()
                        .fail(
                            function () {
				console.log("en fail");
				console.log(data);		    
 			        console.log("Place Order fail.");
				
                            }
                        ).done(
                            function () {                                  
                                    if (self.redirectAfterPlaceOrder) {
                                        redirectOnSuccessAction.execute();
                                    }

                            }
                        );
                    return true;
                }
                return false;
            },
            getData: function () {
                return {
                    'method': "ccpayment",
                    'additional_data': {
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_number': this.creditCardNumber(),
                        'interest_free': $('#interest_free').val(),
			'paymentResponse': $('#response').val()

                    }
                };
            },
	    
	    setPaymentResponse: function (paymentResponse) {
                this.paymentResponse = paymentResponse;
            },
	    getPaymentResponse: function () {
                return this.paymentResponse;
            },
            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },
            getCustomerFullName: function() {
                return customerData.firstname+' '+customerData.lastname;
            },
            validateAddress : function(){
		var address = {
                    line1: (typeof customerData.street === 'undefined' || customerData.street[0].length === 0)? "Panama" : customerData.street[0],
                    city: (typeof customerData.city === 'undefined' || customerData.city.length === 0)? "Panama" : customerData.city,
                    state: (typeof customerData.region === 'undefined' || customerData.region.length === 0)? "Panama" : customerData.region,
                    postal_code:  (typeof customerData.postcode === 'undefined' || customerData.postcode === null)? "622" : customerData.postcode,
                    country_code: (typeof customerData.countryId === 'undefined' || customerData.countryId.length === 0)? "PA" :  customerData.countryId
		}
                return address;

	    }
        });
    }
);
