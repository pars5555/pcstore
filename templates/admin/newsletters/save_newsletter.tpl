<select onkeyup="this.blur();
			this.focus();" multiple style="width: 100%;min-height: 100px" id="sn_newsletter_select">
	{html_options options=$ns.all_newsletters selected=$ns.selected_newsletter_id}
</select>
<input type="text" id="sn_newsletter_title" value="{$ns.selected_newsletter_title}" newsletter_id="{$ns.selected_newsletter_id}"/>