ngs.ItemWarrantyLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company", ajaxLoader);
	},
	getUrl: function() {
		return "item_warranty";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "hp_" + this.getName() + "_tab";
	},
	getName: function() {
		return "item_warranty";
	},
	beforeLoad: function() {
		$('global_modal_loading_div').style.display = 'block';
	},
	afterLoad: function() {
		ngs.UrlChangeEventObserver.setFakeURL("/warranty");
		$('global_modal_loading_div').style.display = 'none';
		$('add_item_warranty_form').onsubmit = this.onSubmit.bind(this);
		if ($('add_item_warranty_button')) {
			$('add_item_warranty_button').onclick = this.onAddItemWarranty.bind(this);
		}
		if ($('save_item_warranty_button')) {
			$('save_item_warranty_button').onclick = this.onSaveItemWarranty.bind(this);
		}
		if ($('cancel_item_warranty_button')) {
			$('cancel_item_warranty_button').onclick = this.onCancelItemWarranty.bind(this);
		}
		$('search_item_warranty_form').onsubmit = this.onSearchItemWarrantyFormSubmit.bind(this);
		var edit_item_warranty_links = $$("#iw_items_container .edit_item_warranty_link");
		this.addItemWarrantyEditClickHandler(edit_item_warranty_links);
		var delete_item_warranty_links = $$("#iw_items_container .delete_item_warranty_link");
		this.addItemWarrantyDeleteClickHandler(delete_item_warranty_links);

	},
	onCancelItemWarranty: function() {
		ngs.load("item_warranty", {});
	},
	onSubmit: function() {
		return false;
	},
	addItemWarrantyEditClickHandler: function(elements_array) {
		for (var i = 0; i < elements_array.length; i++) {
			var iw_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
			elements_array[i].onclick = this.onItemWarrantyEditClicked.bind(this, iw_id);
		}
	},
	onItemWarrantyEditClicked: function(iw_id) {
		ngs.load("item_warranty", {
			"warranty_item_id": iw_id
		});
	},
	addItemWarrantyDeleteClickHandler: function(elements_array) {
		for (var i = 0; i < elements_array.length; i++) {
			var iw_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
			elements_array[i].onclick = this.onItemWarrantyDeleteClicked.bind(this, iw_id);
		}
	},
	onItemWarrantyDeleteClicked: function(iw_id) {
		var answer = confirm("Are you sure you want to delete the item warranty?");
		if (answer) {
			ngs.action("add_change_item_warranty_action", {
				"action_type": "delete",
				"warranty_item_id": iw_id
			});
		}
	},
	onSearchItemWarrantyFormSubmit: function() {

		var searchSerialNumber = $('search_serial_number').value;
		ngs.load("item_warranty", {
			"search_serial_number": searchSerialNumber
		});
		return false;
	},
	onAddItemWarranty: function() {
		var valForm = this.validateForm();
		if (valForm === 'ok') {
			var serialized_form = $('add_item_warranty_form').serialize(true);
			ngs.action("add_change_item_warranty_action", {
				"action_type": "add",
				"serial_number": serialized_form.serial_number,
				"customer_warranty_start_date": (serialized_form.SellDateYear + '-' + serialized_form.SellDateMonth + '-' + serialized_form.SellDateDay),
				"item_buyer": serialized_form.item_buyer,
				"item_category": serialized_form.item_category,
				"warranty_period": serialized_form.warranty_period,
				"supplier": serialized_form.item_supplier,
				"supplier_warranty_start_date": (serialized_form.SupplierSellDateYear + '-' + serialized_form.SupplierSellDateMonth + '-' + serialized_form.SupplierSellDateDay),
				"supplier_warranty_period": serialized_form.supplier_warranty_period
			});
		} else {
                         ngs.DialogsManager.closeDialog(483, "<div>" + valForm+ "</div>");
		}
		return false;
	},
	onSaveItemWarranty: function() {
		var valForm = this.validateForm();
		if (valForm === 'ok') {
			var serialized_form = $('add_item_warranty_form').serialize(true);
			ngs.action("add_change_item_warranty_action", {
				"action_type": "edit",
				"warranty_item_id": $('warranty_item_id').value,
				"serial_number": serialized_form.serial_number,
				"customer_warranty_start_date": (serialized_form.SellDateYear + '-' + serialized_form.SellDateMonth + '-' + serialized_form.SellDateDay),
				"item_buyer": serialized_form.item_buyer,
				"item_category": serialized_form.item_category,
				"warranty_period": serialized_form.warranty_period,
				"supplier": serialized_form.item_supplier,
				"supplier_warranty_start_date": (serialized_form.SupplierSellDateYear + '-' + serialized_form.SupplierSellDateMonth + '-' + serialized_form.SupplierSellDateDay),
				"supplier_warranty_period": serialized_form.supplier_warranty_period
			});
		} else {
			 ngs.DialogsManager.closeDialog(483, "<div>" + valForm+ "</div>");
		}
		return false;
	},
	validateForm: function() {
		var serial_number = $("serial_number");
		if (!serial_number.value || this.trim(serial_number.value).length < 6) {
			serial_number.focus();
			return "Serial Number must have at least 6 charecter!";
		}
		return 'ok';
	},
	onLoadDestroy: function()
	{
		jQuery("#" + this.getContainer()).html("");
	}
});
