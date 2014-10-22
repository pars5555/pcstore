ngs.YourProfileLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "your_profile";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "hp_" + this.getName() + "_tab";
    },
    getName: function() {
        return "your_profile";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {
        ngs.UrlChangeEventObserver.setFakeURL("/uprofile");
        $('global_modal_loading_div').style.display = 'none';

        if (jQuery('#yp_password_form').length > 0) {
            jQuery('#yp_password_form').submit(function() {
                ngs.load('your_profile', {'password': jQuery('#yp_password_check').val()});
                return false;
            });
            return;
        }

        if ($('password_change_checkbox')) {
            $('password_change_checkbox').onchange = this.onPasswordChangeCheckBoxValueChanged.bind(this);
        }
        if ($('yp_save_button')) {
            $('yp_save_button').onclick = this.onSaveButtonClicked.bind(this);
        }
        if ($('yp_reset_button')) {
            $('yp_reset_button').onclick = this.onResetButtonClicked.bind(this);
        }

        jQuery('#up_enable_vip').click(function() {
            ngs.DialogsManager.closeDialog(483, "<div><p>" + ngs.LanguageManager.getPhraseSpan(484) + "</p></div>");
        });
        this.setPasswordChangeDivVisibility();
    },
    onPasswordChangeCheckBoxValueChanged: function() {
        this.setPasswordChangeDivVisibility();
    },
    setPasswordChangeDivVisibility: function() {
        if ($('change_password_div')) {
            if ($('password_change_checkbox').checked) {
                $('change_password_div').style.display = 'block';
            } else {
                $('change_password_div').style.display = 'none';
            }
        }
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

        var serialized_form = $('yp_form').serialize(true);
        var validateFormMessage = this.validateForm(serialized_form);

        if (validateFormMessage != 'ok') {
            $('yp_error_p').style.display = 'block';
            $('yp_error_message').innerHTML = 'Error: ' + validateFormMessage + '!';

        } else {

            ngs.action("change_user_profile_action", {
                "pass": serialized_form.pass,
                "name": serialized_form.name,
                "lname": serialized_form.lname,
                "change_pass": serialized_form.change_pass,
                "new_pass": serialized_form.new_pass,
                "repeat_new_pass": serialized_form.repeat_new_pass,
                "phone1": serialized_form.phone1,
                "phone2": serialized_form.phone2,
                "phone3": serialized_form.phone3,
                "address": serialized_form.address,
                "region": serialized_form.region,
                "enable_vip": serialized_form.enable_vip

            });
        }
    },
    onResetButtonClicked: function() {
        $('yp_form').reset();
        this.setPasswordChangeDivVisibility();
    },
    onLoadDestroy: function()
    {
        jQuery("#" + this.getContainer()).html("");
    }
});
