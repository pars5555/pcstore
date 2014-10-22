ngs.ForgotLoginLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "forgot_login";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "global_empty_container1";
	},
	getName: function() {
		return "forgot_login";
	},
	afterLoad: function() {

		jQuery('#fl_forgot_form').submit(function() {
			jQuery("#fl_send_email_button").attr('disabled', true);
			jQuery("#fl_forgot_form :input").attr("disabled", true);
			ngs.action("customer_forgot_password_action", {"email": jQuery('#fl_email_input').val()});
			return false;
		});

		jQuery('#fl_forgot_login_dialog').dialog({
			resizable: false,
			height: 220,
			width: 300,
			modal: true,
			title: ngs.LanguageManager.getPhrase(353),
			buttons: {
				"Send": {
					text: ngs.LanguageManager.getPhrase(354),
					'class': "dialog_default_button_class translatable_search_content",
					phrase_id: 354,
					id: 'fl_send_email_button',
					click: function() {
						jQuery("#fl_send_email_button").attr('disabled', true);
						jQuery("#fl_forgot_form :input").attr("disabled", true);
						ngs.action("customer_forgot_password_action", {"email": jQuery('#fl_email_input').val()});

					}
				},
				"Cancel": {
					text: ngs.LanguageManager.getPhrase(49),
					'class': "translatable_search_content",
					phrase_id: 49,
					id: 'fl_send_email_button',
					click: function() {
						jQuery(this).remove();

					}
				}
			},
			close: function() {
				jQuery(this).remove();
			},
			open: function(event, ui) {
				jQuery(this).parent().attr('phrase_id', 353);
				jQuery(this).parent().addClass('translatable_search_content');
			}
		});
	}
});
