<select onkeyup="this.blur();
			this.focus();" multiple style="width: 100%;height: 100%" id="mn_newsletter_select">
	{html_options options=$ns.all_newsletters selected=$ns.selected_newsletter_id}
</select>