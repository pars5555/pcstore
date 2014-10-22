ngs.AddChangeItemAction= Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company_item", ajaxLoader);
	},

	getUrl : function() {
		return "do_add_change_item";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() { 
	},

	afterAction : function(transport) {
		var data = transport.evalJSON();
		if (data.status === "ok") {
			jQuery('#mi_edit_item_dialog').remove();
			ngs.load("manage_items", {	"company_id":$('mi_select_company').value});
		} else if (data.status === "err") {			
			jQuery("#mi_save_button").attr('disabled', false);
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
