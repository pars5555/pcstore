/**
 * listener : function(location, oldLocation)
 */
ngs.UrlChangeEventObserver = {
	location: null,
	listeners: null,
	urlChangedByProgram: null,
	initialize: function(listener) {
		this.urlChangedByProgram = false;
		this.listeners = new Array();
		this.addListener(listener);
		this.location = window.location.href.sub(window.location.protocol + "//" + window.location.host + "/", '');
		this.timeout();
	},
	addListener: function(listener) {
		this.listeners.push(listener);
	},
	timeout: function() {
		if (window.location.href.sub(window.location.protocol + "//" + window.location.host + "/", '') !== this.location) {
			var newLocation = window.location.href.sub(window.location.protocol + "//" + window.location.host + "/", '');
			for (var i = 0; i < this.listeners.length; i++) {
				this.listeners[i](this.location, newLocation);
			}
			this.location = newLocation;
			this.urlChangedByProgram = false;
		}
		setTimeout(this.timeout.bind(this), 100);
	},	
	setFakeURL: function(newUrl) {

		if (typeof history !== 'undefined' && typeof (history.pushState) === "function") {
			this.urlChangedByProgram = true;
			history.pushState({}, "", newUrl);            
		}
	}
};
