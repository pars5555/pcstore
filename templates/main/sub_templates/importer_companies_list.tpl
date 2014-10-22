{if (($ns.allCompanies|@count )>0)}
	<h1 class="avo_style_CompaniesBlockTitle">{$ns.langManager->getPhraseSpan(578)}</h1>
{/if}

<div class="avo_style_importerCompaniesBlock">    	
	{if ($ns.userLevel === $ns.userGroupsUser)}
		<div style="width:100%;text-align: center;margin-bottom: 50px">
			<div style="font-size: 20px;color: #500;
				 text-shadow:
				 -1px -1px 0 #fff,  
				 1px -1px 0 #fff,
				 -1px 1px 0 #fff,
				 1px 1px 0 #fff;padding: 5px;font-weight: bold">{$ns.langManager->getPhraseSpan(570)}</div><br>
			{foreach from=$ns.allNonHiddenCompanies item=company name=cl}
				<img style="margin:3px;vertical-align: middle" src="{$SITE_PATH}/images/small_logo/{$company->getId()}" alt="logo"/>
			{/foreach}			
			<div style="font-size: 14px;color: #500;">{$ns.langManager->getPhraseSpan(571)}</div>
			<button  id="add_company_button" class="button" style="margin-top: 10px">
				{$ns.langManager->getPhraseSpan(8)}...
			</button>

		</div>
	{/if}
	<div style="clear:both"></div>
	<div>
		<div class="avo_style_blockLeft">
			{$ns.langManager->getPhraseSpan(569)}:
			<input type="search" id="cl_search_in_prices" value="{$ns.search_text}"/>			
		</div>

		<a href="{$SITE_PATH}/price/all_zipped_prices">			
			<span>Download All Prices:</span>
			<img style="vertical-align: middle" src = "{$SITE_PATH}/img/file_types_icons/zip_icon.png"  alt="zip"/>
		</a>


		<div class="avo_style_blockRight">
			{$ns.langManager->getPhraseSpan(454)}
			<select id="f_show_only_last_hours_select" name="cho_credit_supplier_id" 
					onkeyup="this.blur();
							this.focus();" class="cmf-skinned-select cmf-skinned-text">
				{foreach from=$ns.show_only_last_hours_values item=value key=key}
					<option value="{$value}" {if $ns.show_only_last_hours_selected == $value}selected="selected"{/if} class="translatable_element" phrase_id="{$ns.show_only_last_hours_names_phrase_ids_array[$key]}">{$ns.show_only_last_hours_names[$key]}</option>

				{/foreach}
			</select>
			{$ns.langManager->getPhraseSpan(458)}
		</div>

	</div>
	<div style="clear:both"></div>

</div>


{if (($ns.allCompanies|@count )>0)}
	<table class="avo_style_companyesTable" id="icl_list_container_table" cellspacing="0">
		<thead>
			<tr>
				<th></th>
				<th>{$ns.langManager->getPhraseSpan(61)}</th>
				<th>{$ns.langManager->getPhraseSpan(31)}</th>
				<th>{$ns.langManager->getPhraseSpan(10)}</th>                        
				<th>{$ns.langManager->getPhraseSpan(12)}</th>
				<th>{$ns.langManager->getPhraseSpan(13)}</th>
				<th>{$ns.langManager->getPhraseSpan(26)}</th>
			</tr>
		</thead>
		<tbody style="line-height: 14px">
			{foreach from=$ns.allCompanies item=company name=cl}
				
				{assign var="companyId" value = $company->getId()}

				<tr class="{if $smarty.foreach.cl.index % 2 == 1}avo_style_blueTableBox{/if}">
					<td class="avo_style_companyesTableCellIndexBox">{$smarty.foreach.cl.index+1}</td>
					<td> 

						{assign var="passive" value=$company->getPassive()}							
							{$company->getName()}
						{if $passive != 1}
							<div class="clear"></div>
							{assign var="rating" value=$company->getRating()}
							<div class="classification" title="{$rating}%">
								<div class="cover"> </div>
								<div class="progress" style="width:{$rating}%;"> </div>								
							</div> 
						{/if}
						{if $ns.userLevel === $ns.userGroupsCompany && $ns.userId == $companyId}
							<div class="clear"></div>
							<div style="cursor: pointer" title="{$ns.langManager->getPhrase(377)} {$company->getAccessKey()}"
								 class="translatable_attribute_element" attribute_phrase_id="377" attribute_name_to_translate="title">
								<img src="{$SITE_PATH}/img/increase_rating.png" />
							</div>
						{/if}
					</td>
					<td>
						<a href="javascript:void(0);" class="company_gmap_pin" company_id="{$companyId}"><img src="{$SITE_PATH}/img/google_map_pin.png" width=40 alt="logo"/></a>
							{assign var="url" value=$company->getUrl()}	
						<a {if $url != ''}href="http://{$url}"{/if}
										  target="_blank" title="{$url|default:$ns.langManager->getPhrase(376)}"									
										  class="translatable_attribute_element" attribute_phrase_id="{if !empty($url)}376{/if}" attribute_name_to_translate="title">
							<img {if $passive == 1} class="grayscale"{/if} src="{$SITE_PATH}/images/small_logo/{$companyId}" alt="logo"/>
						</a>
					</td>
					
					<td class="avo_style_price_files_content">
						{if ($ns.userLevel === $ns.userGroupsCompany)}
							<div class="squaredOne" style="float:left;margin: 13px 15px 0 0;" title="{$ns.langManager->getPhrase(399)}"
								 class="translatable_attribute_element" attribute_phrase_id="399" attribute_name_to_translate="title">
								{assign var="interested_companies_ids_for_sms" value=$ns.user->getInterestedCompaniesIdsForSms()}
								{assign var="interested_companies_ids_for_sms_array" value=','|explode:$interested_companies_ids_for_sms}

								<input type="checkbox" id="receive_sms_from^{$companyId}" autocomplete="off" class="f_receive_sms_from_checkboxes" value="1"	style="visibility: hidden;" 
									{if in_array($companyId,$interested_companies_ids_for_sms_array)} checked="checked"{/if} >
								<label for="receive_sms_from^{$companyId}"></label>
							</div>
						{/if}
						<div style="float:left;margin-right: 7px;margin-top:12px">
							<a href="javascript:void(0);" class="price_scroll_left_a" company_id="{$companyId}">
								<img src = "{$SITE_PATH}/img/price_left_arrow.png"  alt=">"/> 
							</a>
						</div>
	
						<div style="overflow: hidden;float:left;max-width: 120px;min-width: 120px;">
							<div id="price_files_content_{$companyId}">                                                                   
								
								{* company last price*}							
								{if $company->getPriceId()>0}
									<a  style="display: inline-block;white-space: nowrap" href="{$SITE_PATH}/price/last_price/{$companyId}"> 
										{assign var="company_last_price_ext" value=$company->getPriceExt()} 										
										{assign var="priceListDate" value=$company->getPriceUploadDateTime()} 										
										{assign var="icon_local_path" value="`$ns.DOCUMENT_ROOT`/img/file_types_icons/`$company_last_price_ext`_icon.png"} 		
										{assign var="icon_path" value="`$SITE_PATH`/img/file_types_icons/`$company_last_price_ext`_icon.png"} 		
										<img style="float:left;" src = "{if file_exists($icon_local_path)}{$icon_path}{else}{$SITE_PATH}/img/file_types_icons/unknown_icon.png{/if}"  alt="document"/>
										<div style="clear: both"></div>
										<span style="word-wrap: break-word;float:left;color:{$ns.companiesPriceListManager->getCompanyPriceColor($priceListDate)}"> 
											{if $priceListDate}
												{$priceListDate|date_format:"%m/%d"}
												<br />
												{$priceListDate|date_format:"%H:%M"}
											{else}
												{$ns.langManager->getPhraseSpan(14)}
											{/if} </span> 
									</a>
								{/if}
								{* company previouse prices*}
								{*assign var="companyHistoryPrices" value = $ns.companiesPriceListManager->getCompanyHistoryPricesOrderByDate($companyId,0,100)*}	
								{foreach from=$ns.groupCompaniesZippedPricesByCompanyId[$companyId] item=pr}
									<a  style="display: inline-block;white-space: nowrap;margin-left:5px" href="{$SITE_PATH}/price/zipped_price_unzipped/{$pr->getId()}"> 
										<img style="float:left" src = "{$SITE_PATH}/img/file_types_icons/zip_icon.png"  alt="zip"/>
										<div style="clear: both"></div>
										{assign var="uploadDateTime" value = $pr->getUploadDateTime()}					
										<span style="word-wrap: break-word;float:left;"> 
											{if $uploadDateTime}
												{$uploadDateTime|date_format:"%m/%d"}
												<br />
												{$uploadDateTime|date_format:"%H:%M"}												
											{/if} </span> 
									</a>
								{/foreach}
	
	
	
	
							</div>
						</div>
						<div style="float:left;margin-left: 7px;margin-top:12px">
							<a href="javascript:void(0);" class="price_scroll_right_a" company_id="{$companyId}">
								<img src = "{$SITE_PATH}/img/price_right_arrow.png"  alt="<"/> 
							</a>
						</div>
					</td>
	
					<td> 
						<span style="display: inline-block;"> 
							{assign var=phones value=","|explode:$company->getPhones()}
							{foreach from=$phones item=phone}
								{$phone}<br/>
							{/foreach}  
						</span>
					</td>
	
	
					<td style="white-space: normal;">
	
						{assign var="addrs" value=";"|explode:$company->getStreet()}					
						{assign var="zips" value=","|explode:$company->getZip()}					
						{assign var="regions" value=","|explode:$company->getRegion()}					
	
						<span style="display: inline-block;">
							{foreach from=$addrs item=addr key=index}
								{assign var="region_phrase_id" value=$ns.langManager->getPhraseIdByPhraseEn($regions[$index])}
								{$addr}, {$zips[$index]}, {$ns.langManager->getPhraseSpan($region_phrase_id)}<br/>
							{/foreach}  
						</span>
					</td>
					<td style="min-width: 160px;max-width: 250px;white-space: normal;" >
						{if $passive != 1}
				
							{assign var="company_offers" value=$company->getOffers()}
				
							{if $ns.userLevel === $ns.userGroupsUser or $ns.userLevel === $ns.userGroupsCompany}
								<marquee scrollamount="2" style="color:#D45500" behavior="scroll" direction="left">								
									{assign var="offers" value="^"|explode:$company_offers}								
									{foreach from=$offers item=offer}	
										{$offer}<br/>
									{/foreach} 
								</marquee>								
							{else}
								<textarea wrap="off" style="resize: none;" id="company_offers_textarea^{$companyId}" >{$company_offers}</textarea>
								<div style="clear: both"></div>
								<button  id="company_offers_save_button^{$companyId}" class="company_offers_save_buttons" style="width: 100%;">save</button>
							{/if}
						{/if}
					</td>

				</tr>

			{/foreach}
		</tbody>
</table>
{/if}