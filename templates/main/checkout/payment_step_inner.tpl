<div style="left: 10px;right:10px; bottom:40px;top:40px;position: absolute;">
	<form id="payment_details_form" style="padding: 10px;font-size: 16px" autocomplete="off">
		{include file="$TEMPLATE_DIR/main/checkout/payment/payment_types_radios.tpl"}
		<div style="clear: both;border-bottom: 2px dashed #666666;"> </div>
		{if $ns.req_params.cho_payment_type == '' || $ns.req_params.cho_payment_type == 'cash'}
			{include file="$TEMPLATE_DIR/main/checkout/payment/cash_payment.tpl"}
		{/if}
		{if $ns.req_params.cho_payment_type == 'credit'}
			{include file="$TEMPLATE_DIR/main/checkout/payment/credit_payment.tpl"}
		{/if}
		{if $ns.req_params.cho_payment_type == 'paypal'}
			{include file="$TEMPLATE_DIR/main/checkout/payment/paypal_payment.tpl"}
		{/if}
		{if $ns.req_params.cho_payment_type == 'arca'}
			{include file="$TEMPLATE_DIR/main/checkout/payment/arca_payment.tpl"}
		{/if}
		{if $ns.req_params.cho_payment_type == 'bank'}
			{include file="$TEMPLATE_DIR/main/checkout/payment/bank_transfer.tpl"}
		{/if}
		{if $ns.req_params.cho_payment_type == 'creditcard'}
			{include file="$TEMPLATE_DIR/main/checkout/payment/creditcard.tpl"}
		{/if}
	</form>
</div>
{if $ns.req_params.cho_payment_type == 'credit'}
	{if $ns.req_params.cho_do_shipping == 1}
		{assign var="nextButtonTitlePhraseId" value="421"}
		{assign var="disableButton" value='true'}	
	{else}
		{if $ns.req_params.cho_include_vat==1}
			{assign var="disableButton" value='true'}
			{assign var="nextButtonTitlePhraseId" value="566"}
		{else}
			{if $ns.minimum_credit_amount>$ns.grandTotalAMD-$ns.req_params.cho_selected_deposit_amount}
				{assign var="nextButtonTitlePhraseId" value="`433` `$ns.minimum_credit_amount`  Դր."}
				{assign var="disableButton" value='true'}		
			{/if}
		{/if}

	{/if}
{/if}

<input type="hidden" id = "cart_next_button_status_and_title" title="{$ns.langManager->getPhrase($nextButtonTitlePhraseId)}" phrase_id="{$nextButtonTitlePhraseId}" button_disabled="{$disableButton}"/>
