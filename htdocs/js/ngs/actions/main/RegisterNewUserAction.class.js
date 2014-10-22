ngs.RegisterNewUserAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "do_register_new_user";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        jQuery("#ur_register_button").attr("disabled", false);
        var data = transport.responseText.evalJSON();
        if (data.status === "ok") {
            jQuery('#user_registration_div').dialog('close');
            ngs.DialogsManager.closeDialog(514, "<div><p>" + ngs.LanguageManager.getPhraseSpan(522) + "</p></div>", 280, function() {
                window.location.href = "/";
            });
        } else if (data.status === "err") {
            $('error_p').style.display = 'block';
            $('error_message').innerHTML = data.errText;
        }
    }
});
