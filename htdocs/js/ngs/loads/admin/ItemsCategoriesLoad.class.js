ngs.ItemsCategoriesLoad = Class.create(ngs.AbstractLoad, {
    treeViewUtility: null,
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "items_categories";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "hp_" + this.getName() + "_tab";
    },
    getName: function() {
        return "items_categories";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {
        $('global_modal_loading_div').style.display = 'none';
        if ($('selected_sub_categories_ids')) {
            $('selected_sub_categories_ids').value = '';
        }
        this.treeViewUtility = new ngs.TreeViewUtility('my_tree');
        this.treeViewUtility.addNodeSelectListener(this);
        $('edit_category_attributes_div').style.display = 'none';
        $('add_category_button').onclick = this.onAddCategoryButtonClicked.bind(this);
        $('remove_category_button').onclick = this.onRemoveCategoryButtonClicked.bind(this);
        $('save_category_attributes_button').onclick = this.onSaveCategoryAttributesButtonClicked.bind(this);
        $('category_attributes_form').onsubmit = this.onSaveCategoryAttributesButtonClicked.bind(this);
        $('move_up_category_button').onclick = this.onMoveUpCategoryButtonClicked.bind(this);
        $('move_down_category_button').onclick = this.onMoveDownCategoryButtonClicked.bind(this);
    },
    onNodeSelect: function(nodeKey) {
        var d = this.treeViewUtility.getNodeData(nodeKey);
        if (d) {
            $('edit_category_attributes_div').style.display = 'block';
            $('category_attributes_form').title.value = d.display_name;
            $('category_attributes_form').last_clickable.checked = d.last_clickable == '1' ? true : false;
            $('selected_category_id').innerHTML = d.id;

        } else
            $('edit_category_attributes_div').style.display = 'none';
    },
    onMoveUpCategoryButtonClicked: function() {
        var selectedNodeKey = this.treeViewUtility.getSelectedNodeKey();
        if (!selectedNodeKey) {
            ngs.DialogsManager.closeDialog(483, "<div>" + 'Please select a category.' + "</div>");
            return;
        }
        ngs.action("change_category_order_action", {
            "move_up": 1,
            "category_id": selectedNodeKey
        });
    },
    onMoveDownCategoryButtonClicked: function() {
        var selectedNodeKey = this.treeViewUtility.getSelectedNodeKey();
        if (!selectedNodeKey) {
            ngs.DialogsManager.closeDialog(483, "<div>" + 'Please select a category.' + "</div>");
            return;
        }
        ngs.action("change_category_order_action", {
            "move_up": 0,
            "category_id": selectedNodeKey
        });
    },
    onSaveCategoryAttributesButtonClicked: function() {
        var selectedNodeKey = this.treeViewUtility.getSelectedNodeKey();
        if (selectedNodeKey != null) {
            var display_name = $('category_attributes_form').title.value;
            if (display_name && this.trim(display_name).length < 2) {
                ngs.DialogsManager.closeDialog(483, "<div>" + 'Category title should have at least 2 charecters!' + "</div>");
                return false;
            }

            var last_clickable = $('category_attributes_form').last_clickable.checked ? '1' : '0';
            ngs.action("change_category_attributes_action", {
                "category_id": selectedNodeKey,
                "display_name": display_name,
                "last_clickable": last_clickable
            });
        } else {
            ngs.DialogsManager.closeDialog(483, "<div>" + 'Please select a category.' + "</div>");
        }
        return false;

    },
    onRemoveCategoryButtonClicked: function() {
        var selectedNodeKey = this.treeViewUtility.getSelectedNodeKey();
        if (selectedNodeKey != null) {
            if (this.treeViewUtility.getNodeData(selectedNodeKey) != null) {
                ngs.action("remove_category_action", {
                    "category_id": selectedNodeKey

                });
            } else {
                ngs.DialogsManager.closeDialog(483, "<div>" + 'You can not remove the ROOT' + "</div>");
            }
        } else {
            ngs.DialogsManager.closeDialog(483, "<div>" + 'Please select a category.' + "</div>");
        }
    },
    onAddCategoryButtonClicked: function() {
        var selectedNodeKey = this.treeViewUtility.getSelectedNodeKey();
        if (selectedNodeKey != null) {
            if (this.treeViewUtility.getNodeData(selectedNodeKey) != null) {

                var selectedCategoryId = this.treeViewUtility.getNodeData(selectedNodeKey).id;
            } else {
                var selectedCategoryId = '0';
            }
            var category_title = prompt("Please enter category title", "");
            if (category_title != null && this.trim(category_title).length > 0) {
                ngs.action("add_category_action", {
                    "category_title": category_title,
                    "parent_category_id": selectedCategoryId
                });
            }

        } else {
            ngs.DialogsManager.closeDialog(483, "<div>" + 'Please select parent category.' + "</div>");
        }
    },
    onLoadDestroy: function()
    {
        jQuery("#" + this.getContainer()).html("");
    }
});
