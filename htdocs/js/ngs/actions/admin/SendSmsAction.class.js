ngs.SendSmsAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "do_send_sms";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status === "ok") {
            jQuery('#send_sms_from_pcstore_dialog_container').remove();
        } else if (data.status === "err") {
            jQuery('#ss_error_message').html(data.message);
            jQuery('#admin_send_sms_button').attr('disabled', false);
            jQuery('#admin_send_sms_cancel_button').attr('disabled', false);
        }
    }
});
