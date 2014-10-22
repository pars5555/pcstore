{foreach from=$ns.allSelectedComponentsIdsArray item=selectedComponentIds name=cl}
	{if is_array($selectedComponentIds)}
		{assign var="selectedComponentIds" value=$selectedComponentIds|@implode:','}		
	{/if}
	<input id='selected_component_{$smarty.foreach.cl.index+1}' type="hidden" value="{$selectedComponentIds}"/>
{/foreach}

