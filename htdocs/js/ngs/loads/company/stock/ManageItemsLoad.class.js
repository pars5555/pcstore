ngs.ManageItemsLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "company_stock", ajaxLoader);
    },
    getUrl: function() {
        return "manage_items";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "hp_" + this.getName() + "_tab";
    },
    getName: function() {
        return "manage_items";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {
        ngs.UrlChangeEventObserver.setFakeURL("/stock");
        $('global_modal_loading_div').style.display = 'none';

        $('mi_select_company').onchange = this.onMISelectCompanyChanged.bind(this);

        if ($('add_new_item_button')) {
            $('add_new_item_button').onclick = this.onAddNewItemButtonClicked.bind(this);
        }
        if (jQuery('#import_price_button').length > 0) {
            jQuery('#import_price_button').click(function() {
                var dWidth = jQuery(window).width() * 0.9;
                var dHeight = jQuery(window).height() * 0.9;
                jQuery("<div title='select column names' id='import_price_load_container'></div>").dialog({
                    width: dWidth,
                    height: dHeight,
                    modal: true,
                    close: function() {
                        jQuery(this).remove();
                    },
                    open: function() {
                        jQuery(this).parent().addClass('translatable_search_content');
                    }
                });
                ngs.load('import_price', {'company_id': jQuery('#mi_select_company').val()});
            });
            
            if (jQuery('#import_price_button').is(":disabled"))
            {
                ngs.action('admin_group_actions', {'action': "is_price_values_ready", "company_id": jQuery('#mi_select_company').val()});
            }
        }
        if ($('hidden_plus_days_button')) {
            $('hidden_plus_days_button').onclick = this.onHiddenPlusDaysButtonClicked.bind(this);
        }

        if ($('paste_item_button')) {
            $('paste_item_button').onclick = this.onPasteItemButtonClicked.bind(this);
        }
        var remove_company_items_links = $$("#f_company_items_table_container .remove_company_items_links");
        var edit_company_items_links = $$("#f_company_items_table_container .edit_company_items_links");
        var hide_company_item_checkboxs = $$("#f_company_items_table_container .hide_company_item_checkboxs");
        var item_dealer_price_in_table = $$("#f_company_items_table_container .f_item_dealer_price_in_table");
        var item_vat_price_in_table = $$("#f_company_items_table_container .f_item_vat_price_in_table");
        var item_dealer_price_amd_in_table = $$("#f_company_items_table_container .f_item_dealer_price_amd_in_table");
        var item_vat_price_amd_in_table = $$("#f_company_items_table_container .f_item_vat_price_amd_in_table");
        var item_display_name_in_table = $$("#f_company_items_table_container .f_item_display_name_in_table");
        var item_price_order_index_in_table = $$("#f_company_items_table_container .f_item_price_order_index_in_table");
        this.addRemoveClickHandler(remove_company_items_links);
        this.addEditClickHandler(edit_company_items_links);
        this.addHideCompanyItemCheckboxsClickHandler(hide_company_item_checkboxs);
        this.addItemDealerPriceInTableDoubleClickedHandler(item_dealer_price_in_table);
        this.addItemVatPriceInTableDoubleClickedHandler(item_vat_price_in_table);
        this.addItemDealerPriceAmdInTableDoubleClickedHandler(item_dealer_price_amd_in_table);
        this.addItemVatPriceAmdInTableDoubleClickedHandler(item_vat_price_amd_in_table);
        this.addItemDisplayNameInTableDoubleClickedHandler(item_display_name_in_table);
        this.addItemPriceOrderIndexInTableDoubleClickedHandler(item_price_order_index_in_table);

        if ($('mi_reset_all_indexes'))
        {
            jQuery('#mi_reset_all_indexes').click(function() {
                ngs.action("reset_company_items_indexes_action", {'company_id': $('mi_select_company').value});
            });
        }

        if ($('mi_hide_all_items'))
        {
            jQuery('#mi_hide_all_items').click(function() {
                ngs.action("hide_company_items_action", {'company_id': $('mi_select_company').value});
            });
        }

        jQuery('.mi_item_default_pictures').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.DialogsManager.closeDialog('Item Pictures', '<div id="f_item_pictures_container"></div>', 'close');
            ngs.load("item_pictures", {'item_id': item_id});
        });
 var thisInstance = this;
        jQuery('.mi_item_spec_button').click(function() {
            var item_id = jQuery(this).attr('item_id');

            jQuery('<div>short spec<br><textarea id="mi_item_short_spec_input" style="width:100%;height:80px;border:1px solid"></textarea>' +
                    '<br>full spec<br><textarea style="width:100%;min-height:500px" id="mi_item_full_spec_input"></textarea></div>').dialog({
                resizable: true,
                width: 1100,
                height: 650,
                modal: true,
                title:"item specification",
                buttons: {
                    "save": {
                        text: 'save',
                        click: function() {
                            tinymce.activeEditor.save();
                            var sSpec = jQuery('#mi_item_short_spec_input').val();
                            var fSpec = jQuery('#mi_item_full_spec_input').val();
                            ngs.action('admin_group_actions',
                                    {'action': "set_item_spec", "item_id": item_id, 'short_spec': sSpec, 'full_spec': fSpec});
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

                    thisInstance.initTinyMCE('textarea#mi_item_full_spec_input');
                }
            });
             ngs.action('admin_group_actions', {'action': "get_item_spec", "item_id": item_id});
             return false;
        });
       

    },
    onPasteItemButtonClicked: function() {
        var selectedCompanyId = $('mi_select_company').value;

        var position = prompt("Please enter item position", "");
        if (position != null && position.strip().length > 0) {
            position = parseInt(position.strip());

            if (position > 0) {
                ngs.action("paste_item_action", {
                    "item_position": position,
                    "selected_company_id": selectedCompanyId
                });
            } else {
                ngs.DialogsManager.closeDialog(483, "<div>" + 'Item position should be positive number' + "</div>");
            }

        } else {
            ngs.DialogsManager.closeDialog(483, "<div>" + 'You didn\'t specify the item position!' + "</div>");
        }

    },
    onHiddenPlusDaysButtonClicked: function() {
        if ($('mi_select_company') && parseInt($('mi_select_company').value) > 0) {
            ngs.action("increase_company_expire_items_availablity_days_action", {
                'company_id': $('mi_select_company').value
            });
        }
    },
    uniqid: function() {
        var newDate = new Date;
        return newDate.getTime();
    },
    addHideCompanyItemCheckboxsClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.lastIndexOf("_") + 1);
            elements_array[i].onchange = this.onHideCompanyItemCheckboxValueChanged.bind(this, item_id);
        }
    },
    addItemDealerPriceInTableDoubleClickedHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.lastIndexOf("_") + 1);
            elements_array[i].ondblclick = this.onItemDealerPriceInTableDbClicked.bind(this, elements_array[i], item_id);
        }
    },
    addItemVatPriceInTableDoubleClickedHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.lastIndexOf("_") + 1);
            elements_array[i].ondblclick = this.onItemVatPriceInTableDbClicked.bind(this, elements_array[i], item_id);
        }
    },
    addItemDealerPriceAmdInTableDoubleClickedHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.lastIndexOf("_") + 1);
            elements_array[i].ondblclick = this.onItemDealerPriceAmdInTableDbClicked.bind(this, elements_array[i], item_id);
        }
    },
    addItemVatPriceAmdInTableDoubleClickedHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.lastIndexOf("_") + 1);
            elements_array[i].ondblclick = this.onItemVatPriceAmdInTableDbClicked.bind(this, elements_array[i], item_id);
        }
    },
    addItemDisplayNameInTableDoubleClickedHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.lastIndexOf("_") + 1);
            elements_array[i].ondblclick = this.onItemDisplayNameInTableDbClicked.bind(this, elements_array[i], item_id);
        }
    }, addItemPriceOrderIndexInTableDoubleClickedHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.lastIndexOf("_") + 1);
            elements_array[i].ondblclick = this.onItemPriceOrderIndexInTableDbClicked.bind(this, elements_array[i], item_id);
        }
    },
    onItemDealerPriceInTableDbClicked: function(element, item_id) {
        element.style.display = 'none';
        $('f_item_inline_price_input_' + item_id).value = element.innerHTML;
        $('f_item_inline_price_input_' + item_id).style.display = 'block';
        $('f_item_inline_price_input_' + item_id).focus();
        var executed = false;
        jQuery('#f_item_inline_price_input_' + item_id).one('change blur', function() {
            if (executed === false) {
                ngs.ManageItemsLoad.prototype.onItemInlineDealerPriceBlur(item_id);
                executed = true;
            }
        });
    },
    onItemVatPriceInTableDbClicked: function(element, item_id) {
        element.style.display = 'none';
        $('f_item_inline_vat_price_input_' + item_id).value = element.innerHTML;
        $('f_item_inline_vat_price_input_' + item_id).style.display = 'block';
        $('f_item_inline_vat_price_input_' + item_id).focus();
        var executed = false;
        jQuery('#f_item_inline_vat_price_input_' + item_id).one('change blur', function() {
            if (executed === false) {
                ngs.ManageItemsLoad.prototype.onItemInlineVatPriceBlur(item_id);
                executed = true;
            }
        });
    },
    onItemDealerPriceAmdInTableDbClicked: function(element, item_id) {
        element.style.display = 'none';
        $('f_item_inline_price_amd_input_' + item_id).value = element.innerHTML;
        $('f_item_inline_price_amd_input_' + item_id).style.display = 'block';
        $('f_item_inline_price_amd_input_' + item_id).focus();
        var executed = false;
        jQuery('#f_item_inline_price_amd_input_' + item_id).one('change blur', function() {
            if (executed === false) {
                ngs.ManageItemsLoad.prototype.onItemInlineDealerPriceAmdBlur(item_id);
                executed = true;
            }
        });

    },
    onItemVatPriceAmdInTableDbClicked: function(element, item_id) {
        element.style.display = 'none';
        $('f_item_inline_vat_price_amd_input_' + item_id).value = element.innerHTML;
        $('f_item_inline_vat_price_amd_input_' + item_id).style.display = 'block';
        $('f_item_inline_vat_price_amd_input_' + item_id).focus();
        var executed = false;
        jQuery('#f_item_inline_vat_price_amd_input_' + item_id).one('change blur', function() {
            if (executed === false) {
                ngs.ManageItemsLoad.prototype.onItemInlineVatPriceAmdBlur(item_id);
                executed = true;
            }
        });
    },
    onItemDisplayNameInTableDbClicked: function(element, item_id) {
        element.style.display = 'none';
        $('f_item_inline_display_name_input_' + item_id).value = element.innerHTML;
        $('f_item_inline_display_name_input_' + item_id).style.display = 'block';
        $('f_item_inline_display_name_input_' + item_id).focus();
        var executed = false;
        jQuery('#f_item_inline_display_name_input_' + item_id).one('change blur', function() {
            if (executed === false) {
                ngs.ManageItemsLoad.prototype.onItemInlineDisplayNameBlur(item_id);
                executed = true;
            }
        });
    },
    onItemPriceOrderIndexInTableDbClicked: function(element, item_id) {
        element.style.display = 'none';
        $('f_item_inline_price_order_index_input_' + item_id).value = element.innerHTML;
        $('f_item_inline_price_order_index_input_' + item_id).style.display = 'block';
        $('f_item_inline_price_order_index_input_' + item_id).focus();
        var executed = false;
        jQuery('#f_item_inline_price_order_index_input_' + item_id).one('change blur', function() {
            if (executed === false) {
                ngs.ManageItemsLoad.prototype.onItemInlinePriceOrderIndexBlur(item_id);
                executed = true;
            }
        });
    },
    onItemInlineDealerPriceBlur: function(item_id) {
        var itemNewPrice = parseFloat($('f_item_inline_price_input_' + item_id).value);
        ngs.action("change_item_dealer_price_action", {
            'dealer_price': itemNewPrice,
            'item_id': item_id
        });
        $('f_item_inline_price_input_' + item_id).style.display = 'none';
    },
    onItemInlineVatPriceBlur: function(item_id) {
        var itemNewVatPrice = parseFloat($('f_item_inline_vat_price_input_' + item_id).value);
        ngs.action("change_item_vat_price_action", {
            'vat_price': itemNewVatPrice,
            'item_id': item_id
        });
        $('f_item_inline_vat_price_input_' + item_id).style.display = 'none';
    },
    onItemInlineDealerPriceAmdBlur: function(item_id) {
        var itemNewPriceAmd = parseInt($('f_item_inline_price_amd_input_' + item_id).value);
        ngs.action("change_item_dealer_price_amd_action", {
            'dealer_price_amd': itemNewPriceAmd,
            'item_id': item_id
        });
        $('f_item_inline_price_amd_input_' + item_id).style.display = 'none';
    },
    onItemInlineVatPriceAmdBlur: function(item_id) {
        var itemNewVatPriceAmd = parseInt($('f_item_inline_vat_price_amd_input_' + item_id).value);
        ngs.action("change_item_vat_price_amd_action", {
            'vat_price_amd': itemNewVatPriceAmd,
            'item_id': item_id
        });
        $('f_item_inline_vat_price_amd_input_' + item_id).style.display = 'none';
    },
    onItemInlineDisplayNameBlur: function(item_id) {
        var itemNewDisplayName = $('f_item_inline_display_name_input_' + item_id).value;
        ngs.action("change_item_display_name_action", {
            'display_name': itemNewDisplayName,
            'item_id': item_id
        });
        $('f_item_inline_display_name_input_' + item_id).style.display = 'none';
    },
    onItemInlinePriceOrderIndexBlur: function(item_id) {
        var itemNewPriceOrderIndex = parseInt($('f_item_inline_price_order_index_input_' + item_id).value);
        ngs.action("change_item_price_order_index_action", {
            'price_order_index': itemNewPriceOrderIndex,
            'item_id': item_id
        });
        $('f_item_inline_price_order_index_input_' + item_id).style.display = 'none';
    },
    onHideCompanyItemCheckboxValueChanged: function(itemId) {
        var value = $('hide_company_item_checkbox_' + itemId).checked;
        ngs.action("change_item_hidden_attribute_action", {
            'item_hidden': value ? 1 : 0,
            'item_id': itemId
        });

    },
    addRemoveClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.lastIndexOf("_") + 1);
            elements_array[i].onclick = this.onRemoveCompanyItem.bind(this, item_id);
        }
    },
    addEditClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.lastIndexOf("_") + 1);
            elements_array[i].onclick = this.onEditCompanyItem.bind(this, item_id);
        }
    },
    onAddNewItemButtonClicked: function() {
        ngs.load('add_edit_item', {"company_id": $('mi_select_company').value});

        /*	*/

    },
    onEditCompanyItem: function(itemId) {
        ngs.load('add_edit_item', {"item_id": itemId, "company_id": $('mi_select_company').value});


        /*
         ngs.load("manage_items", {
         'company_id': $('mi_select_company').value,
         'item_id': itemId
         });*/
    },
    onRemoveCompanyItem: function(itemId) {
        var answer = confirm("Are you sure you want to remove the item?");
        if (answer) {
            ngs.action("remove_company_item_action", {
                "item_id": itemId,
                "company_id": $('mi_select_company').value
            });
        }
    },
    onMISelectCompanyChanged: function() {

        var selectedCompany = $('mi_select_company').value;

        ngs.load("manage_items", {
            'company_id': selectedCompany
        });
    },
    onLoadDestroy: function()
    {
        jQuery("#" + this.getContainer()).html("");
    }
});
