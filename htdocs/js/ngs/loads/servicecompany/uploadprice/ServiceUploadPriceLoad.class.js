ngs.ServiceUploadPriceLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "servicecompany_uploadprice", ajaxLoader);
    },
    getUrl: function() {
		return "service_upload_price";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "hp_" + this.getName() + "_tab";
    },
    getName: function() {
		return "service_upload_price";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {
		ngs.UrlChangeEventObserver.setFakeURL("/serviceupload");
        $('global_modal_loading_div').style.display = 'none';
        $('select_price_file_button').onclick = this.onSelectPriceFileButtonClicked.bind(this);
        $('upload_company_price_button').onclick = this.onUploadCompanyPriceButtonClicked.bind(this);
		if ($('up_selected_service_company')) {
			$('up_selected_service_company').onchange = this.onSelectedCompanyChanged.bind(this);
        }
        var remove_company_price_links = $$("#upload_price_container .company_price_remove_link_class");
        this.addRemoveCompanyPriceClickHandler(remove_company_price_links);
        $('up_selected_file_name').onclick = this.onSelectPriceFileButtonClicked.bind(this);

        if ($('send_price_email'))
        {
            $('send_price_email').onclick = this.onSendPriceEmail.bind(this);
        }


        var thisInstance = this;
        jQuery('#save_price_email').click(function() {
            thisInstance.onSavePriceEmail();
        });

        if ($('dealer_emails_textarea'))
        {
            this.addChangeHandlerToFormatRecipients();
        }
        this.revertCompanyLastPriceHandler();
        this.initTinyMCE("textarea.msgBodyTinyMCEEditor");
        this.initCompanyFileAttachment();
    },
    initCompanyFileAttachment: function() {
        jQuery('#company_attach_new_file_button').click(function() {
            jQuery('#company_attach_file_input').trigger('click');
            jQuery('#company_attach_file_input').change(function() {
                jQuery('#up_add_attachment_form').trigger('submit');
            });
        });
        jQuery('#company_email_attachments_container').on('click', '.f_up_delete_attachment', function() {
            var file_name = jQuery(this).attr('file_real_name');
            ngs.action('company_group_actions', {'action': 'delete_attachment', "file_name": file_name});
            jQuery(this).parent().remove();
        });
    },
    revertCompanyLastPriceHandler: function() {
        jQuery("#revert_company_last_uploaded_price").click(function() {
            jQuery("<div>" + ngs.LanguageManager.getPhraseSpan(491) + "</div>").dialog({
                resizable: false,
                title: ngs.LanguageManager.getPhrase(483),
                modal: true,
                buttons: [{
                        text: ngs.LanguageManager.getPhrase(489),
                        'class': "dialog_default_button_class translatable_search_content",
                        phrase_id: 489,
                        click: function() {
                            var service_company_id = jQuery('#up_selected_service_company').val();
                            jQuery(this).remove();
                            ngs.action('revert_service_company_last_price_action', {'service_company_id': service_company_id});
                        }
                    },
                    {
                        text: ngs.LanguageManager.getPhrase(49),
                        'class': "translatable_search_content",
                        phrase_id: 49,
                        click: function() {
                            jQuery(this).remove();
                        }
                    }
                ],
                close: function() {
                    jQuery(this).remove();
                },
                open: function(event, ui) {
                    jQuery(this).parent().attr('phrase_id', 483);
                    jQuery(this).parent().addClass('translatable_search_content');
                }
            });
        });
    },
    validateEmail: function(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    },
    onSendPriceEmail: function()
    {
        if (!this.validateEmail(jQuery('#sender_email').val()))
        {
            jQuery('#sender_email').css({'border': '1px solid red'});
            jQuery(window.self).scrollTop(0);
            return false;
        } else
        {
            jQuery('#sender_email').css({'border': 'auto'});
        }
        tinyMCE.activeEditor.save();
        $('global_modal_loading_div').style.display = 'block';
        var subject = $('price_email_subject').value;
        var body = document.getElementsByName('price_email_body')[0].value;
        var to = $('dealer_emails_textarea').value;
        ngs.action("send_price_email_action", {"save_only": 0, "subject": subject, "body": body, "to": to, "from_email": jQuery('#sender_email').val()});
        return false;
    },
    onSavePriceEmail: function()
    {
        tinyMCE.activeEditor.save();
        $('global_modal_loading_div').style.display = 'block';
        var subject = $('price_email_subject').value;
        var body = document.getElementsByName('price_email_body')[0].value;
        var to = $('dealer_emails_textarea').value;
		ngs.action("send_price_email_action", {"save_only" : 1,"subject": subject, "body": body, "to": to});
        return false;
    },
    addRemoveCompanyPriceClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var price_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
            elements_array[i].onclick = this.onRemoveCompanyPrice.bind(this, price_id);
        }
    },
    onRemoveCompanyPrice: function(price_id) {
        var answer = confirm("Are you sure you want to remove the price?");
        if (answer) {
            ngs.action('remove_company_price_action', {"price_id": price_id});
        }
    },
    addChangeHandlerToFormatRecipients: function()
    {
        var thisInstance = this;
        jQuery('#dealer_emails_textarea').on('change cut paste keydown', function() {
            if (thisInstance.timer) {
                window.clearTimeout(thisInstance.timer);
            }
            thisInstance.timer = window.setTimeout(function() {
                ngs.action("format_price_email_recipients_action", {"to_emails": $('dealer_emails_textarea').value});
            }, 2000);
        });
        jQuery('#dealer_emails_textarea').on('blur', function() {
            if (thisInstance.timer) {
                window.clearTimeout(thisInstance.timer);
            }
            ngs.action("format_price_email_recipients_action", {"to_emails": $('dealer_emails_textarea').value});
        });
    },
    onSelectedCompanyChanged: function() {
		ngs.load('service_upload_price', {
			'selected_company': $('up_selected_service_company').value
        });
    },
    onSelectPriceFileButtonClicked: function() {
        var inpFile = $("company_price_file_input");
        inpFile.click();
        inpFile.onchange = function() {
            $('up_selected_file_name').value = this.value;
            //$("company_price_file_input").submit();
        };
    },
    onUploadCompanyPriceButtonClicked: function() {
        if (this.validateUploadForm()) {
            //$('upload_company_price_button').disabled=true;
            $('upload_company_price_button').style.visibility = "hidden";
            $('upload_price_overlay_div').style.display = 'block';
            $('up_price_upload_form').submit();
        }
    },
    validateUploadForm: function() {
        //todo validate
        return true;
    },   
    onLoadDestroy: function()
    {
        jQuery("#" + this.getContainer()).html("");
    }
});
