ngs.RemoveDealerFromCompanyAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company", ajaxLoader);
	},

	getUrl : function() {
		return "do_remove_dealer_from_company";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {
			ngs.load('dealers_list');
			ngs.DialogsManager.closeDialog(534, "<div>" + data.message + "</div>");
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");	
		}
	}
});
