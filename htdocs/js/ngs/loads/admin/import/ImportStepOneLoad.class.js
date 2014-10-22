ngs.ImportStepOneLoad = Class.create(ngs.AbstractLoad, {
    linkSourceStockItemId: 0,
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_import", ajaxLoader);
    },
    getUrl: function() {
        return "import_step_one";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "import_price_load_container";
    },
    getName: function() {
        return "import_step_one";
    },
    beforeLoad: function() {
        jQuery("#import_price_load_container").block({message: 'processing...'});
    },
    afterLoad: function() {
        jQuery("#import_price_load_container").unblock();
        var thisObject = this;
        jQuery('#import_price_load_container').dialog('option', 'title', 'Check matched items! Please unbind if they are not matched.');
        var btns = {
            "Prev": {
                text: ngs.LanguageManager.getPhrase(283),
                'class': "translatable_search_content",
                phrase_id: 283,
                click: function() {
                    jQuery('#import_price_load_container').dialog('option', 'buttons', {});
                    ngs.load("import_price", thisObject.params);
                }
            },
            "Next": {
                text: ngs.LanguageManager.getPhrase(282),
                'class': "dialog_default_button_class translatable_search_content",
                phrase_id: 282,
                click: function() {
                    jQuery('#import_price_load_container').dialog('option', 'buttons', {});
                    ngs.load("import_step_two", thisObject.params);
                }
            }
        };
        jQuery('#import_price_load_container').dialog('option', 'buttons', btns);


        jQuery('#is1_aceptable_simillarity_percent').change(function() {
            var aceptable_simillarity_percent = jQuery(this).val();
            ngs.load("import_step_one", jQuery.extend(thisObject.params, {'dont_recalculate': '0', 'aceptable_simillarity_percent': aceptable_simillarity_percent}));

        });



        this.initTableEditableFunctionality();
        this.initUnbindRowEvent();
        this.initLinkSourceTargetButtons();
        jQuery('#is1_popup_menu').menu();
        this.initTdPopupMenuesEvent();
        this.initMenuItemsActions();

    },
    initMenuItemsActions: function() {
        jQuery("#is1_menu_copy").click(function() {
            jQuery('#is1_popup_menu').attr('copiedValue', jQuery('#is1_popup_menu').attr('cellValue'));
        });
        jQuery("#is1_menu_copy_original").click(function() {
            jQuery('#is1_popup_menu').attr('copiedValue', jQuery('#is1_popup_menu').attr('originalCellValue'));
        });
        jQuery("#is1_menu_paste").click(function() {
            var value = jQuery('#is1_popup_menu').attr('copiedValue');
            if (typeof value !== 'undefined')
            {
                var dtoFieldName = jQuery('#is1_popup_menu').attr('dtoFieldName');
                var pk_value = jQuery('#is1_popup_menu').attr('pk_value');
                ngs.action('import_steps_actions_group_action', {'action': "edit_cell_value", "field_name": dtoFieldName, "pk_value": pk_value, 'cell_value': value});
            } else
            {
                ngs.DialogsManager.closeDialog(483, "<div>" + 'First you should copy item!' + "</div>");
            }
        });
        jQuery("#is1_menu_delete").click(function() {
            var dtoFieldName = jQuery('#is1_popup_menu').attr('dtoFieldName');
            var pk_value = jQuery('#is1_popup_menu').attr('pk_value');
            ngs.action('import_steps_actions_group_action', {'action': "edit_cell_value", "field_name": dtoFieldName, "pk_value": pk_value, 'cell_value': ''});

        });
    },
    initTdPopupMenuesEvent: function() {
        jQuery('.is1_popup_menu_td').mousedown(function(e) {
            if (e.button === 2) {
                jQuery('#is1_popup_menu').show();
                jQuery('#is1_popup_menu').css('left', e.clientX + 'px');
                jQuery('#is1_popup_menu').css('top', e.clientY + 'px');
                jQuery('#is1_popup_menu').attr('dtoFieldName', jQuery(this).attr('dtoFieldName'));
                jQuery('#is1_popup_menu').attr('pk_value', jQuery(this).attr('pk_value'));
                jQuery('#is1_popup_menu').attr('cellValue', jQuery(this).attr('cellValue'));
                jQuery('#is1_popup_menu').attr('originalCellValue', jQuery(this).attr('originalCellValue'));
                e.stopPropagation();
                return false;
            }
            return true;
        });
        jQuery('#is1_container_div').click(function(event) {

            jQuery('#is1_popup_menu').hide();
        });

    },
    initLinkSourceTargetButtons: function() {
        var thisObject = this;
        jQuery('.ii_link_source_button').click(function() {
            thisObject.linkSourceStockItemId = jQuery(this).attr('pk_value');

        });
        jQuery('.ii_link_target_button').click(function() {
            if (thisObject.linkSourceStockItemId > 0) {
                var linkTargetPriceRowId = jQuery(this).attr('pk_value');
                var params = jQuery.extend(thisObject.params, {'action': 'step_1_link_stock_item_to_price_item',
                    'price_item_id': linkTargetPriceRowId,
                    'stock_item_id': thisObject.linkSourceStockItemId});
                ngs.action('import_steps_actions_group_action', params);
                thisObject.linkSourceStockItemId = 0;
            } else {
                ngs.DialogsManager.closeDialog(483, "<div>" + 'please select link source!' + "</div>");
            }
        });
    },
    initUnbindRowEvent: function() {
        var thisObject = this;
        jQuery('.is1_unbind_item').click(function() {
            var price_item_id = jQuery(this).attr('price_item_id');
            var params = jQuery.extend(thisObject.params, {'action': 'step_1_unbind_price_row', 'price_item_id': price_item_id});
            ngs.action('import_steps_actions_group_action', params);
        });
    },
    initTableEditableFunctionality: function() {
        var thisInstance = this;
        jQuery('#ii_table_view .editable_cell').dblclick(function(e) {
            if (jQuery('#ii_cell_edit_input').length > 0)
            {
                e.stopPropagation();
                return;
            }
            var cell = jQuery(this);
            var cellValue = cell.html();
            //var cellOriginalValue = cell.find('span:nth-child(2)').html();
            var dtoFieldName = cell.attr('dtoFieldName');
            var pk_value = cell.attr('pk_value');
            var input = jQuery('<textarea style="width:100%" pk_value="' + pk_value + '" dtoFieldName="' + dtoFieldName + '" id="ii_cell_edit_input"></textarea>');
            input.val(cellValue);
            input.click(function(e) {
                e.stopPropagation();
            });
            input.keydown(function(e) {
                if (e.which === 27) {
                    cell.html(cellValue);
                    e.stopPropagation();
                }
                if (e.which === 13) {
                    thisInstance.updateCellValue();
                    e.stopPropagation();
                }
            });
            cell.html(input);
            jQuery('#ii_cell_edit_input').focusout(function(e) {
                thisInstance.updateCellValue();
                e.stopPropagation();
            });
            input.focus();
            e.stopPropagation();
        });

    },
    updateCellValue: function()
    {
        var cellInputElement = jQuery('#ii_table_view #ii_cell_edit_input');
        if (cellInputElement.length > 0) {
            jQuery('#ii_table_view').block({message: 'processing...'});

            var cellValue = cellInputElement.val();
            var dtoFieldName = cellInputElement.attr('dtoFieldName');
            var pk_value = cellInputElement.attr('pk_value');
            ngs.action('import_steps_actions_group_action', {'action': "edit_cell_value", 'cell_value': cellValue, "field_name": dtoFieldName, "pk_value": pk_value});
        }
    },
    onLoadDestroy: function()
    {

    }
});

