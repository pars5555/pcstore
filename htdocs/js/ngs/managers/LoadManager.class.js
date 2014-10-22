ngs.LoadManager = Class.create();
ngs.LoadManager.prototype={
	
  initialize: function(ajaxLoader, breadCrumbManager, urlFormater){
  	this.ajaxLoader = ajaxLoader;
  	this.breadCrumbManager = breadCrumbManager;
  	this.loadFactory = null;
  	this.urlObserver = new ngs.UrlObserver(this.onUrlChange.bind(this));
  	this.urlFormater = urlFormater;
  	this.initialLoads = false;
  },
  
  start: function(loadFactory, breadCrumbManager){
  	this.loadFactory = loadFactory;
  	this.breadCrumbManager = breadCrumbManager;
  	return this.initLoads();
  },
  
  initLoads: function(){
  	var loadStr = this.urlObserver.getUrl();
  	var loadObj = this.toObject(loadStr);
  	if(this.breadCrumbManager!=null){
  		this.breadCrumbManager.init();
  	}
  	
  	if(!loadObj || !loadObj.length){
  		return false;
  	}
  	for(var i = 0; i< loadObj.length; i++){
  		var obj = loadObj[i];
	  	var load = this.loadFactory.getLoad(obj.s);
	  	load.setParams(obj.p);	  	
	  }
  	
		this.runLoads(loadObj);
		this.initialLoads = true;
		return true;
  },
  
  hasInitialLoads: function(){
  	return this.initialLoads;
  },
  
  setLoad: function(load){
  	var method = load.getMethod();
  	if(method.toUpperCase() == "POST"){
  		return;
  	}
  	var loadStr = this.urlObserver.getUrl();
  	var loadObj = this.toObject(loadStr);
  	var curLoads = this.computeObject(loadObj);
  	var loads = ngs.DeepnessArranger.arrange(this.createObj(load), curLoads);
  	if(this.breadCrumbManager!=null){
	  	this.breadCrumbManager.init();
	  	this.breadCrumbManager.runLoads(loads);
	  }
	  var loadStr = this.toString(loads);
	  this.urlObserver.setUrl(loadStr);
  },
  
  onUrlChange: function(oldLocation, location){
  	if(this.breadCrumbManager!=null){
  		this.breadCrumbManager.init();
  	}
  	var loadObj = this.toObject(location);
  	this.runLoads(loadObj);  	  	
  },
  
  runLoads: function(objs){
  	if(!objs){
  		return;
  	}
  	for(var i = 0; i< objs.length; i++){
  		var obj = objs[i];
	  	var load = this.loadFactory.getLoad(obj.s);
	  	load.setParams(obj.p);
	  	load.load();
	  	//this.breadCrumbManager.setPages(load.getBreadCrumbs());
	  	if(obj.a && obj.a.length > 0){
	  		this.runLoads(obj.a);
	  	}
	  }
  },
	
	toString: function(obj){
		var str = this.urlFormater.toString(obj);
		return str;
//----------------------------------------------		
		var str = "[";
		var delim = "";
		for(var i=0; i<obj.length; i++){
			var load = obj[i].load;
			str+= delim;
			str+= "{";
			str+= "s:"+"'"+load.getShortCut()+"'";
			var method = load.getMethod();
			if(method.toUpperCase() == "GET"){
				str+= ",p:";
				str+= "{";
				var delim1 = "";
				var params = load.getParams();
				for(var key in params){
					str+= delim1;
					str+= key+":'"+params[key]+"'";
					delim1 = ",";
				}
				str+= "}";
			}			

			if(obj[i].elems.length > 0){
				str+= ",a:";
				//str+= "[";
				//var delim3 = "";
				//for(var j=0; j<obj[i].elems.length; j++){
					//str+= delim3;
					//str+= this.toString(obj[i].elems[j]);
					str+= this.toString(obj[i].elems);
					//delim3 = ",";
				//}
				//str+= "]";
			}
			
			str+= "}";
			delim = ",";
		}		
		str+= "]";
		
		return str;
	},
	
	toObject: function(str){
		var obj = this.urlFormater.toObject(str);
		return obj;
//----------------------------------------------	
		var obj = eval(str);
		if(typeof(obj) == "undefined"){
			obj = [];
		}
		return obj;
	},
	
	computeObject: function(objs){
		if(!objs){
  		return [];
  	}
  	var newObjs = [];
  	for(var i = 0; i< objs.length; i++){
  		var obji = objs[i];
  		var load = this.loadFactory.getLoad(obji.s);
	  	load.setParams(obji.p);
  		var obj = {
									elem: $(load.getContainer()),
									elems: [],
									load: load
								};
  		
	  	if(obji.a && obji.a.length > 0){
	  		obj.elems = this.computeObject(obji.a);
	  	}
	  	newObjs.push(obj);
	  }
	  return newObjs;
	},
	
	createObj: function(load){
		var obj = {
								elem: $(load.getContainer()),
								elems: [],
								load: load
							};
		
		return obj;
	}
};

//[{s:short1,p:[param:value],a:[{s:short2,p:[param:value],{s:short1,p:[param:value],a:[short2]}]