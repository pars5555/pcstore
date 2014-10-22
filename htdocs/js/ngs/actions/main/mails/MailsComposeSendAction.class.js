ngs.MailsComposeSendAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_mails", ajaxLoader);
	},

	getUrl : function() {
		return "do_mails_compose_send";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {	           
            ngs.load("mails_inbox",{});
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");	
		}
	}
});
