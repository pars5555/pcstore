ngs.ChangeItemVatPriceAmdAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company_item", ajaxLoader);
	},

	getUrl : function() {
		return "do_change_item_vat_price_amd";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();		
		if (data.status === "ok") {						
			var vat_price_amd = parseInt(data.vat_price_amd);						
			if ($('f_item_vat_price_amd_in_table_' + data.item_id)){
				$('f_item_vat_price_amd_in_table_' + data.item_id).style.display = 'block';
				$('f_item_vat_price_amd_in_table_' + data.item_id).innerHTML = vat_price_amd;
			}
			var vat_price = parseFloat(data.vat_price);		
			if ($('f_item_vat_price_in_table_' + data.item_id)){
				$('f_item_vat_price_in_table_' + data.item_id).style.display = 'block';
				$('f_item_vat_price_in_table_' + data.item_id).innerHTML = vat_price.formatMoney(2,'.',',');
			}
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
