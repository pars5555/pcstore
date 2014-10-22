ngs.CopyItemAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},

	getUrl : function() {
		return "do_copy_item";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {
			jQuery('#paste_item_button').css('visibility','visible');
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");		
		}
	}
});
