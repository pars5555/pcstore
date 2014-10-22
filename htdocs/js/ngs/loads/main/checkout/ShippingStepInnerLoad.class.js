ngs.ShippingStepInnerLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_checkout", ajaxLoader);
	},
	getUrl: function() {
		return "shipping_step_inner";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "checkout_steps_container";
	},
	getName: function() {
		return "shipping_step_inner";
	},
	afterLoad: function() {
		var thisInstance = this;
		jQuery('#shopping_cart_div').dialog('option', 'title', ngs.LanguageManager.getPhrase(445));
		jQuery('#shopping_cart_div').dialog().parent().attr('phrase_id', 445);
		var btns = {
			"Prev": {
				text: ngs.LanguageManager.getPhrase(283),
				'class': "translatable_search_content",
				phrase_id: 283,
				click: function() {
					var shipping_data = thisInstance.getShippingDataObject();
					var params = $H(thisInstance.params).merge(shipping_data);
					jQuery('#shopping_cart_div').dialog('option', 'buttons', {});
					ngs.load("cart_step_inner", params.toObject());
				}
			},
			"Next": {
				text: ngs.LanguageManager.getPhrase(282),
				'class': "dialog_default_button_class translatable_search_content",
				phrase_id: 282,
				id: 'dialogNextButtonId',
				click: function() {
					var shipping_data = thisInstance.getShippingDataObject();
					var params = $H(thisInstance.params).merge(shipping_data);
					jQuery('#shopping_cart_div').dialog('option', 'buttons', {});
					ngs.load("payment_step_inner", params.toObject());
				}}

		};
		jQuery('#shopping_cart_div').dialog('option', 'buttons', btns);


		if ($('cho_do_shipping')) {
			$('cho_do_shipping').onchange = this.onDoShippingCheckboxChanged.bind(this);
		}
		this.onDoShippingCheckboxChanged();

		if ($('billing_is_different_checkbox')) {
			$('billing_is_different_checkbox').onchange = this.onBillingIsDifferentCheckboxChanged.bind(this);
		}
		this.onBillingIsDifferentCheckboxChanged();
	},
	onDoShippingCheckboxChanged: function() {
		var do_shipping = $('cho_do_shipping').checked;
		$('cho_shipping_billing_details_container').style.display = do_shipping ? 'block' : 'none';
	},
	onBillingIsDifferentCheckboxChanged: function() {
		var billing_is_different = $('billing_is_different_checkbox').checked;
		$('billing_address_details_container').style.display = billing_is_different ? 'block' : 'none';

		$('cho_valid_cellphone_massage1').style.display = billing_is_different ? 'none' : 'block';
		$('cho_valid_cellphone_massage2').style.display = billing_is_different ? 'block' : 'none';
	},
	getShippingDataObject: function()
	{
		var shipping_data = $("shipping_details_form").serialize(true);
		if (!$("cho_do_shipping").checked)
		{
			shipping_data.cho_do_shipping = '0';
		}
		if (!$("billing_is_different_checkbox").checked)
		{
			shipping_data.billing_is_different_checkbox = '0';
		}
		return shipping_data;
	}
});
