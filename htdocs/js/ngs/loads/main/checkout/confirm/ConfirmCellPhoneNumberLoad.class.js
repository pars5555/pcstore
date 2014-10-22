ngs.ConfirmCellPhoneNumberLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_checkout_confirm", ajaxLoader);
	},
	getUrl: function() {
		return "confirm_cell_phone_number";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "global_empty_container";
	},
	getName: function() {
		return "confirm_cell_phone_number";
	},
	afterLoad: function() {
		var thisInstance = this;
		var sendConfirmButtonText;
		var sendConfirmButtonTextId;
		if (jQuery('#co_send_sms_button').length !== 0) {
			sendConfirmButtonText = jQuery('#co_send_sms_button').val();
			sendConfirmButtonTextId = jQuery('#co_send_sms_button').attr('phrase_id');
		} else
		{
			sendConfirmButtonText = jQuery('#co_confirm_button').val();
			sendConfirmButtonTextId = jQuery('#co_confirm_button').attr('phrase_id');
		}

		jQuery("#confirm_cell_phone_number_div").dialog({
			modal: true,
			width: 400,
			height: 300,
			buttons: {
				"Send": {
					text: sendConfirmButtonText,
					'class': "dialog_default_button_class translatable_search_content",
					phrase_id: sendConfirmButtonTextId,
					click: function() {
						jQuery('#shopping_cart_div').dialog('option', 'buttons', {});
						if (jQuery('#co_send_sms_button').length !== 0) {
							thisInstance.onSendSMS('', thisInstance);
						} else
						{
							thisInstance.onConfirm('', thisInstance);
						}

					}
				},
				"Cancel": {
					text: ngs.LanguageManager.getPhrase(280),
					'class': "translatable_search_content",
					phrase_id: 280,
					click: function() {
						jQuery('#shopping_cart_div').dialog('option', 'buttons', ngs.finalStepDialogButtons);
						jQuery(this).remove();
					}
				}
			},
			close: function() {
				jQuery('#shopping_cart_div').dialog('option', 'buttons', ngs.finalStepDialogButtons);
				jQuery(this).remove();
			},
			open: function(event, ui) {
				jQuery(this).parent().attr('phrase_id', 316);
				jQuery(this).parent().addClass('translatable_search_content');
			}
		});

		if ($('co_input_number_form')) {
			$('co_input_number_form').onsubmit = this.onSendSMS.bind(this);
			$('co_cell_phone_number').focus();
		}
		if ($('co_input_code_form')) {
			$('co_input_code_form').onsubmit = this.onConfirm.bind(this);
			$('co_code').focus();
		}

		if ($('order_confirmed_element'))
		{
			jQuery("#confirm_cell_phone_number_div").remove();
			ngs.action('confirm_order_action', this.params);
		}
	},
	onSendSMS: function(e, thisClassInstance) {
		if (typeof thisClassInstance === 'undefined')
		{
			thisClassInstance = this;
		}
		var params = $H(thisClassInstance.params).merge({'send_to_cell_phone': $('co_cell_phone_number').value});
		ngs.load("confirm_cell_phone_number", params.toObject());
		jQuery("#confirm_cell_phone_number_div").remove();
		return false;
	},
	onConfirm: function(e, thisClassInstance) {
		if (typeof thisClassInstance === 'undefined')
		{
			thisClassInstance = this;
		}
		var sms_data = {'co_code': $('co_code').value};
		var params = $H(thisClassInstance.params).merge(sms_data);
		$('global_modal_loading_div').style.display = 'block';
		ngs.load("confirm_cell_phone_number", params.toObject());
		jQuery("#confirm_cell_phone_number_div").remove();
		return false;
	}
});
