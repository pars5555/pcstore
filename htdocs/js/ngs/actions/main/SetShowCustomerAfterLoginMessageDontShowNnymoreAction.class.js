ngs.SetShowCustomerAfterLoginMessageDontShowNnymoreAction = Class.create(ngs.AbstractAction, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "do_set_show_customer_after_login_message_dont_show_any_more";
	},
	getMethod: function() {
		return "POST";
	},
	beforeAction: function() {
	},
	afterAction: function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {			
		} else if (data.status === "err") {

		}
	}
});
