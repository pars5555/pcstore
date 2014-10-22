{assign var="bundleInfo" value=$orderItem[0]}
{foreach from=$orderItem item=bundleItem name=foo}	
	<div  style="{if not $smarty.foreach.foo.last}border-bottom: 1px solid #aaa{/if}">		
	<div class="grid_4" style="float: left"> </div>
	<div class="grid_4" style="float: left"> </div>
	{if $bundleItem->getOrderDetailsSpecialFeeId()>0}
		{assign var="special_fee_display_name_id" value=$bundleItem->getOrderDetailsSpecialFeeDisplayNameId()}
		<div class="grid_37" style="float: left">{$ns.langManager->getPhraseSpan($special_fee_display_name_id)}</div>
	{else}	
		<div class="grid_37" style="float: left">{$bundleItem->getOrderDetailsItemDisplayName()}</div>
	{/if}	
	<div class="grid_8" style="float: left;text-align: center">{$bundleItem->getOrderDetailsItemCount()}</div>
	
	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
		<div class="grid_11" style="float: left;text-align: right;line-height: 20px;">
			{if $bundleItem->getOrderDetailsSpecialFeeId()>0}
				{$bundleItem->getOrderDetailsSpecialFeePrice()} Դր.
			{else}
				{if $bundleItem->getOrderDetailsIsDealerOfItem() != 1}				
				  {assign var="customer_item_price_in_amd" value=$ns.itemManager->exchangeFromUsdToAMD($bundleItem->getOrderDetailsCustomerItemPrice(), $bundleItem->getDollarExchangeUsdAmd())}				  				  
				  {math equation="price*(1 - x/100)" x=$bundleItem->getOrderDetailsDiscount() price =$customer_item_price_in_amd assign="customer_item_price_in_amd_without_discount"}
				  
				  	<span style="font-size: 12px;color: #666;text-decoration: line-through;">
						{$customer_item_price_in_amd|number_format:0} Դր.
						</span>
						<br />
						<span style="font-size: 14px;">
						{$customer_item_price_in_amd_without_discount|number_format:0} Դր.
						</span>
				  
				  
				{/if}
			{/if}
		</div>
	{/if}
	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
		<div class="grid_11" style="float: left;text-align: right">			
			{if $bundleItem->getOrderDetailsIsDealerOfItem() == 1}
			 ${$bundleItem->getOrderDetailsItemDealerPrice()|number_format:1}
			{/if}			
		</div>
	{/if}
	
	<div style="clear:both"> </div>
	</div>
	<div style="clear:both"> </div>
{/foreach}