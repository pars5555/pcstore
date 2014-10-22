ngs.UpdateAllAmdItemsPricesAction= Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin", ajaxLoader);
	},

	getUrl : function() {
		return "do_update_all_amd_items_prices";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {
			ngs.DialogsManager.closeDialog(534, "<div>" + 'Updated!' + "</div>");
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.message + "</div>");
		}
	}
});
