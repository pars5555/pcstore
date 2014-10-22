ngs.PccSelectOptLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_pcc", ajaxLoader);
		this.componentKey = 'opt';
	},
	getUrl: function() {
		return "pcc_select_opt";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "pcc_components_container";
	},
	getName: function() {
		return "pcc_select_opt";
	},
	afterLoad: function() {

		ngs.PcConfiguratorManager.onComponentAfterLoad(8, this.params, this.componentKey);
 //ngs.PcConfiguratorManager.optBackendSelection(selected_components_ids.length > 1 ? selected_components_ids : selected_components_ids.length === 1 ? selected_components_ids[0] : 0);
        var thisInstance = this;
        jQuery('#pcc_select_component_inner_container .opt_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#opt_checkbox_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .opt_checkboxes').change(function() {
            thisInstance.sendOptSelectionChangedtoServer();
        });

        jQuery('#pcc_select_component_inner_container .opt_select_count').change(function() {
            thisInstance.sendOptSelectionChangedtoServer();
        });
    },
    sendOptSelectionChangedtoServer: function() {
        var selected_opts = this.getAllSelectedOpts();
        if (selected_opts.length === 1) {
            selected_opts = selected_opts[0];
        }
        ngs.PcConfiguratorManager.optSelected(selected_opts);
    },
    getAllSelectedOpts: function() {
        var selectedOptsIds = new Array();        
        jQuery('#pcc_select_component_inner_container .opt_checkboxes').each(function() {            
            if (jQuery(this).prop('checked'))
            {                
                var item_id = jQuery(this).attr('item_id');
                var itemCount = 1;
                if (jQuery("#opt_select_count_" + item_id).length > 0) {
                    itemCount = parseInt(jQuery("#opt_select_count_" + item_id).val());
                }
                while (itemCount >= 1) {
                    itemCount--;
                    selectedOptsIds.push(item_id);
                }
            }
        });
        return selectedOptsIds;
    }
});
