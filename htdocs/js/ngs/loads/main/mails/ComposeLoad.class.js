ngs.ComposeLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_mails", ajaxLoader);
    },
    getUrl: function() {
        return "compose";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "mails_main_container";
    },
    getName: function() {
        return "compose";
    },
    afterLoad: function() {
        jQuery('#global_modal_loading_div').css('display', 'none');
        ngs.UrlChangeEventObserver.setFakeURL("/mails/compose");
        $('global_modal_loading_div').style.display = 'none';
        if ($('ym_cancel_email')) {
            $('ym_cancel_email').onclick = this.onCancelClicked.bind(this);
        }
        if ($('ym_send_email')) {
            $('ym_send_email').onclick = this.onSendClicked.bind(this);
        }
        this.initTinyMCE("textarea.emailsTinyMCEEditor");
        if ($('cm_to_emails')) {
            $('cm_to_emails').onclick = this.onToEmailsClicked.bind(this);
        }
        if ($('cm_email_to')) {
            jQuery('#cm_to_emails').append(ngs.InsertContactLoad.prototype.createContactDivToShowInRecipientsField($('cm_email_to').value,
            $('cm_email_to_name').value, $('cm_email_to_type').value, 'cm_email_email_element'));
        }


    },
    onToEmailsClicked: function()
    {
        ngs.load("insert_contact", {'customer_emails': this.getToContactsList(), 'result_element_id': 'cm_to_emails', 'result_inner_divs_class': 'cm_email_email_element'});
    },
    getToContactsList: function()
    {
        var customer_emails = new Array();
        jQuery('.cm_email_email_element').each(function()
        {
            customer_emails.push(jQuery(this).attr('cust_email'));
        });
        return customer_emails;
    },
    onCancelClicked: function() {
        ngs.load("mails_inbox", {});
    },
    onSendClicked: function() {
        var toEmails = this.getToContactsList().join(',');
        var subject = $('cm_subject').value;
        tinyMCE.activeEditor.save();
        var body = document.getElementsByName('cm_body')[0].value;
        if (toEmails.length > 0) {
            jQuery('#mails_main_container').block({message: 'processing...'});
            ngs.action("mails_compose_send_action", {"to_emails": toEmails, "subject": subject, "body": body});
        }
        else {
            ngs.DialogsManager.closeDialog(483, "<div><p>" + ngs.LanguageManager.getPhraseSpan(493) + "</p></div>");
        }
    }
});
