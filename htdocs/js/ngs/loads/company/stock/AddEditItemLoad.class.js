ngs.AddEditItemLoad = Class.create(ngs.AbstractLoad, {
	removedPicturesIds: new Array(),
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company_stock", ajaxLoader);
	},
	getUrl: function() {
		return "add_edit_item";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "f_mi_add_edit_container";
	},
	getName: function() {
		return "add_edit_item";
	},
	afterLoad: function() {
        var thisInstance = this;
		jQuery('#mi_edit_item_dialog').dialog({
			resizable: true,
			height: 600,
			width: 1100,
			modal: true,
			title: ngs.LanguageManager.getPhrase(500),
			buttons: {
				"Save": {
					text: ngs.LanguageManager.getPhrase(43),
					'class': "dialog_default_button_class translatable_search_content",
					phrase_id: 43,
					id: 'mi_save_button',
					click: function() {
						jQuery("#mi_save_button").attr('disabled', true);
						$('create_item_form').submit();
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
				jQuery(this).parent().attr('phrase_id', 500);
				jQuery(this).parent().addClass('translatable_search_content');
                thisInstance.initTinyMCE("textarea.item_full_description_textarea");
			}
		});

		$('create_item_form').onsubmit = this.onCreateItemFormSubmit.bind(this);
		$('select_sub_categories_button').onclick = this.onSelectSubCategoriesButtonClicked.bind(this);


		$('select_picture_button').onclick = this.onSelectPictureButtonClicked.bind(this);
		$('add_picture').onchange = this.onAddPictureCheckBoxValueChanged.bind(this);


		$('check_availability').onchange = this.calcItemAvailabilityReadOnly.bind(this);
		if ($('unchange_item_date')) {
			$('unchange_item_date').onchange = this.setItemAvailabilityAndCheckAvailabilityElementsReadOnly.bind(this);
		}

		if ($('unchange_item_date')) {
			$('unchange_item_date').onchange = this.setItemAvailabilityAndCheckAvailabilityElementsReadOnly.bind(this);
			this.setItemAvailabilityAndCheckAvailabilityElementsReadOnly();
		}
		this.calcItemAvailabilityReadOnly();

		this.setAddPictureDivVisibility();

		$('item_removed_pictures_ids').value = '';


		var remove_item_picture_x_divs = $$("#item_pictures_container_div .remove_item_picture_x");
		this.addRemoveItemPictureDivsClickHandlers(remove_item_picture_x_divs);

		this.removedPicturesIds = new Array();
		$('item_removed_pictures_ids').value = '';


		jQuery('#item_price_amd').change(function() {
			jQuery('#item_price').val('0');
		});
		jQuery('#item_vat_price_amd').change(function() {
			jQuery('#item_vat_price').val('0');
		});
		jQuery('#item_price').change(function() {
			jQuery('#item_price_amd').val('0');
		});
		jQuery('#item_vat_price').change(function() {
			jQuery('#item_vat_price_amd').val('0');
		});

	},
	onCreateItemFormSubmit: function() {
        tinymce.activeEditor.save();
        tinymce.destroy();
		return false;
	},
	validateItemFields: function() {
		var form = $('create_item_form').serialize(true);
		// form.item_title
		// form.short_description
		// form.item_price
		// form.item_vat_price
		// form.warranty_period
		// form.item_root_category
		// todo validate fields
		return true;
	},
	onSelectSubCategoriesButtonClicked: function() {
		var selectElement = $('item_root_category');
		var rootCategoryId = selectElement.value;
		jQuery('<div id="f_sub_category_dialog_container"></div>').appendTo("body");
		ngs.load("sub_categories_selection", {
			"item_root_category": rootCategoryId,
			"result_hidden_element_id": "selected_sub_categories_ids"
		});
		return false;
	},
	calcItemAvailabilityReadOnly: function() {
		$('item_availability').disabled = $('check_availability').checked || ($('unchange_item_date') && $('unchange_item_date').checked);
	},
	setItemAvailabilityAndCheckAvailabilityElementsReadOnly: function() {
		$('item_availability').disabled = $('check_availability').checked || $('unchange_item_date').checked;
		$('check_availability').disabled = $('unchange_item_date').checked;
	},
	onSelectPictureButtonClicked: function() {
		jQuery("#mi_file_input").trigger('click');
		return false;
	},
	onAddPictureCheckBoxValueChanged: function() {
		this.setAddPictureDivVisibility();
	},
	setAddPictureDivVisibility: function() {
		if ($('add_picture').checked)
			$('select_picture_button').style.display = 'block';
		else
			$('select_picture_button').style.display = 'none';
	},
	addRemoveItemPictureDivsClickHandlers: function(elements_array) {
		for (var i = 0; i < elements_array.length; i++) {
			var picture_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
			elements_array[i].onclick = this.onRemoveItemPicture.bind(this, picture_id);
		}
	},
	onRemoveItemPicture: function(pictureId) {
		this.removedPicturesIds.push(pictureId);
		var h = $('item_removed_pictures_ids');
		h.value = this.removedPicturesIds.join(',');
		$('item_image_div^' + pictureId).remove();

	}
});
