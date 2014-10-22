ngs.ChangeItemHiddenAttributeAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company_item", ajaxLoader);
	},

	getUrl : function() {
		return "do_change_item_hidden_attribute";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();		
		if (data.status === "ok") {						
			var item_available = data.is_item_available;
			var item_hidden = data.is_item_hidden;			
			if (item_hidden == '0' && item_available == '1') {
				$('item_display_name_td_field_' + data.item_id).style.color = '';
			} else {
				$('item_display_name_td_field_' + data.item_id).style.color = 'red';
			}
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
