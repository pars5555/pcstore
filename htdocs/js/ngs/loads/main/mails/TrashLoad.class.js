ngs.TrashLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_mails", ajaxLoader);
	},
	getUrl: function() {
		return "trash";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "mails_main_container";
	},
	getName: function() {
		return "trash";
	},
	afterLoad: function() {
		jQuery('#global_modal_loading_div').css('display', 'none');
		ngs.UrlChangeEventObserver.setFakeURL("/mails/trash");
		$('global_modal_loading_div').style.display = 'none';
		if ($('ym_delete_email')) {
			$('ym_delete_email').onclick = this.onDeleteClicked.bind(this);
		}
		if ($('ym_restore_email')) {
			$('ym_restore_email').onclick = this.onRestoreClicked.bind(this);
		}

		ngs.YourMailsLoad.prototype.checkboxesClickFetchAndPrevent();
		ngs.YourMailsLoad.prototype.controlSelectingTheEmailCheckboxes();
		this.controlToolbarButtonsVisibility();
		ngs.YourMailsLoad.prototype.initResizable();
	},
	controlToolbarButtonsVisibility: function()
	{
		jQuery(".f_emails_row_checkboxes").change(function() {
			ngs.TrashLoad.prototype.onCheckboxChange();
		});
		this.onCheckboxChange();
	},
	onCheckboxChange: function()
	{
		var selectedCheckboxes = jQuery('input[class=f_emails_row_checkboxes]:checked');
		if (selectedCheckboxes.length > 0)
		{
			jQuery('#ym_delete_email').css({visibility: "visible"});
			jQuery('#ym_restore_email').css({visibility: "visible"});

		} else
		{
			jQuery('#ym_delete_email').css({visibility: "hidden"});
			jQuery('#ym_restore_email').css({visibility: "hidden"});

		}
	},
	onDeleteClicked: function() {
		var email_ids_to_delete = this.getSelectedEmailsIdsArray();
		if (email_ids_to_delete.length > 0) {
			jQuery("<div phrase_id='490' class='replaceable_lang_element'>" + ngs.LanguageManager.getPhrase(490) + "</div>").dialog({
				resizable: false,
				modal: true,
				title: ngs.LanguageManager.getPhrase(483),
				buttons: [{
						text: ngs.LanguageManager.getPhrase(489),
						'class': "dialog_default_button_class translatable_search_content",
						phrase_id: 489,
						click: function() {
							jQuery(this).remove();
							ngs.action("delete_customer_local_emails_action", {"email_ids": email_ids_to_delete.join(), "delete": 1});
							ngs.YourMailsLoad.prototype.removeEmailsRowByEmailsIds(email_ids_to_delete);
							ngs.TrashLoad.prototype.onCheckboxChange();
						}
					},
					{
						text: ngs.LanguageManager.getPhrase(49),
						'class': "translatable_search_content",
						phrase_id: 49,
						click: function() {
							jQuery(this).remove();
						}
					}
				],
				close: function() {
					jQuery(this).remove();
				},
				open: function(event, ui) {
					jQuery(this).parent().attr('phrase_id', 483);
					jQuery(this).parent().addClass('translatable_search_content');
				}

			});
		}

	},
	onRestoreClicked: function() {
		var email_ids_to_delete = this.getSelectedEmailsIdsArray();
		if (email_ids_to_delete.length > 0) {
			ngs.action("restore_customer_local_emails_action", {"email_ids": email_ids_to_delete.join()});
			ngs.YourMailsLoad.prototype.removeEmailsRowByEmailsIds(email_ids_to_delete);
		}

	},
	getSelectedEmailsIdsArray: function()
	{
		var email_ids = new Array();
		jQuery('#mails_trash_body input:checked').each(function() {
			var id = jQuery(this).attr('id');
			var mailId = parseInt(id.substr(id.lastIndexOf("_") + 1));
			email_ids.push(mailId);
		});
		return email_ids;
	}
});
