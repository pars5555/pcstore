ngs.ChangeUserProfileAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "do_change_user_profile";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status === "ok") {
            if (typeof data.cart_items_count !== 'undefined') {
                $("shopping_cart_item_count").innerHTML = data.cart_items_count;
            }
            ngs.DialogsManager.closeDialog(534, "<div>" + ngs.LanguageManager.getPhraseSpan(601)+ "</div>");
            ngs.load('your_profile', {'refresh': 1});
        } else if (data.status === "err") {
            ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
        }
    }
});
