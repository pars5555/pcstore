ngs.PostItemToListamAction= Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin", ajaxLoader);
	},

	getUrl : function() {
		return "do_post_item_to_listam";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {			
                        ngs.DialogsManager.closeDialog(534, "<div>"+"Added on List.am" + "</div>");
		} else if (data.status === "err") {			
			 ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
