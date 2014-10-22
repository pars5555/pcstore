<div style="width: 100%;">
	{if $ns.checkPassword == 1}
		<div style="position:absolute;top:100px;width:100%;text-align: center">
			{if isset($ns.message)}
				<div style="color:red;margin:10px;font-size: 16px">
					{$ns.message}
				</div>
			{/if} 
			<form id="scp_password_form">
				<input id="scp_password_check" type="password" autocomplete="off" placeholder="{$ns.langManager->getPhrase(4)}"/>
				<div style="clear:both;margin-bottom: 10px"></div>
                                <button class="button blue">{$ns.langManager->getPhraseSpan(582)}</button>
                                
			</form>
		</div>
	{else}
		<div style="width: 100%;">

			<div class="box-header">
				<h1>{$ns.langManager->getPhraseSpan(20)}</h1>
			</div >
			<form id='cp_form' target="cp_upload_target" enctype="multipart/form-data" method="post" autocomplete="off"
				  action="{$SITE_PATH}/dyn/company/do_change_service_company_profile" style="padding: 10px;margin: 10px;position: relative">

				<div style="line-height:20px;float: left;width:160px;text-align: left;">
					<label for="cp_branch_select" >{$ns.langManager->getPhraseSpan(562)}: </label>
				</div>
				<select id="cp_branch_select" name="cp_branch_select" class="cmf-skinned-select cmf-skinned-text" style="float:left">
					{html_options values=$ns.company_branches_ids selected=$ns.selected_company_branch_id output=$ns.company_branches_names} 
				</select>
				<div id="cp_add_company_branch" class="pcc_add_item_icon"></div>
				<div id="cp_remove_selected_company_branch" class="pcc_remove_selected_item_icon"></div>
				<div style="clear: both"></div>



				<div style="width: 50%;float: left;">


					<p id = "cp_error_p"  style="display: none;">
						<span class="error"> </span>
						<span id="cp_error_message" style="color:#FF4422;" > </span>
					</p>

					<p>
					<div style="float: left;width:160px;text-align: left;">
						<label for="email">{$ns.langManager->getPhraseSpan(21)}: </label>
					</div>
					<label name= 'email'>{$ns.user->getEmail()}</label>
					</p>
					<p>
					<div style="float: left;width:160px;text-align: left;">
						<label for="rating">{$ns.langManager->getPhraseSpan(22)}: </label>
					</div>
					<label name= 'rating'>{$ns.user->getRating()}</label>

					</p>

					<p>
					<div style="line-height:20px;float: left;width:160px;text-align: left;">
						<label for="company_name">{$ns.langManager->getPhraseSpan(23)}:</label>
					</div>
					<input name='company_name' type="text" value="{$ns.user->getName()}"/>
					</p>
					<p>

					<div style="float: left;width:160px;text-align: left;">
						<label for="short_name">{$ns.langManager->getPhraseSpan(24)}:</label>
					</div>
					<label name= 'short_name'>{$ns.user->getShortName()}</label>
					</p>


					<p>
					<div style="line-height:20px;float: left;width:160px;text-align: left;">
						<label for="url">{$ns.langManager->getPhraseSpan(25)}:</label>
					</div>

					<input name='url' type="text" value="{$ns.user->getUrl()}"/>

					</p>

					<p>
					<div style="line-height:20px;float: left;width:160px;text-align: left;">
						<label for="change_pass">{$ns.langManager->getPhraseSpan(27)}:</label>
					</div>

					<input  name='change_pass' type="checkbox" id="password_change_checkbox"/>
					</p>

					<div id="change_password_div" style="padding-left: 20px">
						<p>
						<div style="line-height:20px;float: left;width:160px;text-align: left;">
							<label for="new_pass">{$ns.langManager->getPhraseSpan(28)}:</label>
						</div>

						<input  name='new_pass' type="password"/>
						</p>

						<p>
						<div style="line-height:20px;float: left;width:160px;text-align: left;">
							<label for="repeat_new_pass">{$ns.langManager->getPhraseSpan(29)}:</label>
						</div>

						<input  name='repeat_new_pass' type="password"/>
						</p>
					</div>

					<p>
					<div style="line-height:20px;float: left;width:160px;text-align: left;">
						<label for="dealers_access_key">{$ns.langManager->getPhraseSpan(30)}:</label>
					</div>

					<input  name='dealers_access_key' type="text" value="{$ns.user->getAccessKey()}" />
					</p>

					<p>
					<div style="line-height:20px;float: left;width:160px;text-align: left;">
						<label for="phone1">{$ns.langManager->getPhraseSpan(33)} 1:</label>
					</div>

					<input  name='phone1' type="text" value="{$ns.phones[0]}" />
					</p>
					<p>
					<div style="line-height:20px;float: left;width:160px;text-align: left;">
						<label for="phone2">{$ns.langManager->getPhraseSpan(33)} 2:</label>
					</div>

					<input name='phone2' type="text" value="{$ns.phones[1]}"/>
					</p>
					<p>
					<div style="line-height:20px;float: left;width:160px;text-align: left;">
						<label for="phone3">{$ns.langManager->getPhraseSpan(33)} 3:</label>
					</div>

					<input name='phone3' type="text" value="{$ns.phones[2]}"/>
					</p>
					<p>
					<div style="line-height:20px;float: left;width:160px;text-align: left;">
						<label for="longitute_latitude">longitute/latitude:</label>
					</div>

					<input name='longitute' type="text" style="width:75px" value="{$ns.lng}"/>
					<input name='latitude' type="text" style="width:75px" value="{$ns.lat}"/>
					</p>


				</div>
				<div style="width: 50%;float: left;">
					<p>
					<div style="line-height:20px;float: left;width:160px;text-align: left;">
						<label for="address">{$ns.langManager->getPhraseSpan(13)}:</label>
					</div>

					<input name='address' type="text" value="{$ns.branch_address}"/>
					</p>

					<p>
					<div style="line-height:25px;float: left;width:160px;text-align: left;">
						<label for="working_days">{$ns.langManager->getPhraseSpan(34)}:</label>
					</div>
					<table  style="width: 154px;height: 30px">
						<tr>
							<th width="22px">{$ns.langManager->getPhraseSpan(35)}</th>
							<th width="22px">{$ns.langManager->getPhraseSpan(36)}</th>
							<th width="22px">{$ns.langManager->getPhraseSpan(37)}</th>
							<th width="22px">{$ns.langManager->getPhraseSpan(38)}</th>
							<th width="22px">{$ns.langManager->getPhraseSpan(39)}</th>
							<th width="22px">{$ns.langManager->getPhraseSpan(40)}</th>
							<th width="22px">{$ns.langManager->getPhraseSpan(41)}</th>
						<tr>
						<tr>
							<th>
								<input name='monday_checkbox' id='monday_checkbox' type="checkbox"/>
							</th>
							<th>
								<input name='tuseday_checkbox' id='tuseday_checkbox' type="checkbox"/>
							</th>
							<th>
								<input name='wednesday_checkbox' id='wednesday_checkbox' type="checkbox"/>
							</th>
							<th>
								<input name='thursday_checkbox' id='thursday_checkbox' type="checkbox"/>
							</th>
							<th>
								<input name='friday_checkbox' id='friday_checkbox' type="checkbox"/>
							</th>
							<th>
								<input name='saturday_checkbox' id='saturday_checkbox' type="checkbox"/>
							</th>
							<th>
								<input name='sunday_checkbox' id='sunday_checkbox' type="checkbox"/>
							</th>
						<tr>
					</table>
					<!--input name='working_days' type="text" value="{$ns.user->getWorkingDays()}"/-->
					</p>

					<p>
					<div style="line-height:20px;float: left;width:160px;text-align: left;">
						<label for="region">{$ns.langManager->getPhraseSpan(45)}:</label>
					</div>
					<select  onkeyup="this.blur();
							this.focus();" name='region' class="cmf-skinned-select cmf-skinned-text" style="width:110px">
						{foreach from=$ns.regions_phrase_ids_array item=value key=key}
							<option value="{$ns.langManager->getPhrase($value, 'en')}" {if $ns.region_selected == $ns.langManager->getPhrase($value, 'en')}selected="selected"{/if} class="translatable_element" phrase_id="{$value}">{$ns.langManager->getPhrase($value)}</option>
						{/foreach}	
					</select>

					</p>
					<p>
					<div style="line-height:25px;float: left;width:160px;text-align: left;">
						<label for="select_logo_button">{$ns.langManager->getPhraseSpan(42)}:</label>
					</div>

					<input id="cp_selected_logo_name" type="text" readonly="readonly" value="select a logo..." style="line-height: 50px;float:left"/>					
					<input type="button" id ="select_logo_button" class="button glyph"  value = '...' style="line-height: 50px;float:left"/>

					<div style="position: absolute;right: 0px;top:0px;">
						<img src="{$SITE_PATH}/images/sc_big_logo/{$ns.user->getId()}/logo.png?{$smarty.now}" />
					</div>


					</p>

					<p>
					<div style="line-height:20px;float: left;width:350px;text-align: left;color:#006600">
						<label for="receive_email_on_stock_update">{$ns.langManager->getPhraseSpan(416)}:</label>
					</div>

					<input  name='receive_email_on_stock_update' type="checkbox" id="receive_email_on_stock_update"  
					{if $ns.user->getReceiveEmailOnStockUpdate() == 1} checked="checked" {/if}/>
				</p>
				<div style="clear: both"></div>



			</div>
			<input id="cp_submit_form_button" type="submit" style="display: none"/>
			<input id="cp_file_input" name="service_company_logo"  type="file" accept="image/*" style="display:none" />
			<iframe id="cp_upload_target" name="cp_upload_target" style="width:0;height:0;border:0px solid #fff;display: none;" ></iframe>
			<input  type="hidden" name="working_days" id="working_days" value="{$ns.working_days}"/>
		</form>

	</div>

	<div style="clear: both"></div>

	<div id = "cp_bottom_div" style=" width:100%;text-align: center;margin: 50px 0 20px 0;float: left">

		<button id="cp_save_button" class="button blue" >
			{$ns.langManager->getPhraseSpan(43)}

		</button>
		<button id="cp_reset_button" class="button glyph">
			{$ns.langManager->getPhraseSpan(44)}			
		</button>

	</div>
	<div style="clear: both"></div>
{/if}
</div>
