ngs.ImportStepTwoLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_import", ajaxLoader);
    },
    getUrl: function() {
        return "import_step_two";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "import_price_load_container";
    },
    getName: function() {
        return "import_step_two";
    },
    beforeLoad: function() {
        jQuery("#import_price_load_container").block({message: 'processing...'});
    },
    afterLoad: function() {
        jQuery("#import_price_load_container").unblock();
        var thisInstance = this;
        jQuery('#import_price_load_container').dialog('option', 'title', 'New items which should be imported into stock!');
        var btns = {
            "Prev": {
                text: ngs.LanguageManager.getPhrase(283),
                'class': "translatable_search_content",
                phrase_id: 283,
                click: function() {
                    jQuery('#import_price_load_container').dialog('option', 'buttons', {});
                    var selectRowsIds = thisInstance.getSelectedRowsIds();
                    ngs.load("import_step_one", jQuery.extend(thisInstance.params, {'dont_recalculate': '1', 'new_items_row_ids': selectRowsIds.join(',')}));
                }
            },
            "Next": {
                text: ngs.LanguageManager.getPhrase(282),
                'class': "dialog_default_button_class translatable_search_content",
                phrase_id: 282,
                click: function() {
                    jQuery('#import_price_load_container').dialog('option', 'buttons', {});
                    var selectRowsIds = thisInstance.getSelectedRowsIds();
                    ngs.load("import_step_three", jQuery.extend(thisInstance.params, {'new_items_row_ids': selectRowsIds.join(',')}));
                }
            }

        };
        jQuery('#import_price_load_container').dialog('option', 'buttons', btns);
        this.subCategoriesSelectionButtonsInit();
        this.imageSelectionButtonsInit();
        jQuery('.price_items_roots_categories_selects').change(function() {

            var pk_value = jQuery(this).attr('pk_value');
            var root_category = jQuery(this).val();
            jQuery('#is2_price_item_sub_categories_ids_' + pk_value).val('');
            ngs.action('import_steps_actions_group_action', {'action': "edit_cell_value", "field_name": "rootCategoryId", "pk_value": pk_value, 'cell_value': root_category});
            ngs.action('import_steps_actions_group_action', {'action': "edit_cell_value", "field_name": "subCategoriesIds", "pk_value": pk_value, 'cell_value': ''});
        });
        jQuery('.price_items_sub_categories_hiddens').change(function() {
            var pk_value = jQuery(this).attr('pk_value');
            var sub_categories = jQuery(this).val();
            ngs.action('import_steps_actions_group_action', {'action': "edit_cell_value", "field_name": "subCategoriesIds", "pk_value": pk_value, 'cell_value': sub_categories});
        });
        this.getSelectedRowsIds();
        jQuery('#is2_select_all').click(function() {
            jQuery('.is2_include_row').prop('checked', true);
            jQuery('.is2_include_row').trigger('change');
        });
        jQuery('#is2_select_none').click(function() {
            jQuery('.is2_include_row').prop('checked', false);
            jQuery('.is2_include_row').trigger('change');
        });
        jQuery('.is2_include_row').change(function() {
            thisInstance.hilightSelectedRows();
        });
        jQuery('.is2_simillar_item_search_texts').keydown(function(e) {
            if (e.which === 13) {
                var pk_value = jQuery(this).attr('pk_value');
                jQuery('#is2_find_simillar_items_button_' + pk_value).trigger('click');
                e.stopPropagation();
            }
        });
        jQuery('.is2_find_simillar_items_button').click(function() {
            var pk_value = jQuery(this).attr('pk_value');
            var searchText = jQuery('#is2_simillar_item_search_text_' + pk_value).val();
            ngs.action('import_steps_actions_group_action',
                    {'action': "find_similar_items",
                        'pk_value': pk_value,
                        'search_text': searchText}
            );
        });
        jQuery('.is2_simillar_items_select').change(function() {
            var simillar_item_id = jQuery(this).val();
            var pk_value = jQuery(this).attr('pk_value');
            ngs.action('import_steps_actions_group_action',
                    {'action': "edit_cell_value",
                        'pk_value': pk_value,
                        'cell_value': simillar_item_id,
                        'field_name': 'simillarItemId'});
            ngs.action('import_steps_actions_group_action',
                    {'action': "get_item_cat_spec",
                        'pk_value': pk_value,
                        'item_id': simillar_item_id,
                        'step_number': 2
                    });
        });
        jQuery('#f_find_all_simillar_items').click(function() {
            jQuery('.is2_find_simillar_items_button').trigger('click');
        });
        jQuery('#import_price_load_container').scroll(function() {
            var stickerTop = parseInt(jQuery('#is2_header_container').offset().top);
            jQuery("#is2_header_content").css(
                    (parseInt(jQuery("#import_price_load_container").scrollTop()) + parseInt(jQuery("#is2_header_container").css('margin-top'))) > stickerTop ?
                    {position: 'fixed', top: jQuery('#import_price_load_container').offset().top + 'px'} : {position: 'relative', top: "0px"});
        });
        this.initItemsPictureUploadFunctionality();
        this.initTableEditableFunctionality();
        this.hilightSelectedRows();
        this.manageItemsSpecButtonsActions();
        this.manageItemsGroupCategorySelection();
    },
    initItemsPictureUploadFunctionality: function() {
        jQuery('.f_upload_photo_button').click(function() {
            var rowId = jQuery(this).attr('row_id');
            jQuery("#item_picture_" + rowId).trigger('click');
            return false;
        });
        jQuery(".item_picture").change(function() {
            jQuery(this).parent('form').submit();
        });
    },
    manageItemsGroupCategorySelection: function() {
        var thisInstance = this;
        this.calcSelectionCatButtonState();
        jQuery('.is2_cat_checkbox').change(function() {
            thisInstance.calcSelectionCatButtonState();
        });
        jQuery('#is2_selected_price_item_root_category').change(function() {
            var selectedRootCategory = jQuery(this).val();
            var pksArray = jQuery('#is2_set_category_for_selection').attr('selected_items_pks').split(',');
            pksArray.each(function(value, index) {
                var pk = value;
                jQuery('#is2_price_item_root_category_' + pk).val(selectedRootCategory);
                jQuery('#is2_price_item_root_category_' + pk).trigger('change');
            });
        });
        jQuery('#is2_selected_price_items_sub_categories_button').click(function() {
            var root_category_id = jQuery('#is2_selected_price_item_root_category').val();
            jQuery('<div id="f_sub_category_dialog_container"></div>').appendTo("body");
            ngs.load("sub_categories_selection", {
                "item_root_category": root_category_id,
                "result_hidden_element_id": 'is2_selected_price_items_sub_categories_ids'
            });
        });
        jQuery('#is2_selected_price_items_sub_categories_ids').change(function() {
            var selectedSubCategoriesIds = jQuery(this).val();
            var pksArray = jQuery('#is2_set_category_for_selection').attr('selected_items_pks').split(',');
            pksArray.each(function(value, index) {
                var pk = value;
                jQuery('#is2_price_item_sub_categories_ids_' + pk).val(selectedSubCategoriesIds);
                jQuery('#is2_price_item_sub_categories_ids_' + pk).trigger('change');
            });
        });
    },
    calcSelectionCatButtonState: function() {
        jQuery('#is2_set_category_for_selection').css({'visibility': jQuery('.is2_cat_checkbox:checked').length > 0 ? 'visible' : 'hidden'});
        var pk_values = new Array();
        var items_sub_categories_ids_array = new Array();
        jQuery('.is2_cat_checkbox:checked').each(function() {
            var pk_value = jQuery(this).attr('pk_value');
            pk_values.push(pk_value);
            var item_sub_categories_ids = jQuery('#is2_price_item_sub_categories_ids_' + pk_value).val();
            items_sub_categories_ids_array.push(item_sub_categories_ids);
        });
        if (items_sub_categories_ids_array.AllValuesSame() === true && items_sub_categories_ids_array.length > 0) {
            jQuery('#is2_selected_price_items_sub_categories_ids').val(items_sub_categories_ids_array[0]);
        } else {
            jQuery('#is2_selected_price_items_sub_categories_ids').val('');
        }
        jQuery('#is2_set_category_for_selection').attr('selected_items_pks', pk_values.join(','));
    },
    manageItemsSpecButtonsActions: function() {
        var thisInstance = this;
        jQuery('.is2_spec_button').click(function() {
            var pkValue = jQuery(this).attr('pk_value');
            var shortSpec = jQuery('#is2_item_short_spec_' + pkValue).val();
            var fullSpec = jQuery('#is2_item_full_spec_' + pkValue).val();
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
                            jQuery('#is2_item_short_spec_' + pkValue).text(sSpec);
                            var fSpec = jQuery('#item_full_spec_input').val();
                            ngs.action('import_steps_actions_group_action',
                                    {'action': "edit_cell_value", "field_name": "fullSpec",
                                        "pk_value": pkValue,
                                        'cell_value': fSpec});
                            jQuery('#is2_item_full_spec_' + pkValue).html(fSpec);
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
        jQuery('.is2_include_row').each(function() {
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
    getSelectedRowsIds: function() {
        var ret = new Array();
        jQuery('.is2_include_row').each(function() {
            if (jQuery(this).prop('checked'))
            {
                ret.push(jQuery(this).attr('pk_value'));
            }
        });
        return ret;
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

