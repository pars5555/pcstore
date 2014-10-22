ngs.TableViewMultiAction = Class.create(ngs.AbstractAction, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "do_table_view_multi";
	},
	getMethod: function() {
		return "POST";
	},
	beforeAction: function() {
	},
	afterAction: function(transport) {
		var data = transport.responseText.evalJSON();
		if (data.status === "ok") {
		} else if (data.status === "err") {
			ngs.DialogsManager.closeDialog(583, "<div>" + data.errText + "</div>");
		}
		ngs.TableViewLoad.prototype.refreshLoad(data);
	}
});
