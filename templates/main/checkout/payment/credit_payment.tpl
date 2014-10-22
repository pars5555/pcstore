{if $ns.req_params.cho_do_shipping == 1}
	<div style="margin-top: 10px">
		<span style="color:#000;font-size: 16px;font-weight: bold">
			{$ns.langManager->getPhraseSpan(421)}</br></br>
			{$ns.langManager->getPhraseSpan(422)}</br></br>
			{$ns.langManager->getPhraseSpan(307)}</br></br>
			{$ns.langManager->getPhraseSpan(423)}
		</span>

	</div>	

{else}
	{if $ns.req_params.cho_include_vat==1}
		<div style="margin-top: 10px">
			<span style="color:#000;font-size: 16px;font-weight: bold">
				{$ns.langManager->getPhraseSpan(566)}
			</span>

		</div>
	{else}
		<div style="color:#000;font-size: 16px;">
			{if $ns.grandTotalUSD>0}
				<p>
				<h3 style="color:red"
					class="translatable_search_content" phrase_id="`428`${$ns.grandTotalUSD}`352`"
					>{$ns.langManager->getPhraseSpan(428)}${$ns.grandTotalUSD}{$ns.langManager->getPhraseSpan(352)} </h3>
			</p>
		{/if}

		{* credit total amount*}
		<div style="margin-top: 10px">
			{$ns.langManager->getPhraseSpan(429)}: <span style="color:#000;font-size: 16px;font-weight: bold">{$ns.grandTotalAmdWithCommission} Դր.</span>
		</div>

		{* credit suppler select *}
		<div style="margin-top: 10px">
			{$ns.langManager->getPhraseSpan(426)}:
			<select id="cho_credit_supplier_id" name="cho_credit_supplier_id"
					onkeyup="this.blur();
							this.focus();" class="cmf-skinned-select cmf-skinned-text"  >
				{html_options values=$ns.creditSuppliersIds selected=$ns.req_params.cho_credit_supplier_id output=$ns.creditSuppliersDisplayNames}
			</select>
			{$ns.credit_supplier_interest_percent}%
			{if $ns.credit_supplier_annual_commision>0}
				+ {$ns.credit_supplier_annual_commision}% ({$ns.langManager->getPhraseSpan(568)})
			{/if}
		</div>


		{if ($ns.grandTotalAMD>=$ns.minimum_credit_amount)}
			{* credit months*}
			<div style="margin-top: 10px">
				{$ns.langManager->getPhraseSpan(424)}:
				<select id="cp_cho_selected_credit_months" name="cho_selected_credit_months" 
						onkeyup="this.blur();
							this.focus();" class="cmf-skinned-select cmf-skinned-text"  >
					{html_options values=$ns.possibleCreditMonths selected=$ns.req_params.cho_selected_credit_months output=$ns.possibleCreditMonths}
				</select> {$ns.langManager->getPhraseSpan(183)}
			</div>

			{assign var="credit_amount_include_deposit" value=$ns.grandTotalAMD-$ns.req_params.cho_selected_deposit_amount}	
			{* credit deposit amount*}
			<div style="margin-top: 10px">
				{$ns.langManager->getPhraseSpan(427)}:		
				<input id="cho_selected_deposit_amount" name="cho_selected_deposit_amount" type="text" value="{$ns.req_params.cho_selected_deposit_amount}" 
					   style="{if $ns.minimum_credit_amount>$credit_amount_include_deposit}color:red{/if}"/>
				<button id="calculate_credit_monthly_payments_button">
					{$ns.langManager->getPhraseSpan(430)}
				</button>
				{if $ns.minimum_credit_amount>$credit_amount_include_deposit}
					<span style="color:#AA0000;font-size: 16px;font-weight: bold;margin-left: 10px;">{$ns.langManager->getPhraseSpan(433)} {$ns.minimum_credit_amount} Դր.</span>
				{/if}

			</div>

			{* credit monthly payment*}
			<div style="margin-top: 10px">
				{$ns.langManager->getPhraseSpan(425)}: <span style="color:#000;font-size: 20px;font-weight: bold">{$ns.credit_monthly_payment} Դր.</span> ({$ns.langManager->getPhraseSpan(561)})
			</div>
		{else}
			<div style="margin-top: 10px;color:#993300">
				{$ns.langManager->getPhraseSpan(420)}: <span style="font-size: 16px;font-weight: bold">{$ns.minimum_credit_amount} Դր.</span>
			</div>

		{/if}
	</div>
{/if}	
{/if}


