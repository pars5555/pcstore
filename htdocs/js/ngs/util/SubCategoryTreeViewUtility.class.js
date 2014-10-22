ngs.SubCategoryTreeViewUtility = Class.create(ngs.TreeViewUtility, {
	category_select_listeners : null,

	initialize : function($super, treeViewId) {
		$super(treeViewId);
		this.category_select_listeners = new Array();
		var checkboxes = $$('#' + treeViewId + ' .checkboxes');
		this.addClickHandlerToCheckBoxes(checkboxes);
	},
	addClickHandlerToCheckBoxes : function(checkboxes) {
		if (checkboxes && checkboxes.length > 0) {
			for (var i = 0; i < checkboxes.length; i++) {
				var nodeKey = checkboxes[i].name.substr(checkboxes[i].name.indexOf("^") + 1);
				checkboxes[i].onclick = this.onCategorySelect.bind(this, nodeKey);
			}
		}
	},
	onCategorySelect : function(categoryId, e) {				
		if (!e) var e = window.event;
    e.cancelBubble = true;
    if (e.stopPropagation) e.stopPropagation(); 
		for (var l in this.category_select_listeners) {
			if (this.category_select_listeners[l].onCategorySelected) {
				this.category_select_listeners[l].onCategorySelected(categoryId);
			}
		}
		this.onNodeClicked(categoryId);
		//var ch = document.getElementsByName('category^'+categoryId)[0].checked;
		//document.getElementsByName('category^'+categoryId)[0].checked = !ch;

	},
	addCategorySelectListener : function(listener) {
		if (this.category_select_listeners.indexOf(listener) == -1) {
			this.category_select_listeners.push(listener);
		}
	},
	getSelectedCategoriesIds : function() {
		var selected_categories_ids = new Array();
		var checkboxes = $$('#' + this.treeViewId + ' .checkboxes');
		if (checkboxes && checkboxes.length > 0) {
			for (var i = 0; i < checkboxes.length; i++) {
				var nodeKey = checkboxes[i].name.substr(checkboxes[i].name.indexOf("^") + 1);
				if (checkboxes[i].checked) {
					selected_categories_ids.push(nodeKey);
				}
			}
		}
		return selected_categories_ids;
	},
	getSelectedCategoriesTitles : function() {
		var selected_categories_ids = new Array();
		var checkboxes = $$('#' + this.treeViewId + ' .checkboxes');
		for (var i = 0; i < checkboxes.length; i++) {
			var nodeKey = checkboxes[i].name.substr(checkboxes[i].name.indexOf("^") + 1);
			if (checkboxes[i].checked) {
				selected_categories_ids.push(this.getNodeData(nodeKey).display_name);
			}
		}
		return selected_categories_ids;
	},
	onNodeClicked : function(nodeKey) {		
		this.fireNodeSelectedEvent(nodeKey);
		var checkbox = document.getElementsByName('category^'+nodeKey)[0];		
		if (checkbox.checked) {
			this.setCheckNodeParents(nodeKey);
		} else {
			this.setUnckeckedNodeChildren(nodeKey);
		}
		
	},
	setCheckNodeParents : function(nodeKey) {
		var parentKey = this.getNodeParentKey(nodeKey);
		while (parentKey) {
			var checkbox = document.getElementsByName('category^'+parentKey)[0];
			if (checkbox) {
				checkbox.checked = true;
				parentKey = this.getNodeParentKey(parentKey);
			} else {
				break;
			}
		}
	},
	setUnckeckedNodeChildren : function(nodeKey) {
		var childrenKeysArray = this.getNodeChildrenKeys(nodeKey);
		if (childrenKeysArray) {
			for (var i = 0; i < childrenKeysArray.length; i++) {
				var checkbox = document.getElementsByName('category^'+childrenKeysArray[i])[0];
				if (checkbox) {
					checkbox.checked = false;
					this.setUnckeckedNodeChildren(childrenKeysArray[i]);
				}
			}

		}
	},
	setSelectedNodes : function(selectedNodesKeys) {
		var checkboxes = $$('#' + this.treeViewId + ' .checkboxes');
		if (checkboxes && checkboxes.length > 0) {
			for (var i = 0; i < checkboxes.length; i++) {
				checkboxes[i].checked = false;

			}
		}
		if (selectedNodesKeys && selectedNodesKeys.length > 0) {
			for (var i = 0; i < selectedNodesKeys.length; i++) {
				var checkbox = document.getElementsByName('category^'+selectedNodesKeys[i])[0];
				if (checkbox) {
					checkbox.checked = true;
				}
			}
		}
	}
});
