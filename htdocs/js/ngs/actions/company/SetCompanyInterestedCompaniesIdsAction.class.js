ngs.SetCompanyInterestedCompaniesIdsAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "company", ajaxLoader);
    },
    getUrl: function() {
        return "do_set_company_interested_companies_ids";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status === "ok") {
        } else if (data.status === "warning") {
            ngs.DialogsManager.closeDialog(483, "<div>" + data.message + "</div>");
        }

    }
});
