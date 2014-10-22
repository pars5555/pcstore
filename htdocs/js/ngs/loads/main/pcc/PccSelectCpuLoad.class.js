ngs.PccSelectCpuLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'cpu';
    },
    getUrl: function() {
        return "pcc_select_cpu";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_cpu";
    },
    afterLoad: function() {

        ngs.PcConfiguratorManager.onComponentAfterLoad(2, this.params, this.componentKey);

        jQuery('#pcc_select_component_inner_container .cpu_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#cpu_radio_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .cpu_radios').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.PcConfiguratorManager.cpuSelected(item_id);
        });

        jQuery('#remove_selected_' + this.componentKey + '_component').click(function() {
            ngs.PcConfiguratorManager.cpuSelected(null);
        });

    }
});
