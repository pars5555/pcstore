ngs.LoginDialogLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "login_dialog";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "global_empty_container";
	},
	getName: function() {
		return "login_dialog";
	},
	afterLoad: function() {
		jQuery('#ldlg_login_form').submit(function() {
			jQuery("#ld_login_button").attr('disabled', true);
			jQuery("#ldlg_login_form :input").attr("disabled", true);
			ngs.action("login_action", {
				"user_email": jQuery('#ld_email_input').val(),
				"user_pass": jQuery('#ld_password_input').val()
			});
			return false;
		});

		$('login_dialog_forgot_password').onclick = this.onLoginForgotPasswordClicked.bind(this);

		jQuery("#register_new_user_link").click(function() {
			ngs.load('user_registration', {});
		});

		jQuery('#ld_login_dialog').dialog({
			resizable: false,
			height: 250,
			width: 400,
			modal: true,
			title: ngs.LanguageManager.getPhrase(1),
			buttons: {
				"Login": {
					text: ngs.LanguageManager.getPhrase(1),
					id: 'ld_login_button',
					'class': "dialog_default_button_class translatable_search_content",
					phrase_id: 1,
					click: function() {
						jQuery("#ld_login_button").attr('disabled', true);
						jQuery("#ldlg_login_form :input").attr("disabled", true);
						ngs.action("login_action", {
							"user_email": jQuery('#ld_email_input').val(),
							"user_pass": jQuery('#ld_password_input').val()
						});

					}
				},
				"Cancel": {
					text: ngs.LanguageManager.getPhrase(49),
					'class': "translatable_search_content",
					phrase_id: 49,
					click: function() {
						jQuery(this).remove();
					}
				}


			},
			close: function() {
				jQuery(this).remove();
			},
			open: function() {
				jQuery(this).dialog().parent().attr('phrase_id', 1);
				jQuery(this).dialog().parent().addClass('translatable_search_content');
			}
		});


	},
	onLoginForgotPasswordClicked: function() {
		ngs.LoginLoad.prototype.onLoginForgotPasswordClicked();
	}
});
