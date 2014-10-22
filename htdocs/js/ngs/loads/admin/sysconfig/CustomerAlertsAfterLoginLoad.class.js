ngs.CustomerAlertsAfterLoginLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_sysconfig", ajaxLoader);
    },
    getUrl: function() {
        return "customer_alerts_after_login";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "f_customer_alerts_after_login_container";
    },
    getName: function() {
        return "customer_alerts_after_login";
    },
    afterLoad: function() {
        var thisInstance = this;
        jQuery('#ca_add_new_alert_button').off();
        jQuery('#ca_add_new_alert_button').click(function() {
            ngs.DialogsManager.actionOrCancelDialog('add', 'cna_create_alert', false, 'cna_cancel', 'title', '<div id="f_create_new_alert_container_div"></div>',
                    thisInstance.sendCreateAlertAction, false, 700, 600);
            ngs.load('create_new_alert', {});
        });
        jQuery('.f_ca_delete_row').click(function() {
            var pk = jQuery(this).attr('pk');
            ngs.action('admin_group_actions', {'action': 'delete_customer_amessage_after_login', 'id': pk});
        });
        jQuery('.f_ca_preview_row').click(function() {
            var pk = jQuery(this).attr('pk');
            ngs.action('admin_group_actions', {'action': 'preview_customer_message', 'id': pk});
            
        });
        
    },
    sendCreateAlertAction: function() {
        var customer_emails = new Array();
        jQuery('.cna_emails').each(function()
        {
            customer_emails.push(jQuery(this).attr('cust_email'));
        });
        tinyMCE.activeEditor.save();
        ngs.action('admin_create_alert', {'to': customer_emails.join(','), 'title_formula': jQuery('#cna_alert_title').val(), 'message_formula': jQuery('#cna_alert_message').val()
            , 'type': jQuery('#cna_alert_type').val(), 'shows_count': jQuery('#cna_alert_shows_count').val()});
    }
});