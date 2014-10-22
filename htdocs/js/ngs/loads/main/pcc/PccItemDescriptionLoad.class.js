ngs.PccItemDescriptionLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_pcc", ajaxLoader);
	},
	getUrl: function() {
		return "pcc_item_description";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "pcc_selected_item_details_container";
	},
	getName: function() {
		return "pcc_item_description";
	},
	afterLoad: function() {
		jQuery('#gallery a').lightBox();
	}
});
