ngs.InviteUserAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},

	getUrl : function() {
		return "do_invite_user";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {
			ngs.DialogsManager.closeDialog(534, "<div>" + data.message + "</div>");
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");	
		}
		ngs.load("pending_users_list", {});		
	}
});
