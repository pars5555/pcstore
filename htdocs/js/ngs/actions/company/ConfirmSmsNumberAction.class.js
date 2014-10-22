ngs.ConfirmSmsNumberAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "company", ajaxLoader);
    },
    getUrl: function() {
        return "do_confirm_sms_number";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        $('global_modal_loading_div').style.display = 'none';
        var data = transport.responseText.evalJSON();
        if (data.status == "ok") {
            $('cp_error_p').style.display = 'none';
            if (data.number_valid == 'true') {
                $('sms_phone_number').disabled = true;
                $('f_cp_confirm_sms_number_a').style.display = 'none';
                $('f_confirm_sms_code_container').style.display = 'block';
                $('f_sms_code').focus();
            } else if (data.code_valid == 'true') {
                $('sms_number_changed').value = false;
                $('f_confirm_sms_code_container').style.display = 'none';
                $('sms_phone_number').disabled = false;
            }
        } else if (data.status == "err") {
            if (data.number_valid == 'false') {
                $('sms_phone_number').focus();
                $('cp_error_p').style.display = 'block';
                $('cp_error_message').innerHTML = data.errText;

            } else if (data.code_valid == 'false') {
                $('f_sms_code').focus();
                $('cp_error_p').style.display = 'block';
                $('cp_error_message').innerHTML = data.errText;
            }
        }
    }
});
