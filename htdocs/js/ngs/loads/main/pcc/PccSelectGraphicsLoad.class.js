ngs.PccSelectGraphicsLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'graphics';
    },
    getUrl: function() {
        return "pcc_select_graphics";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_graphics";
    },
    afterLoad: function() {

        ngs.PcConfiguratorManager.onComponentAfterLoad(10, this.params, this.componentKey);
        var thisInstance = this;
        jQuery('#pcc_select_component_inner_container .graphics_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#graphics_checkbox_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .graphics_checkboxes').change(function() {
            thisInstance.sendGraphicsSelectionChangedtoServer();
        });

        jQuery('#pcc_select_component_inner_container .graphics_select_count').change(function() {
            thisInstance.sendGraphicsSelectionChangedtoServer();
        });
    },
    sendGraphicsSelectionChangedtoServer: function() {
        var selected_graphics = this.getAllSelectedGraphicss();
        if (selected_graphics.length === 1) {
            selected_graphics = selected_graphics[0];
        }
        ngs.PcConfiguratorManager.graphicsSelected(selected_graphics);
    },
    getAllSelectedGraphicss: function() {
        var selectedGraphicsIds = new Array();
        jQuery('#pcc_select_component_inner_container .graphics_checkboxes').each(function() {
            if (jQuery(this).prop('checked'))
            {
                var item_id = jQuery(this).attr('item_id');
                var itemCount = 1;
                if (jQuery("#graphics_select_count_" + item_id).length > 0) {
                    itemCount = parseInt(jQuery("#graphics_select_count_" + item_id).val());
                }
                while (itemCount >= 1) {
                    itemCount--;
                    selectedGraphicsIds.push(item_id);
                }
            }
        });
        return selectedGraphicsIds;
    }
});