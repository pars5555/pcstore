	<div class="grid_3" style="float: left"> </div>
	<div class="grid_2" style="float: left"> </div>	
	<div class="grid_40" style="float: left">{$orderItem->getOrderDetailsItemDisplayName()}</div>
	<div class="grid_8" style="float: left;text-align: center">{$orderItem->getOrderDetailsItemCount()}</div>
	
	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
		<div class="grid_11" style="float: left;text-align: right">
			{if $orderItem->getOrderDetailsIsDealerOfItem() != 1}
			 {$ns.itemManager->exchangeFromUsdToAMD($orderItem->getOrderDetailsCustomerItemPrice(), $orderItem->getDollarExchangeUsdAmd())|number_format:0} Դր.
			{/if}
		</div>
	{/if}
	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
		<div class="grid_11" style="float: left;text-align: right">			
			{if $orderItem->getOrderDetailsIsDealerOfItem() == 1}
			 ${$orderItem->getOrderDetailsCustomerItemPrice()|number_format:1}
			{/if}			
		</div>
	{/if}
	
	<div style="clear:both"> </div>
