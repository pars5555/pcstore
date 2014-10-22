ngs.TreeViewUtility = Class.create({

	node_select_listeners : null,
	node_expand : null,
	treeViewId : null,
	selectedNodeKey : null,
	selectable : false,

	initialize : function(treeViewId, selectable) {
		this.node_select_listeners = new Array();
		this.node_expand = new Array();
		this.treeViewId = treeViewId;
		selectedNodeKey = null;
		this.selectable = typeof selectable !== 'undefined' ? selectable : true;
		var ktvTopDivs = $$('#' + treeViewId + ' .ktvTop');
		var ktvMinusElements = $$('#' + treeViewId + ' .ktvMinus');
		var ktvPlusElements = $$('#' + treeViewId + ' .ktvPlus');
		this.addClickHandlerToNodes(ktvTopDivs);
		this.addClickHandlerToPMs(ktvMinusElements, true);
		this.addClickHandlerToPMs(ktvPlusElements, false);
	},
	addClickHandlerToNodes : function(elements_array) {
		for (var i = 0; i < elements_array.length; i++) {
			var nodeKey = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
			elements_array[i].onclick = this.onNodeClicked.bind(this, nodeKey);
		}
	},
	addClickHandlerToPMs : function(pms, plus) {
		for (var i = 0; i < pms.length; i++) {
			var nodeKey = pms[i].id.substr(pms[i].id.indexOf("^") + 1);
			pms[i].onclick = this.onPMClicked.bind(this, nodeKey);
			this.node_expand[nodeKey] = plus;
		}
	},

	getNodeData : function(nodeKey) {
		if ($('tw_data^' + nodeKey)) {
			var JsonData = $('tw_data^' + nodeKey).value;			
			if (JsonData && JsonData.length > 0) {
				return JsonData.evalJSON();
			}
		}
		return null;
	},
	getNodeParentKey : function(nodeKey) {
		if ($('tw_parent^' + nodeKey)) {
			var parentKey = $('tw_parent^' + nodeKey).value;
			return parentKey;
		}
		return null;

	},
	getNodeChildrenKeys : function(nodeKey) {
		if ($('tw_children_keys^' + nodeKey)) {
			var childrenKeys = $('tw_children_keys^' + nodeKey).value;
			var childrenKeysArray = childrenKeys.split(',');
			return childrenKeysArray;
		}
		return null;

	},

	onPMClicked : function(nodeKey, e) {		
		if (!e) var e = window.event;
    e.cancelBubble = true;
    if (e.stopPropagation) e.stopPropagation();    
		if (this.node_expand[nodeKey]) {
			this.node_expand[nodeKey] = false;
			$('ktvPMSpan^' + nodeKey).className = "ktvPM " + (this.node_expand[nodeKey] ? 'ktvMinus' : 'ktvPlus');

			new Effect.BlindUp($('ktvUI^' + nodeKey), {
				duration : 0.3
			});
		} else {
			this.node_expand[nodeKey] = true;
			$('ktvPMSpan^' + nodeKey).className = "ktvPM " + (this.node_expand[nodeKey] ? 'ktvMinus' : 'ktvPlus');
			new Effect.BlindDown($('ktvUI^' + nodeKey), {
				duration : 0.3
			});
		}		
	},
	onNodeClicked : function(nodeKey) {	
		if (this.selectable) {
			this.selectNode(nodeKey);
		}
		this.fireNodeSelectedEvent(nodeKey);
	},
	fireNodeSelectedEvent : function(nodeKey) {
		for (var l in this.node_select_listeners) {
			if (this.node_select_listeners[l].onNodeSelect) {
				this.node_select_listeners[l].onNodeSelect(nodeKey);
			}
		}
	},

	selectNode : function(nodeKey) {		
		var ktvTopDivs = $$('#' + this.treeViewId + ' .ktvTop');
		for (var a in ktvTopDivs) {
			ktvTopDivs[a].className = 'ktvTop';
		}

		var li = $('ktvTopDiv^' + nodeKey);
		li.className = 'ktvTop ktvSelected';
		this.selectedNodeKey = nodeKey;

	},

	getSelectedNodeKey : function() {
		return this.selectedNodeKey;
	},

	addNodeSelectListener : function(listener) {

		if (this.node_select_listeners.indexOf(listener) == -1) {
			this.node_select_listeners.push(listener);
		}
	}
});
