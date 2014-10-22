/**
 * @author Vahagn Sookiasian
 * @site http://naghashyan.com
 * @mail vahagnsookaisyan@gmail.com
 * @year 2012
 */
ngs.PagingManager = {
	params: {},
	load: "",
	beforeCallFunction: null,
	init: function(load, params, beforeCallFunction) {
		if (beforeCallFunction)
		{
			this.beforeCallFunction = beforeCallFunction;
		}
		if (typeof params !== 'undefined') {
			this.params = params;
		} else
		{
			this.params = {};
		}		
		if (typeof load !== 'undefined') {
			this.load = load;
		}
		if ($("f_pageingBox")) {
			var page = parseInt($("f_curPage").value);
			this.params.spg = page;
			var pageCount = parseInt($("f_pageCount").value);
			if ($("f_first")) {
				$("f_first").onclick = this.goToPage.bind(this, 1);
			}
			if ($("f_prev")) {
				$("f_prev").onclick = this.goToPage.bind(this, page - 1);
			}
			if ($("f_next")) {
				$("f_next").onclick = this.goToPage.bind(this, page + 1);
			}
			if ($("f_last")) {
				$("f_last").onclick = this.goToPage.bind(this, pageCount);
			}
			var pages = $$("#f_pageingBox .f_pagenum");
			for (var i = 0; i < pages.length; i++) {
				var page = pages[i].id.substr(pages[i].id.indexOf("_") + 1);
				pages[i].onclick = this.goToPage.bind(this, page);
			}
		}
	},
	updateParam: function(params) {
		this.params = params;
	},
	goToPage: function(page) {
		this.params.spg = page;
		if (typeof this.beforeCallFunction === 'function') {
			this.beforeCallFunction();
		}
		ngs.load(this.load, this.params);
		return false;
	}


};
