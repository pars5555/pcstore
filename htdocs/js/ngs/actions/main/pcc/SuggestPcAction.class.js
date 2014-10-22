ngs.SuggestPcAction = Class.create(ngs.AbstractAction, {

	initialize : function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main_pcc", ajaxLoader);
	},

	getUrl : function() {
		return "do_suggest_pc";
	},

	getMethod : function() {
		return "POST";
	},

	beforeAction : function() {
	},

	afterAction : function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status == "ok") {
			var ci = ngs.PcConfiguratorManager.componentsIndex;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.CASE ] = data.chassis;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.CPU] = data.cpu;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.MB] = data.mb;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.COOLER] = data.cooler;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.RAM] = data.ram;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.HDD] = data.hdd;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.SSD] = data.ssd;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.OPT] = data.opt;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.MONITOR] = data.monitor;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.VIDEO] = data.graphics;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.POWER] = data.power;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.KEY] = data.keyboard;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.MOUSE] = data.mouse;
			ngs.PcConfiguratorManager.selectedComponentsArray[ci.SPEAKER] = data.speaker;
			ngs.PcConfiguratorManager.refreshComponentPage();			
		} else if (data.status == "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
	}
});
