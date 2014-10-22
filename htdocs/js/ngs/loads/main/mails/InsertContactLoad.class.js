ngs.InsertContactLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main_mails", ajaxLoader);
    },
    getUrl: function() {
        return "insert_contact";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "global_empty_container";
    },
    getName: function() {
        return "insert_contact";
    },
    beforeLoad: function() {
        jQuery('#global_modal_loading_div').css('display', 'block');
    },
    afterLoad: function() {

        jQuery('#global_modal_loading_div').css('display', 'none');
        this.initSelectedContactsInTabs();

        //clear recipitnt list
        jQuery('#mails_clear_all_contacts').click(function() {
            jQuery('.cm_email_email_element').remove();
        });
        var classInstance = this;
        jQuery("#ic_contact_tabs").tabs();

        jQuery("#ic_contact_tabs").dialog(
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
                                var current_tab_index = jQuery(this).tabs('option', 'active');
                                var current_tab_id = jQuery('#ic_contact_tabs .ui-tabs-panel:eq(' + current_tab_index + ')').attr('id');
                                jQuery('#' + current_tab_id).find('input:checkbox').prop('checked', true);
                            }
                        },
                        "Deselect All": {
                            text: ngs.LanguageManager.getPhrase(487),
                            phrase_id: 487,
                            'class': "translatable_search_content",
                            click: function() {
                                var current_tab_index = jQuery(this).tabs('option', 'active');
                                var current_tab_id = jQuery('#ic_contact_tabs .ui-tabs-panel:eq(' + current_tab_index + ')').attr('id');
                                jQuery('#' + current_tab_id).find('input:checkbox').prop('checked', false);
                            }
                        },
                        "Ok": {
                            text: ngs.LanguageManager.getPhrase(485),
                            phrase_id: 485,
                            'class': "dialog_default_button_class translatable_search_content",
                            click: function() {
                                jQuery('.' + classInstance.params.result_inner_divs_class).remove();
                                jQuery('#ic_contact_tabs input:checked').each(function() {
                                    var cust_email = jQuery(this).attr('cust_email');
                                    var cust_name = jQuery(this).attr('cust_name');
                                    var cust_type = jQuery(this).attr('cust_type');
                                    jQuery('#' + classInstance.params.result_element_id).append(classInstance.createContactDivToShowInRecipientsField(cust_email, cust_name, cust_type));

                                });
                                jQuery('.remove_parent').click(function(event) {
                                    event.stopPropagation();
                                    jQuery(this).parent().remove();
                                });
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
    },
    createContactDivToShowInRecipientsField: function(cust_email, cust_name, cust_type_string, elementClassName)
    {
        var divColor = '#EEE';
        if (cust_type_string === 'user') {
            divColor = "#EEE";
        } else if (cust_type_string === 'company') {
            divColor = "#FFC";
        } else if (cust_type_string === 'admin') {
            divColor = "#FCC";
        }
        var _elementClassName = "";
        if (typeof elementClassName !== 'undefined')
        {
            _elementClassName = elementClassName;
        } else
        {
            _elementClassName = this.params.result_inner_divs_class;

        }
        return '<div class="' + _elementClassName + '" style="background-color:' + divColor + '" cust_email="' + cust_email + '"><div style="float:left">'
                + cust_name + ' (' + this.ucFirst(cust_type_string) + ')' + '</div><div class="remove_parent" style="float:right;margin-left:5px;cursor:pointer">X</div></div>';
    },
    ucFirst: function(string) {
        return string.substring(0, 1).toUpperCase() + string.substring(1).toLowerCase();
    },
    initSelectedContactsInTabs: function()
    {
        var emails_to_be_selected = this.params.customer_emails;
        jQuery('#ic_contact_tabs input:checkbox').each(function() {
            var cust_email = jQuery(this).attr('cust_email');
            if (jQuery.inArray(cust_email, emails_to_be_selected) >= 0)
            {
                jQuery(this).prop('checked', true);
            }
        });

    }
});
