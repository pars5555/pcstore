ngs.PingPongAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "do_ping_pong";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status === "ok") {
            if (typeof data.alerts !== 'undefined') {
                if (this.supportsAudio() && ngs.sound_on == 1) {
                    var snd = new Audio("/sounds/DingLing.wav");
                    snd.play();
                }

                if (data.alerts.check_items_availability) {
                    ngs.CustomerAlertsManager.addCompanyItemAvailablilityCheck(data.alerts.check_items_availability);
                }
                if (data.alerts.responded_items_availability) {
                    ngs.CustomerAlertsManager.addResponseItemAvailabilityDialog(data.alerts.responded_items_availability);
                }
            }
            if (typeof data.messages !== 'undefined') {
                var customerMessages = data.messages;
                for (var index in customerMessages) {
                    if (customerMessages.hasOwnProperty(index)) {
                        var alertDto = customerMessages[index];
                        if (ngs.sound_on == 1) {
                            var snd = new Audio("/sounds/DingLing.wav");
                            snd.play();
                        }
                        ngs.CustomerAlertsManager.showAutoHideMessage(alertDto.message, alertDto.title);
                        if (alertDto.type === "price_upload")
                        {
                            if (ngs.activeLoadName === 'companies_list') {
                                ngs.load('companies_list', {});
                            }
                        }

                        if (alertDto.type === "new_email")
                        {
                            var unreadEmailsCount = parseInt(alertDto.metadata);
                            var yourEmailsTitle = jQuery('#hp_tabs_container a[href="#hp_your_mails_tab"]').html();
                            if (yourEmailsTitle.indexOf('*') === -1 && unreadEmailsCount > 0)
                            {
                                yourEmailsTitle += '*';
                            }
                            jQuery('#hp_tabs_container a[href="#hp_your_mails_tab"]').html(yourEmailsTitle);
                            if (ngs.activeLoadName === 'your_mails') {
                                jQuery('#customer_inbox_unread_email_count_span').html(unreadEmailsCount);

                            }
                        }

                    }
                }
            }
            if (typeof data.customer_after_login_messages !== 'undefined')
            {
                this.prepareAfterLoginCustmerMessagesDialogParams(data.customer_after_login_messages);
            }
        }
        else if (data.status === "err") {

        }
    },
    supportsAudio: function() {
        var a = document.createElement('audio');
        return !!(a.canPlayType && a.canPlayType('audio/mpeg;').replace(/no/, ''));
    },
    prepareAfterLoginCustmerMessagesDialogParams: function(customerAfterLoginMessages)
    {
        var attention_messages = new Array();
        var warning_messages = new Array();
        var news_messages = new Array();
        for (var i = 0; i < customerAfterLoginMessages.length; i++)
        {
            var messageDtoArray = customerAfterLoginMessages[i];
            var id = messageDtoArray.id;
            var title_formula = messageDtoArray.titleFormula;
            var message_formula = messageDtoArray.messageFormula;
            var showed_count = messageDtoArray.showedCount;
            var shows_count = messageDtoArray.showsCount;
            var type = messageDtoArray.type;
            switch (type)
            {
                case 'news':
                    news_messages.push({'id': id, 'title_formula': title_formula, 'message_formula': message_formula, 'type': type, 'showed_count': showed_count, 'shows_count': shows_count});
                    break;
                case 'attention':
                    attention_messages.push({'id': id, 'title_formula': title_formula, 'message_formula': message_formula, 'type': type, 'showed_count': showed_count, 'shows_count': shows_count});
                    break;
                case 'warning':
                    warning_messages.push({'id': id, 'title_formula': title_formula, 'message_formula': message_formula, 'type': type, 'showed_count': showed_count, 'shows_count': shows_count});
                    break;
            }
        }
        this.openAfterLoginCustmerMessagesDialog(news_messages, attention_messages, warning_messages);
    },
    openAfterLoginCustmerMessagesDialog: function(news_messages, attention_messages, warning_messages)
    {
        var totalMessagesCount = news_messages.length + attention_messages.length + warning_messages.length;

        if (totalMessagesCount === 1)
        {
            if (news_messages.length === 1) {
                this.openAfterLoginCustmerMessagesDialogSinlgeMessage(news_messages[0]);
            } else if (attention_messages.length === 1) {
                this.openAfterLoginCustmerMessagesDialogSinlgeMessage(attention_messages[0]);
            } else if (warning_messages.length === 1) {
                this.openAfterLoginCustmerMessagesDialogSinlgeMessage(warning_messages[0]);
            }
        } else
        {
            this.openAfterLoginCustmerMessagesDialogMultiMessage(warning_messages, attention_messages, news_messages);
        }
    },
    openAfterLoginCustmerMessagesDialogMultiMessage: function(warning_messages, attention_messages, news_messages)
    {
        var warningMessagesHtml = '';
        if (warning_messages.length > 0) {
            warningMessagesHtml = '<h2 style="color:#AA1111">' + ngs.LanguageManager.getPhraseSpan(483) + '</h2><br/>';
            for (var i = 0; i < warning_messages.length; i++)
            {
                var message = warning_messages[i];
                var messageBodyText = ngs.LanguageManager.getPhraseSpan(message.message_formula);
                var messageTitleText = ngs.LanguageManager.getPhraseSpan(message.title_formula);

                var messageDontShowAgainCheckbox = "";
                if (message.showed_count < message.shows_count)
                {
                    messageDontShowAgainCheckbox = '<input id="customer_after_login_message_' + message.id +
                            '" type="checkbox" message_id="' + message.id + '"/><label style="padding-left:10px" for="customer_after_login_message_' + message.id +
                            '">' + ngs.LanguageManager.getPhraseSpan(539) + '</label>';
                }
                warningMessagesHtml += '<h5>' + messageTitleText + '</h5><div>' + messageBodyText + '<br/>' + messageDontShowAgainCheckbox + '</div>';
            }
        }


        var attentionMessagesHtml = '';
        if (attention_messages.length > 0) {
            attentionMessagesHtml = '<h2 style="color:#AA1111">' + ngs.LanguageManager.getPhraseSpan(534) + '</h2><br/>';
            for (var i = 0; i < attention_messages.length; i++)
            {
                var message = attention_messages[i];
                var messageBodyText = ngs.LanguageManager.getPhraseSpan(message.message_formula);
                var messageTitleText = ngs.LanguageManager.getPhraseSpan(message.title_formula);
                var messageDontShowAgainCheckbox = "";
                if (message.showed_count < message.shows_count)
                {
                    messageDontShowAgainCheckbox = '<input id="customer_after_login_message_' + message.id +
                            '" type="checkbox" message_id="' + message.id + '"/><label style="padding-left:10px" for="customer_after_login_message_' + message.id +
                            '">' + ngs.LanguageManager.getPhraseSpan(539) + '</label>';
                }
                attentionMessagesHtml += '<h5>' + messageTitleText + '</h5><div>' + messageBodyText + '<br/>' + messageDontShowAgainCheckbox + '</div>';
            }
        }

        var newsMessagesHtml = '';
        if (news_messages.length > 0) {
            newsMessagesHtml = '<h2 style="color:#AA1111">' + ngs.LanguageManager.getPhraseSpan(549) + '</h2><br/>';

            for (var i = 0; i < news_messages.length; i++)
            {
                var message = news_messages[i];
                var messageBodyText = ngs.LanguageManager.getPhraseSpan(message.message_formula);
                var messageTitleText = ngs.LanguageManager.getPhraseSpan(message.title_formula);
                var messageDontShowAgainCheckbox = '';
                if (message.showed_count < message.shows_count)
                {
                    messageDontShowAgainCheckbox = '<input id="customer_after_login_message_' + message.id +
                            '" type="checkbox" message_id="' + message.id + '"/><label style="padding-left:10px" for="customer_after_login_message_' + message.id +
                            '">' + ngs.LanguageManager.getPhraseSpan(539) + '</label>';
                }
                newsMessagesHtml += '<h5>' + messageTitleText + '</h5><div>' + messageBodyText + '<br/>' + messageDontShowAgainCheckbox + '</div>';
            }
        }


        var dialogContentHTML = warningMessagesHtml + attentionMessagesHtml + newsMessagesHtml;
        jQuery('<div>' + dialogContentHTML + '</div>').dialog({
            resizable: true,
            width: 530,
            height: 350,
            modal: true,
            title: ngs.LanguageManager.getPhrase(534),
            buttons: {
                "ok": {
                    text: ngs.LanguageManager.getPhrase(485),
                    id: 'ur_register_button',
                    'class': "dialog_default_button_class translatable_search_content",
                    phrase_id: 485,
                    click: function() {
                        jQuery(this).dialog("close");
                    }
                }
            },
            close: function() {
                var dont_show_message_ids = new Array();
                jQuery(this).find("input:checked").each(function() {
                    dont_show_message_ids.push(jQuery(this).attr('message_id'));
                });
                if (dont_show_message_ids.length > 0) {
                    ngs.action("set_show_customer_after_login_message_dont_show_anymore_action", {"message_ids": dont_show_message_ids.join(',')});
                }
                jQuery(this).remove();
            },
            open: function() {
                jQuery(this).dialog().parent().attr('phrase_id', 534);
                jQuery(this).dialog().parent().addClass('translatable_search_content');
            }
        });
    },
    openAfterLoginCustmerMessagesDialogSinlgeMessage: function(message, preview)
    {
        if (typeof preview === 'undefined')
        {
            preview = false;
        }
        var messageText = '<div>' + ngs.LanguageManager.getPhraseSpan(message.message_formula) + '</div>';
        var messageDontShowAgainCheckbox = '';
        if (message.showed_count < message.shows_count)
        {
            messageDontShowAgainCheckbox = '<div>' + '<input id="customer_after_login_message_' + message.id +
                    '" type="checkbox" message_id="' + message.id + '"/><label style="padding-left:10px" for="customer_after_login_message_' + message.id +
                    '">' + ngs.LanguageManager.getPhraseSpan(539) + '</label>' + '</div>';
        }
        var dialogContentHTML = messageText + messageDontShowAgainCheckbox;
        jQuery('<div>' + dialogContentHTML + '</div>').dialog({
            resizable: true,
            width: 530,
            height: 350,
            modal: true,
            title: ngs.LanguageManager.getPhrase(message.title_formula),
            buttons: {
                "ok": {
                    text: ngs.LanguageManager.getPhrase(485),
                    id: 'ur_register_button',
                    'class': "dialog_default_button_class translatable_search_content",
                    phrase_id: 485,
                    click: function() {
                        jQuery(this).dialog("close");
                    }
                }
            },
            close: function() {
                var dont_show_message_ids = new Array();
                jQuery(this).find("input:checked").each(function() {
                    dont_show_message_ids.push(jQuery(this).attr('message_id'));
                });
                if (dont_show_message_ids.length > 0) {
                    if (preview == false) {
                        ngs.action("set_show_customer_after_login_message_dont_show_anymore_action", {"message_ids": dont_show_message_ids.join(',')});
                    }
                }
                jQuery(this).remove();
            },
            open: function() {
                jQuery(this).dialog().parent().attr('phrase_id', message.title_formula);
                jQuery(this).dialog().parent().addClass('translatable_search_content');
            }
        });
    }

});

