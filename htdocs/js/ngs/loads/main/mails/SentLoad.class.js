ngs.SentLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_mails", ajaxLoader);
	},
	getUrl: function() {
		return "sent";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "mails_main_container";
	},
	getName: function() {
		return "sent";
	},
	afterLoad: function() {
		jQuery('#global_modal_loading_div').css('display', 'none');
		ngs.UrlChangeEventObserver.setFakeURL("/mails/sent");
		$('global_modal_loading_div').style.display = 'none';
		if ($('ym_delete_email')) {
			$('ym_delete_email').onclick = this.onDeleteClicked.bind(this);
		}

		ngs.YourMailsLoad.prototype.checkboxesClickFetchAndPrevent();
		ngs.YourMailsLoad.prototype.controlSelectingTheEmailCheckboxes();
		ngs.YourMailsLoad.prototype.initResizable();
		this.controlToolbarButtonsVisibility();
	},
	controlToolbarButtonsVisibility: function()
	{
		jQuery(".f_emails_row_checkboxes").change(function() {
			ngs.SentLoad.prototype.onCheckboxChange();
		});
		this.onCheckboxChange();
	},
	onDeleteClicked: function() {
		var email_ids_to_delete = this.getSelectedEmailsIdsArray();
		if (email_ids_to_delete.length > 0) {
			ngs.action("delete_customer_local_emails_action", {"email_ids": email_ids_to_delete.join(), "to_trash": 1});
			ngs.YourMailsLoad.prototype.removeEmailsRowByEmailsIds(email_ids_to_delete);
			this.onCheckboxChange();
		}

	},
	onCheckboxChange: function()
	{
		var selectedCheckboxes = jQuery('input[class=f_emails_row_checkboxes]:checked');
		if (selectedCheckboxes.length > 0)
		{
			jQuery('#ym_delete_email').css({visibility: "visible"});

		} else
		{
			jQuery('#ym_delete_email').css({visibility: "hidden"});
		}
	},
	getSelectedEmailsIdsArray: function()
	{
		var email_ids = new Array();
		jQuery('#mails_sent_body input:checked').each(function() {
			var id = jQuery(this).attr('id');
			var mailId = parseInt(id.substr(id.lastIndexOf("_") + 1));
			email_ids.push(mailId);
		});
		return email_ids;
	}
});
