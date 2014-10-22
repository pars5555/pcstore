ngs.ServiceCompanyProfileLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "servicecompany", ajaxLoader);
	},
	getUrl: function() {
		return "service_company_profile";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "hp_" + this.getName() + "_tab";
	},
	getName: function() {
		return "service_company_profile";
	},
	beforeLoad: function() {
		$('global_modal_loading_div').style.display = 'block';
	},
	afterLoad: function() {

		ngs.UrlChangeEventObserver.setFakeURL("/scprofile");
		$('global_modal_loading_div').style.display = 'none';
		
		if (jQuery('#scp_password_form').length > 0) {
			jQuery('#scp_password_form').submit(function() {
				ngs.load('service_company_profile', {'password': jQuery('#scp_password_check').val()});
				return false;
			});
			return;
		}
		
		if ($('password_change_checkbox')) {
			//$('password_change_checkbox').onchange = this.onPasswordChangeCheckBoxValueChanged.bind(this);
			$('password_change_checkbox').onclick = this.onPasswordChangeCheckBoxValueChanged.bind(this);
		}
		if ($('cp_save_button')) {
			$('cp_save_button').onclick = this.onSaveButtonClicked.bind(this);
		}
		if ($('cp_reset_button')) {
			$('cp_reset_button').onclick = this.onResetButtonClicked.bind(this);
		}
		this.setPasswordChangeDivVisibility();

		this.setWorkingDaysForTheFirstTime();

		$('select_logo_button').onclick = this.onSelectLogoButtonClicked.bind(this);
		$('cp_form').onsubmit = this.onCPFormSubmit.bind(this);




		var thisInstance = this;
		jQuery('#cp_branch_select').change(function() {
			var selected_branch_id = jQuery(this).val();
			ngs.load('service_company_profile', {'selected_branch_id': selected_branch_id, "refresh":1});

		});
		jQuery('#cp_remove_selected_company_branch').click(function() {
			var selected_branch_id = jQuery('#cp_branch_select').val();
			ngs.action('add_remove_service_company_branch_action', {"action": "delete", 'branch_id': selected_branch_id});

		});

		jQuery('#cp_add_company_branch').click(function() {
			var regionsPIdsArray = CMS_VARS['armenia_regions_phrase_ids'].split(',');
			var regionsSelectHtml = '<select id="cp_branch_region_select">';
			jQuery(regionsPIdsArray).each(function(index, pId) {
				regionsSelectHtml += '<option value="' + ngs.LanguageManager.getPhrase(pId, 'en') + '" class="translatable_element" phrase_id="' + pId + '">' + ngs.LanguageManager.getPhrase(pId) + '</option>';
			});
			regionsSelectHtml += '</select>';

			var dialogContentHtml = '<div id="cp_create_new_branch_dialog"><form id="cp_create_new_branch_form">';
			dialogContentHtml += '<div style="padding:10px"><div style="padding:5px">' + ngs.LanguageManager.getPhraseSpan(13) + ': </div><input type="text" id="cp_branch_address_input"/></div>';
			dialogContentHtml += '<div style="padding:10px"><div style="padding:5px">' + ngs.LanguageManager.getPhraseSpan(45) + ': </div>' + regionsSelectHtml + '</div>';
			dialogContentHtml += '<div style="padding:10px"><div style="padding:5px">' + ngs.LanguageManager.getPhraseSpan(604) + ': </div>' + '<input type="text" id="cp_branch_zip_input"/></div>';
			dialogContentHtml += '</form></div>';

			jQuery(dialogContentHtml).dialog({
				title: ngs.LanguageManager.getPhrase(580),
				model: true,
				buttons: {
					"Add": {
						text: ngs.LanguageManager.getPhrase(563),
						'class': "dialog_default_button_class translatable_search_content",
						phrase_id: 563,
						id: 'cp_add_branch_button',
						click: function() {
							thisInstance.createNewBranch();
						}
					},
					"Cancel": {
						text: ngs.LanguageManager.getPhrase(49),
						'class': "translatable_search_content",
						phrase_id: 49,
						id: 'cp_cancel_add_branch_button',
						click: function() {
							jQuery(this).remove();

						}
					}
				},
				close: function() {
					jQuery(this).remove();
				},
				open: function(event, ui) {
					jQuery(this).parent().attr('phrase_id', 580);
					jQuery(this).parent().addClass('translatable_search_content');
					jQuery('#cp_create_new_branch_form').submit(function() {
						thisInstance.createNewBranch();
						return false;
					});
				}
			});
		});
	},
	createNewBranch: function()
	{
		var branch_address = jQuery("#cp_branch_address_input").val();
		if (branch_address.length > 0) {
			var branch_region = jQuery("#cp_branch_region_select").val();
			var branch_zip = jQuery("#cp_branch_zip_input").val();
			jQuery("#cp_add_branch_button").attr("disabled", true);
			jQuery("#cp_cancel_add_branch_button").attr("disabled", true);
			jQuery('#cp_create_new_branch_dialog').parent().block({message: 'processing...'});
			ngs.action("add_remove_service_company_branch_action", {"action": "add", "branch_region": branch_region, "branch_address": branch_address, "branch_zip": branch_zip});
		}
		else
		{
                         ngs.DialogsManager.closeDialog(483, "<div>" + 'Please input address'+ "</div>");
		}
	},
	onSelectLogoButtonClicked: function() {
		var inpFile = $("cp_file_input");
		inpFile.click();
		inpFile.onchange = function() {
			$('cp_selected_logo_name').value = this.value;
			$('cp_selected_logo_name').style.border = '1px soiid';

		};
		return false;
	},
	setWorkingDaysForTheFirstTime: function() {
		var workingDays = $('working_days').value;
		$('monday_checkbox').checked = workingDays[0] == 1 ? true : false;
		$('tuseday_checkbox').checked = workingDays[1] == 1 ? true : false;
		$('wednesday_checkbox').checked = workingDays[2] == 1 ? true : false;
		$('thursday_checkbox').checked = workingDays[3] == 1 ? true : false;
		$('friday_checkbox').checked = workingDays[4] == 1 ? true : false;
		$('saturday_checkbox').checked = workingDays[5] == 1 ? true : false;
		$('sunday_checkbox').checked = workingDays[6] == 1 ? true : false;

	},
	onCPFormSubmit: function() {
		var serialized_form = $('cp_form').serialize(true);
		var validateFormMessage = this.validateForm(serialized_form);
		if (validateFormMessage !== 'ok') {
			$('cp_error_p').style.display = 'block';
			$('cp_error_message').innerHTML = 'Error: ' + validateFormMessage + '!';
			return false;
		} else {
			var working_days = "";
			working_days += serialized_form.monday_checkbox ? '1' : '0';
			working_days += serialized_form.tuseday_checkbox ? '1' : '0';
			working_days += serialized_form.wednesday_checkbox ? '1' : '0';
			working_days += serialized_form.thursday_checkbox ? '1' : '0';
			working_days += serialized_form.friday_checkbox ? '1' : '0';
			working_days += serialized_form.saturday_checkbox ? '1' : '0';
			working_days += serialized_form.sunday_checkbox ? '1' : '0';
			$('working_days').value = working_days;
			return true;
		}
		$('global_modal_loading_div').style.display = 'block';
	},
	onPasswordChangeCheckBoxValueChanged: function() {
		this.setPasswordChangeDivVisibility();

	},
	setPasswordChangeDivVisibility: function() {
		if ($('password_change_checkbox').checked)
			$('change_password_div').style.display = 'block';
		else
			$('change_password_div').style.display = 'none';
	},
	validateForm: function(form) {		
		if (form.change_pass) {
			if (form.new_pass == null || form.new_pass.strip().length == 0) {
				return "Please input your new password";
			}
			if (form.repeat_new_pass == null || form.repeat_new_pass.strip().length == 0) {
				return "Please input repeat new password";
			}
			if (form.new_pass.strip() != form.repeat_new_pass.strip()) {
				return "New password and Repeat new password should be the same";
			}
		}
		if (form.phone1 != null) {
			var phone1 = form.phone1.strip();
			if (phone1.length != 0 && phone1.indexOf(',') != -1) {
				return "Phone number can not contain comma charecter";
			}
		}
		if (form.phone2 != null) {
			var phone2 = form.phone2.strip();
			if (phone2.length != 0 && phone2.indexOf(',') != -1) {
				return "Phone number can not contain comma charecter";
			}
		}
		if (form.phone3 != null) {
			var phone3 = form.phone3.strip();
			if (phone3.length != 0 && phone3.indexOf(',') != -1) {
				return "Phone number can not contain comma charecter";
			}
		}

		return 'ok';

	},
	onSaveButtonClicked: function() {

		if (this.onCPFormSubmit()) {
			$('cp_form').submit();
		}
	},
	onResetButtonClicked: function() {
		ngs.load("service_company_profile", {"refresh":1});
	},
	onLoadDestroy: function()
	{
		jQuery("#" + this.getContainer()).html("");
	}
});
