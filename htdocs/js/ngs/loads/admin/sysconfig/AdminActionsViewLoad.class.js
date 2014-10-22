ngs.AdminActionsViewLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_sysconfig", ajaxLoader);
    },
    getUrl: function() {
        return "admin_actions_view";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "f_admin_actions_view_container";
    },
    getName: function() {
        return "admin_actions_view";
    },
    afterLoad: function() {
        var thisInstance = this;
        jQuery("#update_all_amd_items_prices_link").click(function() {
            ngs.action('update_all_amd_items_prices_action', {});
        });
        jQuery("#update_all_companies_price_texts_link").click(function() {
            thisInstance.openSelectCompanyDialog();
        });
        jQuery("#delete_all_unnecessary_items_pictures_link").click(function() {
            ngs.action('admin_group_actions', {'action': 'delete_all_unnecessary_items_pictures'});
        });
        jQuery("#deploy_latest_pcstore_changes").click(function() {
            ngs.action('admin_group_actions', {'action': 'deploy_latest_pcstore_changes'});
        });
        jQuery("#delete_old_hidden_items").click(function() {
            var optionText = "Older than ";
            var option1Month = "<option value='1'>" + optionText + '1 month' + "</option>";
            var option2Months = "<option value='2'>" + optionText + '2 months' + "</option>";
            var option3Months = "<option value='3'>" + optionText + '3 months' + "</option>";
            var option6Months = "<option value='6'>" + optionText + '6 months' + "</option>";
            var option12Months = "<option value='12'>" + optionText + '12 months' + "</option>";
            var html = "<div><select id='delete_old_hidden_items_month_select'>" +
                    option1Month + option2Months + option3Months +
                    option6Months + option12Months + "</select></div>";
            ngs.DialogsManager.actionOrCancelDialog('Remove', null, true, null,
                    'Deleting old hidden items!', html, function() {
                        var monthsNumber = jQuery('#delete_old_hidden_items_month_select').val();
                        ngs.action('admin_group_actions', {'action': 'delete_old_hidden_items', "months_number": monthsNumber});
                    });
        });
    },
    openSelectCompanyDialog: function() {
        ngs.DialogsManager.actionOrCancelDialog('Update Price Text', null, true, null, 'Update Company Price Text',
                '#select_company_to_update_price_text_dialog_root_div', function() {
                    var company_id = jQuery('#select_company_to_update_price_text').val();
                    ngs.action('admin_group_actions', {'action': 'update_companies_price_text', 'company_id': company_id});
                }, false, 400, 200, false);
    }
});