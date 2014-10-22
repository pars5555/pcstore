ngs.ContactUsAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},

	getUrl : function() {
		return "do_contact_us";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {
			jQuery("#f_contactus_container_dialog").remove();			
            ngs.DialogsManager.closeDialog(534, "<div>" + data.message + "</div>");            
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.message + "</div>");
            jQuery('#f_contact_us_send_button').attr('disabled', false);
            
		}
	}
});
