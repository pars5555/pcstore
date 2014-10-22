ngs.FooterLinksContentLoad = Class.create(ngs.AbstractLoad, {
	initialize: function($super, shortCut, ajaxLoader) {
		$super(shortCut, "main", ajaxLoader);
	},
	getUrl: function() {
		return "footer_links_content";
	},
	getMethod: function() {
		return "POST";
	},
	getContainer: function() {
		return "hp_" + this.getName() + "_tab";
	},
	getName: function() {
		return "footer_links_content";
	},
	afterLoad: function() {
		if ($('save_help_text')) {
			$('save_help_text').onclick = this.saveHelpText.bind(this);
			this.initTinyMCE("textarea#help_content_text");
		}


	},
	saveHelpText: function()
	{
		tinyMCE.activeEditor.save();
		var phrase_id = $("translation_phrase_id").value;
		var phrase_text = $("help_content_text").value;
		ngs.action("update_language_phrase_action", {"phrase_id": phrase_id, "phrase_text": phrase_text});
	}
});
