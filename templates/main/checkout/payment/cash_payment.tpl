<div style="margin-top: 10px;font-size: 18px;line-height: 20px">
{if $ns.req_params.cho_do_shipping == '1'}
	<div style="padding: 10px;">
		<h3>{$ns.langManager->getPhraseSpan(305)}</h3><br/>
		{if $ns.req_params.billing_is_different_checkbox == 1}
			 {$ns.req_params.cho_billing_recipient_name}<br/>
			 {$ns.req_params.cho_billing_address}<br/>
			 {$ns.req_params.cho_billing_region}<br/>
			 {$ns.req_params.cho_billing_tel}
		 {else}
			 {$ns.req_params.cho_shipping_recipient_name}<br/>
			 {$ns.req_params.cho_shipping_address}<br/>
			 {$ns.req_params.cho_shipping_region}<br/>
			 {$ns.req_params.cho_shipping_tel}
		 {/if}
	</div>
{else}
	<div style="padding: 10px;">
		<h3>{$ns.langManager->getPhraseSpan(306)}</h3><br/>
		{$ns.langManager->getPhraseSpan(307)}
			
	</div>
{/if}
</div>