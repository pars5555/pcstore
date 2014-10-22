ngs.SearchStatisticsViewLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin_sysconfig", ajaxLoader);
	},
	getUrl: function() {
		return "search_statistics_view";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "f_admin_config_search_statistics_container";
	},
	getName: function() {
		return "search_statistics_view";
	},
	afterLoad: function() {
		jQuery("#ss_days_number").change(function() {
			var daysNumber = jQuery(this).val();
			ngs.load("search_statistics_view", {"days_number": daysNumber});
		});
	}
});