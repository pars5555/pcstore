ngs.ChangeItemPriceOrderIndexAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company_item", ajaxLoader);
	},

	getUrl : function() {
		return "do_change_item_price_order_index";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();		
		if (data.status == "ok") {						
			var price_order_index = data.price_order_index;						
			if ($('f_item_price_order_index_in_table_' + data.item_id)){
				$('f_item_price_order_index_in_table_' + data.item_id).style.display = 'block';
				$('f_item_price_order_index_in_table_' + data.item_id).innerHTML = price_order_index;
			}
		} else if (data.status == "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
