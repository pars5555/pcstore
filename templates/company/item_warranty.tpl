<div style="width: 100%;">
	<div id ="item_warranty_header_div" style="width: 100%;">

		<div style="position: relative;padding: 10px">
			<a href="{$SITE_PATH}/item_warranties/{$ns.company_id}/items_warranties.xls" style="position: absolute;right: 0px;top: 0px;color:green;font-size: 14px">{$ns.langManager->getPhraseSpan(515)}<img src="{$SITE_PATH}/img/document.png"/></a>

			<form name="add_item_warranty_form" id="add_item_warranty_form" style=" position: relative;padding-left: 10px;">
				<div style="float:left;width: 380px">

					<p>
					<div style="line-height:20px;float: left;width:110px;text-align: left;">
						<label for="serial_number">Serial Number:</label>
					</div>

					<input name="serial_number" id = "serial_number" value="{$ns.serial_number}" type="text" size="20"/>
					</p>
					<p>
					<p>
					<div style="line-height:20px;float: left;width:110px;text-align: left;">
						<label for="sell_date">{$ns.langManager->getPhraseSpan(118)}:</label>
					</div>
					{html_select_date prefix='SellDate' time=$ns.customer_warranty_start_date start_year='-20' end_year='+1' display_days=true}

					</p>
					<p>

					<div style="line-height:20px;float: left;width:110px;text-align: left;">
						<label for="item_buyer">{$ns.langManager->getPhraseSpan(119)}:</label>
					</div>

					<input name="item_buyer" id="item_buyer" type="text" value="{$ns.buyer}"  size="20"/>
					</p>

					<p>
					<div style="line-height:20px;float: left;width:110px;text-align: left;">
						<label for="item_category">{$ns.langManager->getPhraseSpan(120)}:</label>
					</div>

					<select onkeyup="this.blur();
								this.focus();" class="cmf-skinned-select cmf-skinned-text" name='item_category' id='item_category' style="width:120px" >
						{html_options values=$ns.item_warranty_categories_options  selected=$ns.item_warranty_categories_options_selected  output=$ns.item_warranty_categories_options}
					</select>
					</p>
					<p>
					<div style="line-height:20px;float: left;width:110px;text-align: left;">
						<label for="warranty_period">{$ns.langManager->getPhraseSpan(104)}:</label>
					</div>
					<select onkeyup="this.blur();
								this.focus();" class="cmf-skinned-select cmf-skinned-text" name='warranty_period' id='warranty_period' style="width:120px" >
						{html_options values=$ns.item_warranty_options  selected=$ns.item_warranty_options_selected  output=$ns.item_warranty_options}
					</select>

					{if ($ns.warranty_item_id)}
						<div style="line-height:20px;float: left;width: 100%" align="right">
							<button id="save_item_warranty_button" class="button blue">
								Save
							</button>
							<button id="cancel_item_warranty_button" class="button glyph">
								Cancel
							</button>
						</div>
					{else}
						<div style="line-height:20px;float: left;width: 100%" align="right">
							<button id="add_item_warranty_button" class="button glyph">
								{$ns.langManager->getPhraseSpan(125)}
							</button>
						</div>
					{/if}
					</p>

				</div>
				<div style="float:left;width: 350px;position: relative">

					<p>
					<div style="line-height:20px;float: left;width:110px;text-align: left;">
						<label for="item_supplier">{$ns.langManager->getPhraseSpan(122)}:</label>
					</div>
					<input name="item_supplier" id="item_supplier" type="text" size="20" value="{$ns.supplier}"/>
					</p>
					<p>
					<div style="line-height:20px;float: left;width:110px;text-align: left;">
						<label for="supplier_sell_date">{$ns.langManager->getPhraseSpan(123)}</label>
					</div>
					{html_select_date prefix='SupplierSellDate' time=$ns.supplier_warranty_start_date start_year='-20' display_days=true}
					</p>
					<p>
					<div style="line-height:20px;float: left;width:110px;text-align: left;">
						<label for="supplier_warranty_period">{$ns.langManager->getPhraseSpan(124)}:</label>
					</div>
					<select onkeyup="this.blur();
								this.focus();" class="cmf-skinned-select cmf-skinned-text" name='supplier_warranty_period' id='supplier_warranty_period' style="width:120px" >
						{html_options values=$ns.item_warranty_options  selected=$ns.supplier_warranty_period  output=$ns.item_warranty_options}
					</select>

					</p>

				</div>

			</form>
			<div style="clear: both"></div>
		</div>

		<div style="float: right;margin:10px">
			<form name="search_item_warranty_form" id="search_item_warranty_form">

				<input placeholder="Serial Number"  name="search_serial_number" id="search_serial_number" value="{$ns.search_serial_number}" type="text"/>
				<button id="add_item_warranty_button" class="button blue">
					{$ns.langManager->getPhraseSpan(91)}
				</button>


			</form>
		</div>
		<div style="clear: both"></div>
	</div>
	<div style="width: 100%;">
		{if (($ns.ItemsWarranties |@count )>0)}
			<div id="iw_items_container">
				<div style="text-align: center;margin: 30px">
					<h1>{$ns.langManager->getPhraseSpan(128)}</h1>
				</div>
				<div style="width: 100%">
					<table cellspacing="0"  style="width:100%">
						<thead>						
						<th>Serial Number</th>
						<th>{$ns.langManager->getPhraseSpan(119)}</th>
						<th>{$ns.langManager->getPhraseSpan(120)}</th>
						<th>{$ns.langManager->getPhraseSpan(118)}</th>
						<th>{$ns.langManager->getPhraseSpan(104)}</th>
						<th>{$ns.langManager->getPhraseSpan(122)}</th>
						<th> </th>
						<th> </th>
						</thead>
						<tbody>
							{foreach from=$ns.ItemsWarranties item=iw_dto name=iw}
								<tr>						
									<td>{$iw_dto->getSerialNumber()}</td>
									<td style="white-space: normal;">{$iw_dto->getBuyer()}</td>
									<td>{$iw_dto->getItemCategory()}</td>
									<td>{$iw_dto->getCustomerWarrantyStartDate()}</td>
									<td>{$iw_dto->getCustomerWarrantyPeriod()}</td>
									<td>{$iw_dto->getSupplier()}</td>
									<td><a class="edit_item_warranty_link" id="items_warranty_edit^{$iw_dto->getId()}" href="javascript:void(0);" style="color:red">{$ns.langManager->getPhraseSpan(112)}...</a></td>
									<td><a class="delete_item_warranty_link" id="items_warranty_delete^{$iw_dto->getId()}" href="javascript:void(0);" style="color:red">{$ns.langManager->getPhraseSpan(71)}...</a></td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
		{else}
			<div style="padding-top: 30px ">
				<div style="text-align: center">
					<h1>{$ns.langManager->getPhraseSpan(129)}</h1>
				</div>
			</div>
		{/if}
	</div>
</div>
{if ($ns.warranty_item_id)}
	<input type="hidden" id="warranty_item_id" value="{$ns.warranty_item_id}"/>
{/if} 