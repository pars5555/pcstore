ngs.RemoveCompanyPriceAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "do_remove_company_price";
    },
    getMethod: function() {
        return "GET";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {

        var data = transport.responseText.evalJSON();

        if (data.status === "ok") {
            this.updatePageIfNeeded();
        } else if (data.status === "err") {
            this.updatePageIfNeeded();
            ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
        }
    },
    updatePageIfNeeded: function() {
        if ($('upload_price')) {
            if ($('up_selected_company')) {
                ngs.load('upload_price', {
                    'selected_company': $('up_selected_company').value
                });
            } else {
                ngs.load('upload_price');
            }
        }
    }
});
