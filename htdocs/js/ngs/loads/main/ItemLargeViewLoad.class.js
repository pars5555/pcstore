ngs.ItemLargeViewLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "item_large_view";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "hp_item_search_tab";
    },
    getName: function() {
        return "item_large_view";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
        if (typeof ngs.prev_active_tabs_load_name === 'undefined')
        {
            ngs.prev_active_tabs_load_name = new Array();
        }
        ngs.prev_active_tabs_load_name.push(ngs.activeLoadName);
        if (typeof ngs.ItemLargeViewLoad.previousHtml === 'undefined')
        {
            ngs.ItemLargeViewLoad.previousHtml = new Array();
        }
        ngs.ItemLargeViewLoad.previousHtml.push(jQuery("#hp_item_search_tab").html());
    },
    afterLoad: function() {
        $('global_modal_loading_div').style.display = 'none';
        ngs.activeLoadName = "item_large_view";
        window.scrollTo(0, 0);
        var item_id = null;
        if (jQuery('#selected_large_view_item_id').length > 0) {

            item_id = jQuery('#selected_large_view_item_id').val();

            ngs.UrlChangeEventObserver.setFakeURL("/item/" + item_id);

            jQuery('#gallery a').lightBox();
            var add_to_cart_button = $('large_view_add_to_cart_button');
            if (add_to_cart_button) {
                add_to_cart_button.onclick = this.onAddToCartButton.bind(this, item_id);
            }
            var check_item_availability_button = $('large_view_check_item_availability_button');
            if (check_item_availability_button) {
                check_item_availability_button.onclick = this.onCheckItemAvailabilityClicked.bind(this, item_id);
            }
            if ($('save_item_spec_button')) {
                $('save_item_spec_button').onclick = this.saveSpecText.bind(this);
            }
            if ($('save_item_short_spec_button')) {
                $('save_item_short_spec_button').onclick = this.saveShortSpecText.bind(this);
            }
        }
        var back_to_search_result_link = jQuery('#back_to_search_result_link');
        if (back_to_search_result_link.length > 0) {

            back_to_search_result_link.click(function() {
                ngs.ItemLargeViewLoad.prototype.onLoadDestroy(true);
            });
        }

        jQuery('#f_change_item_sub_categories').click(function() {
            var rootCategoryId = jQuery('#item_root_category_select').val();
            jQuery('<div id="f_sub_category_dialog_container"></div>').appendTo("body");
            ngs.load("sub_categories_selection", {
                "item_root_category": rootCategoryId,
                "result_hidden_element_id": 'ilw_sub_categories'
            });

        });
        jQuery('#item_root_category_select').change(function() {
           var rootCategoryId =jQuery(this).val();
           jQuery('#ilw_sub_categories').val('');
            ngs.action('admin_set_item_categories', {'item_id': jQuery('#selected_large_view_item_id').val(), 'root_category_id': rootCategoryId, 'sub_categories_ids': ''});
        });

        jQuery('#ilw_sub_categories').change(function() {
            var rootCategoryId = jQuery('#item_root_category_select').val();
            var categoriesIds = jQuery('#ilw_sub_categories').val();
            ngs.action('admin_set_item_categories', {'item_id': jQuery('#selected_large_view_item_id').val(), 'root_category_id': rootCategoryId, 'sub_categories_ids': categoriesIds});
        });
        jQuery('#login_to_order_link').click(function() {
            ngs.load('login_dialog', {});
        });

        this.initTinyMCE("textarea#item_spec_editable_div");
    },
    saveSpecText: function()
    {
        tinymce.activeEditor.save();
        var spec = jQuery("#item_spec_editable_div").val();
        ngs.action("change_item_spec_action", {"item_id": jQuery('#selected_large_view_item_id').val(), "spec": spec});
    },
    saveShortSpecText: function()
    {
        var short_spec = jQuery("#item_short_spec_textarea").val();
        ngs.action("change_item_spec_action", {"item_id": jQuery('#selected_large_view_item_id').val(), "spec": short_spec, "short_spec": 1});
    },
    onAddToCartButton: function(itemId) {
        ngs.action("add_to_cart_action", {
            "item_id": itemId
        });
    },
    onCheckItemAvailabilityClicked: function(itemId) {
        ngs.ItemSearchResultLoad.prototype.onCheckItemAvailabilityButtonClick(itemId);
    },
    onLoadDestroy: function(backClicked)
    {
        if (typeof ngs.ItemLargeViewLoad.previousHtml !== 'undefined' && ngs.ItemLargeViewLoad.previousHtml.length > 0) {
            //jQuery("#item_large_view").empty();
            if (backClicked) {
                ngs.activeLoadName = ngs.prev_active_tabs_load_name.pop();
                jQuery("#hp_item_search_tab").html(ngs.ItemLargeViewLoad.previousHtml.pop());
                var loadClassName = ngs.ItemLargeViewLoad.prototype.getLoadClassName(ngs.activeLoadName);
                ngs[loadClassName].prototype.afterLoad();
            }
        } else {
            if (backClicked) {
                window.location = '/search';
            }
        }

    },
    getLoadClassName: function(loadName)
    {
        var parts = loadName.split('_');
        var partsUcFirst = new Array();
        jQuery.each(parts, function(i, e) {
            partsUcFirst.push(ngs.ItemLargeViewLoad.prototype.ucfirst(e));

        });
        return partsUcFirst.join('') + 'Load';
    },
    ucfirst: function(str) {
        var f = str.charAt(0).toUpperCase();
        return f + str.substr(1);
    }
});
