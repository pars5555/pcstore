{if $ns.sms_sent == true}
	<input type="hidden" id = "co_confirm_button" value="{$ns.langManager->getPhrase(321)}" phrase_id="321">

{else}
	<input type="hidden" id = "co_send_sms_button" value="{$ns.langManager->getPhrase(317)}" phrase_id="317">

{/if}

<div  id = "confirm_cell_phone_number_div" title="{$ns.langManager->getPhrase(316)}">
	<div style="padding: 0 15px 0 15px;font-size: 14px;">
		{if $ns.errorMessage}
			<p>
			<div id="error_message" style="color:#FF4422;">
				{$ns.langManager->getPhraseSpan($ns.errorMessage)}				
			</div>
			</p>
		{/if}

		{if $ns.infoMessage}
			<p>
			{$ns.langManager->getPhraseSpan($ns.infoMessage)}
		</p>
	{/if}

	{if $ns.sms_sent}
		<form id="co_input_code_form">
			<p>
				<label for="co_code">{$ns.langManager->getPhraseSpan(322)}</label>
				<input id="co_code" name="co_code" type="text" value="{$ns.co_code}"/>
			<div>
				{$ns.langManager->getPhraseSpan(361)} {$ns.pcstore_contact_number}
			</div>
			</p>
		</form>
	{else}
		<form id="co_input_number_form">
			<p>
				<label for="co_cell_phone_number">{$ns.langManager->getPhraseSpan(309)}</label>
				<input id="co_cell_phone_number" name="co_cell_phone_number" type="text" {if $ns.cell_phone_editable == false} readonly="readonly"{/if} value="{$ns.cell_phone_number}"/>
			</p>
		</form>
	{/if}

</div>

</div>
{if $ns.order_confirmed == true}
	<input type="hidden" id='order_confirmed_element'/>
{/if}
