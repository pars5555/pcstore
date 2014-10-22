ngs.InboxLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_mails", ajaxLoader);
	},
	getUrl: function() {
		return "inbox";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "mails_main_container";
	},
	getName: function() {
		return "inbox";
	},
	afterLoad: function() {
		jQuery('#global_modal_loading_div').css('display', 'none');
		ngs.UrlChangeEventObserver.setFakeURL("/mails/inbox");
		$('global_modal_loading_div').style.display = 'none';
		if ($('ym_delete_email')) {
			$('ym_delete_email').onclick = this.onDeleteClicked.bind(this);
		}
		if ($('ym_reply_email')) {
			$('ym_reply_email').onclick = this.onReplyClicked.bind(this);
		}
		if ($('ym_forward_email')) {
			$('ym_forward_email').onclick = this.onForwardClicked.bind(this);
		}

		this.controlToolbarButtonsVisibility();
		ngs.YourMailsLoad.prototype.checkboxesClickFetchAndPrevent();
		ngs.YourMailsLoad.prototype.controlSelectingTheEmailCheckboxes();
		ngs.YourMailsLoad.prototype.initResizable();


	},
	controlToolbarButtonsVisibility: function()
	{
		jQuery(".f_emails_row_checkboxes").change(function() {
			ngs.InboxLoad.prototype.onCheckboxChange();
		});
		this.onCheckboxChange();
	},
	onCheckboxChange: function()
	{
		var selectedCheckboxes = jQuery('input[class=f_emails_row_checkboxes]:checked');
		if (selectedCheckboxes.length > 0)
		{
			jQuery('#ym_delete_email').css({visibility: "visible"});
			if (selectedCheckboxes.length === 1)
			{
				jQuery('#ym_reply_email').css({visibility: "visible"});
				jQuery('#ym_forward_email').css({visibility: "visible"});
			} else
			{
				jQuery('#ym_reply_email').css({visibility: "hidden"});
				jQuery('#ym_forward_email').css({visibility: "hidden"});
			}
		} else
		{
			jQuery('#ym_delete_email').css({visibility: "hidden"});
			jQuery('#ym_reply_email').css({visibility: "hidden"});
			jQuery('#ym_forward_email').css({visibility: "hidden"});
		}
	},
	onDeleteClicked: function() {
		var email_ids_to_delete = new Array();
		jQuery('#mails_inbox_body input:checked').each(function() {
			var id = jQuery(this).attr('id');
			var mailId = parseInt(id.substr(id.lastIndexOf("_") + 1));
			email_ids_to_delete.push(mailId);
		});
		if (email_ids_to_delete.length > 0) {
			ngs.action("delete_customer_local_emails_action", {"email_ids": email_ids_to_delete.join(), "to_trash": 1});
			ngs.YourMailsLoad.prototype.removeEmailsRowByEmailsIds(email_ids_to_delete);
			this.onCheckboxChange();
		}

	},
	onReplyClicked: function() {
		var selectedCheckboxes = jQuery('input[class=f_emails_row_checkboxes]:checked');

		if (selectedCheckboxes.length === 1)
		{
			var id = selectedCheckboxes.attr('id');
			var mailId = parseInt(id.substr(id.lastIndexOf("_") + 1));
			ngs.load("mails_compose", {"email_id": mailId, "reply": 1});
		}
	},
	onForwardClicked: function() {
		var selectedCheckboxes = jQuery('input[class=f_emails_row_checkboxes]:checked');
		if (selectedCheckboxes.length === 1)
		{
			var id = selectedCheckboxes.attr('id');
			var mailId = parseInt(id.substr(id.lastIndexOf("_") + 1));
			ngs.load("mails_compose", {"email_id": mailId, "forward": 1});
		}
	}
});
