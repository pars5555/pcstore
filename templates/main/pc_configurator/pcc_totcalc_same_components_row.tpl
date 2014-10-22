{if ($ns.userLevel === $ns.userGroupsCompany || $ns.userLevel===$ns.userGroupsAdmin) || $item->getIsDealerOfThisCompany() == 1}
	{assign var="showDealerPrice" value=true}
{/if}

{assign var="admin_show_dealer_price" value=1}
{if $ns.userLevel==$ns.userGroupsAdmin}
	{if $ns.admin_price_group != 'admin'}
		{assign var="admin_show_dealer_price" value=0}
	{/if}
{/if}
<div class="pcc_total_calc_item_price_row">
	<span class="pcc_total_calc_item_price_row_item_title"> {$count} x {$item->getDisplayName()} </span>



	<span class="pcc_total_calc_item_price_row_item_price"> 
		{if $showDealerPrice == true && $admin_show_dealer_price == 1}		
			{assign var="price" value=$item->getDealerPrice()}
			${$price*$count|number_format:1}
		{else}
			{assign var="price" value=$item->getCustomerItemPrice()}
			{assign var="amdPrice" value=$ns.itemManager->exchangeFromUsdToAMD($price)}
			{$amdPrice*$count|number_format:0} Դր.
		{/if} </span>
</div>
