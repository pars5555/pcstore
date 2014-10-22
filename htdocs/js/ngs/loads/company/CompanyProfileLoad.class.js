ngs.CompanyProfileLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "company", ajaxLoader);
    },
    getUrl: function() {
        return "company_profile";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "hp_" + this.getName() + "_tab";
    },
    getName: function() {
        return "company_profile";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {

        ngs.UrlChangeEventObserver.setFakeURL("/cprofile");
        $('global_modal_loading_div').style.display = 'none';

        if (jQuery('#cp_password_form').length > 0) {
            jQuery('#cp_password_form').submit(function() {
                ngs.load('company_profile', {'password': jQuery('#cp_password_check').val()});
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
        this.setSmsTimeControlVisibility();
        this.setWorkingDaysForTheFirstTime();
        this.setSmsReceivingDaysForTheFirstTime();
        $('select_logo_button').onclick = this.onSelectLogoButtonClicked.bind(this);
        $('cp_form').onsubmit = this.onCPFormSubmit.bind(this);

        this.sms_checkbox = document.getElementsByClassName('f_sms_checkbox');
        this.sms_checkbox = this.sms_checkbox[0];

        this.sms_checkbox.onclick = this.onSmsCheckbox.bind(this);
        //this.sms_checkbox.onchange = this.onSmsCheckbox.bind(this);

        if ($('sms_phone_number')) {
            $('sms_phone_number').onchange = this.onSmsPhoneNumberChanged.bind(this);
            $('sms_phone_number').onkeyup = this.onSmsPhoneNumberChanged.bind(this);
            $('sms_phone_number').onpaste = this.onSmsPhoneNumberChanged.bind(this);
            $('sms_phone_number').oncut = this.onSmsPhoneNumberChanged.bind(this);
        }
        if ($('f_cp_confirm_sms_number_a')) {
            $('f_cp_confirm_sms_number_a').onclick = this.onConfirmSmsNumber.bind(this);
        }
        if ($('f_cp_confirm_sms_code_a')) {
            $('f_cp_confirm_sms_code_a').onclick = this.onConfirmSmsCode.bind(this);
        }

        if ($('sms_time_control')) {
            //$('sms_time_control').onchange = this.onSmsTimeControlCheckBoxValueChanged.bind(this);
            $('sms_time_control').onclick = this.onSmsTimeControlCheckBoxValueChanged.bind(this);
        }

        if ($('sms_from_time')) {
            $('sms_from_time').onchange = this.onSmsFromTimeChanged.bind(this);

        }
        var thisInstance = this;
        jQuery('#cp_branch_select').change(function() {
            var selected_branch_id = jQuery(this).val();
            ngs.load('company_profile', {'selected_branch_id': selected_branch_id, 'refresh':1});

        });
        jQuery('#cp_remove_selected_company_branch').click(function() {
            var selected_branch_id = jQuery('#cp_branch_select').val();
            ngs.action('add_remove_company_branch_action', {"action": "delete", 'branch_id': selected_branch_id});

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
            ngs.DialogsManager.actionOrCancelDialog(563, 'cp_add_branch_button', false, 'cp_cancel_add_branch_button', 580, dialogContentHtml, function() {                
                thisInstance.createNewBranch();
            });
            jQuery('#cp_create_new_branch_form').submit(function() {
                thisInstance.createNewBranch();
                return false;
            });
        });
    },
    createNewBranch: function()
    {
        var branch_address = jQuery("#cp_branch_address_input").val();
        if (branch_address.length > 0) {
            var branch_region = jQuery("#cp_branch_region_select").val();
            var branch_zip = jQuery("#cp_branch_zip_input").val();            
            jQuery('#cp_create_new_branch_dialog').parent().block({message: 'processing...'});
            ngs.action("add_remove_company_branch_action", {"action": "add", "branch_region": branch_region, "branch_address": branch_address, "branch_zip": branch_zip});
        }
        else
        {
             ngs.DialogsManager.closeDialog(483, "<div>" + 'Please input address'+ "</div>");
        }
    },
    onSmsFromTimeChanged: function() {
        ngs.load("next_24_hours_select", {
            "start_time": $('sms_from_time').value
        });
    },
    onConfirmSmsNumber: function() {
        ngs.action("confirm_sms_number_action", {
            "number": $('sms_phone_number').value
        });
        $('global_modal_loading_div').style.display = 'block';
    },
    onConfirmSmsCode: function() {
        ngs.action("confirm_sms_number_action", {
            "code": $('f_sms_code').value
        });
        $('global_modal_loading_div').style.display = 'block';
    },
    onSmsPhoneNumberChanged: function() {
        $('f_cp_confirm_sms_number_a').style.display = 'block';
        $('sms_number_changed').value = 'true';
    },
    onSmsCheckbox: function() {
        $('sms_setting_container').style.display = this.sms_checkbox.checked ? 'block' : 'none';
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
    setSmsReceivingDaysForTheFirstTime: function() {
        var sms_receiving_days = $('sms_receiving_days').value;
        $('sms_monday_checkbox').checked = sms_receiving_days[0] == 1 ? true : false;
        $('sms_tuseday_checkbox').checked = sms_receiving_days[1] == 1 ? true : false;
        $('sms_wednesday_checkbox').checked = sms_receiving_days[2] == 1 ? true : false;
        $('sms_thursday_checkbox').checked = sms_receiving_days[3] == 1 ? true : false;
        $('sms_friday_checkbox').checked = sms_receiving_days[4] == 1 ? true : false;
        $('sms_saturday_checkbox').checked = sms_receiving_days[5] == 1 ? true : false;
        $('sms_sunday_checkbox').checked = sms_receiving_days[6] == 1 ? true : false;
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
            var sms_receiving_days = "";
            sms_receiving_days += serialized_form.sms_monday_checkbox ? '1' : '0';
            sms_receiving_days += serialized_form.sms_tuseday_checkbox ? '1' : '0';
            sms_receiving_days += serialized_form.sms_wednesday_checkbox ? '1' : '0';
            sms_receiving_days += serialized_form.sms_thursday_checkbox ? '1' : '0';
            sms_receiving_days += serialized_form.sms_friday_checkbox ? '1' : '0';
            sms_receiving_days += serialized_form.sms_saturday_checkbox ? '1' : '0';
            sms_receiving_days += serialized_form.sms_sunday_checkbox ? '1' : '0';
            $('sms_receiving_days').value = sms_receiving_days;
            return true;
        }
        $('global_modal_loading_div').style.display = 'block';
    },
    onPasswordChangeCheckBoxValueChanged: function() {
        this.setPasswordChangeDivVisibility();

    },
    onSmsTimeControlCheckBoxValueChanged: function() {
        this.setSmsTimeControlVisibility();
    },
    setPasswordChangeDivVisibility: function() {
        if ($('password_change_checkbox').checked)
            $('change_password_div').style.display = 'block';
        else
            $('change_password_div').style.display = 'none';
    },
    setSmsTimeControlVisibility: function() {
        if ($('sms_time_control').checked)
            $('sms_time_control_container').style.display = 'block';
        else
            $('sms_time_control_container').style.display = 'none';
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
        if ($('squaredOne').checked && form.sms_number_changed == 'true') {
            return "You should confirm the SMS number";
        }
        return 'ok';

    },
    onSaveButtonClicked: function() {

        if (this.onCPFormSubmit()) {
            $('cp_form').submit();
        }
    },
    onResetButtonClicked: function() {
        ngs.load("company_profile", {});
    },
    onLoadDestroy: function()
    {
        jQuery("#" + this.getContainer()).html("");
    }
});
