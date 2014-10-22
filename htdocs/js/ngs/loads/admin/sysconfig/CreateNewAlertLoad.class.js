ngs.CreateNewAlertLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_sysconfig", ajaxLoader);
    },
    getUrl: function() {
        return "create_new_alert";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "f_create_new_alert_container_div";
    },
    getName: function() {
        return "create_new_alert";
    },
    afterLoad: function() {
        this.initTinyMCE("textarea.alertTinyMCEEditor");
        jQuery('#cns_preview').click(function() {
            tinyMCE.activeEditor.save();
            var message = {'id': 0, 'title_formula': jQuery('#cna_alert_title').val(), 'message_formula': jQuery('#cna_alert_message').val()
            , 'type': jQuery('#cna_alert_type').val(), 'showed_count': 0, 'shows_count': 1};           
            ngs.PingPongAction.prototype.openAfterLoginCustmerMessagesDialogSinlgeMessage(message, true);
        });
        jQuery('#cna_to_emails').click(function() {
            var customer_emails = new Array();
            jQuery('.cna_emails').each(function()
            {
                customer_emails.push(jQuery(this).attr('cust_email'));
            });
            ngs.load("insert_contact", {'customer_emails': customer_emails, 'result_element_id': 'cna_to_emails', 'result_inner_divs_class': 'cna_emails'});
        });
    }
});