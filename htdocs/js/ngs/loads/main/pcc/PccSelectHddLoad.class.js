ngs.PccSelectHddLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'hdd';
    },
    getUrl: function() {
        return "pcc_select_hdd";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_hdd";
    },
    afterLoad: function() {

        ngs.PcConfiguratorManager.onComponentAfterLoad(6, this.params, this.componentKey);

        //ngs.PcConfiguratorManager.hddBackendSelection(selected_components_ids.length > 1 ? selected_components_ids : selected_components_ids.length === 1 ? selected_components_ids[0] : 0);
        var thisInstance = this;
        jQuery('#pcc_select_component_inner_container .hdd_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#hdd_checkbox_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .hdd_checkboxes').change(function() {
            thisInstance.sendHddSelectionChangedtoServer();
        });

        jQuery('#pcc_select_component_inner_container .hdd_select_count').change(function() {
            thisInstance.sendHddSelectionChangedtoServer();
        });
    },
    sendHddSelectionChangedtoServer: function() {
        var selected_hdds = this.getAllSelectedHdds();
        if (selected_hdds.length === 1) {
            selected_hdds = selected_hdds[0];
        }
        ngs.PcConfiguratorManager.hddSelected(selected_hdds);
    },
    getAllSelectedHdds: function() {
        var selectedHddsIds = new Array();        
        jQuery('#pcc_select_component_inner_container .hdd_checkboxes').each(function() {            
            if (jQuery(this).prop('checked'))
            {                
                var item_id = jQuery(this).attr('item_id');
                var itemCount = 1;
                if (jQuery("#hdd_select_count_" + item_id).length > 0) {
                    itemCount = parseInt(jQuery("#hdd_select_count_" + item_id).val());
                }
                while (itemCount >= 1) {
                    itemCount--;
                    selectedHddsIds.push(item_id);
                }
            }
        });
        return selectedHddsIds;
    }
});
