ngs.ManageNewslettersLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin_newsletters", ajaxLoader);
	},
	getUrl: function() {
		return "manage_newsletters";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "sys_config_manage_newsletters_container";
	},
	getName: function() {
		return "manage_newsletters";
	},
	afterLoad: function() {
		jQuery("#mn_delete_newsletter_button").one('click', function() {
			var id = jQuery('#mn_newsletter_select').val();
			ngs.action("delete_newsletter_action", {"newsletter_id": id});
		});
	},
	onLoadDestroy: function()
	{

	}
});

