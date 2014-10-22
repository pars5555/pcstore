ngs.ShoppingCartLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_checkout", ajaxLoader);
	},
	getUrl: function() {
		return "shopping_cart";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "shopping_cart_container";
	},
	getName: function() {
		return "shopping_cart";
	},
     beforeLoad: function() {
		$('global_modal_loading_div').style.display = 'block';
	},
	afterLoad: function() {
		$('global_modal_loading_div').style.display = 'none';
		jQuery("#shopping_cart_div").dialog({
			modal: true,
			width: 1120,
			height: 600,
			zIndex: 10000,
			close: function() {
				jQuery(this).remove();
			},
			open: function() {
				jQuery(this).parent().addClass('translatable_search_content');
			}
		});


		this.checkoutStep = 1;
		ngs.nestLoad("cart_step_inner", {});
	}
});
