ngs.CmsUsersLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "cms", ajaxLoader);
    },
    getUrl: function() {
        return "users";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "content";
    },
    getName: function() {
        return "cms_users";
    },
    afterLoad: function() {
    }
});
