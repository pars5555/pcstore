ngs.SaveNewsletterAction= Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin_newsletters", ajaxLoader);
	},

	getUrl : function() {
		return "do_save_newsletter";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		
	}
});
