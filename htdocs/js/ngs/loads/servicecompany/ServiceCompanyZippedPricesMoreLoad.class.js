ngs.ServiceCompanyZippedPricesMoreLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "servicecompany", ajaxLoader);
	},
	getUrl: function() {
		return "service_company_zipped_prices_more";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "tmp_zip_prices_container";
	},
	getName: function() {
		return "service_company_zipped_prices_more";
	},
	afterLoad: function() {
		var serviceCompanyId = this.params.service_company_id;
		var html = jQuery('#tmp_zip_prices_container').html();
		jQuery('#price_files_content_' + serviceCompanyId).append(html);
		jQuery('#tmp_zip_prices_container').remove();

	}
});
