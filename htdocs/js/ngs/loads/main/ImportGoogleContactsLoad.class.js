ngs.ImportGoogleContactsLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "import_google_contacts";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "global_empty_container";
    },
    getName: function() {
        return "import_google_contacts";
    },
    beforeLoad: function() {
        jQuery('#global_modal_loading_div').css('display', 'block');
    },
    afterLoad: function() {
        jQuery('#global_modal_loading_div').css('display', 'none');

        jQuery("#igc_container").dialog(
                {
                    maxHeight: 600,
                    height: 600,
                    minWidth: 300,
                    width: 500,
                    modal: true,
                    buttons: {
                        "Select All": {
                            text: ngs.LanguageManager.getPhrase(486),
                            phrase_id: 486,
                            'class': "translatable_search_content",
                            click: function() {
                                jQuery('#igc_container').find('input').prop('checked', true);
                            }
                        },
                        "Deselect All": {
                            text: ngs.LanguageManager.getPhrase(487),
                            phrase_id: 487,
                            'class': "translatable_search_content",
                            click: function() {
                                jQuery('#igc_container').find('input').prop('checked', false);
                            }
                        },
                        "Ok": {
                            text: ngs.LanguageManager.getPhrase(485),
                            phrase_id: 485,
                            'class': "dialog_default_button_class translatable_search_content",
                            click: function() {
                                var emailsArray = new Array();
                                jQuery('#igc_container').find('input:checked').each(function() {
                                    var email = jQuery(this).attr('email');
                                    emailsArray.push(email);
                                });
                                if (emailsArray.length > 0)
                                {
                                    ngs.action('invite_user_action', {'emails': emailsArray.join(',')});
                                }
                                jQuery(this).remove();
                            }
                        },
                        "Cancel": {
                            text: ngs.LanguageManager.getPhrase(49),
                            phrase_id: 49,
                            'class': "translatable_search_content",
                            click: function() {
                                jQuery(this).remove();
                            }
                        }
                    },
                    close: function() {
                        jQuery(this).remove();
                    },
                    open: function(event, ui) {
                        jQuery(this).parent().attr('phrase_id', 497);
                        jQuery(this).parent().addClass('translatable_search_content');
                    }
                });
    }
});
