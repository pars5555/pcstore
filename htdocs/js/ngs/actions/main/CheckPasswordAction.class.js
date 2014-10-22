ngs.CheckPasswordAction = Class.create(ngs.AbstractAction, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "do_check_password";
	},
	getMethod: function() {
		return "POST";
	},
	beforeAction: function() {
	},
	afterAction: function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {
			if (this.params.check_login_on_checkout === 'true') {
				jQuery("#dialogNextButtonId").attr("style", "visibility: visible");
			}
		} else
		{
			if (this.params.check_login_on_checkout === 'true') {
				jQuery("#dialogNextButtonId").attr("style", "visibility: hidden");
			}

		}

	}
});
