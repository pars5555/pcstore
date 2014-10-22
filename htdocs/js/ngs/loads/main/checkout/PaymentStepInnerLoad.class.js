ngs.PaymentStepInnerLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_checkout", ajaxLoader);
    },
    getUrl: function() {
        return "payment_step_inner";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "checkout_steps_container";
    },
    getName: function() {
        return "payment_step_inner";
    },
    afterLoad: function() {
        var thisObject = this;
        jQuery('#shopping_cart_div').dialog('option', 'title', ngs.LanguageManager.getPhrase(444));
        jQuery('#shopping_cart_div').dialog().parent().attr('phrase_id', 444);

        var nextButtonTitle = jQuery('#cart_next_button_status_and_title').attr('title');
        var nextButtonDisabled = jQuery('#cart_next_button_status_and_title').attr('button_disabled') === 'true' ? true : false;
        var nextButtonTitlePhraseId = jQuery('#cart_next_button_status_and_title').attr('phrase_id');


        var thisInstance = this;
        var btns = {
            "Prev": {
                text: ngs.LanguageManager.getPhrase(283),
                'class': "translatable_search_content",
                phrase_id: 283,
                click: function() {
                    var payment_data = $("payment_details_form").serialize(true);
                    var params = $H(thisInstance.params).merge(payment_data);
                    jQuery('#shopping_cart_div').dialog('option', 'buttons', {});
                    ngs.load("shipping_step_inner", params.toObject());
                }
            },
            "Next": {
                text: ngs.LanguageManager.getPhrase(282),
                'class': "dialog_default_button_class translatable_search_content translatable_attribute_element",
                attribute_phrase_id: nextButtonTitlePhraseId,
                attribute_name_to_translate: "title",
                phrase_id: 282,
                title: nextButtonTitle,
                disabled: nextButtonDisabled,
                id: 'dialogNextButtonId',
                click: function() {
                    if (thisInstance.depositChanged === true) {
                        thisInstance.paymentParamsChanged();
                    } else {
                        var allowToGoNext = true;
                        if (thisInstance.params.cho_payment_type === 'bank')
                        {
                            allowToGoNext = thisInstance.checkBankPaymentCompanyDetails();

                        }
                        if (allowToGoNext == true) {
                            jQuery('#shopping_cart_div').dialog('option', 'buttons', {});
                            var payment_data = $("payment_details_form").serialize(true);
                            var params = $H(thisInstance.params).merge(payment_data);
                            ngs.load("final_step_inner", params.toObject());
                        }
                    }
                }}


        };
        jQuery('#shopping_cart_div').dialog('option', 'buttons', btns);

        jQuery('#cho_payment_type_cash, #cho_payment_type_credit, #cho_payment_type_paypal, #cho_payment_type_arca, #cho_payment_type_bank, #cho_payment_type_creditcard').change(function() {
            thisObject.paymentParamsChanged();
        });



        if ($('cho_credit_supplier_id')) {
            $('cho_credit_supplier_id').onchange = this.paymentParamsChanged.bind(this);
        }

        if ($('cp_cho_selected_credit_months')) {
            $('cp_cho_selected_credit_months').onchange = this.paymentParamsChanged.bind(this);
        }
        if ($('calculate_credit_monthly_payments_button')) {
            $('calculate_credit_monthly_payments_button').onclick = this.paymentParamsChanged.bind(this);
        }

        this.depositChanged = false;
        if ($('cho_selected_deposit_amount')) {
            $('cho_selected_deposit_amount').onchange = this.onDepositChanged.bind(this);
            $('cho_selected_deposit_amount').onkeyup = this.onDepositChanged.bind(this);
            $('cho_selected_deposit_amount').onmouseup = this.onDepositChanged.bind(this);

        }
    },
    checkBankPaymentCompanyDetails: function() {
        var companyName = jQuery('#cho_company_name').val();
        var companyHvhh = jQuery('#cho_company_hvhh').val();
        var companyAddress = jQuery('#cho_company_address').val();
        var companyBank = jQuery('#cho_company_bank').val();
        var companyBankAccountNumber = jQuery('#cho_company_bank_account_number').val();
        var companyDeliveringAddress = jQuery('#cho_company_delivering_address').val();
        var ret = true;
        jQuery('.f_company_details .f_error').css({'display': 'none'});
        if (companyName.trim() === '')
        {
            jQuery('#cho_company_name_error').css({'display': 'block'});
            ret = false;
        }
        if (companyHvhh.trim() === '')
        {
            jQuery('#cho_company_hvhh_error').css({'display': 'block'});
            ret = false;
        }
        if (companyAddress.trim() === '')
        {
            jQuery('#cho_company_address_error').css({'display': 'block'});
            ret = false;
        }
        if (companyBank.trim() === '')
        {
            jQuery('#cho_company_bank_error').css({'display': 'block'});
            ret = false;
        }
        if (companyBankAccountNumber.trim() === '')
        {
            jQuery('#cho_company_bank_account_number_error').css({'display': 'block'});
            ret = false;
        }
        if (companyDeliveringAddress.trim() === '')
        {
            jQuery('#cho_company_delivering_address_error').css({'display': 'block'});
            ret = false;
        }
        return ret;
    },
    onDepositChanged: function() {
        this.depositChanged = true;
        var calculatebuttonName = $('calculate_credit_monthly_payments_button').innerHTML;
        jQuery('#dialogNextButtonId').button('option', 'label', calculatebuttonName);
    },
    paymentParamsChanged: function() {
        var payment_data = $("payment_details_form").serialize(true);
        var params = $H(this.params).merge(payment_data);
        ngs.load("payment_step_inner", params.toObject());
        return false;
    }
});
