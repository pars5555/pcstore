ngs.CmsMainLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "cms", ajaxLoader);
    },
    getUrl: function() {
        return "main";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "content";
    },
    getName: function() {
        return "cms_main";
    },
    afterLoad: function() {
        ngs.nestLoad(jQuery('#contentLoad').val(), {});
    }
});
