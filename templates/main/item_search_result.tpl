<input id="selected_category_property_ids" type="hidden" value="{$ns.selected_category_property_ids}" />
{if (($ns.foundItems |@count )>0)}
{foreach from=$ns.foundItems item=item name=fi}
{include file="$TEMPLATE_DIR/main/search_item_view.tpl"}
{/foreach}
{else}
<div style="text-align: center">
	<h1>{$ns.langManager->getPhraseSpan(117)}</h1>
</div>
{/if}
<div class="avo_style_pageinggBox">
	{nest ns=paging}	
</div>

<div style="clear: both"></div>


<input id="default_monthly_credit_interest_ratio" type="hidden" value="{$ns.defaultCreditInterestMonthlyRatio}"/>
<input id="default_credit_supplier_commission" type="hidden" value="{$ns.defaultSupplierCommission}"/>