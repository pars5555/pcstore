ngs.UserManagementLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "user_management";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "hp_" + this.getName() + "_tab";
	},
	getName: function() {
		return "user_management";
	},
	beforeLoad: function() {
		$('global_modal_loading_div').style.display = 'block';
	},
	afterLoad: function() {
		$('global_modal_loading_div').style.display = 'none';
		
		ngs.nestLoad('sub_users_list', {});
		ngs.nestLoad('pending_users_list', {});
		
		jQuery("#um_tabs a").click(function() {
			var url_param = jQuery(this).attr('url_param');
			ngs.UrlChangeEventObserver.setFakeURL("/subusers/"+url_param);
			
		});
},
	onLoadDestroy: function()
	{
		jQuery("#" + this.getContainer()).html("");
	}
});
