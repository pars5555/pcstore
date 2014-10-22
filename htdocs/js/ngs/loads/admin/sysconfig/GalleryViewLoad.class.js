ngs.GalleryViewLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin_sysconfig", ajaxLoader);
	},
	getUrl: function() {
		return "gallery_view";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "f_admin_config_gallery_container";
	},
	getName: function() {
		return "gallery_view";
	},
	afterLoad: function() {

		var options = {
			url: site_PROTOCOL + SITE_URL + '/js/lib/elfinder-2.0-rc1/php/connector.php',
			lang: 'en'
		};
		jQuery('#elfinder').elfinder(options);
	}
});