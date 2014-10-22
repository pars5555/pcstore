ngs.RemoveCategoryAction= Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin", ajaxLoader);
	},

	getUrl : function() {
		return "do_remove_category";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {
			ngs.load('items_categories');			
		} else if (data.status === "err") {
			 ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
