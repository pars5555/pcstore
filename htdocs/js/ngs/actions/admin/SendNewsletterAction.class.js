ngs.SendNewsletterAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "do_send_newsletter";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        jQuery('#sc_send_newsletter_email').css('visibility', 'visible');
        jQuery('#f_admin_config_newsletter_view_container').unblock();
        if (data.status === "ok") {
            ngs.DialogsManager.closeDialog(534, "<div>Successfully sent to " + data.count + " emails!</div>");
        } else if (data.status === "err") {
            ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
        }
    }
});
