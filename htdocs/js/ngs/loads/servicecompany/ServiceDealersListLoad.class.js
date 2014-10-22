ngs.ServiceDealersListLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "servicecompany", ajaxLoader);
	},
	getUrl: function() {
		return "service_dealers_list";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "hp_" + this.getName() + "_tab";
	},
	getName: function() {
		return "service_dealers_list";
	},
	beforeLoad: function() {
		$('global_modal_loading_div').style.display = 'block';
	},
	afterLoad: function() {
		ngs.UrlChangeEventObserver.setFakeURL("/servicedealers");
		$('global_modal_loading_div').style.display = 'none';
		var removeUserA_tags = $$("#dl_dealers_container .red_a_style");
		this.addClickHandler(removeUserA_tags);
	},
	addClickHandler: function(elements_array) {
		for (var i = 0; i < elements_array.length; i++) {
			var user_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
			elements_array[i].onclick = this.onRemoveDealerFromCompanyClicked.bind(this, user_id);
		}
	},
	onRemoveDealerFromCompanyClicked: function($userId) {
		var answer = confirm("Are you sure you want to remove dealer from you dealers list?");
		if (answer) {
			ngs.action("remove_dealer_from_service_company_action", {
				"user_id": $userId
			});
		} else {
			return false;
		}

	},
	onLoadDestroy: function()
	{
		jQuery("#" + this.getContainer()).html("");
	}
});
