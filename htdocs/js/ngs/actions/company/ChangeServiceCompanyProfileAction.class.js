ngs.ChangeServiceCompanyProfileAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "company", ajaxLoader);
    },
    getUrl: function() {
        return "do_change_service_company_profile";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.evalJSON();
        if (data.status === "ok") {
            if (data.message && data.message !== 'ok' && data.message !== 'No file was uploaded.') {                
                ngs.DialogsManager.closeDialog(483, "<div>" + data.message + "</div>");
            } else {
                ngs.DialogsManager.closeDialog(534, "<div>" + 'You have seccussfully updated company profile!' + "</div>");
            }            
            ngs.load('service_company_profile', {'selected_branch_id':jQuery("#cp_branch_select").val(), 'refresh': 1});
        } else if (data.status === "err") {
            ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
        }
    }
});
