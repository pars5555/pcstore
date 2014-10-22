ngs.PccSelectCoolerLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_pcc", ajaxLoader);
		this.componentKey = 'cooler';
	},
	getUrl: function() {
		return "pcc_select_cooler";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "pcc_components_container";
	},
	getName: function() {
		return "pcc_select_cooler";
	},
	afterLoad: function() {

		ngs.PcConfiguratorManager.onComponentAfterLoad(4, this.params, this.componentKey);

		jQuery('#pcc_select_component_inner_container .cooler_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#cooler_radio_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .cooler_radios').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.PcConfiguratorManager.coolerSelected(item_id);
        });
		 jQuery('#remove_selected_' + this.componentKey + '_component').click(function() {
            ngs.PcConfiguratorManager.coolerSelected(null);
        });
	}
});
