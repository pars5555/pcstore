ngs.DialogsManager = {
    closeDialog: function(titlePhraseFormula, contentHtml, buttonTitle, func, resizable) {

        if (typeof buttonTitle === 'undefined' || buttonTitle <= 0)
        {
            buttonTitle = 280;
        }
        if (typeof resizable === 'undefined' || resizable === null)
        {
            resizable = false;
        }
        jQuery(contentHtml).dialog({
            resizable: resizable,
            title: ngs.LanguageManager.getPhraseSpan(titlePhraseFormula),
            show: {effect: "slide", direction: "up", duration: 400},
            hide: {effect: "slide", direction: "up", duration: 400},
            modal: true,
            buttons: [
                {
                    text: ngs.LanguageManager.getPhrase(buttonTitle),
                    'class': "translatable_search_content",
                    phrase_id: buttonTitle,
                    click: function() {
                        if (typeof func === 'function') {
                            func();
                        }
                        jQuery(this).remove();
                    }
                }
            ],
            close: function() {
                jQuery(this).remove();
            },
            open: function(event, ui) {
                jQuery(this).parent().attr('phrase_id', titlePhraseFormula);
                jQuery(this).parent().addClass('translatable_search_content');
            }
        });
    },
    actionOrCancelDialog: function(actionButtonTitle, actionButtonId, closeAfterAction, cancelButtonId, titlePhraseFormula, contentHtml, actionFunc, resizable, width, height, removeOnClose) {
        if (typeof actionButtonId === 'undefined' || actionButtonId === '')
        {
            actionButtonId = this.randomString(10);
        }
        if (typeof removeOnClose === 'undefined' || removeOnClose == null)
        {
            removeOnClose = true;
        }
        if (typeof cancelButtonId === 'undefined' || cancelButtonId === '')
        {
            cancelButtonId = this.randomString(10);
        }
        if (typeof resizable === 'undefined' || resizable === null)
        {
            resizable = false;
        }
        jQuery(contentHtml).dialog({
            resizable: resizable,
            title: ngs.LanguageManager.getPhraseSpan(titlePhraseFormula),
            modal: true,
            width: width,
            height: height,
            buttons: {
                "Action": {
                    text: ngs.LanguageManager.getPhrase(actionButtonTitle),
                    'class': "dialog_default_button_class translatable_search_content",
                    phrase_id: actionButtonTitle,
                    id: actionButtonId,
                    click: function() {
                        if (typeof actionFunc === 'function') {
                            jQuery('#' + actionButtonId).attr('disabled', true);
                            jQuery('#' + cancelButtonId).attr('disabled', true);
                            actionFunc();
                        }
                        if (closeAfterAction)
                        {
                            jQuery(this).dialog('close');
                        }
                    }
                },
                "Cancel": {
                    text: ngs.LanguageManager.getPhrase(49),
                    'class': "translatable_search_content",
                    phrase_id: 49,
                    id: cancelButtonId,
                    click: function() {
                        jQuery(this).dialog('close');
                    }
                }
            },
            close: function() {
                if (removeOnClose == true) {
                    jQuery(this).remove();
                }
            },
            open: function(event, ui) {
                jQuery(this).parent().attr('phrase_id', titlePhraseFormula);
                jQuery(this).parent().addClass('translatable_search_content');
            }
        });
    },
    randomString: function(length) {
        var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');

        if (!length) {
            length = Math.floor(Math.random() * chars.length);
        }

        var str = '';
        for (var i = 0; i < length; i++) {
            str += chars[Math.floor(Math.random() * chars.length)];
        }
        return str;
    }

};