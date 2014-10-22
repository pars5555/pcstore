ngs.UploadAttachmentAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "do_upload_attachment";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.evalJSON();
        if (data.status === "ok") {
            var attachmentIconPathToShowInFrontend = data.attachmentIconPathToShowInFrontend;
            var file_name = data.file_name;
            var file_real_name = data.file_real_name;
            var attachmentElement = jQuery('#attachment_element_hidden_div').clone();
            var imgSrc = SITE_PATH+ attachmentIconPathToShowInFrontend;
            attachmentElement.find('img').attr('src', imgSrc);
            attachmentElement.find('label').html(file_name);
            attachmentElement.find('div').attr('file_real_name',file_real_name);
            attachmentElement.css({'display': 'block'});

            jQuery('#company_email_attachments_container').append(attachmentElement);
        } else if (data.status === "err") {
            alert(data.message);
        }
    }
});
