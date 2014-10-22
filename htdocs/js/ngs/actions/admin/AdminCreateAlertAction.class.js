ngs.AdminCreateAlertAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "do_create_alert";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status === "ok") {
            jQuery('#f_create_new_alert_container_div').dialog('close');
            ngs.load('customer_alerts_after_login', {});
        } else if (data.status === "err") {
            jQuery('#cna_error_message').html(data.message);
            jQuery('#cna_create_alert').attr('disabled', false);
            jQuery('#cna_cancel').attr('disabled', false);
        }
    }
});
