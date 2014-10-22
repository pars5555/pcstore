ngs.FilterItemsTreeViewUtility = Class.create(ngs.TreeViewUtility, {
	category_click_listeners : null,

	initialize : function($super, treeViewId) {
		$super(treeViewId, false);
		this.category_click_listeners = new Array();
		var categoryLinks = $$('#' + treeViewId + ' .category_links');
		this.addClickHandlerToCategoryLinks(categoryLinks);
	},
	addClickHandlerToCategoryLinks : function(elements) {
		if (elements && elements.length > 0) {
			for (var i = 0; i < elements.length; i++) {
				var categoryId = elements[i].id.substr(elements[i].id.indexOf("^") + 1);
				elements[i].onclick = this.onCategoryClicked.bind(this, categoryId);
			}
		}
	},
	onCategoryClicked : function(categoryId) {
		for (var l in this.category_click_listeners) {
			if (this.category_click_listeners[l].onCategoryClicked) {
				this.category_click_listeners[l].onCategoryClicked(categoryId);
			}
		}
		return false;
	},
	addCategoryClickListener : function(listener) {
		if (this.category_click_listeners.indexOf(listener) == -1) {
			this.category_click_listeners.push(listener);
		}
	},

	getCategoryIdsPath : function(categoryId) {
		var path = new Array();
		while (categoryId > 0) {
			path.splice(0, 0, categoryId);
			categoryId = this.getNodeParentKey(categoryId);
		}
		return path;
	}
});
