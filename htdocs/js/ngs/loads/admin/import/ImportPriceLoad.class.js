ngs.ImportPriceLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_import", ajaxLoader);
    },
    getUrl: function() {
        return "import_price";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "import_price_load_container";
    },
    getName: function() {
        return "import_price";
    },
    beforeLoad: function() {
        jQuery("#import_price_load_container").block({message: 'processing...'});
    },
    afterLoad: function() {
        jQuery("#import_price_load_container").unblock();
        var thisInstance = this;
        jQuery('#import_price_load_container').dialog('option', 'title', 'check all rows and matched items!');
        var btns = {
            "Ok": {
                text: ngs.LanguageManager.getPhrase(282),
                'class': "dialog_default_button_class translatable_search_content",
                phrase_id: 282,
                click: function() {
                    jQuery('#import_price_load_container').dialog('option', 'buttons', {});
                    var column_ids_array = new Array();
                    var column_name_indexes = new Array();
                    jQuery('.f_price_column_name').each(function() {
                        if (jQuery(this).val() > 0)
                        {
                            column_ids_array.push(jQuery(this).attr('column_id'));
                            column_name_indexes.push(jQuery(this).val());
                        }
                    });
                    var selectedRowsIndex = thisInstance.getSelectedRowsIndex();
                    var params = jQuery.extend(thisInstance.params, {'used_columns_ids': column_ids_array.join(','), 'used_columns_indexes': column_name_indexes.join(','),
                        "price_index": jQuery('#ip_select_price').val(),
                        "sheet_index": jQuery('#ip_select_worksheet').val(),
                        "brand_model_name_concat_method": jQuery('#ip_select_brand_model_name_concat_method').val(),
                        'dont_recalculate': 0, 'selected_rows_index': selectedRowsIndex.join(',')});
                    ngs.load("import_step_one", params);
                }
            }
        };
        jQuery('#import_price_load_container').dialog('option', 'buttons', btns);
        jQuery('#ip_select_price').change(function() {
            var select_price_index = jQuery(this).val();
            ngs.load("import_price", {"price_index": select_price_index, "sheet_index": 0, 'company_id': jQuery('#mi_select_company').val()});
        });

        jQuery('#ip_select_worksheet').change(function() {
            var select_worksheet_index = jQuery(this).val();
            ngs.load("import_price", {"sheet_index": select_worksheet_index, "price_index": jQuery('#ip_select_price').val(), 'company_id': jQuery('#mi_select_company').val()});
        });


        jQuery('#ip_select_all').click(function() {
            jQuery('.ip_include_row').prop('checked', true);
            jQuery('.ip_include_row').trigger('change');
        });
        jQuery('#ip_select_none').click(function() {
            jQuery('.ip_include_row').prop('checked', false);
            jQuery('.ip_include_row').trigger('change');
        });
        jQuery('.ip_include_row').change(function() {
            if (jQuery(this).prop('checked')) {
                jQuery(this).closest('tr').children('td').css('background-color', 'yellow');
            } else
            {
                jQuery(this).closest('tr').children('td').css('background-color', '');
            }
        });
        jQuery('.ip_delete_column').click(function() {
            var price_index= jQuery('#ip_select_price').val();
            var sheet_index = jQuery('#ip_select_worksheet').val();
            var company_id = jQuery('#mi_select_company').val();
            var column_letter = jQuery(this).attr('column_letter');
            ngs.action('admin_group_actions', {'action': "delete_price_values_column", "price_index":price_index,"sheet_index": sheet_index,
                'company_id': company_id, 'column_letter': column_letter});
        });
        this.controlCheckboxSelection();
        this.hilightSelectedRows();
    },
    hilightSelectedRows: function()
    {
        jQuery('.ip_include_row').each(function() {
            if (jQuery(this).prop('checked')) {
                jQuery(this).closest('tr').children('td').css('background-color', 'yellow');
            } else
            {
                jQuery(this).closest('tr').children('td').css('background-color', '');
            }
        });
    },
    controlCheckboxSelection: function() {
        var classThis = this;
        jQuery('.ip_include_row').click(function(e) {
            var rowIndex = jQuery(this).attr('row_index');
            if (e.shiftKey && classThis.lastSelectedRowIndex >= 0)
            {
                var maxRowId = Math.max(rowIndex, classThis.lastSelectedRowIndex);
                var minRowId = Math.min(rowIndex, classThis.lastSelectedRowIndex);
                var checkboxesToBeSelect = jQuery(".ip_include_row").filter(function() {
                    return  parseInt(jQuery(this).attr('row_index')) > minRowId && parseInt(jQuery(this).attr('row_index')) < maxRowId;
                });
                checkboxesToBeSelect.each(function() {
                    jQuery(this).attr('checked', !jQuery(this).attr('checked'));
                    jQuery(this).trigger('change');
                });

            } else {
                //jQuery(".ip_include_row").attr('checked', false);
            }
            classThis.lastSelectedRowIndex = rowIndex;
            document.getSelection().removeAllRanges();

        });
    },
    getSelectedRowsIndex: function() {
        var ret = new Array();
        jQuery('.ip_include_row').each(function() {
            if (jQuery(this).prop('checked'))
            {
                ret.push(jQuery(this).attr('row_index'));
            }
        });
        return ret;
    },
    onLoadDestroy: function()
    {

    }
});

