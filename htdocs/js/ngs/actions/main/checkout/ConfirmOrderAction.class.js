ngs.ConfirmOrderAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_checkout", ajaxLoader);
	},

	getUrl : function() {
		return "do_confirm_order";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {			
			jQuery("#shopping_cart_div").remove();
			window.location.href = "/orders?order_id="+data.order_id;
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.message + "</div>");
		}
	}
});
