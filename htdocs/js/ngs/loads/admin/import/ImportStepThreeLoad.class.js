ngs.ImportStepThreeLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_import", ajaxLoader);
    },
    getUrl: function() {
        return "import_step_three";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "import_price_load_container";
    },
    getName: function() {
        return "import_step_three";
    },
    beforeLoad: function() {
        jQuery("#import_price_load_container").block({message: 'processing...'});
    },
    afterLoad: function() {
        jQuery("#import_price_load_container").unblock();
        var thisInstance = this;
        jQuery('#import_price_load_container').dialog('option', 'title', 'stock items that should be changed!');
        var btns = {
            "Prev": {
                text: ngs.LanguageManager.getPhrase(283),
                'class': "translatable_search_content",
                phrase_id: 283,
                click: function() {
                    jQuery('#import_price_load_container').dialog('option', 'buttons', {});
                    ngs.load("import_step_two", thisInstance.params);
                }
            },
            "Next": {
                text: 'import',
                'class': "dialog_default_button_class",
                click: function() {
                    jQuery('#import_price_load_container').dialog('option', 'buttons', {});
                    var changedRowsIds = new Array();
                    jQuery('.is3_include_row').each(function() {
                        if (jQuery(this).prop('checked'))
                        {
                            changedRowsIds.push(jQuery(this).attr('row_id'));
                        }
                    });
                    ngs.action('import_steps_actions_group_action', jQuery.extend(thisInstance.params, {'action': "import", 'changed_rows_ids': changedRowsIds.join(',')}));
                    jQuery(this).remove();
                }
            }

        };
        jQuery('#import_price_load_container').dialog('option', 'buttons', btns);
        this.subCategoriesSelectionButtonsInit();
        this.imageSelectionButtonsInit();
        jQuery('#is3_popup_menu').menu();
        this.initTdPopupMenuesEvent();
        this.initMenuItemsActions();
        this.initTableEditableFunctionality();
        jQuery('#is3_select_all').click(function() {
            jQuery('.is3_include_row').prop('checked', true);
            jQuery('.is3_include_row').trigger('change');
        });
        jQuery('#is3_select_none').click(function() {
            jQuery('.is3_include_row').prop('checked', false);
            jQuery('.is3_include_row').trigger('change');
        });
        jQuery('.is3_include_row').change(function() {
            thisInstance.hilightSelectedRows();
        });

        jQuery('.is3_simillar_item_search_texts').keydown(function(e) {
            if (e.which === 13) {
                var pk_value = jQuery(this).attr('pk_value');
                jQuery('#is3_find_simillar_items_button_' + pk_value).trigger('click');
                e.stopPropagation();
            }
        });
        jQuery('.is3_find_simillar_items_button').click(function() {
            var pk_value = jQuery(this).attr('pk_value');
            var searchText = jQuery('#is3_simillar_item_search_text_' + pk_value).val();
            ngs.action('import_steps_actions_group_action',
                    {'action': "find_similar_items",
                        'pk_value': pk_value,
                        'search_text': searchText}
            );
        });

        jQuery('.is3_simillar_items_select').change(function() {
            var simillar_item_id = jQuery(this).val();
            var pk_value = jQuery(this).attr('pk_value');
            ngs.action('import_steps_actions_group_action',
                    {'action': "edit_cell_value",
                        'pk_value': pk_value,
                        'cell_value': simillar_item_id,
                        'field_name': 'simillarItemId'});
            if (simillar_item_id > 0) {
                ngs.action('import_steps_actions_group_action',
                        {'action': "get_item_cat_spec",
                            'pk_value': pk_value,
                            'item_id': simillar_item_id,
                            'step_number': 3
                        });
            }
        });

        this.hilightSelectedRows();
        this.manageItemsSpecButtonsActions();
    },
    manageItemsSpecButtonsActions: function() {
         var thisInstance = this;
        jQuery('.is3_spec_button').click(function() {
            var pkValue = jQuery(this).attr('pk_value');
            var shortSpec = jQuery('#is3_item_short_spec_' + pkValue).val();
            var fullSpec = jQuery('#is3_item_full_spec_' + pkValue).val();
            jQuery('<div>short spec<br><textarea id="item_short_spec_input" style="width:100%;height:80px;border:1px solid gray" >' + shortSpec + '</textarea>' +
                    '<br>full spec<br><textarea id="item_full_spec_input" style="width:100%;min-height:300px;">' +
                    fullSpec + '</textarea></div>').dialog({
                resizable: true,
                width: 1100,
                height: 600,
                modal: true,
                buttons: {
                    "ok": {
                        text: 'ok',
                        click: function() {
                             tinymce.activeEditor.save();
                            var sSpec = jQuery('#item_short_spec_input').val();
                            ngs.action('import_steps_actions_group_action',
                                    {'action': "edit_cell_value", "field_name": "shortSpec",
                                        "pk_value": pkValue,
                                        'cell_value': sSpec});
                            jQuery('#is3_item_short_spec_' + pkValue).val(sSpec);


                            var fSpec = jQuery('#item_full_spec_input').val();
                            ngs.action('import_steps_actions_group_action',
                                    {'action': "edit_cell_value", "field_name": "fullSpec",
                                        "pk_value": pkValue,
                                        'cell_value': fSpec});
                            jQuery('#is3_item_full_spec_' + pkValue).val(fSpec);
                            jQuery(this).remove();
                        }
                    },
                    "cancel": {
                        text: 'cancel',
                        click: function() {
                            jQuery(this).remove();
                        }
                    }

                },
                close: function() {
                    jQuery(this).remove();
                },
                open: function(event, ui) {

                    thisInstance.initTinyMCE("textarea#item_full_spec_input");
                }
            });

        });
    },   
    hilightSelectedRows: function()
    {
        jQuery('.is3_include_row').each(function() {
            if (jQuery(this).prop('checked')) {
                jQuery(this).closest('tr').children('td').css('background-color', 'yellow');
            } else
            {
                jQuery(this).closest('tr').children('td').css('background-color', '');
            }
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
    initMenuItemsActions: function() {
        jQuery("#is3_take_stock_value").click(function() {
            var cellIds = jQuery('#is3_popup_menu').attr('cellIds');

            var cellIdsArray = cellIds.split(',');
            for (var i = 0; i < cellIdsArray.length; i++)
            {
                var cellId = cellIdsArray[i];
                var cell = jQuery('#' + cellId);
                var value = cell.attr('stockItemValue');
                var dtoFieldName = cell.attr('fieldName');
                var pk_value = cell.attr('pk_value');
                ngs.action('import_steps_actions_group_action', {'action': "edit_cell_value", "field_name": dtoFieldName, "pk_value": pk_value, 'cell_value': value});
                jQuery('#is3_editable_td_' + dtoFieldName + '_' + pk_value).css('color', 'black');
                jQuery('#ii_table_editable_span_' + pk_value + '_' + dtoFieldName).html(value);
            }
            jQuery('#is3_popup_menu').hide();
        });
        jQuery("#is3_menu_delete").click(function() {
            var cellIds = jQuery('#is3_popup_menu').attr('cellIds');

            var cellIdsArray = cellIds.split(',');
            for (var i = 0; i < cellIdsArray.length; i++)
            {
                var cellId = cellIdsArray[i];
                var cell = jQuery('#' + cellId);
                var dtoFieldName = cell.attr('fieldName');
                var pk_value = cell.attr('pk_value');
                ngs.action('import_steps_actions_group_action', {'action': "edit_cell_value", "field_name": dtoFieldName, "pk_value": pk_value, 'cell_value': ''});
                jQuery('#is3_editable_td_' + dtoFieldName + '_' + pk_value).css('color', 'black');
                jQuery('#ii_table_editable_span_' + pk_value + '_' + dtoFieldName).html('');
            }

            jQuery('#is3_popup_menu').hide();
        });
    },
    initTdPopupMenuesEvent: function() {
        jQuery('.is3_popup_menu_th').mousedown(function(e) {
            if (e.button === 2) {
                var fieldName = jQuery(this).attr('fieldName');
                var cellIdsArray = new Array();
                jQuery('.is3_popup_menu_td.' + fieldName).each(function() {
                    cellIdsArray.push(jQuery(this).attr('id'));
                });
                jQuery('#is3_popup_menu').show();
                jQuery('#is3_popup_menu').css('left', e.clientX + 'px');
                jQuery('#is3_popup_menu').css('top', e.clientY + 'px');
                jQuery('#is3_popup_menu').attr('cellIds', cellIdsArray.join(','));

                e.stopPropagation();
                return false;
            }
            return true;
        });
        jQuery('.is3_popup_menu_td').mousedown(function(e) {
            if (e.button === 2) {
                jQuery('#is3_popup_menu').show();
                jQuery('#is3_popup_menu').css('left', e.clientX + 'px');
                jQuery('#is3_popup_menu').css('top', e.clientY + 'px');
                var cellId = jQuery(this).attr('id');
                jQuery('#is3_popup_menu').attr('cellIds', cellId);
                /*jQuery('#is3_popup_menu').attr('fieldName', jQuery(this).attr('fieldName'));
                 jQuery('#is3_popup_menu').attr('pk_value', jQuery(this).attr('pk_value'));
                 jQuery('#is3_popup_menu').attr('stockItemValue', jQuery(this).attr('stockItemValue'));*/
                e.stopPropagation();
                return false;
            }
            return true;
        });
        jQuery('#is3_container_div').click(function(event) {

            jQuery('#is3_popup_menu').hide();
        });

    },
    imageSelectionButtonsInit: function() {
        $(function() {
            $('.fileupload').fileupload({
                dataType: 'json',
                done: function(e, data) {
                    $.each(data.result.files, function(index, file) {
                        $('<p/>').text(file.name).appendTo(document.body);
                    });
                }
            });

        });

    },
    subCategoriesSelectionButtonsInit: function() {
        jQuery('.is2_sub_categories_button').click(function() {
            var pk_value = jQuery(this).attr('pk_value');
            var root_category_id = jQuery("#is2_price_item_root_category_" + pk_value).val();
            jQuery('<div id="f_sub_category_dialog_container"></div>').appendTo("body");
            ngs.load("sub_categories_selection", {
                "item_root_category": root_category_id,
                "result_hidden_element_id": 'is2_price_item_sub_categories_ids_' + pk_value
            });
        });



    },
    onLoadDestroy: function()
    {

    }
});

