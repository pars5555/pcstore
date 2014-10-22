ngs.UpdateLanguagePhraseAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin", ajaxLoader);
	},

	getUrl : function() {
		return "do_update_language_phrase";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.evalJSON();		
		if (data.status === "ok") {					
		} else if (data.status === "err") {			
                        ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
