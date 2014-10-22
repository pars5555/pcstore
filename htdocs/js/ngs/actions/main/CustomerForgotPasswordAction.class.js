ngs.CustomerForgotPasswordAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},

	getUrl : function() {
		return "do_customer_forgot_password";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {				
			if ($('fl_forgot_login_dialog'))
			{
				jQuery('#fl_forgot_login_dialog').remove();
			}			
		} else if (data.status === "err") {
			if ($('fl_forgot_form'))
			{
				jQuery('#fl_forgot_error_msg').html(data.errText);
				jQuery("#fl_send_email_button").attr('disabled', false);
				jQuery("#fl_forgot_form :input").attr("disabled", false);
			}
		}
	}
});
