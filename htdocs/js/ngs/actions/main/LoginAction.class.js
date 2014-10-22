ngs.LoginAction = Class.create(ngs.AbstractAction, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "do_login";
	},
	getMethod: function() {
		return "POST";
	},
	beforeAction: function() {
	},
	afterAction: function(transport) {
		jQuery('#ld_login_form').unblock();
		jQuery("#ld_login_form :input").prop("disabled", false);		
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {			
				window.location.reload(true);		
		} else if (data.status === "err") {
			if ($('ld_login_dialog')) {
				jQuery('#ld_error_msg').html(data.errText);
				jQuery("#ld_login_button").attr('disabled', false);
				jQuery("#ldlg_login_form :input").attr("disabled", false);
			} else {
				ngs.load('login_dialog', {"email": this.params.user_email});
			}

		}
	}
});
