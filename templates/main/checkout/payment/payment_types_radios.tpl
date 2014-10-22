<div style="text-align:left; float: left;line-height: 50px;">

    <input type="radio" name="cho_payment_type" id="cho_payment_type_cash" value="{$ns.payment_option_values.0}" {if $ns.cho_payment_type == '' or $ns.req_params.cho_payment_type == 'cash'}checked="checked"{/if} style="float:left;margin-top: 15px;"/>
    <label for="cho_payment_type_cash"><img src="{$SITE_PATH}/img/checkout/cash_48_48.png"style="float:left;margin-left: 5px;" />
        <div style="float: left;margin-left: 10px;">
            {$ns.langManager->getPhraseSpan($ns.payment_options_display_names_ids.0)}		
        </div> </label>
</div>
{if $ns.req_params.cho_do_shipping== 1}
    {assign var="pid" value=421}
{else}
    {if $ns.req_params.cho_include_vat== 1}
        {assign var="pid" value=584}
    {/if}

{/if}
<div style="text-align:left; float: left;line-height: 50px;margin-left: 15px;" {if $pid}title="{$ns.langManager->getPhrase($pid)}"
     class="translatable_attribute_element" attribute_phrase_id="{$pid}" attribute_name_to_translate="title"{/if}>
    <input type="radio" name="cho_payment_type" id="cho_payment_type_credit" value="{$ns.payment_option_values.1}" {if $ns.req_params.cho_payment_type == 'credit'}checked="checked"{/if} 
           {if $ns.req_params.cho_do_shipping== 1 || $ns.req_params.cho_include_vat==1}disabled="true"{/if} style="float:left;margin-top: 15px;"/>
    <label for="cho_payment_type_credit"><img src="{$SITE_PATH}/img/checkout/credit_48_48.png"style="float:left;margin-left: 5px;" />
        <div style="float: left;margin-left: 10px;">
            {$ns.langManager->getPhraseSpan($ns.payment_options_display_names_ids.1)}
        </div> </label>
</div>

<div style="text-align:left; float: left;line-height: 50px;margin-left: 15px;">
    <input type="radio" name="cho_payment_type" id="cho_payment_type_paypal" value="{$ns.payment_option_values.2}" {if $ns.req_params.cho_payment_type == 'paypal'}checked="checked"{/if} style="float:left;margin-top: 15px;"/>
    <label for="cho_payment_type_paypal"><img src="{$SITE_PATH}/img/checkout/paypal_48_48.png"style="float:left;margin-left: 5px;" />
        <div style="float: left;margin-left: 10px;">
            {$ns.langManager->getPhraseSpan($ns.payment_options_display_names_ids.2)}
        </div> </label>
</div>

<div style="text-align:left; float: left;line-height: 50px;margin-left: 15px;margin-bottom: 20px;">
    <input type="radio" name="cho_payment_type" id="cho_payment_type_arca" disabled="true" value="{$ns.payment_option_values.3}" {if $ns.req_params.cho_payment_type == 'arca'}checked="checked"{/if} style="float:left;margin-top: 15px;"/>
    <label for="cho_payment_type_arca"><img src="{$SITE_PATH}/img/checkout/arca_48_48.png"style="float:left;margin-left: 5px;" />
        <div style="float: left;margin-left: 10px;">
            {$ns.langManager->getPhraseSpan($ns.payment_options_display_names_ids.3)}
        </div> </label>
</div>

<div style="text-align:left; float: left;line-height: 50px;margin-left: 15px;">
    <input type="radio" name="cho_payment_type" id="cho_payment_type_bank" value="{$ns.payment_option_values.4}" {if $ns.req_params.cho_payment_type == 'bank'}checked="checked"{/if} style="float:left;margin-top: 15px;"/>
    <label for="cho_payment_type_bank"><img src="{$SITE_PATH}/img/checkout/bank_transfer_48_48.png"style="float:left;margin-left: 5px;" />
        <div style="float: left;margin-left: 10px;">
            {$ns.langManager->getPhraseSpan($ns.payment_options_display_names_ids.4)}
        </div> </label>
</div>

<div style="text-align:left; float: left;line-height: 50px;margin-left: 15px;">
    <input type="radio" name="cho_payment_type" id="cho_payment_type_creditcard" value="{$ns.payment_option_values.5}" {if $ns.req_params.cho_payment_type == 'creditcard'}checked="checked"{/if} style="float:left;margin-top: 15px;"/>
    <label for="cho_payment_type_creditcard"><img src="{$SITE_PATH}/img/checkout/credit_cards.png"style="float:left;margin-left: 5px;" />
        <div style="float: left;margin-left: 10px;">
            {$ns.langManager->getPhraseSpan($ns.payment_options_display_names_ids.5)}
        </div> </label>
</div>