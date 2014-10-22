/**
* listener : function(location, oldLocation)
*/

ngs.UrlObserver = Class.create();
ngs.UrlObserver.prototype={
	
  initialize: function(listener){  	
      ngs._isIELoaded = true;//--for fixing IE delay with iframe loading
  	if(Prototype.Browser.IE){
  		this.initIE();
  	}
  	
  	this.listeners = new Array();
  	this.addListener(listener);
  	this.cloneLocation();
  	new PeriodicalExecuter(this._observe.bind(this), 3);
  },
  
  addListener: function(listener){
  	this.listeners.push(listener);
  },
  
  _observe: function(){
  	if(!ngs._isIELoaded){
  		return;
  	}
  	var newHash = this.getUrl();
 		if(this.location.hash.substring(1) != newHash){
 			for(var i=0; i < this.listeners.length; i++){
 				this.listeners[i](this.location, newHash);
 				this.cloneLocation();
 			}
 		}
  },
  
  setUrl: function(url){
  	if(url == ""){
  		return;
  	}
  	window.location.hash = url;
  	this.cloneLocation();
  },
  
  getUrl: function(){  	
  	return window.location.hash.substring(1);
  },
  
  cloneLocation: function(){
  	this.location = {};
  	for(var i in window.location){
  		this.location[i] = window.location[i];
  	}
  },

//---For IE
  
  initIE: function(){
  	var url = this.getUrl();
  	this.initURL = false;
  	if(url != ""){
  		this.initURL = url;
  	}
  	this.IE_URL = location.protocol+"//"+SITE_URL+"/navig.php";
  	this.IE_iframe = document.createElement("iframe");
  	Element.hide(this.IE_iframe);
  	document.body.appendChild(this.IE_iframe);
  	this.IE_iframe.src = this.IE_URL+"?"+url;
  	this.setUrl = this.setUrlIE;
  	this.getUrl = this.getUrlIE;
  },
  
  setUrlIE: function(url){
  	if(url == ""){
  		return;
  	}
  	ngs._isIELoaded = false;
  	window.location.hash = url;
  	
  	this.IE_iframe.src = this.IE_URL+"?"+url;  	   
  	this.cloneLocation();
  },
  
  getUrlIE: function(){
  	if(this.initURL){
  		var u = this.initURL;
  		this.initURL = false;
  		return u;
  	}
  	var url = "";
  	var urlField = null;
  	var oDoc = this.IE_iframe.contentWindow || this.IE_iframe.contentDocument;
    if (oDoc.document) {
        oDoc = oDoc.document;
    }
  	if(urlField = oDoc.getElementById("url")){
  		url = urlField.value;
		  if(window.location.hash.substring(1) != url){
		  	window.location.hash = url;
		  }
  	}

  	return url;
  }
};