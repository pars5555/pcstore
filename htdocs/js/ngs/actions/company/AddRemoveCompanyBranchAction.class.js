ngs.AddRemoveCompanyBranchAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "company", ajaxLoader);
    },
    getUrl: function() {
        return "do_add_remove_company_branch";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        jQuery("#cp_add_branch_button").attr("disabled", true);
        jQuery("#cp_cancel_add_branch_button").attr("disabled", true);
        jQuery('#cp_create_new_branch_dialog').unblock();
        var data = transport.responseText.evalJSON();
        if (data.status === "ok") {
            jQuery('#cp_create_new_branch_dialog').remove();
            $('global_modal_loading_div').style.display = 'block';
            ngs.load("company_profile", {'selected_branch_id': data.selected_branch_id, 'refresh':1});
        } else
        {
             ngs.DialogsManager.closeDialog(583, "<div>" + data.errMsg + "</div>");
        }

    }
});
