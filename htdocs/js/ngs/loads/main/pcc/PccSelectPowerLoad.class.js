ngs.PccSelectPowerLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'power';
    },
    getUrl: function() {
        return "pcc_select_power";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_power";
    },
    afterLoad: function() {

        ngs.PcConfiguratorManager.onComponentAfterLoad(11, this.params, this.componentKey);
        jQuery('#pcc_select_component_inner_container .power_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#power_radio_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .power_radios').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.PcConfiguratorManager.powerSelected(item_id);
        });
        jQuery('#remove_selected_' + this.componentKey + '_component').click(function() {
            ngs.PcConfiguratorManager.powerSelected(null);
        });

    }
});
