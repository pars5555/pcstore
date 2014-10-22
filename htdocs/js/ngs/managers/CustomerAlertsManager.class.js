ngs.CustomerAlertsManager = {
	alertsContainer: null,
	auto_hide_message_timeout_seconds: null,
	company_item_check_message_timeout_seconds: null,
	init: function(ajaxLoader) {
		this.ajaxLoader = ajaxLoader;
		this.alertsContainer = $('alerts_fixed_footer_div');

		var ahmts = CMS_VARS.auto_hide_message_timeout_seconds;
		if (ahmts && parseInt(ahmts.value) >= 1) {
			this.auto_hide_message_timeout_seconds = parseInt(ahmts.value) * 1000;
		} else {
			this.auto_hide_message_timeout_seconds = 30 * 1000;
		}
		var cicmts = $('company_item_check_message_timeout_seconds');
		if (cicmts && parseInt(cicmts.value) >= 1) {
			this.company_item_check_message_timeout_seconds = parseInt(cicmts.value) * 1000;
		} else {
			this.company_item_check_message_timeout_seconds = 30 * 1000;
		}

	},
	addCompanyItemAvailablilityCheck: function(data) {
		var thisInstance = this;
		for (var i = 0; i < data.length; i++) {
			var itemId = data[i].item_id;
			var questionFromName = ((data[i].keep_anonymous == 0) ? '`537` ' + data[i].from_name + ' `538`' : '');
			var itemTitle = data[i].item_display_name;
			var ItemTitleHtml = "<div>" + ngs.LanguageManager.getPhraseSpan(536) + '<br/>' + itemTitle + "</div>";
			var dialogHtml = "<div>" + ItemTitleHtml + "</div>";
			var tempDlg = jQuery("<div id='customer_alert_dialog_container_" + itemId + "' class='customer_alert_dialog_container'></div>");
			tempDlg.appendTo("#alerts_fixed_footer_div");
			jQuery(dialogHtml).dialog({
				width: 240,
				height: 195,
				resizable: false,
				draggable: false,
				title: ngs.LanguageManager.getPhrase(questionFromName),
				buttons: {
					"yes": {
						text: ngs.LanguageManager.getPhrase(489),
						'class': "dialog_default_button_class translatable_search_content",
						phrase_id: 489,
						click: function() {
							thisInstance.onReplyItemAvailabilityCheckButtonClicked(jQuery(this).attr('item_id'), 1);
						}
					},
					"no": {
						text: ngs.LanguageManager.getPhrase(535),
						'class': "translatable_search_content",
						phrase_id: 535,
						click: function() {
							thisInstance.onReplyItemAvailabilityCheckButtonClicked(jQuery(this).attr('item_id'), -1);
						}
					}
				},
				close: function() {
					jQuery("#customer_alert_dialog_container_" + jQuery(this).attr('item_id')).remove();
				},
				open: function() {
					jQuery(this).parent().attr('phrase_id', questionFromName);
					jQuery(this).parent().addClass('translatable_search_content');
					//jQuery(this).dialog("widget").attr('id', 'check_item_availability_dialog_' + data[i].item_id);
					jQuery(this).dialog("widget").appendTo(tempDlg);
					jQuery(this).dialog("widget").addClass("customer_alert_dialog");
					jQuery(this).attr('item_id', itemId);
					
					 var dialogCloseSeconds = Math.round(thisInstance.company_item_check_message_timeout_seconds);
					 window.setTimeout(function() {
					 jQuery("#customer_alert_dialog_container_" + itemId).remove();
					 }, dialogCloseSeconds);

				}
			});
		}
	},
	onReplyItemAvailabilityCheckButtonClicked: function(itemId, response) {
		jQuery("#customer_alert_dialog_container_" + itemId).remove();
		ngs.action("company_respose_to_item_availability_action", {
			"item_id": itemId,
			"item_availability": response
		});
	},
	addResponseItemAvailabilityDialog: function(responded_items_availability) {
		var data = responded_items_availability;
		var messageInnerHTML = "";
		for (var i = 0; i < data.length; i++) {
			messageInnerHTML += data[i].item_display_name;
			if (data[i].item_availability == -1) {
				messageInnerHTML += '<br/><span style="color:red">' + ngs.LanguageManager.getPhraseSpan(19) + '</span>';
			} else if (data[i].item_availability == 1) {
				messageInnerHTML += '<br/><span style="color:green">' + ngs.LanguageManager.getPhraseSpan(530) + '</span>';
			} else {
				messageInnerHTML += '<br/><span style="color:orange">' + ngs.LanguageManager.getPhraseSpan(532) + '</span>';
			}
			messageInnerHTML += '<br/><br/>';

		}
                ngs.DialogsManager.closeDialog(529, "<div style='padding: 10px;'>" + messageInnerHTML + "</div>");
	},
	showAutoHideMessage: function(messageInnerHTML, title) {
		var thisInstance = this;
		var tempDlg = jQuery("<div class='customer_alert_dialog_container'></div>");
		tempDlg.appendTo("#alerts_fixed_footer_div");
		jQuery('<div>' + messageInnerHTML + '</div>').dialog({
			title: title,
			width: 240,
			height: 195,
			resizable: false,
			draggable: false,
			buttons: {
				"send": {
					text: ngs.LanguageManager.getPhrase(485),
					'class': "dialog_default_button_class translatable_search_content",
					phrase_id: 485,
					click: function() {
						tempDlg.remove();
					}
				}
			},
			close: function() {
				tempDlg.remove();
			},
			open: function() {
				jQuery(this).dialog("widget").appendTo(tempDlg);
				jQuery(this).dialog("widget").addClass("customer_alert_dialog");
			
				var dialogCloseSeconds = Math.round(thisInstance.company_item_check_message_timeout_seconds);
				window.setTimeout(function() {
					tempDlg.remove();
				}, dialogCloseSeconds);

			}
		});
	}
};
