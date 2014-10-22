ngs.PccSelectMbLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'mb';
    },
    getUrl: function() {
        return "pcc_select_mb";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_mb";
    },
    afterLoad: function() {

        ngs.PcConfiguratorManager.onComponentAfterLoad(3, this.params, this.componentKey);

        jQuery('#pcc_select_component_inner_container .mb_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#mb_radio_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .mb_radios').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.PcConfiguratorManager.mbSelected(item_id);
        });

        jQuery('#remove_selected_' + this.componentKey + '_component').click(function() {
            ngs.PcConfiguratorManager.mbSelected(null);
        });


    }
});
