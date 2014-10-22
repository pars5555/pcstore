<div id="cart_header_container" style="width:100%;height: 20px;margin:10px 0; line-height: 20px;">
	<div class="grid_2" style="float: left"> </div>
	<div style="float:left;">
	<div class="grid_3" style="float: left"> </div>
	<div class="grid_40" style="float: left">{$ns.langManager->getPhraseSpan(109)}</div>	
	<div class="grid_8" style="float: left;text-align: center">{$ns.langManager->getPhraseSpan(328)}</div>
	<div class="grid_11" style="float: left;text-align: right">{$ns.langManager->getPhraseSpan(88)} (Դր.)</div>
	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
		<div class="grid_11" style="float: left;text-align: right">{$ns.langManager->getPhraseSpan(88)} ($)</div>
	{/if}
	</div>
</div>