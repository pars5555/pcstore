/**
 * @fileoverview 
 * @class parrent class for all actions
 * @author  Vahagn Sookiasian vahagnsookaisyan@gmail.com
 * @version 1.0
 */
ngs.AbstractAction = Class.create();
ngs.AbstractAction.prototype={

/**
 * Constructor of the class, does main initialization of the class
 * 
 * @param  shortCut  shortcut for the load, shorcuts are used for calling the load via ngs class,
 * and also they are being mapped into to the URL for URL masking
 * @param  package package of the server side load
 * @param  ajaxLoader instance of the ajaxLoader class
 * @see
 */			
  initialize: function(shortCut, package, ajaxLoader){
  	this.shortCut = shortCut;
  	this.package = package;
  	this.ajaxLoader = ajaxLoader;
  	this.wasError = false;
  	this.params = {};
  },

/**
 * The main method, which invokes action operation, i.e ajax call to the backend
 * 
 * @see
 */    
  action: function(){
  	this.ajaxLoader.action(this);
  },
  
/**
 * Method returns security level of the action, by default it is false(http), children classes can override it
 * 
 * @return  security level of the action
 * @see
 */			
	isSecure: function(){
		if (window.location.protocol === "https:") {
			return true;
		}		
		return false;
	},

/**
 * Returns shortcut of the action. Shortcuts are used in the URLs for tracking actions(URL masking support)
 *  and also for the quick access to the instance of the action object, via ngs class <b>action</b> method
 * @return  The shortcut of the action
 * @see
 */		
	getShortCut : function(){
		return this.shortCut;
	},

/**
 * Returns the server side package of the action, if there are included packages, "_" delimiter should be used
 * 
 * @return  The server side package of the action
 * @see
 */	
	getPackage: function(){
		return this.package;
	},

/**
 * HTTP request method of the action, by default it is POST, Children  of the AbstractAction class 
 * can override this method, action will be tracked via URL masking only if HTTP GET is used
 * @return  HTTP request method of the action
 * @see
 */		
	getMethod: function(){
		return "POST";
	},

/**
 * Abstract function, Child classes should be override this function, 
 * and should return the name of the server action, formated with framework's URL nameing convention
 * @return The name of the server action, formated with framework's URL nameing convention
 * @see
 */ 	
	getUrl: function(){
		return "";
	},

/**
 * Method is used for setting action's http parameters
 * 
 * @param  params  The http parameters of the action, which will be sent to the server side action
 * @see
 */		
	setParams: function(params){
		this.params = params;
	},
	
	/**
 * Method is used for setting error indicator if it was sent from the server. Intended to be used internally
 * 
 * @param  wasError boolean parameter, shows existence of the error
 * @see
 */		
	setError: function(wasError){
		this.wasError = wasError;
	},

/**
 * Method returns Action's http parameters
 * 
 * @return  http parameters of the action
 * @see
 */		
	getParams: function(){		
		return this.params;
	},

/**
 * Function, which is called before ajax request of the action. Can be overridden by the children of the class
 * 
 * @see
 */		
	beforeAction: function(){
		
	},

/**
 * Function, which is called after action is done. Can be overridden by the children of the class
 * @param transport  Object of the HttpXmlRequest class
 * @see
 */		
	afterAction: function(transport){
		if(transport.status > 200 && transport.status < 300){
			if(this["onException"+transport.status]){
				this["onException"+transport.status]();
			}
		}
	},
	
	/**
 * Function, which is called after load is returned exception. Can be overridden by the children of the class
 * @errorArr  Array of error messages, with key values pairs: [error => {code: 1, message: 'some message'}]
 * @see
 */	
	onError: function(errorArr){		
                ngs.DialogsManager.closeDialog(583, "<div>" +errorArr[-1].message + "</div>");
                
	},

/**
 * Corresponds to the serverside Action's redirectToLoad function, i.e if action returns some load content
 * corresponding load's html container will updated with it and load's afterLoad method will be called.
 * @param loadObj  Object of the load, to which action will be redirected
 * @param responseText  response content which is returned by the server side load, to which action was
 * redirected
 * @see
 */		
	redirectToLoad: function(loadObj, responseText, redirectOnError){
		if(this.wasError && !redirectOnError){
			return;
		}
		var container = loadObj.getComputedContainer(false);
		var content = responseText;
		Element.update(container, content);
		if (this.params) {
			loadObj.setParams(this.params);
		}
//--additing this logic to not allow to add the actions params into the load's url		
		loadObj.getMethod = this.getMethod;
//-----------------------------------------------
		this.ajaxLoader.afterLoad(loadObj, true);
		loadObj.initPagging();		
	}

};