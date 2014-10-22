ngs.PcConfiguratorLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);

	},
	getUrl: function() {
		return "pc_configurator";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "hp_" + this.getName() + "_tab";
	},
	getName: function() {
		return "pc_configurator";
	},
	beforeLoad: function() {
		$('global_modal_loading_div').style.display = 'block';
	},
	afterLoad: function() {
		ngs.UrlChangeEventObserver.setFakeURL("/configurator");
		$('global_modal_loading_div').style.display = 'none';
		var componentLinks = $$("#pc_configurator_header .component_icon_a_link_style");
		this.addComponentLinksClickHandler(componentLinks);

		ngs.nestLoad("pcc_select_case", {});
		//ngs.nestLoad("pcc_total_calculations", {});
		ngs.nestLoad("pcc_auto_configuration_by_filters", {});

		ngs.PcConfiguratorManager.reset();
		this.selectComponent(1);


	},
	addComponentLinksClickHandler: function(elements_array) {
		for (var i = 0; i < elements_array.length; i++) {
			var component_id = elements_array[i].id.substr(elements_array[i].id.lastIndexOf("_") + 1);
			elements_array[i].onclick = this.onComponentLinkClick.bind(this, component_id);
		}
	},
	onComponentLinkClick: function(component_id) {
		ngs.PcConfiguratorManager.onComponentOrTabChanged(parseInt(component_id));
		this.selectComponent(component_id);
	},
	selectComponent: function(component_id) {
		for (var i = 1; i < objVarCount(ngs.PcConfiguratorManager.componentsIndex) + 1; i++) {
			if (i == component_id) {
				continue;
			}
			var el_id = "component_icon_div_" + i;
			$(el_id).style.background = '';
		}
		var el_id = "component_icon_div_" + component_id;
		$(el_id).style.background = '#477DAE';
	},
	onLoadDestroy: function()
	{
		jQuery("#" + this.getContainer()).html("");
	}
});
