ngs.PendingUsersListLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "pending_users_list";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "um_pending_users_tab";
    },
    getName: function() {
        return "pending_users_list";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {
        $('global_modal_loading_div').style.display = 'none';
        if ($('invite_user_form')) {
            $('invite_user_form').onsubmit = this.onInviteUserFormSubmit.bind(this);
        }
        jQuery('.f_resendinvitation').click(function() {
            jQuery(this).remove();
            var pk = jQuery(this).attr('pk');
            ngs.action("invite_user_action", {
                "invitation_id": pk
            });
        });

        if (jQuery('#googleGetAllContactsBtn').length > 0) {
            ngs.logoutGoogle = 1;
            gapi.signin.render('googleGetAllContactsBtn', {});
        }
    },
    googleContactsCallBack: function(emailsArray)
    {
        ngs.load('import_google_contacts', {'emails':emailsArray.join(',')});
    },
    addClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var user_email = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
            elements_array[i].onclick = this.onRemoveUserFromUserSubsClicked.bind(this, user_email);
        }
    },
    validateEmail: function(email) {
        var reg = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (reg.test(email) == false) {
            ngs.DialogsManager.closeDialog(583, "<div>" + 'Invalid Email Address' + "</div>");
            return false;
        }
        return true;
    },
    onInviteUserFormSubmit: function() {
        var user_email = $('invite_user_form').invited_user_email.value;
        if (this.validateEmail(user_email)) {
            $('um_invite_button').remove();
            ngs.action("invite_user_action", {
                "user_email": user_email
            });
        }
        return false;
    },
    onRemoveUserFromUserSubsClicked: function($user_email) {

        ngs.action("remove_user_from_user_invited_users_action", {
            "user_email": $user_email
        });
    }
});
