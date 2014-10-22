ngs.LiveSearchField = Class.create();
ngs.LiveSearchField.prototype={
	
  initialize: function(inputId, container, valueCollector, ajaxLoader, url, afterUpdate, isSecure){
  	this.isSecure = isSecure? isSecure: false;
  	ajaxLoader.setSecure(this.isSecure);
  	this.baseUrl = ajaxLoader.computeUrl(url);
  	this.ajaxLoader = ajaxLoader;
  	this.valueCollector = valueCollector;
  	this.url = url;
  	this.afterUpdate = afterUpdate;
  	this.container = $(container);
  	var indicator = ajaxLoader.getIndicator();
  	var options = {
  									callback: this.getQueryString.bind(this),
  									minChars: 0,
										indicator: indicator
  								};
  	this.autoCompliter = new Ajax.Autocompleter(inputId, container, this.baseUrl, options);
  	this.autoCompliter.hide = Prototype.emptyFunction;
  	this.autoCompliter.onClick = Prototype.emptyFunction;
  	this.autoCompliter.onHover = Prototype.emptyFunction;
  	this.autoCompliter.options.minChars = 0;
  	var updateElement = this.autoCompliter.updateChoices.bind(this.autoCompliter);
  	this.autoCompliter.updateChoices = function(selectedElement){
  		this.autoCompliter.hasFocus = true;
  		updateElement(selectedElement);
  		if(this.afterUpdate != "undefined"){
  			afterUpdate();
  		}
  	}.bind(this);
  	this.container.show();
  	this.container.style.position = "relative";
  	this.container.hide = Prototype.emptyFunction;
  },
	
	getQueryString: function(){
		var paramsArr = this.valueCollector.getParams();
		var paramsStr = "";
		var delim = "";
		for(key in paramsArr){
			paramsStr += delim+key+"="+paramsArr[key];
			delim = "&";
		}
		
		return paramsStr;
	}

};