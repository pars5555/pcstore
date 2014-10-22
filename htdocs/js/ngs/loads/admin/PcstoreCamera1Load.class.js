ngs.PcstoreCamera1Load = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "pcstore_camera_1";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "f_pcstore_camera_load_container";
    },
    getName: function() {
        return "pcstore_camera_1";
    },
    beforeLoad: function() {

    },
    afterLoad: function() {
        var thisInstance = this;
        jQuery('#pc1_camera_img_id').on("load error", function() {
            thisInstance.reloadImg('pc1_camera_img_id');
        });
    },
    reloadImg: function(id) {
        var img = jQuery('#' + id);
        var src = img.attr('src');
        var i = src.indexOf('&dummy=');
        src = i != -1 ? src.substring(0, i) : src;
        d = new Date();        
        img.attr('src', src + '&dummy=' + d.getTime());
    }
});

