ngs.PccSelectRamLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'ram';
    },
    getUrl: function() {
        return "pcc_select_ram";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_ram";
    },
    afterLoad: function() {

        ngs.PcConfiguratorManager.onComponentAfterLoad(6, this.params, this.componentKey);
        var thisInstance = this;
        jQuery('#pcc_select_component_inner_container .ram_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#ram_radio_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .ram_radios').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.PcConfiguratorManager.ramSelected(item_id);
        });
        jQuery('#pcc_select_component_inner_container .ram_select_count').change(function() {
            var item_id = jQuery(this).attr('item_id');
            var ramsCount = jQuery(this).val();
            var rams_ids = thisInstance.makeArray(ramsCount, item_id);
            ngs.PcConfiguratorManager.ramSelected(rams_ids);
        });
        jQuery('#remove_selected_' + this.componentKey + '_component').click(function() {
            ngs.PcConfiguratorManager.ramSelected(null);
        });
    },
    makeArray: function(howMany, value) {
        var output = new Array();
        while (howMany--) {
            output.push(value);
        }
        return output;
    }
});
