{if (($ns.allServiceCompanies|@count )>0)}
    <h1 class="avo_style_CompaniesBlockTitle">{$ns.langManager->getPhraseSpan(579)}</h1>
    <table class="avo_style_companyesTable" cellspacing="0">

        <thead>
            <tr>
                <th></th>
                <th>{$ns.langManager->getPhraseSpan(61)}</th>
                <th>{$ns.langManager->getPhraseSpan(31)}</th>                                
                <th>{$ns.langManager->getPhraseSpan(10)}</th>                                
                <th>{$ns.langManager->getPhraseSpan(12)}</th>
                <th>{$ns.langManager->getPhraseSpan(13)}</th>                     
            </tr>
        </thead>
        <tbody style="line-height: 14px">
            {foreach from=$ns.allServiceCompanies item=company name=cl}

                {assign var="companyId" value = $company->getId()}

                <tr class="{if $smarty.foreach.cl.index % 2 == 1}avo_style_blueTableBox{/if}">
                    <td style="text-align: center">{$smarty.foreach.cl.index+1}
                    </td>
                    <td>                      						
                        {$company->getName()}

                    </td>
                    <td>
                        <a href="javascript:void(0);" class="service_company_gmap_pin" service_company_id="{$companyId}"><img src="{$SITE_PATH}/img/google_map_pin_blue.png" width=20 alt="logo"/></a>
                            {assign var="url" value=$company->getUrl()}	
                        <a {if $url != ''}href="http://{$url}"{/if}
                                          target="_blank" title="{$url|default:$ns.langManager->getPhrase(376)}"									
                                          class="translatable_attribute_element" attribute_phrase_id="{if !empty($url)}376{/if}" attribute_name_to_translate="title">
                            <img  src="{$SITE_PATH}/images/sc_small_logo/{$companyId}" alt="logo"/>
                        </a>
                    </td>


                    <td style="display: flex">					
                        {if $company->getHasPrice()==1}
                            {if $company->getPriceId()>0} 
                                {if $company->getShowPrice()==1} 
                                    <div style="float:left;margin-right: 7px;margin-top:12px">
                                        <a href="javascript:void(0);" class="service_price_scroll_left_a" service_company_id="{$companyId}">
                                            <img src = "{$SITE_PATH}/img/price_left_arrow.png"  alt=">"/> 
                                        </a>
                                    </div>

                                    <div style="overflow: hidden;float:left;max-width: 120px;min-width: 120px;">
                                        <div id="service_price_files_content_{$companyId}">                                                                    
                                         
                                            {* company last price*}							                            

                                            <a  style="display: inline-block;white-space: nowrap" href="{$SITE_PATH}/price/service_last_price/{$companyId}"> 
                                                {assign var="company_last_price_ext" value=$company->getPriceExt()} 										
                                                {assign var="priceListDate" value=$company->getPriceUploadDateTime()} 										
                                                {assign var="icon_local_path" value="`$ns.DOCUMENT_ROOT`/img/file_types_icons/`$company_last_price_ext`_icon.png"} 		
                                                {assign var="icon_path" value="`$SITE_PATH`/img/file_types_icons/`$company_last_price_ext`_icon.png"} 		
                                                <img style="float:left;" src = "{if file_exists($icon_local_path)}{$icon_path}{else}{$SITE_PATH}/img/file_types_icons/unknown_icon.png{/if}"  alt="document"/>
                                                <div style="clear: both"></div>
                                                <span style="word-wrap: break-word;float:left;color:{$ns.serviceCompaniesPriceListManager->getCompanyPriceColor($priceListDate)}"> 
                                                    {if $priceListDate}
                                                        {$priceListDate|date_format:"%m/%d"}
                                                        <br />
                                                        {$priceListDate|date_format:"%H:%M"}
                                                    {else}
                                                        {$ns.langManager->getPhraseSpan(14)}
                                                    {/if} </span> 
                                            </a>

                                            {* company previouse prices*}
                                            {*assign var="companyHistoryPrices" value = $ns.serviceCompaniesPriceListManager->getCompanyHistoryPricesOrderByDate($companyId,0,100)*}	
                                            {foreach from=$ns.groupServiceCompaniesZippedPricesByCompanyId[$companyId] item=pr}
                                                <a  style="display: inline-block;white-space: nowrap;margin-left:5px" href="{$SITE_PATH}/price/service_zipped_price_unzipped/{$pr->getId()}"> 
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
                                        <a href="javascript:void(0);" class="service_price_scroll_right_a" service_company_id="{$companyId}">
                                            <img src = "{$SITE_PATH}/img/price_right_arrow.png"  alt="<"/> 
                                        </a>
                                    </div>  
                                {else}
                                    <input type="text" id="service_company_access_key_input_{$companyId}" placeholder="{$ns.langManager->getPhrase(600)}" 
                                           class="service_companies_access_key_inputes translatable_attribute_element" 
                                           attribute_name_to_translate="placeholder" attribute_phrase_id="600"
                                           service_company_id="{$companyId}" style="font-size: 13px"/>
                                    <button class="add_service_company_buttons" service_company_id="{$companyId}">add</button>
                                {/if}                            
                            {/if}
                        {/if}
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
                    {/foreach}
        </tbody>
    </table>
{/if}
<input type="hidden" value='{$ns.allServiceCompaniesDtosToArray}' id="all_service_companies_dtos_to_array_json"/>
<input type="hidden" value='{$ns.allServiceCompaniesBranchesDtosToArray}' id="all_service_companies_branches_dtos_to_array_json"/>