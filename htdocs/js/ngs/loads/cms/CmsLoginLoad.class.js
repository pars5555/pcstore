ngs.CmsLoginLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "cms", ajaxLoader);
    },
    getUrl: function() {
        return "login";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "content";
    },
    getName: function() {
        return "cms_login";
    },
    afterLoad: function() {
    }
});
