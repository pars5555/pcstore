jQuery(document).ready(function() {
    jQuery("#linkedinLoginBtn").click(function() {
        IN.UI.Authorize().place();
        IN.Event.on(IN, "auth", function() {
            onLinkedLogin();
        });       
    });
});


function onLinkedLogin() {
    IN.API.Profile("me").fields(["id","firstName","lastName","headline","pictureUrl","publicProfileUrl","emailAddress",
            "educations","dateOfBirth"]).result(function(profiles) {
        var member = profiles.values[0];
        var userId = member.id;
        member.email = member.emailAddress;
        var firstName = member.firstName;
        var lastName = member.lastName;
        var jsonProfile = JSON.stringify(member);
        //var pictureUrl = member.pictureUrl;
        ngs.action("login_action", {"login_type": 'linkedin', 'social_user_id': userId, 'first_name': firstName, 'last_name': lastName, 'json_profile': jsonProfile});

    });
}