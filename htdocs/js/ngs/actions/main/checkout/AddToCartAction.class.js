ngs.AddToCartAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_checkout", ajaxLoader);
	},

	getUrl : function() {
		return "do_add_to_cart";
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
            jQuery( "#shopping_cart_link" ).effect( 'shake', {}, 300);
		} else if (data.status == "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");	
		}
	}
});
