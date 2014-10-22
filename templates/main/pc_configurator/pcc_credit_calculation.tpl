<div style="border-top: 1px solid #666;margin-top: 10px;font-size: 14px;padding: 10px">

	<div style="text-align: center;padding: 10px;">
		<h4>{$ns.langManager->getPhraseSpan(432)}</h4>
	</div>

	<div style="color:#000;font-size: 14px;">
		{if $ns.req_params.pcGrandTotalUsd>0}
			<p>
			<div style="color:red">
				<span class="translatable_search_content" phrase_id="`428`${$ns.grandTotalUSD}`352`"
					  >{$ns.langManager->getPhraseSpan(428)}${$ns.req_params.pcGrandTotalUsd}{$ns.langManager->getPhraseSpan(352)}</span>
			</div>
			</p>
		{/if}

		{* credit total amount*}
		<div style="margin-top: 10px">
			{$ns.langManager->getPhraseSpan(429)}: <span style="color:#000;font-size: 16px;font-weight: bold">{$ns.grandTotalAmdWithCommission|number_format:0} Դր.</span>
		</div>

		<form id="pcc_credit_calculation_form" autocomplete="off">

			{* credit suppler select *}
			<div style="margin-top: 10px">
				{$ns.langManager->getPhraseSpan(426)}:
				<select id="pcc_credit_supplier_id" name="pcc_credit_supplier_id"
						onkeyup="this.blur();
								this.focus();" class="cmf-skinned-select cmf-skinned-text"  >
					{html_options values=$ns.creditSuppliersIds selected=$ns.req_params.pcc_credit_supplier_id output=$ns.creditSuppliersDisplayNames}
				</select>
			</div>
			<div style="margin-top: 10px">
				{$ns.langManager->getPhraseSpan(431)}: <span style="color:#000;font-size: 16px;font-weight: bold">
					{$ns.credit_supplier_interest_percent}%

				</span>

			</div>	
			{if $ns.credit_supplier_annual_commision>0}
				<div style="margin-top: 10px">
					{$ns.langManager->getPhraseSpan(568)}: <span style="color:#000;font-size: 16px;font-weight: bold">
						{$ns.credit_supplier_annual_commision}%
					</span>
				</div>{/if}

				{if ($ns.req_params.pcGrandTotalAmd>$ns.minimum_credit_amount)}
					{* credit months*}
					<div style="margin-top: 10px">
						{$ns.langManager->getPhraseSpan(424)}:
						<select id="pcc_selected_credit_months" name="pcc_selected_credit_months"
								onkeyup="this.blur();
								this.focus();" class="cmf-skinned-select cmf-skinned-text"  >
							{html_options values=$ns.possibleCreditMonths selected=$ns.req_params.pcc_selected_credit_months output=$ns.possibleCreditMonths}
						</select>
						{$ns.langManager->getPhraseSpan(183)|mb_strtolower}
					</div>

					{* credit deposit amount and calculate button*}
					<div style="margin-top: 10px">
						{$ns.langManager->getPhraseSpan(427)}:
						<input id="pcc_selected_deposit_amount" name="pcc_selected_deposit_amount" type="text" value="{$ns.req_params.pcc_selected_deposit_amount}" style="width: 100px"/>
						Դր.
						<button id="pcc_credit_calculate_button">
							{$ns.langManager->getPhraseSpan(430)}
						</button>
					</div>

					{* credit monthly payments*}
					<div style="margin-top: 10px"  title="{$ns.langManager->getPhrase(561)}"
						 class="translatable_attribute_element" attribute_phrase_id="561" attribute_name_to_translate="title">
						{$ns.langManager->getPhraseSpan(425)}: <span  style="color:#993300;font-size: 20px;font-weight: bold">{$ns.credit_monthly_payment|number_format:0} Դր. *</span>
					</div>

				{else}
					<div style="margin-top: 10px;color:#993300">
						{$ns.langManager->getPhraseSpan(420)}: <span style="font-size: 16px;font-weight: bold">{$ns.minimum_credit_amount|number_format:0} Դր.</span>
					</div>

				{/if}


				<input type="hidden" value="{$ns.req_params.pcGrandTotalAmd}" name="pcGrandTotalAmd"/>
				<input type="hidden" value="{$ns.req_params.pcGrandTotalUsd}" name="pcGrandTotalUsd"/>
			</form>
		</div>

	</div>