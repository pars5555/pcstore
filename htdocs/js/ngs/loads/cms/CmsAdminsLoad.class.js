ngs.CmsAdminsLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "cms", ajaxLoader);
    },
    getUrl: function() {
        return "admins";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "content";
    },
    getName: function() {
        return "cms_admins";
    },
    afterLoad: function() {
    }
});
