function googleLoginCallback(authResult) {
    if (jQuery('#googleGetAllContactsBtn').length > 0) {
        if (typeof ngs.logoutGoogle !== 'undefined' && ngs.logoutGoogle == 1)
        {
            ngs.logoutGoogle = 0;
            return;
        }
        if (authResult['status']['signed_in']) {
            jQuery.get("https://www.google.com/m8/feeds/contacts/default/full?alt=json&access_token=" + authResult.access_token + "&max-results=1000&v=3.0",
                    function(response) {
                        if (response.feed.entry.length > 0) {
                            var emails = [];
                            for (var i = 0; i < response.feed.entry.length; i++)
                            {
                                if (response.feed.entry[i].gd$email)
                                {
                                    emails.push(response.feed.entry[i].gd$email[0].address);
                                }
                            }
                            ngs.PendingUsersListLoad.prototype.googleContactsCallBack(emails);
                        }
                    });

        } else {
        }
    } else
    if (jQuery('#googleLoginBtn').length > 0 || jQuery('#r_googleLoginBtn').length > 0) {
        if (authResult['status']['signed_in']) {
            gapi.client.load('plus', 'v1', function() {
                var request = gapi.client.plus.people.get({
                    'userId': 'me'
                });
                request.execute(function(resp) {
                    var email = "";
                    var userId = resp.id;
                    var emails = resp.emails;
                    if (typeof emails === 'undefined')
                    {
                        emails = new Array();
                    }
                    jQuery.each(emails, function(index, value) {
                        var e = value;
                        if (e.type === 'account')
                        {
                            email = e.value;
                        }
                    });
                    var firstName = resp.name.givenName;
                    var lastName = resp.name.familyName;
                    resp.email = email;
                    var jsonProfile = JSON.stringify(resp);
                    ngs.action("login_action", {"login_type": 'google', 'social_user_id': userId, 'first_name': firstName, 'last_name': lastName, 'json_profile': jsonProfile});
                });

            });

        } else {
        }
    }

}


jQuery(document).ready(function() {
    if (jQuery('#googleLoginBtn').length > 0) {
        gapi.signin.render('googleLoginBtn', {});
    }
});



 