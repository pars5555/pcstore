/**
 * @fileoverview 
 * @class parrent class for all loads
 * @author  Vahagn Sookiasian vahagnsookaisyan@gmail.com
 * @version 1.0
 */
ngs.AbstractLoad = Class.create();
ngs.AbstractLoad.prototype = {
	TRANSLATABLE: 1,
	NOT_TRANSLATABLE: 2,
	INNER: 3,
	DEFAULT: 4,
	/**
	 * Constructor of the class, does main initialization of the class
	 * 
	 * @param  shortCut  shortcut for the load, shorcuts are used for calling the load via ngs class,
	 * and also they are being mapped into to the URL for URL masking
	 * @param  package package of the server side load
	 * @param  ajaxLoader instance of the ajaxLoader class
	 * @see
	 */
	initialize: function(shortCut, package, ajaxLoader) {
		this.shortCut = shortCut;
		this.package = package;
		this.ajaxLoader = ajaxLoader;
		this.dynContainer = null;
		this.params = {};
		this.pagging = false;
	},
	/**
	 * The main method, which invokes load operation, i.e. ajax call to the backend and then updates corresponding container with the response
	 * 
	 * @param  params  http parameters which will be sent to the serverside Load, these parameters will be added to the ajax loader's default parameters
	 * @param  replace indicates should container be replaced itself(true) with the load response or should be replaced container's content(false)
	 * @see
	 */
	load: function(params, replace) {
		this.ajaxLoader.load(this, params, replace);

	},
	/**
	 * Method sets dynamic container to the load. Dynamic container replaces Load's hardcoded static container
	 * 
	 * @param  dynContainer  dynamic conatiner of the load
	 * @see
	 */
	setDynContainer: function(dynContainer) {
		this.dynContainer = dynContainer;
	},
	/**
	 * Abstract function, currently isn't in use, was designed for the using with breadcrumb manager
	 * @return The name of the server load
	 * @see
	 */
	getName: function() {
		return "";
	},
	/**
	 * Method returns security level of the load, by default it is false(http), child classes can override it
	 * 
	 * @return  security level of the load
	 * @see
	 */
	isSecure: function() {
		if (window.location.protocol === "https:") {
			return true;
		}
		return false;
	},
	/**
	 * Returns shortcut of the load. Shortcuts are used in the URLs for tracking loads(URL masking support)
	 *  and also for the quick access to the instance of the load object, via ngs class <b>load</b> method
	 * @return  The shortcut of the load
	 * @see
	 */
	getShortCut: function() {
		return this.shortCut;
	},
	/**
	 * Abstract method for returning container of the load, Children of the AbstractLoad class should override this method
	 * 
	 * @return  The container of the load.
	 * @see
	 */
	getContainer: function() {
		return "";
	},
	/**
	 * In case of the pagging framework uses own containers, for indicating the container of the main content,
	 * without pagging panels
	 * @return  The own container of the load
	 * @see
	 */
	getOwnContainer: function() {
		return "";
	},
	/**
	 * Returns the server side package of the load, if there are included packages, "_" delimiter should be used
	 * 
	 * @return  The server side package of the load
	 * @see
	 */
	getPackage: function() {
		return this.package;
	},
	/**
	 * HTTP request method of the load, by default it is POST, Children  of the AbstractLoad class 
	 * can override this method, Load will be tracked via URL masking only if HTTP GET is used
	 * @return  HTTP request method of the load
	 * @see
	 */
	getMethod: function() {
		return "POST";
	},
	/**
	 * Abstract function, Child classes should be override this function, 
	 * and should return the name of the server load, formated with framework's URL nameing convention
	 * @return The name of the server load, formated with framework's URL nameing convention
	 * @see
	 */
	getUrl: function() {
		return "";
	},
	/**
	 * Method is used for setting load's http parameters
	 * 
	 * @param  params  The http parameters of the load, which will be sent to the server side load
	 * @see
	 */
	setParams: function(params) {
		this.params = params;
	},
	/**
	 * Method is used for setting error indicator if it was sent from the server. Intended to be used internally
	 * 
	 * @param  wasError boolean parameter, shows existence of the error
	 * @see
	 */
	setError: function(wasError) {
		this.wasError = wasError;
	},
	/**
	 * Method returns Load's http parameters
	 * 
	 * @return  http parameters of the load
	 * @see
	 */
	getParams: function() {
		if (typeof(this.params) === "undefined") {
			this.params = {};
		}
		return this.params;
	},
	/**
	 * Abstract method for working with breadcrumb manager, which functionallity should be refactored
	 * 
	 * @return  breadcrumbs array in the format, which is required by the breadcrumb manager
	 * @see
	 */
	getBreadCrumbs: function() {
		return null;
	},
	/**
	 * Function, which is called before ajax request of the load. Can be overridden by the children of the class
	 * 
	 * @see
	 */
	beforeLoad: function() {

	},
	/**
	 * Function, which is called after load is done. Can be overridden by the children of the class
	 * @transport  Object of the HttpXmlRequest class
	 * @see
	 */
	afterLoad: function(transport) {

	},
	/**
	 * Function, which is called after load is returned exception. Can be overridden by the children of the class
	 * @errorArr  Array of error messages, with key values pairs: [error => {code: 1, message: 'some message'}]
	 * @see
	 */
	onError: function(errorArr) {
                ngs.DialogsManager.closeDialog(583, "<div>" +errorArr[-1].message + "</div>");
	},
	/**
	 * Should return array of the pagging containers(html elements which have inner elements corresponding to the Navigator formating rules).
	 * default return value is false, which means, load doesn't support pagging, children of the class can overide this method
	 * @return  array of the pagging containers
	 * @see
	 */
	getPaggings: function() {
		return false;
	},
	/**
	 * Method does initializations of the pagging, i.e. creates Navigator's instance.
	 * Method is being used internaly by the framework
	 * @see
	 */
	initPagging: function() {
		var paggings = this.getPaggings();
		if (!this.pagging && paggings) {
			this.navigGroups = new Array();
			for (var i = 0; i < paggings.length; i++) {
				this.navigGroups[i] = new ngs.Navigator(paggings[i], this, "currentPageNumber");
				this.navigGroups[i].addGroup(this.navigGroups);
			}
			this.pagging = true;
		}
	},
	/**
	 * Returns pagging parameters of the pagging which corresponds to the load.
	 * Method is being used internaly by the framework
	 * @return  pagging parameters
	 * @see
	 */
	getPaggingParams: function() {
		return this.navigGroups[0].getParams();
	},
	/**
	 * Returns the first navigator object of the paggings which corresponds to the load
	 * Method is being used internaly by the framework
	 * @return  The first navigator object of the paggings which corresponds to the load
	 * @see
	 */
	getNavigator: function() {
		return this.navigGroups[0];
	},
	getComputedContainer: function(replace) {
		var container;

		if (replace) {
			container = this.getOwnContainer();
		}
//		else if(this.dynContainer){//---it should work in this way, but because of previous bug we are going to comment this part for this project
//			container = this.dynContainer;			
//		}
		else {
			container = this.getContainer();
		}

		return container;
	},
	/**
	 * Removes leading and ending spaces
	 * 
	 * @param {Object} stringToTrim
	 */
	trim: function(stringToTrim)
	{
		if (stringToTrim != null)
			return stringToTrim.replace(/^\s+|\s+$/g, "");
		return "";
	},
	/** 
	 * Returns the translation level of the Load, there are 3 levels: 
	 * TRANSLATEBLE - load is being reloaded after changing the language
	 * NOT_TRANSLATABLE - last translatable load will not be reloaded after  calling a NOT TRANSLATABLE load
	 * INNER - this load doesn't affect to the language manager
	 * @return  The translation level of the load
	 * @see
	 */
	getTranslationLevel: function() {
		return this.NOT_TRANSLATABLE;
	},
    
    printFrame: function(id) {
        var frm = document.getElementById(id).contentWindow;
        frm.focus();// focus on contentWindow is needed on some ie versions
        frm.print();
        return false;
    },
    
     initTinyMCE: function(selector) {
        tinymce.init({
            selector: selector,
            menubar: false,
            statusbar: true,
            resize: true,
            theme: "modern",
            plugins: [
                "advlist link jbimages preview hr code",
                "textcolor table contextmenu paste insertdatetime searchreplace lists"
            ],
            toolbar: "fontselect fontsizeselect | undo redo | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link jbimages | preview media | code"
        });
    }

};