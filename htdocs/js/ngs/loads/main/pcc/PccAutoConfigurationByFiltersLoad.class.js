ngs.PccAutoConfigurationByFiltersLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_pcc", ajaxLoader);
	},
	getUrl: function() {
		return "pcc_auto_configuration_by_filters";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "pcc_autoconfiguration_container";
	},
	getName: function() {
		return "pcc_auto_configuration_by_filters";
	},
	afterLoad: function() {
		if ($('suggest_pc_button')) {
			$('suggest_pc_button').onclick = this.onSuggestButtonClicked.bind(this);
		}
		$('pcc_auto_conf_form').onsubmit = this.onSuggestButtonClicked.bind(this);

	},
	onSuggestButtonClicked: function()
	{
		$('global_modal_loading_div').style.display = 'block';
		ngs.action('suggest_pc_action', {'total_price': $('total_price').value, 'gaming_pc': $('is_gaming').checked ? 1 : 0, 'only_case': $('only_case').checked ? 1 : 0});
		return false;
	}
});
