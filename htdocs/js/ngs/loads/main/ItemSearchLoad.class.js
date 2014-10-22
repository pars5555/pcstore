ngs.ItemSearchLoad = Class.create(ngs.AbstractLoad, {
    categoryTreeViewUtility: null,
    categoryPropertyViewsUtility: null,
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "item_search";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "hp_item_search_tab";
    },
    getName: function() {
        return "item_search";
    }, beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {
        if (jQuery('#selected_large_view_item_id').length > 0) {
            ngs.nestLoad("item_large_view", {});
            return false;
        }
        window.scrollTo(0, 0);

        this.params = this.calcLoadParams();

        ngs.PagingManager.init("item_search", this.params, this.showSearchLoadingOverlay);
        var urlParams = this.params;
        delete urlParams.win_uid;
        ngs.UrlChangeEventObserver.setFakeURL("/search?" + jQuery.param(urlParams));
        $('global_modal_loading_div').style.display = 'none';
        jQuery('#item_search_overlay_div').css('display', 'none');
        $('sort_by_select').onchange = this.onSortBySelectChanged.bind(this);

        $('item_seach_button').onclick = this.onSubmitForm.bind(this, true);

        $('selected_company_id').onchange = this.onSelectedCompanyIdChanged.bind(this);

        if ($('search_item_show_only_vat_items')) {
            $('search_item_show_only_vat_items').onclick = this.onShowOnlyVatItemsClicked.bind(this);
        }

        this.setCursorEnd($('search_item_text_field'));

        var thisObject = this;
        jQuery('#item_search_categories_modal_loading_div').css('display', 'none');
        jQuery('#search_body_categories_container a').click(function() {
            //jQuery('#item_search_categories_modal_loading_div').css('display','block');
            var category_id = jQuery(this).attr('category_id');
            thisObject.onSubmitForm({'cid': category_id, 'scpids': ''});
            return false;
        });

        jQuery('#category_property_view_container input:checkbox').click(function() {
            thisObject.onSubmitForm();
        });


        $("item_search_result_container").scrollTop = 0;
        var thisInstance = this;
        jQuery('.checkitemavailabilitybutton').click(function() {
            var itemId = jQuery(this).attr('item_id');
            thisInstance.onCheckItemAvailabilityButtonClick(itemId);
        });

        var itemLinks = $$("#item_search_result_container .search_item_title");
        this.addItemClickHandler(itemLinks);
        var order_item_buttons = $$("#item_search_result_container .orderbutton");
        this.addAddToCartButtonsClickHandler(order_item_buttons);

        $('total_search_result_items_count').innerHTML = $('total_search_result_items_count_value').value;

        //for credit items
        var credit_months_selects = $$("#item_search_result_container .credit_months_select");
        this.addCreditMonthsChangeHandler(credit_months_selects);

        jQuery(".copy_company_items_links").click(function() {
            jQuery(".copy_company_items_links").css('visibility', 'visible');
            jQuery(this).css('visibility', 'hidden');
            ngs.action("copy_item_action", {
                "item_id": jQuery(this).attr('item_id')
            });
        });
        jQuery(".post_on_list_am").click(function() {
            ngs.action("post_item_to_listam_action", {
                "item_id": jQuery(this).attr('item_id')
            });
        });

        jQuery('.siv_add_picture').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.DialogsManager.closeDialog('Item Pictures', '<div id="f_item_pictures_container"></div>', 'close');
            ngs.load("item_pictures", {'item_id': item_id});
        });

        jQuery('#search_item_text_field,#search_item_price_range_min,#search_item_price_range_max').keyup(function(e) {
            if (e.which == 13) {
                thisInstance.onSubmitForm();
            }
        });

        if (jQuery('#f_admin_search_toolbox').length > 0) {
            this.initAdminToolbox();
        }

        jQuery('.f_login_to_order').click(function() {
            ngs.load('login_dialog', {});
        });
    },
    initAdminToolbox: function()
    {
        var thisInstance = this;
        jQuery('#no_picture_items_only,#no_short_spec_items_only,#no_full_spec_items_only').change(function() {
            thisInstance.onSubmitForm();
        });
    },
    showSearchLoadingOverlay: function()
    {
        jQuery('#item_search_overlay_div').css('display', 'block');
    },
    onShowOnlyVatItemsClicked: function() {
        this.onSubmitForm();
    },
    onSortBySelectChanged: function() {
        this.onSubmitForm();
        return false;
    },
    getLastSelectedCategoryPropertiesIdsArray: function() {
        if ($('selected_category_property_ids')) {
            return $('selected_category_property_ids').value.split(',');
        } else {
            return new Array();
        }

    },
    onSelectedCompanyIdChanged: function() {
        $('search_item_text_field').value = '';
        this.onSubmitForm();
        this.setCursorEnd($('search_item_text_field'));
    },
    transformMinMaxPrice: function() {
        var price_min = $('search_item_price_range_min').value;
        var price_max = $('search_item_price_range_max').value;
        price_min = parseFloat(price_min);
        price_max = parseFloat(price_max);
        if (price_min >= 0) {
            $('search_item_price_range_min').value = price_min;
        } else {
            $('search_item_price_range_min').value = '';
        }
        if (price_max >= 0) {
            $('search_item_price_range_max').value = price_max;
        } else {
            $('search_item_price_range_max').value = '';
        }
    },
    onSubmitForm: function(paramsToMerge) {
        jQuery('#item_search_overlay_div').css('display', 'block');
        var params = this.calcLoadParams();
        if (typeof paramsToMerge !== 'undefined') {
            jQuery.extend(params, paramsToMerge);
        }
        delete params.spg;
        ngs.load("item_search", params);
        return false;
    },
    calcLoadParams: function()
    {
        var params = {};
        if (jQuery.trim(jQuery('#search_item_text_field').val()) !== '')
        {
            params.st = jQuery.trim(jQuery('#search_item_text_field').val());
        }
        if (jQuery('#selected_company_id').length > 0 && parseInt(jQuery('#selected_company_id').val()) > 0) {
            params.sci = parseInt(jQuery('#selected_company_id').val());
        }
        if (parseInt(jQuery('#search_item_price_range_min').val()) > 0) {
            params.prmin = parseInt(jQuery('#search_item_price_range_min').val());
        }
        if (parseInt(jQuery('#search_item_price_range_max').val()) > 0) {
            params.prmax = parseInt(jQuery('#search_item_price_range_max').val());
        }
        params.srt = jQuery('#sort_by_select').val();
        if (jQuery('#search_item_show_only_vat_items').length > 0 && jQuery('#search_item_show_only_vat_items').prop('checked'))
        {
            params.shov = 1;
        }

        if (jQuery("#search_selected_category_id").length > 0) {
            params.cid = jQuery("#search_selected_category_id").val();
        }

        var scpidsArray = new Array();
        jQuery('#category_property_view_container input:checked').each(function() {
            var cid = jQuery(this).attr('category_id');
            scpidsArray.push(cid);
        });
        if (scpidsArray.length > 0)
        {
            params.scpids = scpidsArray.join();
        }
        /*if (typeof ngs.PagingManager !== 'undefined' && typeof ngs.PagingManager.params.spg !== 'undefined')
         {
         ngs.PagingManager.params.spg		
         }*/
        if (typeof this.params !== 'undefined' && typeof this.params.spg !== 'undefined')
        {
            params.spg = this.params.spg;
        }


        if (jQuery('#no_picture_items_only').length > 0) {
            params.show_only_non_picture_items = jQuery('#no_picture_items_only').prop('checked') ? 1 : 0;
        }
        if (jQuery('#no_short_spec_items_only').length > 0) {
            params.show_only_no_short_spec_items = jQuery('#no_short_spec_items_only').prop('checked') ? 1 : 0;
        }
        if (jQuery('#no_full_spec_items_only').length > 0) {
            params.show_only_no_full_spec_items = jQuery('#no_full_spec_items_only').prop('checked') ? 1 : 0;
        }
        params.win_uid = jQuery("#win_uid").val();
        return params;
    },
    setCursorEnd: function(txt) {
        if (txt.createTextRange) {
            //IE
            var FieldRange = txt.createTextRange();
            FieldRange.moveStart('character', txt.value.length);
            FieldRange.collapse();
            FieldRange.select();
        } else {
            //Firefox and Opera
            txt.focus();
            var length = txt.value.length;
            txt.setSelectionRange(length, length);
        }
    },
    addCreditMonthsChangeHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var e = elements_array[i];
            e.onchange = this.onItemCreditMonthsChanged.bind(this, e.readAttribute('item_id'), e);
        }
    },
    onItemCreditMonthsChanged: function(itemId, selectElement) {

        var priceAMD = $('item_price_amd_for_credit^' + itemId).value;
        var monthlyInterestRatio = parseFloat($('default_monthly_credit_interest_ratio').value);
        var commission = parseFloat($('default_credit_supplier_commission').value);
        var selectedCreditMonths = selectElement.value;
        var monthlyPayment = priceAMD / (1 - commission / 100) * monthlyInterestRatio / (1 - 1 / Math.pow(1 + monthlyInterestRatio, selectedCreditMonths));
        $('item_monthly_credit_amount^' + itemId).innerHTML = monthlyPayment.formatMoney(0, '.', ',');
    },
    onCheckItemAvailabilityFinalButtonClick: function(itemId) {
        var keepAnonymous = $('keep_anonymous_checkbox').checked;
        if ($('check_item_availability_button_' + itemId)) {
            $('check_item_availability_button_' + itemId).remove();
        }
        if ($('large_view_check_item_availability_button')) {
            $('large_view_check_item_availability_button').remove();
        }
        ngs.action('add_company_item_check_action', {
            "item_id": itemId,
            "keep_anonymous": keepAnonymous ? 1 : 0
        });
    },
    addItemClickHandler: function(elements_array) {

        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
            elements_array[i].onclick = this.onItemClick.bind(this, item_id);
        }
    },
    onItemClick: function(item_id) {
        ngs.load('item_large_view', {
            "item_id": item_id
        });
        return false;
    },
    addAddToCartButtonsClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var item_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
            elements_array[i].onclick = this.onAddToCartButtonClick.bind(this, item_id);
        }
    },
    onCheckItemAvailabilityButtonClick: function(itemId) {
        jQuery("#check_item_availability_dialog").dialog({
            resizable: false,
            title: ngs.LanguageManager.getPhrase(527),
            modal: true,
            buttons: [{
                    text: ngs.LanguageManager.getPhrase(86),
                    'class': "dialog_default_button_class translatable_search_content",
                    phrase_id: 86,
                    click: function() {
                        ngs.ItemSearchLoad.prototype.onCheckItemAvailabilityFinalButtonClick(itemId);
                        jQuery(this).dialog('close');
                    }
                },
                {
                    text: ngs.LanguageManager.getPhrase(49),
                    'class': "translatable_search_content",
                    phrase_id: 49,
                    click: function() {
                        jQuery(this).dialog('close');
                    }
                }
            ],
            close: function() {
                jQuery(this).dialog('close');
            },
            open: function(event, ui) {
                jQuery(this).parent().attr('phrase_id', 527);
                jQuery(this).parent().addClass('translatable_search_content');
            }
        });
    },
    onAddToCartButtonClick: function(itemId) {

        var clone = jQuery('#search_item_unit_container_' + itemId).clone(false);
        clone.css({
            position: "absolute",
            top: jQuery('#search_item_unit_container_' + itemId).offset().top,
            left: jQuery('#search_item_unit_container_' + itemId).offset().left,
            width: jQuery('#search_item_unit_container_' + itemId).width()

        }).appendTo('body');

        clone.animate({
            opacity: 0.3,
            left: jQuery('#shopping_cart_item_count').offset().left - clone.width() / 2,
            top: jQuery('#shopping_cart_item_count').offset().top

        }, 500, function() {
            $(this).remove();
        });

        ngs.action("add_to_cart_action", {
            "item_id": itemId
        });
    },
    onLoadDestroy: function()
    {
        jQuery("#" + this.getContainer()).html("");
    }
});
