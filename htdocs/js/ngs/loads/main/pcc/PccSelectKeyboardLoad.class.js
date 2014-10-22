ngs.PccSelectKeyboardLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'keyboard';
    },
    getUrl: function() {
        return "pcc_select_keyboard";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_keyboard";
    },
    afterLoad: function() {

        ngs.PcConfiguratorManager.onComponentAfterLoad(12, this.params, this.componentKey);
        jQuery('#pcc_select_component_inner_container .keyboard_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#keyboard_radio_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .keyboard_radios').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.PcConfiguratorManager.keySelected(item_id);
        });

        jQuery('#remove_selected_' + this.componentKey + '_component').click(function() {
            ngs.PcConfiguratorManager.keySelected(null);
        });

    }
});
