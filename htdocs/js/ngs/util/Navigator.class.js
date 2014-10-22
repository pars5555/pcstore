ngs.Navigator=Class.create();
ngs.Navigator.prototype={
  
  initialize: function(navigPan, loadObj, activeClass){
  	this.navigGroup = new Array();
//  	this.ajaxLoader = ajaxLoader;
  	this.loadObj = loadObj;
  	this.navigPan = navigPan;
  	this.prevLink = Element.findChildren(this.navigPan, "f_prev", true, "a")[0];
  	this.nextLink = Element.findChildren(this.navigPan, "f_next", true, "a")[0];
  	this.firstLink = Element.findChildren(this.navigPan, "f_first", true, "a")[0];
  	this.lastLink = Element.findChildren(this.navigPan, "f_last", true, "a")[0];
  	this.pageNumLinks = Element.findChildren(this.navigPan, "f_num", true, "a");
  	this.activeClass = activeClass;
  	this.curPage = 1;
  	if(this.prevLink){
	  	this.prevLink.onclick=this.goPrev.bind(this);
	  }
	  if(this.nextLink){
  		this.nextLink.onclick=this.goNext.bind(this);
  	}
  	if(this.firstLink){
  		this.firstLink.onclick=this.goFirst.bind(this);
  	}
  	if(this.lastLink){
  		this.lastLink.onclick=this.goLast.bind(this);
  	}
    this.hideElements();
    this.initParams();
    
    this.pagesToShow = Math.floor(this.pageNumLinks.length/2);
    
		this.buildBar();
  },
  
  addGroup: function(navigGroup){
  	this.navigGroup = navigGroup;
  },
  
  hideElements: function(){
  	
  	if(this.prevLink){
  		Element.hide(this.prevLink);
  	}
  	if(this.nextLink){
    	Element.hide(this.nextLink);
    }
    if(this.firstLink){
    	Element.hide(this.firstLink);
    }
    if(this.lastLink){
    	Element.hide(this.lastLink);
    }
    
    for(i=0; i<this.pageNumLinks.length; i++){
      Element.hide(this.pageNumLinks[i]);
      this.pageNumLinks[i].removeClassName(this.activeClass);
      this.pageNumLinks[i].onclick="";      
    }
  },
  
  initParams: function(){
  	var paramElems = Element.findChildren(this.navigPan, "f_param", true, "input");
  	this.params = {};
  	for(i=0; i<paramElems.length; i++){
      this.params[paramElems[i].name] = paramElems[i].value;
    }
    this.pageCount = parseInt(this.params["pageCount"]);
    this.curPage = parseInt(this.params["offset"]);
  },
  
  buildBar: function(){
    this.hideElements();
    
    if(this.curPage>1){
    	if(this.prevLink){
    		Element.show(this.prevLink);
    	}
    	if(this.firstLink){
    		Element.show(this.firstLink);
    	}
    }
    if(this.curPage<this.pageCount){
    	if(this.nextLink){
    		Element.show(this.nextLink);
    	}
    	if(this.lastLink){
    		Element.show(this.lastLink);
    	}
    }
 
    var j = (this.curPage - this.pagesToShow)>0?(this.curPage - this.pagesToShow):1;
		var pagesLimit = this.pageNumLinks.length ;
		if(this.curPage<(this.pageNumLinks.length - this.pagesToShow)){
			pagesLimit = this.curPage+this.pagesToShow;
		}
    for(var i=0; i < pagesLimit && j <= this.pageCount; i++, j++){
    	var lnk=this.pageNumLinks[i];
    	if(j!=this.curPage){    		
    	  lnk.onclick=this.changePage.bind(this, j);
    	  Element.update(lnk, j+"");
    	  Element.show(lnk);
      }
      else{
    	  Element.update(lnk, j+"");
    	  lnk.addClassName(this.activeClass);
    	  Element.show(lnk);
      }
    }
    
  },
  
  goPrev: function(){
  	if(this.curPage>1){
  	  this.changePage(this.curPage-1);
    }
  },
  
  goNext: function(){
  	if(this.curPage<this.pageCount){
  	  this.changePage(this.curPage+1);
    }
  },
  
  goFirst: function(){
  	if(this.curPage>1){
  	  this.changePage(1);
    }
  },
  
  goLast: function(){
  	if(this.curPage<this.pageCount){
  	  this.changePage(this.pageCount);
    }
  },  
  
  changePage: function(page){
  	page = parseInt(page);
  	this.params.offset = page;
		this.params._navigator = true;
	  this.loadObj.load(this.params, true);

  	if(!this.navigGroup.length){
	  	this.curPage = parseInt(page);
	  	this.buildBar();
	  }
	  
  	for(var i=0; i < this.navigGroup.length; i++){
			this.navigGroup[i].curPage = page;
  		this.navigGroup[i].buildBar();
  	}
 
  },
  
  refresh: function(){
  	this.changePage(this.curPage);
  },
  
  getParams: function(){
  	return this.params;
  }
  
};