<div style="background: white;">
	<div style="padding: 20px;">	
		{if $ns.userLevel==$ns.userGroupsAdmin}
			<input id = "translation_phrase_id" type="hidden"  value="{$ns.translation_number}"/>
			<button id="save_help_text">Save</button>
			<textarea id="help_content_text" style="text-align: left; width:100%;;min-height:800px">{$ns.langManager->getPhrase($ns.translation_number)}</textarea>
		{else}
			{$ns.langManager->getPhraseSpan($ns.translation_number)}
		{/if}
	</div>
	<input id = "footer_links_content" type="hidden"  value="true"/>
</div>