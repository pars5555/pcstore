ngs.NewMailServerDialogLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company_uploadprice", ajaxLoader);
	},
	getUrl: function() {
		return "new_mail_server_dialog";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "new_mail_server_dialog_content";
	},
	getName: function() {
		return "new_mail_server_dialog";
	},
	afterLoad: function() {

	},
	onLoadDestroy: function()
	{

	}
});
