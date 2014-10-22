ngs.CategoryPropertyViewsUtility = {
	category_click_listeners : null,

	initialize : function(selectedPropertiesIdsArray) {
		this.category_click_listeners = new Array();
		var categoryPropertyCheckBoxes = $$('#search_body_categories_container .category_property');
		this.setSelectedProperties(categoryPropertyCheckBoxes, selectedPropertiesIdsArray);
		this.addClickHandlerToCategoryPropertyCheckboxes(categoryPropertyCheckBoxes);
	},
	setSelectedProperties : function(categoryPropertyCheckBoxes, selectedPropertiesIdsArray) {
		if (selectedPropertiesIdsArray && selectedPropertiesIdsArray.length > 0) {

			for (var i = 0; i < categoryPropertyCheckBoxes.length; i++) {
				var categoryId = categoryPropertyCheckBoxes[i].id.substr(categoryPropertyCheckBoxes[i].id.indexOf("^") + 1);				
				if (selectedPropertiesIdsArray.indexOf(categoryId) != -1) {
					categoryPropertyCheckBoxes[i].checked = true;
				}
			}
		}
	},
	addClickHandlerToCategoryPropertyCheckboxes : function(elements) {
		if (elements && elements.length > 0) {
			for (var i = 0; i < elements.length; i++) {
				var categoryId = elements[i].id.substr(elements[i].id.indexOf("^") + 1);
				elements[i].onclick = this.onCategoryPropertyClicked.bind(this, categoryId);
			}
		}
	},
	onCategoryPropertyClicked : function(categoryId) {
		for (var l in this.category_click_listeners) {
			if (this.category_click_listeners[l].onCategoryPropertyClicked) {
				this.category_click_listeners[l].onCategoryPropertyClicked(categoryId);
			}
		}
	},
	addCategoryClickListener : function(listener) {
		if (this.category_click_listeners.indexOf(listener) == -1) {
			this.category_click_listeners.push(listener);
		}
	},
	getSelectedCategoriesIds : function() {
		var categories_ids_array = new Array();
		var categoryPropertyCheckBoxes = $$('#search_body_categories_container .category_property');
		for (var i = 0; i < categoryPropertyCheckBoxes.length; i++) {
			var categoryId = categoryPropertyCheckBoxes[i].id.substr(categoryPropertyCheckBoxes[i].id.indexOf("^") + 1);
			if (categoryPropertyCheckBoxes[i].checked) {
				categories_ids_array.push(categoryId);
			}
		}
		return categories_ids_array;
	}
};

