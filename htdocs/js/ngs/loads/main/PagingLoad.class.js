ngs.PagingLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "paging";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "content_footer_div";
	},
	getName: function() {
		return "paging";
	},
	afterLoad: function() {
	}

});
