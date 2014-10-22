ngs.UserRegistrationLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "user_registration";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "global_empty_container";
    },
    getName: function() {
        return "user_registration";
    },
    afterLoad: function() {
        var prevPath = window.location.pathname + window.location.search;
        ngs.UrlChangeEventObserver.setFakeURL("/registration");
        var thisInstance = this;


        jQuery('#user_registration_div').dialog({
            resizable: false,
            height: 420,
            width: 430,
            modal: true,
            title: ngs.LanguageManager.getPhrase(72),
            buttons: {
                "Login": {
                    text: ngs.LanguageManager.getPhrase(78),
                    id: 'ur_register_button',
                    'class': "dialog_default_button_class translatable_search_content",
                    phrase_id: 78,
                    click: function() {
                        thisInstance.onRegisterButtonClicked();
                    }
                },
                "Cancel": {
                    text: ngs.LanguageManager.getPhrase(49),
                    'class': "translatable_search_content",
                    phrase_id: 49,
                    click: function() {
                        jQuery(this).dialog('close');
                    }
                }


            },
            close: function() {
                ngs.UrlChangeEventObserver.setFakeURL(prevPath);
                jQuery(this).remove();
            },
            open: function() {
                jQuery(this).dialog().parent().attr('phrase_id', 72);
                jQuery(this).dialog().parent().addClass('translatable_search_content');
            }
        });
        $('email').onchange = this.onEmailValueChanged.bind(this);


        gapi.signin.render('r_googleLoginBtn', {});
        jQuery("#r_linkedinLoginBtn").click(function() {
            IN.UI.Authorize().place();
            IN.Event.on(IN, "auth", function() {
                onLinkedLogin();
            });            
        });
         jQuery("#facebookLoginBtn,#r_facebookLoginBtn").click(function() {
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
                FB.api('/me', onFacebookLogin);
            }
            else {
                FB.login(function(response) {
                }, {scope: 'email'});
            }
        });
    });
    },
    onEmailValueChanged: function() {
        var form = $('ur_form');
        if (form.email !== null) {
            var email = form.email.value.strip();
            if (email.length > 0 && !this.checkEmail(email)) {
                form.email.style.color = 'red';
                return;
            }
        }
        form.email.style.color = '';

    },
    onRegisterButtonClicked: function() {
        var validateForm = this.validateForm($('ur_form'));
        if (this.validateForm($('ur_form')) === 'ok') {
            var form = $('ur_form');
            jQuery("#ur_register_button").attr("disabled", true);
            ngs.action("register_new_user_action", {
                "email": form.email.value.strip(),
                "name": form.name.value.strip(),
                "phone": form.phone.value.strip(),
                "pass": form.pass.value.strip()
            });
        } else {
            $('error_p').style.display = 'block';
            $('error_message').innerHTML = validateForm;

        }
    },
    checkEmail: function(email) {
        var email_regexp = '^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$';
        var filter = new RegExp(email_regexp);
        if (!filter.test(email)) {
            return false;
        }
        return true;
    },
    checkPassword: function(password) {
        var password_regexp = $('password_regexp').value;
        var filter = new RegExp(password_regexp.substr(1, password_regexp.length - 2));
        if (!filter.test(password)) {
            return false;
        }
        return true;
    },
    validateForm: function(form) {
        if (form.email === null || form.email.value.strip().length === 0) {
            return ngs.LanguageManager.getPhraseSpan(518);
        }
        if (!this.checkEmail(form.email.value.strip())) {
            return ngs.LanguageManager.getPhraseSpan(471);
        }

        if (form.name === null || form.name.value.strip().length === 0) {
            return ngs.LanguageManager.getPhraseSpan(356);
        }

        if (form.pass === null || form.pass.value.strip().length === 0) {
            return ngs.LanguageManager.getPhraseSpan(357);
        }
        if (!this.checkPassword(form.pass.value.strip())) {
            return ngs.LanguageManager.getPhraseSpan(358);
        }

        if (form.repeat_pass === null || form.repeat_pass.value.strip().length === 0) {
            return ngs.LanguageManager.getPhraseSpan(519);
        } else {
            if (!(form.repeat_pass.value === form.pass.value))
                return ngs.LanguageManager.getPhraseSpan(520);
        }

        if (form.phone !== null) {
            var phone = form.phone.value.strip();
            if (phone.length > 0 && phone.indexOf(',') !== -1) {
                return ngs.LanguageManager.getPhraseSpan(521);
            }
        }

        return 'ok';

    }
});
