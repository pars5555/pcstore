/**
 * @fileoverview
 * @class core class of the framework which is responsible for ajax requests and delivery of responses
 * work with loads and actions which are for supporting same structure as backend has
 * @author  Vahagn Sookiasian vahagnsookaisyan@gmail.com
 * @version 1.0
 */

ngs.AjaxLoader = Class.create();
ngs.AjaxLoader.prototype = {
	/**
	 * Constructor of the class, does main initialization of the class
	 *
	 * @param  baseUrl  base url of the server, used as a basis for creating request urls to the server side actions and loads
	 * @param  indicator html element which is becoming visible during the ajax request and then unvisible,it is useful for showing animated gif indicators
	 * @param  loadManager isnstance of the load manager class
	 * @param  defaultParams default parameters, will be sent to the serever during each request
	 * @see
	 */

	initialize: function(baseUrl, indicator, loadManager, defaultParams) {
		this.baseUrl = baseUrl;
		this.isSecure = false;
		this.indicator = $(indicator);
		Element.hide(this.indicator);
		this.loadManager = loadManager;
		this.defaultParams = defaultParams;
		this.listeners = new Array();
		this.bodyClass = "";
		this.enableLoadtracking();
	},
	/**
	 * Method which should be called after creating ajaxLoader's object, starts load tracking using LoadLanager.
	 *
	 * @param  loadFactory  instance of loadFactory class
	 * @param  breadCrumbManager instance of the breadcrumb manager, this is optional parameter
	 * @see
	 */
	start: function(loadFactory, breadCrumbManager) {
		if (this.loadManager) {
			this.loadManager.start(loadFactory, breadCrumbManager);
		}
	},
	/**
	 * Method is usefull for setting server side package for the loads and actions, mainly used internaly by the framewor
	 *
	 * @param  package  package of the load ot action
	 * @see
	 */
	setPackage: function(package) {
		this.package = package;
	},
	/**
	 * Method is usefull for getting server side package for the loads and actions, mainly used internaly by the framewor
	 * @return package package of the load or action
	 * @see
	 */
	getPackage: function() {
		return this.package;
	},
	/**
	 * Method is for enabling or disableing https requests, mainly used internaly by the framework
	 *
	 * @param  isSecure  indicates protocol type: true: https, false: http
	 * @see
	 */
	setSecure: function(isSecure) {
		this.isSecure = isSecure;
	},
	/**
	 * Method for computing request URLs depending on the current security level, baseUrl, package and command, mainly used internaly by the framework,
	 *
	 * @param  command  htto name of the load or action: SomeLoad: some, SomeAction: do_some
	 * @return computedUrl computed URL of the request
	 * @see
	 */
	computeUrl: function(command) {
		var http = "http://";
		if (window.location.protocol === "https:") {
			http = "https://";
		}
		return http + this.baseUrl + this.package + "/" + command;
	},
	/**
	 * Method for sending ajax requests for sending actions, mainly used internaly by the framwork
	 * after sending request sets current security level to the false
	 * @param  url  URL of the request, which will be used as a command for computiting final URL.
	 * @param  params  http parameters of the requests, will be added to the default params
	 * @param  onComplete  callback function, which will be called after request is completed
	 * @param  onError  callback function, which will be called after request is returned Error
	 * @see
	 */
	request: function(url, params, onComplete, onError) {
		Object.extend(params, this.defaultParams);
		var options = {
			parameters: params,
			onCreate: this.showIndic.bind(this),
			onComplete: this.hideIndic.bind(this, onComplete),
			on403: this.hideIndic.bind(this, onError)
		};
		new Ajax.Request(this.computeUrl(url), options);
		this.isSecure = false;
	},
	/**
	 * Method for sending ajax requests for sending loads, after getting load response, it updates coresponding container with the response, mainly used internaly by the framwork
	 * after sending request sets current security level to the false
	 * @param  url  URL of the request, which will be used as a command for computiting final URL.
	 * @param  dest  container which should be updated, by the response text of the request
	 * @param  params  http parameters of the requests, will be added to the default params
	 * @param  onComplete  callback function, which will be called after request is completed
	 * @param  replace  indicates should the container itself be replaced(true), or should be replaced it's content(false), usefull for creationg pagginations
	 * @see
	 */
	updateContent: function(url, dest, params, onComplete, onError, replace) {
		Object.extend(params, this.defaultParams);
		//--creating temporary wrapper for the container, which should be replaced by the response content
		//--after updating wrappers content with the new container(after request), wrapper will be removed
		if (replace) {
			dest = $(dest);
			this.wrapper = document.createElement("div");
			var parent = dest.parentNode;
			parent.insertBefore(this.wrapper, dest);
			Element.remove(dest);
			this.wrapper.appendChild(dest);
			this.replace = true;
			this.dest = dest;
			dest = this.wrapper;
		}

		var options = {
			evalScripts: true,
			parameters: params,
			onCreate: this.showIndic.bind(this),
			onComplete: this.hideIndic.bind(this, onComplete),
			on403: this.hideIndic.bind(this, onError)
		};
		new Ajax.Updater({success: dest, failure: ''}, this.computeUrl(url), options);
		this.isSecure = false;
	},
	/**
	 * Method for sending loads, does main operations for load management, mainly used internaly by the framwork
	 * after sending the ajax request calls all the registrated load listeners, and passes to the load's shortcut
	 * if load object uses HTTP Get method, its before load funtion will be called
	 * @param  loadObj  instance of the Load class, which is clone of the server side loads
	 * @param  params  http parameters of the requests, will be added to the default params
	 * @param  replace  indicataes should be load's container be replaced itself(true), or should be replaced it's content(false)
	 * @param  dynContainer  container which can be set dynamicaly, ie. not the container which is hardcoded within the load class
	 * @see
	 */
	load: function(loadObj, params, replace) {
		if (this.isPageSecure() == loadObj.isSecure()) {
			this.setSecure(loadObj.isSecure());
			this.setPackage(loadObj.getPackage());
			loadObj.beforeLoad();
			var loadParams = loadObj.getParams();
			params = params ? params : {};
			Object.extend(loadParams, params);
			var container = loadObj.getComputedContainer(replace);
			this.updateContent(loadObj.getUrl(), container, loadParams, this.afterLoad.bind(this, loadObj, replace), this.onError.bind(this, loadObj), replace);
		}
		else {
			if (this.loadManager && this.loadTracking) {
				this.loadManager.setLoad(loadObj);
			}
			var http = "http://";
			if (loadObj.isSecure()) {
				http = "https://";
			}
//---redirecting the page with inversed protocol, for having access to send ajax request			
			var newUrl = http + location.host + location.pathname + location.hash;
			location = newUrl;
		}


		for (var i = 0; i < this.listeners.length; i++) {
			this.listeners[i](loadObj.getShortCut(), loadObj);
		}
	},
	/**
	 * Method which is being called after load's ajax request is done, mainly used internaly by the framwork
	 * it is possible to set "bodyClass" hidden input into the response, and html body's css class will be updated by the value of that input, used for doing css corrections
	 * old class will be updated by the new one. If load needs to have pagging it will be initializated by this method, method also tracks url, for having URL masking
	 * @param  loadObj  instance of the Load class, which is clone of the server side loads
	 * @param  replace  indicataes should be load's container be replaced itself(true), or should be replaced it's content(false)
	 * @param transport  ajax request's transport parameter
	 * @see
	 */
	afterLoad: function(loadObj, replace, transport) {
		if (!replace) {
			loadObj.initPagging();
		}
		loadObj.afterLoad(transport);
		if (this.loadManager && this.loadTracking) {
			this.loadManager.setLoad(loadObj);
		}
		//--setting body class
		var bodyClass = "";
		if ($("bodyClass")) {
			bodyClass = $("bodyClass").value;
		}

		Element.removeClassName(document.body, this.bodyClass);
		Element.addClassName(document.body, bodyClass);
		this.bodyClass = bodyClass;
	},
	/**
	 * Method for sending actions, does main operations for action management, mainly used internaly by the framwork
	 * after sending the ajax request calls all the registrated load listeners, and passes to the load's shortcut
	 * @param  actionObj  instance of the Action class, which is clone of the server side Actions
	 * @see
	 */
	action: function(actionObj) {
		if (this.isPageSecure() == actionObj.isSecure()) {
			this.setSecure(actionObj.isSecure());
			this.setPackage(actionObj.getPackage());
			actionObj.beforeAction();
			this.request(actionObj.getUrl(), actionObj.getParams(), this.afterAction.bind(this, actionObj), this.onError.bind(this, actionObj));
		}
	},
	/**
	 * Method which is being called after action's ajax request is done, mainly used internaly by the framwork
	 * it calls itself action's afterAction method
	 * @param  actionObj  instance of the Action class, which is clone of the server side actions
	 * @param transport  ajax request's transport parameter
	 * @see
	 */
	afterAction: function(actionObj, transport) {
		if (transport.responseText === 'window.location.reload')
		{
			window.location.reload(true);
		} else {
			actionObj.afterAction(transport);
		}
	},
	/**
	 * Method which is being called after ajax request returns error
	 * @param  requestObj  instance of the Action class, which is clone of the server side actions
	 * @param transport  ajax request's transport parameter
	 * @see
	 */
	onError: function(requestObj, transport) {
		var errArr = eval(transport.responseText)[0];
		requestObj.setError(true);
		requestObj.onError(errArr);
	},
	/**
	 * shows ajax request indicator
	 * @see
	 */
	showIndic: function() {
		Element.show(this.indicator);
	},
	/**
	 * First method which is being called after action's ajax request is done, mainly used internaly by the framwork
	 * it hides the indicator, removes temporary container wrappers if it is needed
	 * @param  onUpdateComplete  callback function which will be called after main operations are completed
	 * @param transport  ajax request's transport parameter
	 * @see
	 */
	hideIndic: function(onUpdateComplete, transport) {
		if (this.replace) {
			for (var i = 0; i < this.wrapper.childNodes.length; i++) {
				var child = this.wrapper.childNodes[i];
				Element.remove(child);
				this.wrapper.parentNode.insertBefore(child, this.wrapper);
				--i;
			}
			Element.remove(this.wrapper);

		}
		Element.hide(this.indicator);
		if (typeof(onUpdateComplete) !== "undefined") {
			onUpdateComplete(transport);
		}
		this.replace = false;
	},
	/**
	 * Returns the base URL of the ajax loader, method mainly used internaly
	 * @return baseUrl base URL of the ajax loader object
	 * @see
	 */
	getBaseUrl: function() {
		return this.baseUrl;
	},
	/**
	 * Returns the indicator's html element, method mainly used internaly
	 * @return indicator indicator's html element
	 * @see
	 */
	getIndicator: function() {
		return this.indicator;
	},
	/**
	 * Returns is the main page secure(https) or not
	 * @return true: if page is secure, false: if not:)
	 * @see
	 */
	isPageSecure: function() {
		if (window.location.protocol === "https:") {
			return true;
		}

		return false;
	},
	/**
	 * Adds load listener, which is callback function with the parameter for specifing load's shortcut
	 * @param  listener  callback function for thr listener
	 * @see
	 */
	addListener: function(listener) {
		this.listeners.push(listener);
	},
	/**
	 * Enables load tracking, load tracking is used for mapping load's shortcut to the URL, after "#" simbol
	 * Load tracking is used for enabling "Prev/Next/Refresh" buttons of the browser for ajax requests
	 * @see
	 */
	enableLoadtracking: function() {
		this.loadTracking = true;
	},
	/**
	 * Disables load tracking
	 * @see
	 */
	disableLoadtracking: function() {
		this.loadTracking = false;
	}

};