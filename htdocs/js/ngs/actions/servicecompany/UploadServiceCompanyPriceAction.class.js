ngs.UploadServiceCompanyPriceAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "servicecompany", ajaxLoader);
    },
    getUrl: function() {
        return "do_upload_service_company_price";
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
        } else if (data.status === "err") {
            this.updatePageIfNeeded();
            ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
        }
    },
    updatePageIfNeeded: function() {        
        if (ngs.activeLoadName === 'service_upload_price') {
            if (jQuery('#up_selected_service_company')) {
                var selectedCompany = jQuery('#up_selected_service_company').val();
                $('global_modal_loading_div').style.display = 'block';
                ngs.load('service_upload_price', {'selected_company': selectedCompany});
            } else {
                $('global_modal_loading_div').style.display = 'block';
                ngs.load('service_upload_price');
            }
        }
    }
});
