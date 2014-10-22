/**
* listener : function(location, oldLocation)
*/

ngs.UrlFormater = Class.create({
	
  initialize: function(){
  
  },
  
  toObject: function(str){
  	var obj = [];
  	if(str==""){
  		return obj;
  	}
  	var strArr = str.split("/");  	
  	obj[0] = 	{
								s: strArr[0]  								
  						};
  						
		if(strArr.length == 1){
			return obj;
		}
  	var p = {};
  	var attCount = 0;
  	for(var i=1; i<strArr.length; i++){
  		if(strArr[i]==""){
  			break;
  		}
  		p[strArr[i]] = strArr[++i];
  		++attCount;
  	}
  	if(attCount == 0){
			return obj;
		}
  	obj[0].p = p;
  	//[{s:'albphlistpage',p:{albumId:'11'}}]
  	return obj;
  },
  
  toString: function(obj){
  	var load = obj[0].load;
  	var str = load.getShortCut()+"/";
  	var params = load.getParams();
  	for(key in params){
  		str += key+"/";
			str += params[key]+"/";
  	}

  	return str;
  }
  
});