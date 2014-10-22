ngs.PccSelectMouseLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_pcc", ajaxLoader);
		this.componentKey = 'mouse';
	},
	getUrl: function() {
		return "pcc_select_mouse";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "pcc_components_container";
	},
	getName: function() {
		return "pcc_select_mouse";
	},
	afterLoad: function() {

		ngs.PcConfiguratorManager.onComponentAfterLoad(13, this.params, this.componentKey);

		jQuery('#pcc_select_component_inner_container .mouse_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#mouse_radio_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .mouse_radios').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.PcConfiguratorManager.mouseSelected(item_id);
        });

        jQuery('#remove_selected_' + this.componentKey + '_component').click(function() {
            ngs.PcConfiguratorManager.mouseSelected(null);
        });
	}
});
