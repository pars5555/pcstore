ngs.AdminStatisticsLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "admin", ajaxLoader);
	},
	getUrl: function() {
		return "admin_statistics";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "hp_" + this.getName() + "_tab";
	},
	getName: function() {
		return "admin_statistics";
	},
	beforeLoad: function() {
		$('global_modal_loading_div').style.display = 'block';
	},
	afterLoad: function() {
		$('global_modal_loading_div').style.display = 'none';
	},
	onLoadDestroy: function()
	{
		jQuery("#" + this.getContainer()).html("");
	}
});

