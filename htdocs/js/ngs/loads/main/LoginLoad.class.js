ngs.LoginLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "login";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "user_login_container";
    },
    getName: function() {
        return "login";
    },
    afterLoad: function() {
        var thisInstance = this;
        jQuery('#ld_login_form').submit(function() {
            thisInstance.doLogin();
            return false;
        });

        $('login_registration').onclick = this.onLoginRegistrationClicked.bind(this);
        $('login_forgot_password').onclick = this.onLoginForgotPasswordClicked.bind(this, $('user_email_input_field').value);

    },
    doLogin: function() {
        jQuery('#ld_login_form').block({message: 'Logging...'});
        jQuery("#ld_login_form :input").prop("disabled", true);
        ngs.action("login_action", {
            "user_email": $('user_email_input_field').value,
            "user_pass": $('user_pass_input_field').value
        });
        return false;
    },
    onLoginRegistrationClicked: function() {
        ngs.load('user_registration', {});
        return false;
    },
    onLoginForgotPasswordClicked: function(email) {
        ngs.load('forgot_login', {"email": email});
        return false;
    }
});
