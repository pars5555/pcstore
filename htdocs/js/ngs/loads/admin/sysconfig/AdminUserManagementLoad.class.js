ngs.AdminUserManagementLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_sysconfig", ajaxLoader);
    },
    getUrl: function() {
        return "admin_user_management";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "f_admin_config_user_management_container";
    },
    getName: function() {
        return "admin_user_management";
    },
    afterLoad: function() {
         jQuery('#aum_load_page_button').click(function() {
            jQuery("#f_admin_config_user_management_container").block({message: 'processing...'});
            ngs.load('admin_user_management', {'load_page': 1});
        });
        jQuery('.aum_delete_user_buttons').click(function() {
            var user_id = jQuery(this).attr('user_id');
            ngs.DialogsManager.actionOrCancelDialog('Delete', '', true, '', 'Warning!', '<div>Are you sure you want to delete the user?</div>', function() {
                jQuery("#f_admin_config_user_management_container").block({message: 'processing...'});
                ngs.action('admin_group_actions', {'action': 'delete_user', 'user_id': user_id});
            });
        });
    }
});