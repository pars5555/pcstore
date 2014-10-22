ngs.PccAfterComponentChangedAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_pcc", ajaxLoader);
	},

	getUrl : function() {
		return "do_pcc_after_component_changed";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		$('global_modal_loading_div').style.display = 'none';
		var data = transport.responseText.evalJSON();
		this.updateComponentSelectedAndRequiredStatus(data.selected_components_ids, data.required_components_ids);
		if (data.required_components_ids.length === 0) {
		} else {
			if ($('configurator_mode_edit_cart_row_id')) {
				this.scroolToRequiredComponent(data.required_components_ids);				
			}
		}
		//data.required_components_keywords
	},
	scroolToRequiredComponent : function(required_components_ids) {

		var firstRequiredComponentIndex = parseInt(required_components_ids[0]) ;
		$('component_icon_a_link_' + firstRequiredComponentIndex).click();
	},

	updateComponentSelectedAndRequiredStatus : function(selected_components_ids, required_components_ids) {		
		var cs = ngs.PcConfiguratorManager.componentsIndex;
		for (var prop in cs) {
			if (cs.hasOwnProperty(prop)) {
				var flag = $('component_icon_flag_div_' + (parseInt(cs[prop]) ));
				flag.className = '';
				if (selected_components_ids.in_array(cs[prop])) {
					flag.className = 'selected_component';
				}

				if (required_components_ids.in_array(cs[prop])) {
					flag.className = 'required_component';

				}
			}
		}

	}
});
