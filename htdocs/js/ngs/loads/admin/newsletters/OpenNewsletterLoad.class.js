ngs.OpenNewsletterLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin_newsletters", ajaxLoader);
	},
	getUrl: function() {
		return "open_newsletter";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "sys_config_open_newsletter_container";
	},
	getName: function() {
		return "open_newsletter";
	},
	afterLoad: function() {
		jQuery("#on_open_newsletter_button").click(function() {
			var title = jQuery('#on_newsletter_select').val();
			jQuery('#sys_config_open_newsletter_container').dialog("close");
			ngs.action("open_newsletter_action", {"newsletter_id": title});
		});
	},
	onLoadDestroy: function()
	{

	}
});

