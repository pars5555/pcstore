ngs.CmsOnlineUsersLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "cms", ajaxLoader);
    },
    getUrl: function() {
        return "online_users";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "content";
    },
    getName: function() {
        return "cms_online_users";
    },
    afterLoad: function() {
    }
});
