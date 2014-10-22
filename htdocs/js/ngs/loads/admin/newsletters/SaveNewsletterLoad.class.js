ngs.SaveNewsletterLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin_newsletters", ajaxLoader);
	},
	getUrl: function() {
		return "save_newsletter";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "sys_config_save_newsletter_container";
	},
	getName: function() {
		return "save_newsletter";
	},
	afterLoad: function() {
		jQuery('#sn_newsletter_select').change(function() {
			var newsletterId = jQuery(this).val();
			var newsletterText = jQuery(this).find('option:selected').text();
			jQuery('#sn_newsletter_title').val(newsletterText);
			jQuery('#sn_newsletter_title').attr('newsletter_id', newsletterId);
		});
		jQuery("#sn_save_button").click(function() {
			var saveTitle = jQuery('#sn_newsletter_title').val();
			tinyMCE.activeEditor.save();
			var bodyHTML = jQuery('#sc_newsletter_html').val();
			jQuery('#sys_config_save_newsletter_container').dialog("close");
			ngs.action("save_newsletter_action", {"title": saveTitle, 'html': bodyHTML, 
             'include_all_active_users':jQuery('#send_to_all_registered_users').is(':checked')});
		});

	},
	onLoadDestroy: function()
	{

	}
});

