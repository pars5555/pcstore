ngs.ConfirmStripePaymentAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_checkout", ajaxLoader);
    },
    getUrl: function() {
        return "do_confirm_stripe_payment";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status == "ok") {
            if (jQuery('#active_load_name').val() == 'your_orders') {
                ngs.load('your_orders');
            }
        } else if (data.status == "err") {
            ngs.DialogsManager.closeDialog(583, "<div>" + data.message + "</div>");
        }
    }
});
