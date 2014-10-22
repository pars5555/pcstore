ngs.YourMailsLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "your_mails";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "hp_" + this.getName() + "_tab";
	},
	getName: function() {
		return "your_mails";
	},
	beforeLoad: function() {
		$('global_modal_loading_div').style.display = 'block';
	},
	afterLoad: function() {
		if ($('ym_inbox')) {
			$('ym_inbox').onclick = this.onInboxLinkClicked.bind(this);
		}
		if ($('ym_sent')) {
			$('ym_sent').onclick = this.onSentLinkClicked.bind(this);
		}
		if ($('ym_trash')) {
			$('ym_trash').onclick = this.onTrashLinkClicked.bind(this);
		}
		if ($('ym_compose_email')) {
			$('ym_compose_email').onclick = this.onComposeButtonClicked.bind(this);
		}
		if ($('active_folder_to_show')) {
			var active_folder_to_show = $('active_folder_to_show').value;
			ngs.nestLoad("mails_" + active_folder_to_show, {});
		}

	},
	checkboxesClickFetchAndPrevent: function()
	{
		this.lastSelectedRowId = -1;
		var classThis = this;
		jQuery(".f_emails_row_checkboxes").click(function(event) {
			classThis.lastSelectedRowId = jQuery(this).attr('row');
			event.stopPropagation();
		});
	},
	initResizable: function() {
		jQuery("#mails_resizable").resizable({
			maxHeight: 400,
			minHeight: 100,
			handles: "s"
		});
	},
	controlSelectingTheEmailCheckboxes: function() {

		var classThis = this;
		jQuery('div.emails_row_container').click(function(e) {
			jQuery('div.emails_row_container').removeClass('selected');
			jQuery(this).addClass('selected');
			var id = jQuery(this).attr('id');
			var mailId = id.substr(id.lastIndexOf("_") + 1);

			ngs.load("mails_email_body", {"email_id": mailId});
			var emailCheckbox = jQuery("input[id='email_checkbox_" + mailId + "']");
			if (e.shiftKey && classThis.lastSelectedRowId >= 0)
			{
				var currentRowId = emailCheckbox.attr('row');
				var maxRowId = Math.max(currentRowId, classThis.lastSelectedRowId);
				var minRowId = Math.min(currentRowId, classThis.lastSelectedRowId);
				var checkboxesToBeSelect = jQuery(".f_emails_row_checkboxes").filter(function() {
					return  parseInt(jQuery(this).attr('row')) >= minRowId && parseInt(jQuery(this).attr('row')) <= maxRowId;
				});
				checkboxesToBeSelect.attr('checked', true);
				classThis.lastSelectedRowId = currentRowId;
			} else {
				jQuery(".f_emails_row_checkboxes").attr('checked', false);

				emailCheckbox.attr('checked', emailCheckbox.is(':checked') ? false : true);
				emailCheckbox.trigger('change');
				classThis.lastSelectedRowId = emailCheckbox.attr('row');
			}
			document.getSelection().removeAllRanges();
		});
	},
	removeEmailsRowByEmailsIds: function(emailsIdsArray) {
		var divsToDelete = jQuery(".emails_row_container").filter(function() {
			return jQuery.inArray(parseInt(jQuery(this).attr('email_id')), emailsIdsArray) >= 0;
		});
		divsToDelete.remove();
	},
	onInboxLinkClicked: function() {
		jQuery('#global_modal_loading_div').css('display', 'block');
		ngs.load("mails_inbox", {});
	},
	onTrashLinkClicked: function() {
		jQuery('#global_modal_loading_div').css('display', 'block');
		ngs.load("mails_trash", {});
	},
	onSentLinkClicked: function() {
		jQuery('#global_modal_loading_div').css('display', 'block');
		ngs.load("mails_sent", {});
	},
	onComposeButtonClicked: function() {
		jQuery('#global_modal_loading_div').css('display', 'block');
		ngs.load("mails_compose", {});
	},
	onLoadDestroy: function()
	{
		jQuery("#" + this.getContainer()).html("");
	}
});
