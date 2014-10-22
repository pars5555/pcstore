ngs.UploadCompanyPriceAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "do_upload_company_price";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.evalJSON();
        $('upload_company_price_button').style.visibility = "visible";
        $('upload_price_overlay_div').style.display = 'none';
        if (data.status === "ok") {
            this.updatePageIfNeeded();
            ngs.DialogsManager.closeDialog(514, "<div>" + ngs.LanguageManager.getPhraseSpan(513) + "</div>");
        } else if (data.status === "war") {
            this.showConfirmOrMergePriceDialog();
        } else if (data.status === "err") {
            this.updatePageIfNeeded();
            ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
        }
    },
    updatePageIfNeeded: function() {
        if (ngs.activeLoadName === 'upload_price') {
            if (jQuery('#up_selected_company')) {
                var selectedCompany = jQuery('#up_selected_company').val();
                $('global_modal_loading_div').style.display = 'block';
                ngs.load('upload_price', {'selected_company': selectedCompany});
            } else {
                $('global_modal_loading_div').style.display = 'block';
                ngs.load('upload_price');
            }
        }
    },
    showConfirmOrMergePriceDialog: function() {
        jQuery("<div>" + ngs.LanguageManager.getPhraseSpan(616) + "</div>").dialog({
            resizable: false,
            title: ngs.LanguageManager.getPhraseSpan(483),
            show: {effect: "slide", direction: "up", duration: 400},
            hide: {effect: "slide", direction: "up", duration: 400},
            modal: true,
            buttons: [
                {
                    text: ngs.LanguageManager.getPhrase(617),
                    'class': "translatable_search_content",
                    phrase_id: 617,
                    click: function() {
                        jQuery('#merge_uploaded_price_into_last_price').prop('checked', true);
                        jQuery('#up_price_upload_form').submit();
                        jQuery(this).remove();
                    }
                },
                {
                    text: ngs.LanguageManager.getPhrase(321),
                    'class': "translatable_search_content",
                    phrase_id: 321,
                    click: function() {
                        jQuery('<input type="hidden" name="new_price_confirmed" value="1"/>').appendTo('#up_price_upload_form');
                        jQuery('#up_price_upload_form').submit();
                        jQuery(this).remove();
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
    }
});
