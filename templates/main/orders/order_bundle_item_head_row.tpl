{assign var="bundleInfo" value=$orderItem[0]}
<div class="grid_2" style="float: left"> </div>	
<div class="grid_3" style="float: left">
	<div id="bundle_collapse_expande_button^{$bundleInfo->getOrderDetailsBundleId()}" class="order_expand_button collapse_expande_buttons"> </div>
</div>
{assign var="bundleDisplayNameId" value=$bundleInfo->getOrderDetailsBundleDisplayNameId()}
<div class="grid_40" style="float: left">{$ns.langManager->getPhraseSpan($bundleDisplayNameId)}  ({$bundleInfo->getOrderDetailsDiscount()}% {$ns.langManager->getPhraseSpan(285)})</div>	
<div class="grid_8" style="float: left;text-align: center">{$bundleInfo->getOrderDetailsBundleCount()}</div>
{if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
<div class="grid_11" style="float: left;text-align: right">{$bundleInfo->getOrderDetailsCustomerBundlePriceAmd()|number_format:0} Դր</div>
{/if}
{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
	<div class="grid_11" style="float: left;text-align: right">${$bundleInfo->getOrderDetailsCustomerBundlePriceUsd()|number_format:1}</div>
{/if}
	
