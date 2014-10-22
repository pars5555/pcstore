{* $items is input parameter for this tpl *} 
{foreach from=$items item=item name=fi}		
	{include file="$TEMPLATE_DIR/main/pc_configurator/pcc_item_view.tpl"}
{/foreach}
		
		{if $ns.load_more_reach_to_end == 'false'}
		<div id="load_more_div" class="avo_style_load_moreBtn">				
			<a id="load_more_button"  href="javascript:void(0);" class="button1 blue">{$ns.langManager->getPhraseSpan(275)}</a>
		</div>		
		<div id="load_more_hidden_div" style="display: none">
		  </div>

{/if}
