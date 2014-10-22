ngs.ChangeItemDisplayNameAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company_item", ajaxLoader);
	},

	getUrl : function() {
		return "do_change_item_display_name";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();		
		if (data.status == "ok") {						
			var display_name = data.display_name;						
			if ($('f_item_display_name_in_table_' + data.item_id)){
				$('f_item_display_name_in_table_' + data.item_id).style.display = 'block';
				$('f_item_display_name_in_table_' + data.item_id).innerHTML = display_name;
			}
		} else if (data.status == "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
