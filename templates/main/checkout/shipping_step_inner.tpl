<div style="left: 10px;right:10px; bottom:40px;top:40px;position: absolute;overflow: auto;font-size: 18px">
	<form id="shipping_details_form" style="padding: 10px;">
		<div style="margin-bottom: 20px;">
			<input id="cho_do_shipping" name="cho_do_shipping" type="checkbox" value="1" {if $ns.req_params.cho_do_shipping == '1'}checked="checked"{/if}/>
			<label for="cho_do_shipping">{$ns.langManager->getPhraseSpan(297)}</label>
		</div>
		<div id="cho_shipping_billing_details_container">
			<h2>{$ns.langManager->getPhraseSpan(290)}</h2>
			<div  style="margin-top: 10px;">
				<label for="cho_shipping_recipient_name" style="float:left;width:230px;">{$ns.langManager->getPhraseSpan(293)}</label>
				<input id="cho_shipping_recipient_name" name="cho_shipping_recipient_name" type="text" style="float:left;width: 200px"  value="{$ns.req_params.cho_shipping_recipient_name}"/>
			</div>
			<div style="clear: both"></div>
			<div  style="margin-top: 5px;">
				<label for="cho_shipping_address" style="float:left;width:230px;">{$ns.langManager->getPhraseSpan(13)}</label>
				<input id="cho_shipping_address" name="cho_shipping_address" type="text" style="float:left;width:200px;"value="{$ns.req_params.cho_shipping_address}"/>
			</div>
			<div style="clear: both"></div>
			<div  style="margin-top: 5px;">
				<label for="cho_shipping_region" style="float:left;width:230px;">{$ns.langManager->getPhraseSpan(45)}</label>				
				<select id="cho_shipping_region" name="cho_shipping_region"
						onkeyup="this.blur();
					this.focus();" name='region' class="cmf-skinned-select cmf-skinned-text" style="float:left"  >
					{foreach from=$ns.regions_phrase_ids_array item=value key=key}
								<option value="{$ns.langManager->getPhrase($value, 'en')}" {if $ns.req_params.cho_shipping_region == $ns.langManager->getPhrase($value, 'en')}selected="selected"{/if} class="translatable_element" phrase_id="{$value}">{$ns.langManager->getPhrase($value)}</option>
					{/foreach}		
					
				</select>
			</div>
			<div style="clear: both"></div>
			<div  style="margin-top: 5px;">
				<label for="cho_shipping_cell" style="float:left;width:230px;">{$ns.langManager->getPhraseSpan(309)}</label>
				<input id="cho_shipping_cell" name="cho_shipping_cell" type="text" style="float:left;width:150px;" value="{$ns.req_params.cho_shipping_cell}"/>
				<div id="cho_valid_cellphone_massage1" style="float:left;color:#F00;display: none;max-width: 500px;margin-left: 10px;">{$ns.langManager->getPhraseSpan(308)}</div>
			</div>
			<div style="clear: both"></div>
			<div  style="margin-top: 5px;">
				<label for="cho_shipping_tel" style="float:left;width:230px;">{$ns.langManager->getPhraseSpan(12)}</label>
				<input id="cho_shipping_tel" name="cho_shipping_tel" type="text" style="float:left;width:150px;"value="{$ns.req_params.cho_shipping_tel}"/>
			</div>

			<div style="clear: both"></div>
			<div  style="margin-top: 20px;">
				<input id="billing_is_different_checkbox" name="billing_is_different_checkbox" type="checkbox" value="1" {if $ns.req_params.billing_is_different_checkbox == '1'}checked="checked"{/if}/>			
				<label for="billing_is_different_checkbox">{$ns.langManager->getPhraseSpan(292)}</label>
			</div>
			<div style="clear: both"></div>

			<div id="billing_address_details_container" style="margin-top: 20px;">
				<h2>{$ns.langManager->getPhraseSpan(291)}</h2>
				<div  style="margin-top: 10px;">
					<label for="cho_billing_recipient_name" style="float:left;width:230px;">{$ns.langManager->getPhraseSpan(304)}</label>
					<input id="cho_billing_recipient_name" name="cho_billing_recipient_name" type="text" style="float:left;width: 200px"value="{$ns.req_params.cho_billing_recipient_name}"/>
				</div>
				<div style="clear: both"></div>
				<div  style="margin-top: 5px;">
					<label for="cho_billing_address" style="float:left;width:230px;">{$ns.langManager->getPhraseSpan(13)}</label>
					<input id="cho_billing_address" name="cho_billing_address" type="text" style="float:left;width:200px;"value="{$ns.req_params.cho_billing_address}"/>
				</div>
				<div style="clear: both"></div>
				<div  style="margin-top: 5px;">
					<label for="cho_billing_region" style="float:left;width:230px;">{$ns.langManager->getPhraseSpan(45)}</label>
					<select id="cho_billing_region" name="cho_billing_region"
							onkeyup="this.blur();
					this.focus();" name='region' class="cmf-skinned-select cmf-skinned-text" style="float:left"  >
						{foreach from=$ns.regions_phrase_ids_array item=value key=key}
								<option value="{$ns.langManager->getPhrase($value, 'en')}" {if $ns.req_params.cho_billing_region == $ns.langManager->getPhrase($value, 'en')}selected="selected"{/if} class="translatable_element" phrase_id="{$value}">{$ns.langManager->getPhrase($value)}</option>
					{/foreach}	
					</select>
					
				</select>
                    
				</div>
				<div style="clear: both"></div>
				<div  style="margin-top: 5px;">
					<label for="cho_billing_cell" style="float:left;width:230px;">{$ns.langManager->getPhraseSpan(309)}</label>
					<input id="cho_billing_cell" name="cho_billing_cell" type="text" style="float:left;width:150px;"value="{$ns.req_params.cho_billing_cell}"/>
					<div id="cho_valid_cellphone_massage2"  style="float:left;color:#F00;display: none;max-width: 500px;margin-left: 10px;">{$ns.langManager->getPhraseSpan(308)}</div>
				</div>
				<div style="clear: both"></div>
				<div  style="margin-top: 5px;">
					<label for="cho_billing_tel" style="float:left;width:230px;">{$ns.langManager->getPhraseSpan(12)}</label>
					<input id="cho_billing_tel" name="cho_billing_tel" type="text" style="float:left;width:150px;"value="{$ns.req_params.cho_billing_tel}"/>
				</div>
				<div style="clear: both"></div>
			</div>
		</div>
	</form>
</div>
