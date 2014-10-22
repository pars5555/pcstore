<div id="cart_header_container" style="width:100%;height: 40px;margin-bottom:10px;;text-align: center">
	<div class="grid_2" style="float: left"> </div>
	<div class="grid_10" style="float: left">{$ns.langManager->getPhraseSpan(325)}</div>
	<div class="grid_20" style="float: left">{$ns.langManager->getPhraseSpan(326)}</div>	
	<div class="grid_15" style="float: left;">{$ns.langManager->getPhraseSpan(114)} (Դր.)</div>
	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
		<div class="grid_15" style="float: left">{$ns.langManager->getPhraseSpan(114)} ($)</div>
	{/if}
		<div class="{if ($ns.userLevel === $ns.userGroupsAdmin)}grid_15{else}grid_30{/if}" style="float: left;;text-align: center">{$ns.langManager->getPhraseSpan(372)}</div>
</div>