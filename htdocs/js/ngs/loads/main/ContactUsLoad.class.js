ngs.ContactUsLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "contact_us";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "f_contactus_container_dialog";
	},
	getName: function() {
		return "contact_us";
	},
	afterLoad: function() {        
	}
});
