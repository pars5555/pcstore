ngs.ItemsManagementLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_sysconfig", ajaxLoader);
    },
    getUrl: function() {
        return "items_management";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "f_items_management_container";
    },
    getName: function() {
        return "items_management";
    },
    afterLoad: function() {
        var thisInstance = this;
        jQuery('#im_load_page_button').click(function() {
            jQuery("#f_items_management_container").block({message: 'processing...'});
            ngs.load('items_management', {'load_page': 1});
        });
        jQuery('#im_include_hiddens').click(function() {
            thisInstance.updateLoad();
        });
        jQuery('#im_empty_model').click(function() {
            thisInstance.updateLoad();
        });
        jQuery('#im_empty_short_spec').click(function() {
            thisInstance.updateLoad();
        });
        jQuery('#im_empty_full_spec').click(function() {
            thisInstance.updateLoad();
        });
        jQuery('#im_pictures_count').change(function() {
            thisInstance.updateLoad();
        });

        jQuery('.im_find_simillar_items_button').click(function() {
            var item_id = jQuery(this).attr('item_id');
            ngs.action('cmsconfig_actions_group_action', {'action': "find_similar_items", "item_id": item_id});
        });
        
        jQuery('.same_items_select').change(function() {
            var base_item_id = jQuery(this).attr('item_id');
            var item_id = jQuery(this).val();
            ngs.action('cmsconfig_actions_group_action', {'action': "get_item_attributes", "item_id": item_id,"base_item_id":base_item_id});
        });
        
        jQuery('.im_copy_item_model_buttons').click(function(){
            var target_item_id = jQuery(this).attr('item_id');
            var source_item_id = jQuery('#most_simillar_items_select_'+target_item_id).val();
            ngs.action('cmsconfig_actions_group_action', {'action': "copy_item_model", "source_item_id": source_item_id,"target_item_id":target_item_id});
        });
        
        jQuery('.im_copy_item_short_spec_buttons').click(function(){
            var target_item_id = jQuery(this).attr('item_id');
            var source_item_id = jQuery('#most_simillar_items_select_'+target_item_id).val();
            ngs.action('cmsconfig_actions_group_action', {'action': "copy_item_short_spec", "source_item_id": source_item_id,"target_item_id":target_item_id});
        });
        jQuery('.im_copy_item_full_spec_buttons').click(function(){
            var target_item_id = jQuery(this).attr('item_id');
            var source_item_id = jQuery('#most_simillar_items_select_'+target_item_id).val();
            ngs.action('cmsconfig_actions_group_action', {'action': "copy_item_full_spec", "source_item_id": source_item_id,"target_item_id":target_item_id});
        });
        jQuery('.im_copy_item_pictures_buttons').click(function(){
            var target_item_id = jQuery(this).attr('item_id');
            var source_item_id = jQuery('#most_simillar_items_select_'+target_item_id).val();
            ngs.action('cmsconfig_actions_group_action', {'action': "copy_item_pictures", "source_item_id": source_item_id,"target_item_id":target_item_id});
        });

    },
    calcLoadParams: function() {
        var include_hiddens = jQuery('#im_include_hiddens').prop('checked') ? '1' : '0';
        var empty_model = jQuery('#im_empty_model').prop('checked') ? '1' : '0';
        var empty_short_spec = jQuery('#im_empty_short_spec').prop('checked') ? '1' : '0';
        var empty_full_spec = jQuery('#im_empty_full_spec').prop('checked') ? '1' : '0';
        var pictures_count = jQuery('#im_pictures_count').val();
        var ret = {'include_hiddens': include_hiddens, 'empty_model': empty_model,
            'empty_short_spec': empty_short_spec, 'empty_full_spec': empty_full_spec, 'pictures_count': pictures_count};
        ret.load_page = 1;
        return ret;
    },
    updateLoad: function()
    {
        jQuery("#f_items_management_container").block({message: 'processing...'});
        ngs.load('items_management', this.calcLoadParams());
    }
});