ngs.AdminSendSmsLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "send_sms";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "send_sms_from_pcstore_dialog_container";
    },
    getName: function() {
        return "admin_send_sms";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
        jQuery('<div id="send_sms_from_pcstore_dialog_container"></div>').appendTo('body');
    },
    afterLoad: function() {
        $('global_modal_loading_div').style.display = 'none';
        ngs.DialogsManager.actionOrCancelDialog('Send SMS', 'admin_send_sms_button', false, 'admin_send_sms_cancel_button', 'Send SMS from Pcstore', '#send_sms_from_pcstore_dialog_container', function() {
            var phone_number = jQuery('#ss_phone_number').val();
            var message = jQuery('#ss_message').val();
            var gateway = jQuery('#ss_gateway').val();
            ngs.action('admin_send_sms', {'phone_number': phone_number, 'gateway': gateway, 'message': message});
        }, true, 500, 400);

    },
    onLoadDestroy: function()
    {

    }
});

