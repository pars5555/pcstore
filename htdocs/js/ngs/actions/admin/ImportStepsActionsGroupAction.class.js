ngs.ImportStepsActionsGroupAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "do_import_steps_actions_group";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        if (typeof transport.responseText !== 'undefined') {
            var data = transport.responseText.evalJSON();
        } else
        {
            var data = transport.evalJSON();
        }
        if (data.status === "ok") {
            if (typeof this.params === 'undefined')
            {
                this.params = {};
            }
            if (typeof this.params.action === 'undefined')
            {
                this.params.action = data.action;
            }
            switch (this.params.action) {
                case 'step_1_unbind_price_row':
                case 'step_1_link_stock_item_to_price_item':
                    var params = jQuery.extend(this.params, {'dont_recalculate': '1',
                        'company_id': jQuery('#mi_select_company').val(),
                        'used_columns_indexes': jQuery('#is1_used_columns_indexes_array').val()});
                    ngs.load('import_step_one', params);
                    break;
                case 'edit_cell_value':
                    if (jQuery('#ii_table_view').length > 0) {
                        var cellValue = data.cell_value;
                        var field_name = this.params.field_name;
                        var pk_value = this.params.pk_value;
                        jQuery('#ii_table_editable_span_' + pk_value + '_' + field_name).html(cellValue);
                        jQuery('#ii_table_view').unblock();
                    }
                    break;
                case 'import':
                    ngs.load("manage_items", {'company_id': this.params.company_id});
                    break;
                case 'get_item_cat_spec':
                    var pk_value = this.params.pk_value;
                    var step = this.params.step_number;
                    var itemShortSpec = data.short_description;
                    var itemFullSpec = data.full_description;
                    var itemCategoriesIdsStr = data.categories_ids;
                    jQuery('#is' + step + '_item_short_spec_' + pk_value).html(itemShortSpec);
                    jQuery('#is' + step + '_item_full_spec_' + pk_value).html(itemFullSpec);
                    if (itemCategoriesIdsStr[0] === ',')
                    {
                        itemCategoriesIdsStr = itemCategoriesIdsStr.slice(1, -1);
                    }
                    var itemCategoriesIdsArray = itemCategoriesIdsStr.split(',');
                    var itemRootCategory = itemCategoriesIdsArray[0];
                    var itemSubCategories = itemCategoriesIdsArray.slice(1);
                    jQuery('#is' + step + '_price_item_root_category_' + pk_value).val(itemRootCategory > 0 ? itemRootCategory : 1);
                    if (jQuery('#is' + step + '_price_item_sub_categories_ids_' + pk_value).length > 0) {
                        jQuery('#is' + step + '_price_item_sub_categories_ids_' + pk_value).val(itemSubCategories.join(','));
                    }
                    ngs.action('import_steps_actions_group_action', {'action': "edit_cell_value", "field_name": "rootCategoryId", "pk_value": pk_value, 'cell_value': itemRootCategory > 0 ? itemRootCategory : 1});
                    ngs.action('import_steps_actions_group_action', {'action': "edit_cell_value", "field_name": "subCategoriesIds", "pk_value": pk_value, 'cell_value': itemSubCategories.join(',')});
                    break;
                case 'find_similar_items':
                    var pk_value = this.params.pk_value;
                    var items = jQuery.parseJSON(data.items);
                    var options = [];
                    options.push('<option value="0">None</option>');
                    for (var i = 0; i < items.length; i++) {
                        options.push('<option value="' + items[i].id, '">' + '(' + items[i].company_name + ') ' + items[i].display_name + '</option>');
                    }
                    jQuery('#simillar_items_select_' + pk_value).html(options.join(''));
                    break;
                case 'upload_new_item_picture':
                    jQuery('#is2_item_picture_' + data.row_id).attr('src', SITE_PATH + '/tmp/import/' + data.picture_name);
                    break;
            }
        } else if (data.status === "err") {
            ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
        }
    }
});
