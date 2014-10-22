ngs.LogoutAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},

	getUrl : function() {
		return "do_logout";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		
	}
});
