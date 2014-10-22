jQuery(document).ready(function() {
    if (typeof FB !== 'undefined') {
        FB.init({
            appId: '604055496277705',
            xfbml: true,
            version: 'v2.0'
        });

        jQuery("#facebookLoginBtn").click(function() {

            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    FB.api('/me', onFacebookLogin);
                } else {
                    FB.login(function(response) {
                        FB.api('/me', onFacebookLogin);
                    }, {scope: 'email'});
                }

            });
        });
    }
});
function onFacebookLogin(response)
{
    var userId = response.id;
    var firstName = response.first_name;
    var lastName = response.last_name;
    var jsonProfile = JSON.stringify(response);
    ngs.action("login_action", {"login_type": 'facebook', 'social_user_id': userId, 'first_name': firstName, 'last_name': lastName, 'json_profile': jsonProfile});
}