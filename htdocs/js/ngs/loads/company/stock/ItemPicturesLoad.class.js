ngs.ItemPicturesLoad = Class.create(ngs.AbstractLoad, {
    removedPicturesIds: new Array(),
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "company_stock", ajaxLoader);
    },
    getUrl: function() {
        return "item_pictures";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "f_item_pictures_container";
    },
    getName: function() {
        return "item_pictures";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {
        $('global_modal_loading_div').style.display = 'none';
        jQuery('#ip_select_picture_button').click(function() {
            jQuery("#ip_file_input").trigger('click');
            return false;
        });
       
       jQuery("#ip_file_input").change(function(){           
           jQuery('#ip_add_item_picture_form').submit();
       });
       
        jQuery(".ip_remove_item_picture_x").click(function(){      
            var item_id = jQuery(this).attr('item_id');
            var picture_index = jQuery(this).attr('picture_index');
           ngs.action('add_remove_item_picture_action', {'action':'delete', 'item_id':item_id , 'picture_index':picture_index });
       });
        jQuery(".ip_default_item_picture").click(function(){      
            var item_id = jQuery(this).attr('item_id');
            var picture_index = jQuery(this).attr('picture_index');
           ngs.action('add_remove_item_picture_action', {'action':'make_default', 'item_id':item_id , 'picture_index':picture_index });
       });
    }
});
