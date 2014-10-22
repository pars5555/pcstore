ngs.ResetCompanyItemsIndexesAction= Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin", ajaxLoader);
	},

	getUrl : function() {
		return "do_reset_company_items_indexes";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {
			ngs.load("manage_items", {'company_id' : $('mi_select_company').value});	
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");	
		}
	}
});
