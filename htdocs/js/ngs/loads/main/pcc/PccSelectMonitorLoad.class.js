ngs.PccSelectMonitorLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'monitor';
    },
    getUrl: function() {
        return "pcc_select_monitor";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_monitor";
    },
    afterLoad: function() {

        ngs.PcConfiguratorManager.onComponentAfterLoad(9, this.params, this.componentKey);
        jQuery('#pcc_select_component_inner_container .monitor_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#monitor_radio_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .monitor_radios').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.PcConfiguratorManager.monitorSelected(item_id);
        });

        jQuery('#remove_selected_' + this.componentKey + '_component').click(function() {
            ngs.PcConfiguratorManager.monitorSelected(null);
        });


    }
});
