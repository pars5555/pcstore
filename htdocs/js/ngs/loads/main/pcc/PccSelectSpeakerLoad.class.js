ngs.PccSelectSpeakerLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
        this.componentKey = 'speaker';
    },
    getUrl: function() {
        return "pcc_select_speaker";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_components_container";
    },
    getName: function() {
        return "pcc_select_speaker";
    },
    afterLoad: function() {
        ngs.PcConfiguratorManager.onComponentAfterLoad(14, this.params, this.componentKey);

        jQuery('#pcc_select_component_inner_container .speaker_titles').click(function() {
            var item_id = jQuery(this).attr('item_id');
            jQuery('#speaker_radio_' + item_id).trigger('click');
        });
        jQuery('#pcc_select_component_inner_container .speaker_radios').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.PcConfiguratorManager.speakerSelected(item_id);
        });
        jQuery('#remove_selected_' + this.componentKey + '_component').click(function() {
            ngs.PcConfiguratorManager.speakerSelected(null);
        });

    }
});
