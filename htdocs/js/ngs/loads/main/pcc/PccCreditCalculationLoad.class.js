ngs.PccCreditCalculationLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_pcc", ajaxLoader);
	},
	getUrl: function() {
		return "pcc_credit_calculation";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "pcc_credit_calculation_container";
	},
	getName: function() {
		return "pcc_credit_calculation";
	},
	afterLoad: function() {
		$('pcc_credit_supplier_id').onchange = this.creditParamsChanged.bind(this);
		if ($('pcc_selected_credit_months')) {
			$('pcc_selected_credit_months').onchange = this.creditParamsChanged.bind(this);
		}
		if ($('pcc_credit_calculate_button')) {
			$('pcc_credit_calculate_button').onclick = this.creditParamsChanged.bind(this);
		}
	},
	creditParamsChanged: function() {
		var credit_data = $("pcc_credit_calculation_form").serialize(true);
		ngs.load("pcc_credit_calculation", credit_data);
		return false;
	}
});
