ngs.InplaceEditor = Class.create();
ngs.InplaceEditor.prototype={
	
  initialize: function(inplaceClass, url){
  	var arr = document.getElementsByClassName(inplaceClass);
		var options = {
										okButton: false,
										cancelLink: false,
										submitOnBlur: true,
										formId: "profiles_form"
									};
		for(var i=0; i< arr.length; i++){
			var elem = arr[i];
			var inplaceSpan = elem.getElementsByTagName("span")[0];
			var inputs = elem.getElementsByTagName("input");
			var params = 	{};
			for(var j=0; j<inputs.length; j++){
				params[inputs[j].name] = inputs[j].value;
			}
			
			options.callback = function(inplaceSpan, params, form, value){
																			params[inplaceSpan.id] = value;
																			return params;
																		}.bind(this, inplaceSpan, params);
			new Ajax.InPlaceEditor(inplaceSpan , url, options);
		}		
  }
	
	
		
};