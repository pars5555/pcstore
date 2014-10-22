ngs.DeepnessArranger = {

	arrange: function(elem, elems, parentElem){
		this.elem = elem;
		parentElem = parentElem? parentElem: document.body;
		var wrap = 	{
									elems: elems
								};
		if(this.manageElems(wrap)){
			return wrap.elems;
		}

		if(this.manageNodes(parentElem, wrap)){
			return wrap.elems;
		}

		return false;
	},

	manageElems: function(wrap){
		if(!wrap.elems){
			return false;
		}
		for(var i=0; i<wrap.elems.length; i++){
			if(this.elem.elem == wrap.elems[i].elem){
				wrap.elems[i].elems = [];
				wrap.elems[i].load = this.elem.load;
				return true;
			}
			else{
				if(wrap.elems[i].elems){
					this.manageElems(wrap.elems[i].elems);
				}
			}
		}
		return false;
	},

	manageNodes : function(domNode, wrap){
		if(this.elem.elem == domNode){
			wrap.elems = [this.elem];
			return true;
		}

		for(var i=0; i<wrap.elems.length; i++){
			if(wrap.elems[i].elem == domNode){
				wrap = wrap.elems[i];
				break;
			}
		}

		for(var i=0;i<domNode.childNodes.length; i++){
			if(domNode.childNodes[i].nodeType == 1){
				if(this.manageNodes(domNode.childNodes[i], wrap)){
					return true;
				}
			}
		}
		
		return false;
	}

};