ngs.PageManager = Class.create();
ngs.PageManager.prototype={
	
  initialize: function(ajaxLoader, loadFactory){
  	this.ajaxLoader = ajaxLoader;
  	this.loadFactory = loadFactory;
  	this.dispatcher = "client";
  },
  
  	initPagging: function(page, cont, afterUpdate){
			var loaderOptions = {
														dispatcher: this.dispatcher,
														page: page,
														cont: cont,
														afterUpdate: afterUpdate
													};
			this.navigator = new ngs.Navigator(this.ajaxLoader, loaderOptions, "currentPage");
	}
};