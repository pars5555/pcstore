ngs.PccSelectSsdLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'ssd';
    },
    getUrl: function() {
        return "pcc_select_ssd";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_ssd";
    },
    afterLoad: function() {

        ngs.PcConfiguratorManager.onComponentAfterLoad(7, this.params, this.componentKey);
        var thisInstance = this;
        jQuery('#pcc_select_component_inner_container .ssd_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#ssd_checkbox_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .ssd_checkboxes').change(function() {
            thisInstance.sendOptSelectionChangedtoServer();
        });

        jQuery('#pcc_select_component_inner_container .ssd_select_count').change(function() {
            thisInstance.sendOptSelectionChangedtoServer();
        });
    },
    sendOptSelectionChangedtoServer: function() {
        var selected_ssds = this.getAllSelectedSsds();
        if (selected_ssds.length === 1) {
            selected_ssds = selected_ssds[0];
        }
        ngs.PcConfiguratorManager.ssdSelected(selected_ssds);
    },
    getAllSelectedSsds: function() {
        var selectedSsdsIds = new Array();
        jQuery('#pcc_select_component_inner_container .ssd_checkboxes').each(function() {
            if (jQuery(this).prop('checked'))
            {
                var item_id = jQuery(this).attr('item_id');
                var itemCount = 1;
                if (jQuery("#ssd_select_count_" + item_id).length > 0) {
                    itemCount = parseInt(jQuery("#ssd_select_count_" + item_id).val());
                }
                while (itemCount >= 1) {
                    itemCount--;
                    selectedSsdsIds.push(item_id);
                }
            }
        });
        return selectedSsdsIds;
    }
});
