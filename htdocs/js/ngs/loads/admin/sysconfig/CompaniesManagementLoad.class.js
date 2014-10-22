ngs.CompaniesManagementLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_sysconfig", ajaxLoader);
    },
    getUrl: function() {
        return "companies_management";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "f_companies_management_container";
    },
    getName: function() {
        return "companies_management";
    },
    afterLoad: function() {
        var thisInstance = this;
        jQuery('#cm_load_page_button').click(function() {
            jQuery("#f_companies_management_container").block({message: 'processing...'});
            ngs.load('companies_management', {'load_page': 1});
        });
       
    }
});