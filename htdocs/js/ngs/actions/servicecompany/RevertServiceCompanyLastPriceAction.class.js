ngs.RevertServiceCompanyLastPriceAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "servicecompany", ajaxLoader);
	},

	getUrl : function() {
		return "do_revert_service_company_last_price";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {			
			jQuery('#tab_link_service_upload_price').trigger('click');
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
