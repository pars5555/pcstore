ngs.CartStepInnerLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_checkout", ajaxLoader);
    },
    getUrl: function() {
        return "cart_step_inner";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "checkout_steps_container";
    },
    getName: function() {
        return "cart_step_inner";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {
        $('global_modal_loading_div').style.display = 'none';
        var thisInstance = this;
        jQuery('#shopping_cart_div').dialog('option', 'title', ngs.LanguageManager.getPhrase(278));
        jQuery('#shopping_cart_div').dialog().parent().attr('phrase_id', 278);

        var nextButtonTitlePhraseId = jQuery('#cart_next_button_status_and_title').attr('phrase_id');
        var nextButtonTitle = jQuery('#cart_next_button_status_and_title').attr('title');
        var nextButtonDisabled = jQuery('#cart_next_button_status_and_title').attr('button_disabled') === 'true' ? true : false;
        var btns = {
            "Next": {
                text: ngs.LanguageManager.getPhrase(281),
                'class': "dialog_default_button_class translatable_search_content translatable_attribute_element",
                attribute_phrase_id: nextButtonTitlePhraseId,
                attribute_name_to_translate: "title",
                phrase_id: 281,
                title: nextButtonTitle,
                disabled: nextButtonDisabled,
                click: function() {
                    var cart_step_data = thisInstance.getCartStepDataObject();
                    var params = $H(thisInstance.params).merge(cart_step_data);
                    jQuery('#shopping_cart_div').dialog('option', 'buttons', {});

                    if (jQuery('#f_check_user_password').val() === 'yes') {
                        ngs.load("login_step_inner", params.toObject());
                    } else {
                        ngs.load("shipping_step_inner", params.toObject());
                    }
                }
            }
        };
        jQuery('#shopping_cart_div').dialog('option', 'buttons', btns);


        var cart_items_select_count_elements = $$("#checkout_steps_container .select_cart_item_count");
        this.addChangeHandlerToSelectCartItemCountElements(cart_items_select_count_elements);
        var cart_items_delete_elements = $$("#checkout_steps_container .cart_items_delete");
        this.addCartItemsDeleteElementsClickHandler(cart_items_delete_elements);
        var cart_items_edit_elements = $$("#checkout_steps_container .cart_items_edit");
        this.addCartItemsEditElementsClickHandler(cart_items_edit_elements);

        var bundle_collapse_expande_buttons = $$("#checkout_steps_container .bundle_collapse_expande_buttons");
        this.addBundleCollapseExpandeButtonsClickHandler(bundle_collapse_expande_buttons);

        jQuery('#cho_include_vat_checkbox').change(function() {
            var cart_step_data = thisInstance.getCartStepDataObject();
            var params = $H(thisInstance.params).merge(cart_step_data);
            ngs.load("cart_step_inner", params.toObject());
        });

    },
    addBundleCollapseExpandeButtonsClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var cart_bundle_item_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
            elements_array[i].onclick = this.onBundleCollapseExpandeButtonClicked.bind(this, cart_bundle_item_id, elements_array[i]);
        }
    },
    getCartStepDataObject: function()
    {
        var data = {};
        data.cho_include_vat = jQuery('#cho_include_vat_checkbox').prop('checked') ? 1 : 0;
        return data;
    },
    onBundleCollapseExpandeButtonClicked: function(bundle_id, element) {
        if (element.hasClassName('bundle_item_expand_button')) {
            new Effect.BlindDown('cart_bundle_items_container_' + bundle_id);
            element.removeClassName('bundle_item_expand_button');
            element.addClassName('bundle_item_collapse_button');
        } else {
            new Effect.BlindUp('cart_bundle_items_container_' + bundle_id, {duration: 0.3});
            element.removeClassName('bundle_item_collapse_button');
            element.addClassName('bundle_item_expand_button');
        }

    },
    addChangeHandlerToSelectCartItemCountElements: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var cart_item_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);

            elements_array[i].onchange = this.onCartItemCountChanged.bind(this, elements_array[i], cart_item_id);
        }
    },
    onCartItemCountChanged: function(element, id) {
        var count = element.value;
        var params = $H(this.params).merge({
            "id": id,
            "count": count
        });
        ngs.load("cart_step_inner", params.toObject());
        ngs.action("update_cart_items_count_action", {});
    },
    addCartItemsDeleteElementsClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var cart_item_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
            elements_array[i].onclick = this.onCartItemsDeleteClicked.bind(this, cart_item_id);
        }
    },
    onCartItemsDeleteClicked: function(id) {
        var params = $H(this.params).merge({
            "id": id,
            "count": 0
        });
        ngs.load("cart_step_inner", params.toObject());
        ngs.action("update_cart_items_count_action", {});

    },
    addCartItemsEditElementsClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var cart_item_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
            elements_array[i].onclick = this.onCartItemsEditClicked.bind(this, cart_item_id);
        }
    },
    onCartItemsEditClicked: function(id) {
        $('global_modal_loading_div').style.display = 'block';
        window.location = "/configurator/" + id;
    }
});
