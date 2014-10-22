ngs.ChangeOrderStatusAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "do_change_order_status";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status == "ok") {
            if ($('f_show_only_select')) {
                var value = $('f_show_only_select').value;
                ngs.load('your_orders', {
                    'show_only': value
                });
            } else {
                ngs.load('your_orders', {});
            }
        } else if (data.status === "err") {
            ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
        }
    }
});
