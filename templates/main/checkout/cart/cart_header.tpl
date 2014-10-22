<div id="cart_header_container" style="width:100%;height: 20px;margin:10px 0;">
	<div class="grid_1" style="float: left"> </div>
	<div class="grid_5" style="float: left">{$ns.langManager->getPhraseSpan(108)}</div>	
	<div class="grid_28" style="float: left">{$ns.langManager->getPhraseSpan(16)}</div>
	<div class="grid_7" style="float: left">{$ns.langManager->getPhraseSpan(328)}</div>
	{if $ns.final_step != 'true'}
		<div class="grid_6" style="float: left"></div>
	{/if}
	
	{if $ns.final_step != 'true'}
		{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
			<div class="grid_6" style="float: left;text-align: right">{$ns.langManager->getPhraseSpan(156)} $</div>
		{/if}
	{/if}
	
	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
		<div class="grid_11" style="float: left;text-align: right">{$ns.langManager->getPhraseSpan(313)} $</div>
	{/if}
	
	{if $ns.final_step != 'true'}
		{if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
			<div class="grid_9" style="float: left;text-align: right">{$ns.langManager->getPhraseSpan(156)} Դր.</div>
		{/if}
	{/if}
	
	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
		<div class="grid_12" style="float: left;text-align: right">{$ns.langManager->getPhraseSpan(313)} Դր.</div>
	{/if}
	
	{if $ns.discountAvailable == true}
		<div class="grid_10" style="float: left;">{$ns.langManager->getPhraseSpan(285)}</div>
	{/if}
	
</div>