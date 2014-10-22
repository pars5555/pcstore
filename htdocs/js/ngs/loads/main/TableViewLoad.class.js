ngs.TableViewLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "table_view";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "f_admin_config_table_view_container";
    },
    getName: function() {
        return "table_view";
    },
    afterLoad: function() {
        jQuery("#system_config_container_created_in_js").unblock();
        var thisInstance = this;
        jQuery('#tv_table_name_select').change(function() {
            jQuery('#tv_search_text').val(''),
                    jQuery('#tv_search_column_name').val('');
            thisInstance.refreshLoad();
        });
        jQuery('#tv_sort_options').change(function() {
            thisInstance.refreshLoad();
        });
        jQuery('#tv_sort_asc_desc_options').change(function() {
            thisInstance.refreshLoad();
        });
        jQuery('#tv_rows_per_page').change(function() {
            thisInstance.refreshLoad();
        });
        jQuery('#tv_page_options').change(function() {
            thisInstance.refreshLoad();
        });
        jQuery('#tv_reload_button').click(function() {
            thisInstance.refreshLoad();
        });
        this.manageEmptyTableAction();

        jQuery('#tv_table_view td').dblclick(function(e) {
            var cell = jQuery(this);
            var cellValue = cell.html();
            var columnName = cell.attr('columnName');
            var columnType = cell.attr('columnType');
            var pk_value = cell.parent('tr').attr('tv_table_pk_value');
            var pk_value_exists = typeof pk_value !== 'undefined' && pk_value !== false;
            if (typeof columnType === 'undefined' || columnName === 'undefined')
            {
                return false;
            }
            if (columnType.indexOf('tinyint(1)') === 0)
            {
                var checkedHtml = "";
                if (cellValue == 1)
                {
                    checkedHtml = 'checked="checked"';
                }
                var input = jQuery('<input type="checkbox" ' + checkedHtml + (pk_value_exists ? (' pk_value="' + pk_value + '"') : '') + ' columnName="' + columnName + '" id="tv_cell_edit_input"></textarea>');
                input.change(function(e) {
                    input.val(jQuery(this).is(':checked') ? 1 : 0);
                    thisInstance.updateCellValue();
                });
            } else {

                var input = jQuery('<textarea style="min-width:100px"' + (pk_value_exists ? (' pk_value="' + pk_value + '"') : '') + ' columnName="' + columnName + '" id="tv_cell_edit_input"></textarea>');
                input.html(cellValue);
            }
            input.click(function(e) {
                e.stopPropagation();
            });
            input.dblclick(function(e) {
                e.stopPropagation();
            });
            input.keydown(function(e) {
                if (e.which === 27) {
                    cell.html(cellValue);
                    e.stopPropagation();
                }
            });

            cell.html(input);
            input.focus();
            e.stopPropagation();
        });
        jQuery('#tv_table_view').click(function() {
            thisInstance.updateCellValue();

        });
        this.manageRowSelection();
        this.manageRowDeletion();
        this.manageSearch();

    },
    manageSearch: function() {
        var thisInstance = this;
        jQuery('#tv_search_button').click(function() {
            var findWhatInput = "<p>Find what: <input type='text' id='tv_search_dialog_text'/></p>";
            var tableColumnsNamesArray = jQuery('#tv_table_columns_joined').val().split(',');

            var optionsTags = '';
            jQuery(tableColumnsNamesArray).each(function(i, v) {
                optionsTags += '<option value="' + v + '">' + v + '</option>';
            });

            var findWhereInput = "<p>Column: <select id='tv_search_dialog_column_name_select' onkeyup='this.blur();this.focus();' class='cmf-skinned-select cmf-skinned-text'>" + optionsTags + "</select></p>";
            var searchDialogContent = "<form id='tv_search_dialog_form'>" + findWhatInput + findWhereInput + "</form>";
            jQuery("<div>" + searchDialogContent + "</div>").dialog({
                resizable: true,
                title: ngs.LanguageManager.getPhrase(131),
                modal: true,
                width: 400,
                buttons: [{
                        text: ngs.LanguageManager.getPhrase(91),
                        'class': "dialog_default_button_class translatable_search_content",
                        phrase_id: 91,
                        click: function() {
                            thisInstance.onSearchInTableFormSubmit();
                            jQuery(this).remove();
                        }
                    },
                    {
                        text: ngs.LanguageManager.getPhrase(49),
                        'class': "translatable_search_content",
                        phrase_id: 49,
                        click: function() {
                            jQuery(this).remove();
                        }
                    }
                ],
                close: function() {
                    jQuery(this).remove();
                },
                open: function(event, ui) {
                    jQuery(this).parent().attr('phrase_id', 131);
                    jQuery(this).parent().addClass('translatable_search_content');
                    jQuery('#tv_search_dialog_column_name_select').val(jQuery('#tv_search_column_name').val());
                    jQuery('#tv_search_dialog_text').val(jQuery('#tv_search_text').val());
                }
            });
            jQuery('#tv_search_dialog_form').submit(function() {
                thisInstance.onSearchInTableFormSubmit();
                return false;
            });
        });


    },
    onSearchInTableFormSubmit: function() {
        var colName = jQuery('#tv_search_dialog_column_name_select').val();
        var searchText = jQuery('#tv_search_dialog_text').val();
        jQuery('#tv_search_text').val(searchText);
        jQuery('#tv_search_column_name').val(colName);
        this.refreshLoad();
    },
    manageEmptyTableAction: function() {
        jQuery('#tv_empty_table_button').click(function() {
            jQuery("<div>" + ngs.LanguageManager.getPhraseSpan(567) + "</div>").dialog({
                resizable: false,
                title: ngs.LanguageManager.getPhrase(483),
                modal: true,
                buttons: [{
                        text: ngs.LanguageManager.getPhrase(489),
                        'class': "dialog_default_button_class translatable_search_content",
                        phrase_id: 489,
                        click: function() {
                            var table_name = jQuery('#tv_table_name').val();
                            ngs.action('table_view_multi_action', {'action': "empty_table",
                                'table_name': table_name});
                            jQuery(this).remove();
                        }
                    },
                    {
                        text: ngs.LanguageManager.getPhrase(49),
                        'class': "translatable_search_content",
                        phrase_id: 49,
                        click: function() {
                            jQuery(this).remove();
                        }
                    }
                ],
                close: function() {
                    jQuery(this).remove();
                },
                open: function(event, ui) {
                    jQuery(this).parent().attr('phrase_id', 483);
                    jQuery(this).parent().addClass('translatable_search_content');
                }
            });
        });
    },
    manageRowDeletion: function() {
        jQuery('#tv_delete_selected_rows_button').click(function() {
            var selectedRowsCheckboxes = jQuery('.tv_row_select_checkbox:checked');
            var pk_values_to_delete = new Array();
            selectedRowsCheckboxes.each(function() {
                pk_values_to_delete.push(jQuery(this).closest('tr').attr('tv_table_pk_value'));
            });
            if (pk_values_to_delete.length > 0)
            {
                var table_name = jQuery('#tv_table_name').val();
                ngs.action('table_view_multi_action', {'action': "delete_rows", 'table_name': table_name, "pk_values": pk_values_to_delete.join(',')});
            }
        });
    },
    manageRowSelection: function() {
        var thisObject = this;
        jQuery('.tv_row_select_checkbox').click(function(e) {
            if (e.shiftKey && thisObject.lastSelectedRowIndex > 0) {
                var currentRowId = jQuery(this).attr('row_index');
                var maxRowId = Math.max(currentRowId, thisObject.lastSelectedRowIndex);
                var minRowId = Math.min(currentRowId, thisObject.lastSelectedRowIndex);
                var checkboxesToBeSelect = jQuery(".tv_row_select_checkbox").filter(function() {
                    return parseInt(jQuery(this).attr('row_index')) >= minRowId && parseInt(jQuery(this).attr('row_index')) <= maxRowId;
                });
                checkboxesToBeSelect.prop('checked', thisObject.lastClickedRowStatus ? true : false);
            }
            thisObject.lastSelectedRowIndex = jQuery(this).attr('row_index');
            thisObject.lastClickedRowStatus = jQuery(this).is(":checked");
            thisObject.onRowSelectionChanged();
        });


    }, onRowSelectionChanged: function() {
        var selectedRowsCheckboxes = jQuery('.tv_row_select_checkbox:checked');
        jQuery('#tv_delete_selected_rows_button').prop("disabled", selectedRowsCheckboxes.length === 0);
    },
    updateCellValue: function()
    {
        var cellInputElement = jQuery('#tv_table_view #tv_cell_edit_input');
        if (cellInputElement.length > 0) {
            jQuery('#tv_table_view').block({message: 'processing...'});
            jQuery('#tv_table_view').off();
            var cellValue = cellInputElement.val();
            var cellName = cellInputElement.attr('columnName');
            var table_name = jQuery('#tv_table_name').val();
            var pk_value = cellInputElement.attr('pk_value');
            var pk_value_exists = typeof pk_value !== 'undefined' && pk_value !== false;
            if (pk_value_exists)
            {
                ngs.action('table_view_multi_action', {'action': "edit_cell_value", 'table_name': table_name, 'cell_value': cellValue, "cell_name": cellName, "pk_value": pk_value});
            } else
            {
                ngs.action('table_view_multi_action', {'action': "add_row", 'table_name': table_name, 'cell_value': cellValue, "cell_name": cellName});
            }
        }
    },
    refreshLoad: function() {
        jQuery("#system_config_container_created_in_js").block({message: 'Logging...'});
        ngs.load('table_view',
                {
                    'table_name': jQuery('#tv_table_name_select').val(),
                    'page': jQuery('#tv_page_options').val(),
                    'rows_per_page': jQuery('#tv_rows_per_page').val(),
                    'order_by_field_name': jQuery('#tv_sort_options').val(),
                    'order_by_asc_desc': jQuery('#tv_sort_asc_desc_options').val(),
                    'search_text': jQuery('#tv_search_text').val(),
                    'search_column_name': jQuery('#tv_search_column_name').val()
                });
    }

});