ngs.EmailBodyLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_mails", ajaxLoader);
	},
	getUrl: function() {
		return "email_body";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "mails_bottom_component";
	},
	getName: function() {
		return "email_body";
	},
	afterLoad: function()
	{
		jQuery('#email_row_container_' + this.params.email_id).css('font-weight', 'normal');
		var unreadEmailsCount = parseInt(jQuery('#customer_unread_email_count').val());
		jQuery('#customer_inbox_unread_email_count_span').html(unreadEmailsCount);
		var yourEmailsTitle = jQuery('#hp_tabs_container a[href="#hp_your_mails_tab"]').html();
							
		if (yourEmailsTitle.indexOf('*') !== -1 && unreadEmailsCount === 0)
		{
			yourEmailsTitle = yourEmailsTitle.replace('*', '');

		}
		if (yourEmailsTitle.indexOf('*') === -1 && unreadEmailsCount > 0)
		{
			yourEmailsTitle += '*';
		}
		jQuery('#hp_tabs_container a[href="#hp_your_mails_tab"]').html(yourEmailsTitle);
							
	}
});
