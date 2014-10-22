ngs.CompanyZippedPricesMoreLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "company_zipped_prices_more";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "tmp_zip_prices_container";
	},
	getName: function() {
		return "company_zipped_prices_more";
	},
	afterLoad: function() {
		var companyId = this.params.company_id;
		var html = jQuery('#tmp_zip_prices_container').html();
		jQuery('#price_files_content_' + companyId).append(html);
		jQuery('#tmp_zip_prices_container').remove();

	}
});
