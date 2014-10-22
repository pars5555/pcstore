ngs.FormatPriceEmailRecipientsAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "company", ajaxLoader);
	},

	getUrl : function() {
		return "do_format_price_email_recipients";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {           
       var data =  transport.responseText.evalJSON();
		if (data.status === "ok") {   
           $('dealer_emails_textarea').value = data.valid_email_addresses;           
            var count =  data.valid_email_addresses===''?0:data.valid_email_addresses.split(";").length;
           $('total_price_email_recipients_number').innerHTML = count;
		} else if (data.status === "err") {
           
		}
	}
});
