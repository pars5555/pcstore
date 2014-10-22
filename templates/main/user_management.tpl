<div style="position: relative; width: 100%">	
	<div id="um_tabs">
		<ul>
			<li><a href="#um_sub_users_tab" url_param="registerd">{$ns.langManager->getPhraseSpan(143)}</a></li>
			<li><a href="#um_pending_users_tab" url_param="invite">{$ns.langManager->getPhraseSpan(144)}</a></li>
		</ul>
		<div id="um_sub_users_tab">
			{nest ns = sub_users_list}
		</div>
		<div id="um_pending_users_tab">
			{nest ns = pending_users_list}
		</div>
	</div>
	<script>
		jQuery("#um_tabs").tabs();
		jQuery("#um_tabs").tabs('select', {$ns.selected_tab_index});
	</script>
</div>

