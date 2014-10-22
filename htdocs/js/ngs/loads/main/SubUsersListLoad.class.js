ngs.SubUsersListLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "sub_users_list";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "um_sub_users_tab";
	},
	getName: function() {
		return "sub_users_list";
	},
	beforeLoad: function() {
		$('global_modal_loading_div').style.display = 'block';
	},
	afterLoad: function() {
		$('global_modal_loading_div').style.display = 'none';
	},
	addClickHandler: function(elements_array) {
		for (var i = 0; i < elements_array.length; i++) {
			var user_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
			elements_array[i].onclick = this.onRemoveDealerFromCompanyClicked.bind(this, user_id);
		}
	},
	onRemoveDealerFromCompanyClicked: function($userId) {
		ngs.action("remove_user_from_user_subs_action", {
			"user_id": $userId
		});
	}
});
