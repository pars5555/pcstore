ngs.ServiceNewMailServerDialogLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "servicecompany_uploadprice", ajaxLoader);
	},
	getUrl: function() {
		return "service_new_mail_server_dialog";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "new_mail_server_dialog_content";
	},
	getName: function() {
		return "service_new_mail_server_dialog";
	},
	afterLoad: function() {

	},
	onLoadDestroy: function()
	{

	}
});
