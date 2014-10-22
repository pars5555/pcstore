ngs.PccTotalCalculationsLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_pcc", ajaxLoader);
    },
    getUrl: function() {
        return "pcc_total_calculations";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "pcc_total_calculation_container";
    },
    getName: function() {
        return "pcc_total_calculations";
    },
    afterLoad: function() {
        ngs.nestLoad("pcc_credit_calculation", {});
        if ($('pcc_footer_order_button')) {
            $('pcc_footer_order_button').onclick = this.onOrderPc.bind(this);
        }
        jQuery('#pcc_footer_login_order_button').click(function() {
            ngs.load('login_dialog', {});
        });
        this.initPrintBtn();
    },
    initPrintBtn: function() {
        var thisInstance = this;
        jQuery('#pcc_print_button').click(function() {
            var selected_components = jQuery.param(ngs.PcConfiguratorManager.getSelectedComponentsParam());
            var iframe = "<div><iframe style='width:100%;height:100%;'  scrolling='no' id='pcc_print_iframe' src='//" + SITE_URL + "/print_pcc?" + selected_components + "'></iframe></div>";
            jQuery(iframe).dialog({
                resizable: true,
                title: ngs.LanguageManager.getPhraseSpan(630),
                modal: true,
                width: 800,
                height: 600,
                buttons: {
                    "Action": {
                        text: ngs.LanguageManager.getPhrase(629),
                        'class': "dialog_default_button_class translatable_search_content",
                        phrase_id: 629,
                        click: function() {
                            thisInstance.printFrame("pcc_print_iframe");
                        }
                    },
                    "Cancel": {
                        text: ngs.LanguageManager.getPhrase(49),
                        'class': "translatable_search_content",
                        phrase_id: 49,
                        click: function() {
                            jQuery(this).remove();
                        }
                    }
                },
                close: function() {
                    jQuery(this).remove();
                },
                open: function(event, ui) {
                    jQuery(this).parent().attr('phrase_id', 630);
                    jQuery(this).parent().addClass('translatable_search_content');
                }
            });
        });
    },
    onOrderPc: function() {
        $('global_modal_loading_div').style.display = 'block';
        var selected_components = ngs.PcConfiguratorManager.getSelectedComponentsIdsJoinWithComma();

        ngs.action("add_configurator_built_pc_to_cart_action", {
            "bundle_items_ids": selected_components,
            "replace_cart_row_id": (parseInt(this.params.configurator_mode_edit_cart_row_id) > 0 ? parseInt(this.params.configurator_mode_edit_cart_row_id) : 0)
        });
    }
});
