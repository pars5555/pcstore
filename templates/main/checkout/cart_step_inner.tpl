
{if $ns.customerMessages}
	{include file="$TEMPLATE_DIR/main/checkout/cart/cart_customer_messages.tpl"}
{/if}
<div id="checkout_steps_container_inner">
	{if not $ns.emptyCart}
		{include file="$TEMPLATE_DIR/main/checkout/cart/cart_header.tpl"}
		{foreach from=$ns.cartItems item=cartItem}
			{if not is_array($cartItem)}

				{include file="$TEMPLATE_DIR/main/checkout/cart/cart_item.tpl" cartItem=$cartItem}

			{else}
				{include file="$TEMPLATE_DIR/main/checkout/cart/cart_bundle_item.tpl" bundleItems=$cartItem}
			{/if}
		{/foreach}
	{else}
		<h1 style="text-align: center">{$ns.langManager->getPhraseSpan(296)}</h1>
	{/if}
	{if !$ns.emptyCart}
</div>

	<div style="font-size: 20px;color: #A00000; position: absolute; bottom: 5px;">

		<div class="grid_20" style="float: left;font-size: 14px;color: #000;">
			<input type="checkbox" id="cho_include_vat_checkbox" {if $ns.req_params.cho_include_vat==1}checked="checked"{/if}>
			<label for="cho_include_vat_checkbox" >{$ns.langManager->getPhraseSpan(565)}</label>
		</div>
		<div class="grid_30" style="float: left;">
			{$ns.langManager->getPhraseSpan(262)}
		</div>
		<div class="grid_5" style="float: left;"></div>
		{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
			<div class="grid_15" style="float: left;text-align: right">
				${$ns.grandTotalUSD|number_format:1}
			</div>
		{/if}
		{if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
			{if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
				<div class="grid_5" style="float: left;text-align: right">
					{$ns.langManager->getPhraseSpan(270)}
				</div>
			{/if}
			<div class="grid_15" style="float: left;text-align: right">
				{$ns.grandTotalAMD|number_format:0} Դր.
			</div>
		{/if}
	</div>
{/if}

{if $ns.emptyCart}
	{assign var="nextButtonTitlePhraseId" value="296"}
{else}
	{if !$ns.allItemsAreAvailable}
		{assign var="nextButtonTitlePhraseId" value="295"}
	{else}
		{if !$ns.minimum_order_amount_exceed}			
			{assign var="nextButtonTitlePhraseId" value="`420` `$ns.minimum_order_amount_amd`"}
		{/if}	
	{/if}
{/if}

{if !$ns.allItemsAreAvailable || $ns.emptyCart || !$ns.minimum_order_amount_exceed || ($ns.req_params.cho_include_vat == 1 && !$ns.all_non_bundle_items_has_vat)}
	{assign var="disableButton" value='true'}
{else}
	{assign var="disableButton" value='false'}
{/if}
<input type="hidden" id="cart_next_button_status_and_title" title="{$ns.langManager->getPhrase($nextButtonTitlePhraseId)}" phrase_id="{$nextButtonTitlePhraseId}" button_disabled="{$disableButton}"/>

<input type="hidden" name="f_check_user_password" value="{if $ns.userLevel == userGroupsUser && $ns.userLoginType !== 'pcstore'}no{else}yes{/if}"
