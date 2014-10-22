ngs.OpenNewsletterAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_newsletters", ajaxLoader);
    },
    getUrl: function() {
        return "do_open_newsletter";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status === 'ok')
        {
            var html = data.html;
            tinyMCE.activeEditor.setContent(html, {format : 'raw'});
            var include_all_active_users = data.include_all_active_users;
            var newsletter_title = data.newsletter_title;
            jQuery('#nl_include_all_active_users').val(include_all_active_users);            
            jQuery('#nl_newsletter_title').html(newsletter_title);                        
        } else
        {
            ngs.DialogsManager.closeDialog('Error!', '<div>' + data.message + '</div>', 'close', null, false);
        }        
    }
});
