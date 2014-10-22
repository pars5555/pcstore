<div id="cart_item_container" style="width:100%;height: 34px;padding: 6px 0; background: white;position: relative;border-bottom: 1px solid  #C4C4C4;
     {if $ns.req_params.cho_include_vat == 1 && $cartItem->getCustomerVatItemPrice()==0 && $cartItem->getItemId()>0}color:red{/if}">
	{math equation="1 - x/100" x=$cartItem->getDiscount() assign="discountParam"}

	{if $bundle_items}
		<div class="grid_2" style="float: left;">
		</div>
	{/if}
	<div class="grid_1" style="float: left;">
	</div>
	<div class="grid_5" style="float: left;height: 100%;line-height: 34px;">			
		{if $cartItem->getItemAvailable() == 1}
			{if $bundle_items}
				{assign var="itemId" value=$cartItem->getBundleItemId()}				
			{else}
				{assign var="itemId" value=$cartItem->getItemId()}																												 
			{/if}
			<img src="{$ns.itemManager->getItemImageURL($itemId,$cartItem->getItemCategoriesIds(), '30_30', 1 , true)}" style="margin:0 8px; border: 1px solid;vertical-align: middle;"/>
		{/if}
	</div>
	<div class="grid_{if $bundle_items}25{else}28{/if}" style="float: left; line-height: 34px;{if $cartItem->getItemAvailable() == 0 and $cartItem->getSpecialFeeId() == 0}color:red;{/if};
		 overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
		{if $cartItem->getSpecialFeeId() > 0}
			{assign var="spec_fee_desc_id" value = $cartItem->getSpecialFeeDescriptionTextId()}
			{$ns.langManager->getPhraseSpan($spec_fee_desc_id)}
		{else}
			{if $cartItem->getItemAvailable() == 1}
				{$cartItem->getItemDisplayName()}
			{else}
				{if $bundle_items}
					{$cartItem->getBundleCachedItemDisplayName()}
				{else}
					{$cartItem->getCachedItemDisplayName()}
				{/if}						
			{/if}
		{/if}
	</div>

	<div class="grid_7" style="float: left; line-height: 34px;">
		{if $cartItem->getItemAvailable() == 1}
			<select id="cart_item^{$cartItem->getId()}" class="select_cart_item_count cmf-skinned-select cmf-skinned-text" {if $bundle_items || $ns.final_step}disabled="true"{/if}  onkeyup="this.blur();
							this.focus();">
				{section name=foo start=1 loop=$ns.maxItemCartCount+1 step=1}
					{assign var='index' value=$smarty.section.foo.index}
					{if $bundle_items}
						{assign var='count' value=$cartItem->getBundleItemCount()}
					{else}
						{assign var='count' value=$cartItem->getCount()}
					{/if}

					<option value="{$index}" {if $count == $index}selected="selected"{/if}>{$index}</option> 
				{/section}

			</select>
		{/if}			
	</div>

	{if $ns.final_step != 'true'}
		<div class="grid_6" style="float: left;">
			{if not $bundle_items}
				<a id="cart_item^{$cartItem->getId()}" href="javascript:void(0);" class="cart_items_delete">{$ns.langManager->getPhraseSpan(71)}...</a>
			{/if}			
		</div>
	{/if}


	{if $bundle_items}
		{assign var= "count" value = $cartItem->getBundleItemCount()}
	{else}	
		{assign var= "count" value = $cartItem->getCount()}
	{/if}


	{if $cartItem->getSpecialFeeId() > 0}	
		{if $cartItem->getSpecialFeeDynamicPrice()>=0}		
			{assign var="price" value=$cartItem->getSpecialFeeDynamicPrice()}
		{else}
			{assign var="price" value=$cartItem->getSpecialFeePrice()}
		{/if}
	{else}


		{if $cartItem->getIsDealerOfThisCompany()==1 || $ns.userLevel === $ns.userGroupsAdmin || $ns.userLevel === $ns.userGroupsCompany}
			{assign var="showDealerPrice" value=1}
			{if $ns.req_params.cho_include_vat == 1 && $cartItem->getItemId()>0}
				{assign var="price" value=$cartItem->getItemVatPrice()}
			{else}
				{assign var="price" value=$cartItem->getItemDealerPrice()}
			{/if}
		{else}
			{assign var="showDealerPrice" value=0}
			{if $ns.req_params.cho_include_vat == 1 && $cartItem->getItemId()>0}
				{assign var="price" value=$cartItem->getCustomerVatItemPrice()}
			{else}
				{assign var="price" value=$cartItem->getCustomerItemPrice()}
			{/if}
		{/if}	

	{/if}	

	{assign var="totP" value="`$count*$price`"}

	{if $ns.final_step != 'true'}
		{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
			<div class="grid_6" style="float: left;text-align: right">
				{if $cartItem->getItemAvailable() == 1}
					{if $showDealerPrice == 1}
						${$price|number_format:1}
					{/if}			
				{/if}
			</div>		
		{/if}
	{/if}

	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
		<div class="grid_11" style="float: left;text-align: right">
			{if $cartItem->getItemAvailable() == 1}
				{if $showDealerPrice == 1}			
					${$totP|number_format:1}
				{/if}			
			{/if}
		</div>		
	{/if}

	{* printing item price *}
	{if $ns.final_step != 'true'}
		{if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}	
			<div class="grid_9" style="float: left;text-align: right">
				{if $cartItem->getSpecialFeeId() > 0}
					<span>
						{$price|number_format:0} Դր.
					</span>
				{else}
					{if $cartItem->getItemAvailable() == 1}
						{if $showDealerPrice == 0}
							{assign var="itemAmdPrice" value=$ns.itemManager->exchangeFromUsdToAMD($price)}
							<span style="{if $discountParam != 1 || $cartItem->getDealDiscountAmd()>0}text-decoration: line-through{/if}">
								{$itemAmdPrice|number_format:0} Դր.
							</span>
							{if $discountParam != 1 && $cartItem->getDealDiscountAmd()<=0}
								<br/>
								<span>
									{$itemAmdPrice*$discountParam|number_format:0} Դր.
								</span>
							{/if}
							{if $cartItem->getDealDiscountAmd()>0}
								<br/>
								<span>
									{$itemAmdPrice-$cartItem->getDealDiscountAmd()|number_format:0} Դր.
								</span>

							{/if}
						{/if}
					{/if}
				{/if}			
			</div>			
		{/if}
	{/if}


	{* printing item total price *}
	{if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
		<div class="grid_12" style="float: left;text-align: right">	
			{if $cartItem->getSpecialFeeId() > 0}
				<span>
					{$price|number_format:0} Դր.
				</span>
			{else}
				{if $cartItem->getItemAvailable() == 1}
					{if  $showDealerPrice == 0}
						{assign var="totPrAMD" value=$ns.itemManager->exchangeFromUsdToAMD($totP)}
						<span  style="{if $discountParam != 1 || $cartItem->getDealDiscountAmd()>0}text-decoration: line-through{/if}">
							{$totPrAMD|number_format:0} Դր.
						</span>
						{if $discountParam != 1 && $cartItem->getDealDiscountAmd()<=0}
							<br/>
							<span>
								{$totPrAMD*$discountParam|number_format:0} Դր.
							</span>
						{/if}
						{if $cartItem->getDealDiscountAmd()>0}
							<br/>
							<span>
								{$totPrAMD-$count*$cartItem->getDealDiscountAmd()|number_format:0} Դր.
							</span>

						{/if}
					{/if}			
				{/if}
			{/if}
		</div>		
	{/if}
	{if $cartItem->getDealDiscountAmd()>0}
		{assign var="deal_discount_applied" value=true}
	{/if}
	{if 0<$cartItem->getDiscount() and $showDealerPrice==0 && $cartItem->getSpecialFeeId()==0 && $cartItem->getItemAvailable()==1}
		{assign var="discount_available" value=true}
	{/if}

	{if $deal_discount_applied || $discount_available}
		<div class="grid_10" style="float: left;">
			{if $discount_available && $cartItem->getDealDiscountAmd()<=0}
				{$cartItem->getDiscount()}%				
			{/if}
			{if $deal_discount_applied}	
				<br/>
				{$count} x {$cartItem->getDealDiscountAmd()|number_format:0} Դր.
			{/if}
		</div>
	{/if}

</div>