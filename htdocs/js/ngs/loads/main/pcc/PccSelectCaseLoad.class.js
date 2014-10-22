ngs.PccSelectCaseLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'case';
    },
    getUrl: function() {
        return "pcc_select_case";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_case";
    },
    afterLoad: function() {
        ngs.PcConfiguratorManager.onComponentAfterLoad(1, this.params, this.componentKey);
        jQuery('#pcc_select_component_inner_container .case_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#case_radio_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .case_radios').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.PcConfiguratorManager.caseSelected(item_id);
        });
        jQuery('#remove_selected_' + this.componentKey + '_component').click(function() {
            ngs.PcConfiguratorManager.caseSelected(null);
        });
    }


});
