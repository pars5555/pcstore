<div style="left: 10px;width:230px; bottom:140px;top:0px;position: absolute;overflow: auto">
    {if $ns.req_params.cho_do_shipping == '1'}
        <div >
            <h2 style="text-align: center;text-transform: capitalize;">{$ns.langManager->getPhraseSpan(290)}</h2>
            <br/>
            <div style="word-wrap: break-word;margin: 0 10px 0 10px;font-size: 14px;">
                {$ns.req_params.cho_shipping_recipient_name}<br/>
                {$ns.req_params.cho_shipping_address}<br/>
                {$ns.req_params.cho_shipping_region}<br/>
                {$ns.req_params.cho_shipping_cell}<br/>
                {$ns.req_params.cho_shipping_tel}
            </div>
        </div>
        <div style="margin-top: 30px;">
            <h2 style="text-align: center;text-transform: capitalize;">{$ns.langManager->getPhraseSpan(291)}</h2>
            <br/>
            <div style="word-wrap: break-word;margin: 0 10px 0 10px;font-size: 14px;">
                {if $ns.req_params.billing_is_different_checkbox == 1}
                    {$ns.req_params.cho_billing_recipient_name}<br/>
                    {$ns.req_params.cho_billing_address}<br/>
                    {$ns.req_params.cho_billing_region}<br/>
                    {$ns.req_params.cho_billing_cell}<br/>
                    {$ns.req_params.cho_billing_tel}
                {else}
                    {$ns.req_params.cho_shipping_recipient_name}<br/>
                    {$ns.req_params.cho_shipping_address}<br/>
                    {$ns.req_params.cho_shipping_region}<br/>
                    {$ns.req_params.cho_shipping_cell}<br/>
                    {$ns.req_params.cho_shipping_tel}
                {/if}
            </div>
        </div>
    {else}
        <div style="padding: 7px;">
            <h4 style="text-align: center;text-transform: capitalize;">{$ns.langManager->getPhraseSpan(323)}</h4>
            <div style="font-size: 14px;line-height: 20px;margin-top: 10px">
                {$ns.langManager->getPhraseSpan(307)}			
            </div>		
        </div>
    {/if}
    <div style="margin-top: 30px;">
        <h4 style="text-align: center;text-transform: capitalize;">{$ns.langManager->getPhraseSpan(367)}</h4>
        <br/>
        <div style="word-wrap: break-word;margin: 0 10px 0 10px;font-size: 14px;">		
            {$ns.langManager->getPhraseSpan($ns.paymentTypeDisplayNameId)}
        </div>
    </div>

</div>
<div style="left: 250px;right:10px; bottom:140px;top:0px;position: absolute;">
    {if $ns.customerMessages}
        {include file="$TEMPLATE_DIR/main/checkout/cart/cart_customer_messages.tpl"}
    {/if}

    <div style="overflow: auto;position:absolute;{if $ns.customerMessages}top: 60px{else}top:0{/if};left: 0; right: 0; bottom: 110px;">
        {include file="$TEMPLATE_DIR/main/checkout/cart/cart_header.tpl"}
        {foreach from=$ns.cartItems item=cartItem}
            {if not is_array($cartItem)}

                {include file="$TEMPLATE_DIR/main/checkout/cart/cart_item.tpl" cartItem=$cartItem}

            {else}
                {include file="$TEMPLATE_DIR/main/checkout/cart/cart_bundle_item.tpl" bundleItems=$cartItem}
            {/if}
        {/foreach}
    </div>

    {*}order total calculation{*}
    <div style="overflow: auto;position:absolute;left: 0; right: 0; bottom: 0;height:100px;font-size: 16px;color: #A00000;line-height: 20px;">
        {if $ns.req_params.cho_payment_type != 'credit'}
            {if $ns.req_params.cho_do_shipping == '1'}
                {*}order total text{*}
                <div class="grid_30" style="float: left;text-align: left">
                    {$ns.langManager->getPhraseSpan(313)}
                </div>

                {*}order total USD without shpping{*}
                {if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
                    <div class="grid_13" style="float: left;text-align: left">
                        ${$ns.grandTotalUSD|number_format:1}
                    </div>
                {/if}

                {*}order total AMD without shipping{*}
                {if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
                    {if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}				
                        <div class="grid_5" style="float: left;text-align: left">
                            {$ns.langManager->getPhraseSpan(270)}
                        </div>
                    {/if}
                    <div class="grid_15" style="float: left;text-align: left">
                        {$ns.grandTotalAMD|number_format:0} Դր.
                    </div>
                {/if}
                <div style="clear: both"> </div>

                {*}order shipping cost text{*}
                <div class="grid_30" style="float: left;text-align: left">
                    {$ns.langManager->getPhraseSpan(314)}
                </div>

                {*}order shippng cost AMD{*}
                <div class="grid_40" style="float: left;text-align: left">				
                    {if $ns.shipping_cost>0}
                        {$ns.shipping_cost|number_format:0} Դր.
                    {else}				
                        {if $ns.shipping_cost == 0}
                            <span style="color:#060">
                                {$ns.langManager->getPhraseSpan(289)}
                            </span>
                        {else}
                            <span style="color:#000">
                                {$ns.langManager->getPhraseSpan(324)}
                            </span>
                        {/if}
                    {/if}
                </div>
            {/if}
            {if $ns.user_points_applicable == 'true'}
                <div style="clear: both"> </div>			
                {*}order grand total text{*}
                <div class="grid_30" style="float: left;text-align: left;font-size: 16px;">
                    {$ns.langManager->getPhraseSpan(388)}
                </div>
                <div class="grid_2" style="float: left;text-align: left;margin-top: 3px">
                    <input id="cho_apply_user_points" type="checkbox" {if $ns.req_params.cho_apply_user_points == 1} checked="checked"{/if}/>
                </div>

                <div class="grid_20" style="float: left;text-align: left;{if $ns.req_params.cho_apply_user_points != 1}color:#aaa{/if}">
                    -{$ns.usablePoints} Դր.
                </div>

            {/if}		

            {if $ns.vip_enabled ==0 && $ns.userLevel !== $ns.userGroupsAdmin && $ns.userLevel !== $ns.userGroupsCompany}
                <div style="clear: both"> </div>	
                {*promo code input*}
                <div class="grid_30" style="float: left;text-align: left;">
                    promo code
                </div>

                <div class="grid_13" style="float: left;text-align: left;">
                    {if $ns.cartTotalDealsDiscountAMD>0}
                        {$ns.cartTotalDealsDiscountAMD} Դր.
                    {/if}
                </div>
                {if $ns.req_params.cho_payment_type == 'cash'}
                    <form id="f_promo_code_form">
                        <input id="promo_code_value" type="text" style="float:left">
                        <a class="button" id="promo_code_apply_button" style="float:left;height:22px;margin-left:5px;">apply</a>					
                        <input type="hidden" id="cho_promo_codes" value="{$ns.req_params.cho_promo_codes}"/>
                    </form>
                {/if}
            {/if}

            <div style="clear: both"> </div>	
            {*order grand total text*}
            <div class="grid_30" style="float: left;text-align: left">
                {$ns.langManager->getPhraseSpan(262)}
            </div>
            {*}order grand total USD{*}
            {if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
                <div class="grid_13" style="float: left;text-align: left">
                    ${$ns.grandTotalUSD|number_format:1}
                </div>
            {/if}
            {*}order grand total AMD{*}
            {if $ns.priceVariety == 'both' or $ns.priceVariety == 'amd'}
                {if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
                    {* putting AND bofore printing the amd price *}
                    <div class="grid_5" style="float: left;text-align: left">
                        {$ns.langManager->getPhraseSpan(270)}
                    </div>
                {/if}
                <div class="grid_15" style="float: left;text-align: left">
                    {$ns.grandTotalAMDWithShipping|number_format:0} Դր.
                </div>
            {/if}	

        {else}
            <div class="grid_30" style="float: left;text-align: left;">
                {$ns.langManager->getPhraseSpan(426)}
            </div>
            <div class="grid_15" style="float: left;text-align: left;">
                {$ns.creditSupplierDisplayName}
            </div>
            <div style="clear: both"> </div>
            <div class="grid_30" style="float: left;text-align: left;">
                {$ns.langManager->getPhraseSpan(424)}
            </div>
            <div class="grid_15" style="float: left;text-align: left;">
                {$ns.req_params.cho_selected_credit_months} {$ns.langManager->getPhraseSpan(183)}
            </div>
            <div style="clear: both"> </div>
            <div class="grid_30" style="float: left;text-align: left;">
                {$ns.langManager->getPhraseSpan(425)}
            </div>
            <div class="grid_15" style="float: left;text-align: left;"  title="{$ns.langManager->getPhrase(561)}"
                 class="translatable_attribute_element" attribute_phrase_id="561" attribute_name_to_translate="title">
                {$ns.monthlyPaymentAmount} Դր. *
            </div>

            <div style="clear: both"> </div>
            <div class="grid_30" style="float: left;text-align: left;">
                {$ns.langManager->getPhraseSpan(427)}
            </div>
            <div class="grid_12" style="float: left;text-align: left;">
                {$ns.req_params.cho_selected_deposit_amount} Դր.				
            </div>
            {if $ns.grandTotalUSD>0}
                {if $ns.priceVariety == 'both' or $ns.priceVariety == 'usd'}
                    {* putting AND bofore printing the usd total *}
                    <div class="grid_5" style="float: left;text-align: left">
                        {$ns.langManager->getPhraseSpan(270)}
                    </div>
                {/if}
                <div class="grid_12" style="float: left;text-align: left">
                    ${$ns.grandTotalUSD|number_format:1}
                </div>
            {/if}	
        {/if}
        <div style="clear: both"> </div>	

    </div>
</div>

{if $ns.req_params.cho_do_shipping == 1 and $ns.shipping_billing_region_ok != true}	
    {assign var="nextButtonTitlePhraseId" value="315"}	
{/if}


{if $ns.emptyCart}	
    {assign var="nextButtonTitlePhraseId" value="296"}	
{else}
    {if !$ns.allItemsAreAvailable}
        {assign var="nextButtonTitlePhraseId" value="295"}	
    {/if}
{/if}


<div style="left: 10px;right:10px; bottom:40px;height:90px;position: absolute;line-height: 20px">
    <span style="color:#A06000;font-size: 16px;padding-left: 15px">{$ns.langManager->getPhraseSpan(414)}</span>
    </br>
    <div style="color:#000;font-size: 18px;margin-right: 10px">
        <input type="checkbox" id="agree_checkbox"/>
        <label for="agree_checkbox" >{$ns.langManager->getPhraseSpan(415)}</label>
        <div style="text-align: right; margin-top:5px;font-size: 14px; font-weight: bold;color: #A00">
            {$ns.langManager->getPhrase($nextButtonTitlePhraseId)}
        </div>
    </div>


</div>



{if !$ns.allItemsAreAvailable or $ns.emptyCart or ($ns.req_params.cho_do_shipping == 1 and $ns.shipping_billing_region_ok != true)}
    {assign var="disableButton" value='true'}
{else}
    {assign var="disableButton" value='false'}
{/if}

<input type="hidden" id = "cart_next_button_status_and_title" title="{$ns.langManager->getPhrase($nextButtonTitlePhraseId)}" phrase_id="{$nextButtonTitlePhraseId}" button_disabled="{$disableButton}"/>


