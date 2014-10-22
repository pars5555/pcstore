ngs.AddRemoveItemPictureAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "company_item", ajaxLoader);
    },
    getUrl: function() {
        return "do_add_remove_item_picture";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = {};
        if (typeof transport.responseText !== 'undefined') {
            data = transport.responseText.evalJSON();
        } else {
            data = transport.evalJSON();
        }
        if (data.status === "ok") {
            var item_id = data.item_id;
            ngs.load("item_pictures", {'item_id': item_id});
        } else if (data.status === "err") {
            jQuery("#mi_save_button").attr('disabled', false);
            ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
        }
    }
});
