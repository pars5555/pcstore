ngs.DeleteNewsletterAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_newsletters", ajaxLoader);
    },
    getUrl: function() {
        return "do_delete_newsletter";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status === 'ok')
        {
            ngs.load("manage_newsletters", {});
        } else
        {
            ngs.DialogsManager.closeDialog(583, "<div>" + data.message + "</div>");

        }
    }
});
