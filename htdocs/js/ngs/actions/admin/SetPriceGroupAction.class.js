ngs.SetPriceGroupAction= Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin", ajaxLoader);
	},

	getUrl : function() {
		return "do_set_price_group";
	},

	getMethod : function() {
		return "GET";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {
					
		} else if (data.status === "err") {
			
		}
	}
});
