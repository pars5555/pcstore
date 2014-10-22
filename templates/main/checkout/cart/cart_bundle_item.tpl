<div id="cart_bundle_container^{$bundleItems.0->getId()}" class="bundle_item" style="width:100%;height: 34px; padding: 6px 0; position: relative;border-bottom: 1px solid  #C4C4C4;">

	{math equation="1 - x/100" x=$bundleItems.0->getDiscount() assign="discountParam"}

	{foreach from=$bundleItems item=cartItem}
		{if $cartItem->getItemAvailable() == 0 and $cartItem->getSpecialFeeId() == 0}
			{assign var='someItemsMissing' value='true'}		 	
		{/if}
	{/foreach}

	<div  class="grid_1" style="float: left;">
		<div id="bundle_collapse_expande_button^{$bundleItems.0->getId()}" class="bundle_item_expand_button bundle_collapse_expande_buttons"> </div>
	</div>

	<div class="grid_5" style="float: left;height: 100%">
		<img src="{$ns.itemManager->getItemImageURL(0,0, '30_30', 1)}" style="margin:8px; border: 1px solid;"/>
	</div>
	<div class="grid_28" style="float: left; line-height: 34px;{if $someItemsMissing}color:red;{/if}">

		{assign var="phrase_id" value = $bundleItems.0->getBundleDisplayNameId()}
		{$ns.langManager->getPhraseSpan($phrase_id)}
	</div>

	<div class="grid_7" style="float: left; line-height: 34px;">
		{if not $someItemsMissing}
			<select id="cart_item^{$bundleItems.0->getId()}" class="select_cart_item_count cmf-skinned-select cmf-skinned-text" {if $ns.final_step}disabled="true"{/if}>

				{section name=foo start=1 loop=21 step=1}
					{assign var='index' value=$smarty.section.foo.index}
					<option value="{$index}" {if $bundleItems.0->getCount() == $index}selected="selected"{/if}>{$index}</option>
				{/section}

			</select>
		{/if}
	</div>

	{if $ns.final_step != 'true'}
		<div class="grid_6" style="float: left;">
			<a id="cart_item^{$bundleItems.0->getId()}" href="javascript:void(0);" class="cart_items_delete">{$ns.langManager->getPhraseSpan(71)}...</a>
			<br/>
			<a id="cart_item^{$bundleItems.0->getId()}" href="javascript:void(0);" class="cart_items_edit">{$ns.langManager->getPhraseSpan(288)}...</a>		
		</div>
	{/if}	
	{assign var="bundlePrice" value=$ns.bundleItemsManager->calcBundlePriceForCustomerWithoutDiscount($bundleItems, $ns.userLevel)}
	{if $ns.final_step != 'true'}		
		{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
			<div class="grid_6" style="float: left;text-align: right">
				{if not $someItemsMissing}
					{if $bundlePrice.1 > 0}
						${$bundlePrice.1|number_format:1}
					{/if}
				{/if}
			</div>
		{/if}
	{/if}

	{assign var= "count" value =$bundleItems.0->getCount()}
	{assign var= "specialFee" value =$bundlePrice.2}
	{assign var="totUSD" value="`$count*$bundlePrice.1`"}

	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
		<div class="grid_11" style="float: left;text-align: right">
			{if not $someItemsMissing}
				{if $bundlePrice.1 > 0}
					${$totUSD|number_format:1}
				{/if}
			{/if}
		</div>
	{/if}


{assign var="bundle_item_total_deal_discount_amd" value=$ns.checkoutManager->getBundleItemTotalDealsDiscountAMD($bundleItems)}
	{if $ns.final_step != 'true'}
		{if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
			<div class="grid_9" style="float: left;text-align: right">
				{if not $someItemsMissing}				 
					{if $bundlePrice.0+$specialFee > 0}				
						<span style="{if $discountParam != 1 || $bundle_item_total_deal_discount_amd>0}text-decoration: line-through{/if}">
							{$bundlePrice.0+$specialFee|number_format:0} Դր.
						</span>
						{if $discountParam != 1}
							<br/>
							<span style="{if $bundle_item_total_deal_discount_amd>0}text-decoration: line-through{/if}">
								{$bundlePrice.0*$discountParam+$specialFee|number_format:0} Դր.
							</span>
						{/if}
						{if $bundle_item_total_deal_discount_amd>0}
						<br/>
						<span>
							{$bundlePrice.0*$discountParam+$specialFee-$bundle_item_total_deal_discount_amd|number_format:0} Դր.							
						</span>
						
					{/if}
					{/if}
				{/if}

			</div>
		{/if}
	{/if}
	
	{math equation="count*(price*discount+specFee)" discount=$discountParam count=$count price=$bundlePrice.0 specFee=$specialFee assign="totAMDWithDiscount"}
	{math equation="count*(price+specFee)" discount=$discountParam count=$count price=$bundlePrice.0 specFee=$specialFee assign="totAMDWithoutDiscount"}
	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
		<div class="grid_12" style="float: left;text-align: right">
			{if not $someItemsMissing}
				{if $bundlePrice.0+$specialFee > 0}
					<span style="{if $discountParam != 1 || $bundle_item_total_deal_discount_amd>0}text-decoration: line-through{/if}">
						{$totAMDWithoutDiscount|number_format:0} Դր.
					</span>
					{if $discountParam != 1}
						<br/>
						<span style="{if $bundle_item_total_deal_discount_amd>0}text-decoration: line-through{/if}">
							{$totAMDWithDiscount|number_format:0} Դր.
						</span>
					{/if}
					{if $bundle_item_total_deal_discount_amd>0}
						<br/>
						<span>
							{$totAMDWithDiscount-$count*$bundle_item_total_deal_discount_amd|number_format:0} Դր.
						</span>
						
					{/if}
				{/if}
			{/if}
		</div>
	{/if}





	{if not $someItemsMissing}
		<div class="grid_10" style="float: left;">
			{if $bundleItems.0->getDiscount() > 0}
				{$bundleItems.0->getDiscount()}%
				<br/>
			{/if}
			{if $bundle_item_total_deal_discount_amd>0}
				{$count} x {$bundle_item_total_deal_discount_amd|number_format:0} Դր.
			{/if}
		</div>

	{/if}

</div>
<div id="cart_bundle_items_container_{$bundleItems.0->getId()}" style="width:100%;position: relative;display: none">
	{assign var="bundle_items" value = "true"}
	{foreach from=$bundleItems item=cartItem}	
		{include file="$TEMPLATE_DIR/main/checkout/cart/cart_item.tpl" cartItem=$cartItem}
	{/foreach}
</div>
