ngs.NewsletterSendersLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin_newsletters", ajaxLoader);
	},
	getUrl: function() {
		return "newsletter_senders";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "admin_newsletter_sender_emails_conteiner";
	},
	getName: function() {
		return "newsletter_senders";
	},
	afterLoad: function() {
		
	}
});

