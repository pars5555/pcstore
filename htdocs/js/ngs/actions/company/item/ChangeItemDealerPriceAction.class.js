ngs.ChangeItemDealerPriceAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company_item", ajaxLoader);
	},

	getUrl : function() {
		return "do_change_item_dealer_price";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();		
		if (data.status == "ok") {						
			var dealer_price = parseFloat(data.dealer_price);									
			if ($('f_item_dealer_price_in_table_' + data.item_id)){
				$('f_item_dealer_price_in_table_' + data.item_id).style.display = 'block';
				$('f_item_dealer_price_in_table_' + data.item_id).innerHTML = dealer_price.formatMoney(2,'.',',');
			}			
			if ($('f_item_dealer_price_amd_in_table_' + data.item_id)){
				$('f_item_dealer_price_amd_in_table_' + data.item_id).style.display = 'block';
				$('f_item_dealer_price_amd_in_table_' + data.item_id).innerHTML = 0;
			}
		} else if (data.status == "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
