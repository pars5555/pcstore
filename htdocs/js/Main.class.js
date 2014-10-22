Object.extend(ngs, {
	ajaxLoader: null,
	pageManager: null,
	toolbarManager: null,
	loadtracking: true,
		
	setCurrentmanager: function(currentManager){
		this.currentManager = currentManager;	
	},
	
	load: function(loadName, params, dynContainer){
		var load = this.getLoad(loadName);
		if(!params || typeof(params)=="undefined"){
			params = {};
		}
  	load.setParams(params);
  	load.setDynContainer(dynContainer);
  	load.load();    
	},
	
	action: function(actionName, params){
		var action = this.actionFactory.getAction(actionName);
  	action.setParams(params);
  	action.action();
	},
	
	getLoad: function(loadName){
		return this.loadFactory.getLoad(loadName);
	},
	
	main: function(){
		this.defaultParams =  {};
//		this.defaultParams.userId = false;
//		if($("defaultUserId") && $("defaultUserId").value != ""){
//			this.defaultParams.userId = $("defaultUserId").value;
//		}
		this.loadManager = new ngs.LoadManager(this, null, new ngs.UrlFormater());
		this.ajaxLoader = new ngs.AjaxLoader(SITE_URL+"/dyn/", "ajax_loader", this.loadManager, this.defaultParams);
		this.loadFactory = new ngs.LoadFactory(this.ajaxLoader);
		this.actionFactory = new ngs.ActionFactory(this.ajaxLoader);
		this.ajaxLoader.start(this.loadFactory, null);			
		ngs.CustomerAlertsManager.init(this.ajaxLoader);
		ngs.PcConfiguratorManager.init(this.ajaxLoader);
		
		if($("sidebar")){
			this.toolbarManager = new ngs.ToolbarManager("sidebar", "overflow", "active", this.ajaxLoader);
		}		
		if($("disableLoadTracking")){
				this.ajaxLoader.disableLoadtracking();
		}
		
		if(!this.loadManager.hasInitialLoads()){
			this.runInitialLoad();
		}		

	},
	
	nestLoad: function(loadName, params){
		var load = this.loadFactory.getLoad(loadName);
		if (params) {
			load.setParams(params);
		}
		this.ajaxLoader.afterLoad(load);
		load.initPagging();
		return load;		
	},
	
	runInitialLoad: function(){
		var initialLoadElem = $("initialLoad");
		if(initialLoadElem){
			this.nestLoad(initialLoadElem.value);
		}
	},
	
	getDefaultParams: function(){
		return this.defaultParams;
	}
	
});


window.onload = ngs.main.bind(ngs);