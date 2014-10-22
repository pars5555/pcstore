ngs.UpdateCartItemsCountAction  = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_checkout", ajaxLoader);
	},

	getUrl : function() {
		return "do_update_cart_items_count";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status == "ok") {			
			$("shopping_cart_item_count").innerHTML = data.cart_items_count; 
		} else if (data.status == "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");	
		}
	}
});
