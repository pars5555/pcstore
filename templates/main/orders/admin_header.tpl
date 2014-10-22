<div style="padding: 10px;">
	<label for="f_show_only_select">show only: </label>
	<select onkeyup="this.blur();
			this.focus();" id="f_show_only_select" class="cmf-skinned-select cmf-skinned-text f_orders_status">

		{foreach from=$ns.showOnlyOrdersValues item=value key=key}
			<option value="{$value}" {if $ns.showOnlyOrdersSelected == $value}selected="selected"{/if} class="translatable_element" phrase_id="{$ns.showOnlyOrdersDisplayNamesPphraseIdsArray[$key]}">{$ns.showOnlyOrdersDisplayNames[$key]}</option>

		{/foreach}

	</select>
</div>
