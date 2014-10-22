ngs.LoginStepInnerLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_checkout", ajaxLoader);
	},
	getUrl: function() {
		return "login_step_inner";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "checkout_steps_container";
	},
	getName: function() {
		return "login_step_inner";
	},
	afterLoad: function() {

		jQuery('#shopping_cart_div').dialog('option', 'title', ngs.LanguageManager.getPhrase(443));
		jQuery('#shopping_cart_div').dialog().parent().attr('phrase_id', 443);
		var thisInstance = this;
		var btns = {
			"Prev": {
				text: ngs.LanguageManager.getPhrase(283),
				'class': "translatable_search_content",
				phrase_id: 283,
				click: function() {
					jQuery('#shopping_cart_div').dialog('option', 'buttons', {});
					ngs.load("cart_step_inner", thisInstance.params);
				}
			},
			"Next": {
				text: ngs.LanguageManager.getPhrase(1),
				id: "dialogNextButtonId",
				'class': "dialog_default_button_class translatable_search_content",
				phrase_id: 1,
				click: function() {
					jQuery('#shopping_cart_div').dialog('option', 'buttons', {});
					ngs.load("shipping_step_inner", thisInstance.params);
				}
			}

		};
		jQuery('#shopping_cart_div').dialog('option', 'buttons', btns);
		jQuery("#dialogNextButtonId").attr("style", "visibility: hidden");

		
			$('checkout_password_check_pass').onchange = this.onPassChanged.bind(this);
			$('checkout_password_check_pass').onkeyup = this.onPassChanged.bind(this);
			$('checkout_password_check_pass').onpaste = this.onPassChanged.bind(this);
			$('checkout_password_check_pass').oncut = this.onPassChanged.bind(this);
			$('checkout_check_password_form').onsubmit = this.onFormSubmit.bind(this);
			if ($('checkout_password_check_pass')) {
				$('checkout_password_check_pass').focus();
			}
		
	},
	onPassChanged: function() {
		ngs.action('check_password_action', {
			'pass': $('checkout_password_check_pass').value,
			'check_login_on_checkout': 'true'
		});
	},
	onFormSubmit: function() {

		if (jQuery('#dialogNextButtonId').css('visibility') === 'visible') {
			jQuery('#dialogNextButtonId').trigger('click');
		}
		return false;
	}
});
