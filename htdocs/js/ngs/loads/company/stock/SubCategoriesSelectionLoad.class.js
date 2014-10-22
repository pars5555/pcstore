ngs.SubCategoriesSelectionLoad = Class.create(ngs.AbstractLoad, {
	subCategoryTreeViewUtility: null,
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company_stock", ajaxLoader);
	},
	getUrl: function() {
		return "sub_categories_selection";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "f_sub_category_dialog_container";
	},
	getName: function() {
		return "sub_categories_selection";
	},
	afterLoad: function() {
		var that = this;
		jQuery('#f_sub_category_dialog_container').dialog(
				{
					resizable: false,
					height: 500,
					width: 350,
					modal: true,
					title: ngs.LanguageManager.getPhrase(501),
					buttons: {
						"Save": {
							text: ngs.LanguageManager.getPhrase(43),
							'class': "dialog_default_button_class translatable_search_content",
							phrase_id: 43,
							click: function() {
								jQuery('#' + that.params.result_hidden_element_id).val(that.subCategoryTreeViewUtility.getSelectedCategoriesIds().join(','));
								jQuery('#' + that.params.result_hidden_element_id).trigger('change');
								jQuery(this).remove();
							}
						},
						"Cancel": {
							text: ngs.LanguageManager.getPhrase(49),
							'class': "translatable_search_content",
							phrase_id: 49,
							click: function() {
								jQuery(this).remove();
							}
						}
					},
					close: function() {
						jQuery(this).remove();
					},
					open: function(event, ui) {
						jQuery(this).parent().attr('phrase_id', 501);
						jQuery(this).parent().addClass('translatable_search_content');
					}
				}
		);
		this.subCategoryTreeViewUtility = new ngs.SubCategoryTreeViewUtility('sub_category_tree');
		this.setTreeSelectionToCategoriesIdsHiddenElementValue();
	},
	setTreeSelectionToCategoriesIdsHiddenElementValue: function() {
		var selectedSubCategoriesIdsString = jQuery('#' + this.params.result_hidden_element_id).val();

		var selectedSubCategoriesIdsArray = selectedSubCategoriesIdsString.split(',');
		this.subCategoryTreeViewUtility.setSelectedNodes(selectedSubCategoriesIdsArray);
	}
});

