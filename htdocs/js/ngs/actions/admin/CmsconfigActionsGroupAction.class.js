ngs.CmsconfigActionsGroupAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "do_cmsconfig_actions_group";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status === "ok") {
            switch (this.params.action) {

                case 'find_similar_items':
                    var items = data.items;
                    var requested_item_id = this.params.item_id;
                    jQuery('#most_simillar_items_select_' + requested_item_id).empty();
                    jQuery.each(items, function(index, value) {
                        var itemId = value[0];                        
                        var itemTitle = value[1];
                        var itemCompanyName = value[2];
                        var shortItemTitle = itemTitle;
                        if (shortItemTitle.length>100)
                        {
                            shortItemTitle = shortItemTitle.substr(0, 70)+'...';
                        }
                        var optionHtml = '<option title="'+itemTitle +'" value="' + itemId + '">' + shortItemTitle + '(' + itemCompanyName + ')' + '</option>';
                        jQuery('#most_simillar_items_select_' + requested_item_id).append(optionHtml);
                    });
                    jQuery('#most_simillar_items_select_' + requested_item_id).trigger('change');
                    break;
                case 'get_item_attributes':
                    var base_item_id = this.params.base_item_id;
                    var item = data.item;
                    var picCount = item.pictures_count;
                    var shortSpec = jQuery.trim(item.short_description);
                    var fullSpec = jQuery.trim(item.full_description);
                    var model = jQuery.trim(item.model);
                    if (picCount > 0)
                    {
                        jQuery('#im_copy_item_pictures_button_' + base_item_id).css('visibility', 'visible').html(' <- ' + picCount + ' Pic');
                    } else
                    {
                        jQuery('#im_copy_item_pictures_button_' + base_item_id).css('visibility', 'hidden');
                    }

                    if (shortSpec !== "") {
                        jQuery('#im_copy_item_short_spec_button_' + base_item_id).css('visibility', 'visible');
                    } else
                    {
                        jQuery('#im_copy_item_short_spec_button_' + base_item_id).css('visibility', 'hidden');
                    }
                    if (fullSpec !== "") {
                        jQuery('#im_copy_item_full_spec_button_' + base_item_id).css('visibility', 'visible');
                    } else {
                        jQuery('#im_copy_item_full_spec_button_' + base_item_id).css('visibility', 'hidden');
                    }
                    if (model !== "") {
                        jQuery('#im_copy_item_model_button_' + base_item_id).css('visibility', 'visible');
                    } else {
                        jQuery('#im_copy_item_model_button_' + base_item_id).css('visibility', 'hidden');
                    }
                    break;
            }
        } else if (data.status === "err") {
            ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
        }
    }
});
