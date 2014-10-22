ngs.FinalStepInnerLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_checkout", ajaxLoader);
	},
	getUrl: function() {
		return "final_step_inner";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "checkout_steps_container";
	},
	getName: function() {
		return "final_step_inner";
	},
	afterLoad: function() {
		var thisInstance = this;
		jQuery('#shopping_cart_div').dialog('option', 'title', ngs.LanguageManager.getPhrase(447));
		jQuery('#shopping_cart_div').dialog().parent().attr('phrase_id', 447);
		var nextButtonTitle = jQuery('#cart_next_button_status_and_title').attr('title');
		var nextButtonDisabled = jQuery('#cart_next_button_status_and_title').attr('button_disabled') === 'true' ? true : false;
		var nextButtonTitlePhraseId = jQuery('#cart_next_button_status_and_title').attr('phrase_id');
		var btns = {
			"Prev": {
				text: ngs.LanguageManager.getPhrase(283),
				'class': "translatable_search_content",
				phrase_id: 283,
				click: function() {
					jQuery('#shopping_cart_div').dialog('option', 'buttons', {});
					ngs.load("payment_step_inner", thisInstance.params);
				}
			},
			"Next": {
				text: ngs.LanguageManager.getPhrase(298),
				'class': "dialog_default_button_class translatable_search_content translatable_attribute_element",
				attribute_phrase_id: nextButtonTitlePhraseId,
				attribute_name_to_translate: "title",
				phrase_id: 298,
				id: 'dialogNextButtonId',
				title: nextButtonTitle,
				disabled: nextButtonDisabled,
				click: function() {
					ngs.finalStepDialogButtons = btns;
					jQuery('#shopping_cart_div').dialog('option', 'buttons', {});
					ngs.load("confirm_cell_phone_number", thisInstance.params);
				}}

		};
		jQuery('#shopping_cart_div').dialog('option', 'buttons', btns);

		var bundle_collapse_expande_buttons = $$("#checkout_steps_container .bundle_collapse_expande_buttons");
		this.addBundleCollapseExpandeButtonsClickHandler(bundle_collapse_expande_buttons);

		if ($('cho_apply_user_points')) {
			$('cho_apply_user_points').onchange = this.onApplyUserPoints.bind(this);
		}

		if ($('agree_checkbox')) {
			$('agree_checkbox').onclick = this.onAgreeClicked.bind(this);

		}
		var allParams = this.params;
		if (jQuery('#promo_code_apply_button').length > 0) {

			jQuery('#f_promo_code_form').submit(function() {
				jQuery('#promo_code_apply_button').trigger('click');
				return false;
			});
			jQuery('#promo_code_apply_button').click(function()
			{
				var prev_promo_codes = jQuery.trim(jQuery('#cho_promo_codes').val());
				var promo_code = jQuery('#promo_code_value').val();
				var promo_codes = (prev_promo_codes.length > 0) ? (promo_code+','+prev_promo_codes) : promo_code;
				var data = {
					'cho_promo_codes': promo_codes
				};
				var params = $H(allParams).merge(data);
				ngs.load("final_step_inner", params.toObject());
				return false;
			});
		}
		this.onAgreeClicked();
	},
	onAgreeClicked: function()
	{
		if ($('agree_checkbox').checked) {
			jQuery("#dialogNextButtonId").css({"visibility": "visible"});
		} else
		{
			jQuery("#dialogNextButtonId").css({"visibility": "hidden"});
		}

	},
	onApplyUserPoints: function() {
		var checked = $('cho_apply_user_points').checked;
		var data = {
			'cho_apply_user_points': checked ? 1 : 0
		};
		var params = $H(this.params).merge(data);
		ngs.load("final_step_inner", params.toObject());
	},
	addBundleCollapseExpandeButtonsClickHandler: function(elements_array) {
		for (var i = 0; i < elements_array.length; i++) {
			var cart_bundle_item_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
			elements_array[i].onclick = this.onBundleCollapseExpandeButtonClicked.bind(this, cart_bundle_item_id, elements_array[i]);
		}
	},
	onBundleCollapseExpandeButtonClicked: function(bundle_id, element) {
		if (element.hasClassName('bundle_item_expand_button')) {
			new Effect.BlindDown('cart_bundle_items_container_' + bundle_id);
			element.removeClassName('bundle_item_expand_button');
			element.addClassName('bundle_item_collapse_button');
		} else {
			new Effect.BlindUp('cart_bundle_items_container_' + bundle_id, {
				duration: 0.3
			});
			element.removeClassName('bundle_item_collapse_button');
			element.addClassName('bundle_item_expand_button');
		}

	}
});
