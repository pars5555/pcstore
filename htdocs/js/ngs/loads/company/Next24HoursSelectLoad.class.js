ngs.Next24HoursSelectLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company", ajaxLoader);
	},
	getUrl: function() {
		return "next_24_hours_select";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "next_24_hours_container";
	},
	getName: function() {
		return "next_24_hours_select";
	},
	afterLoad: function() {

	}
});
