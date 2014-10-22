{if ($ns.userLevel== $ns.userGroupsCompany || $ns.userLevel==$ns.userGroupsAdmin) || $item->getIsDealerOfThisCompany()==1}
    {assign var="isAdminOrCompanyOrDealer" value='true'}
{/if}


{assign var="show_dealer_price" value=1}
{if $ns.userLevel==$ns.userGroupsAdmin}
    {if $ns.admin_price_group != 'admin'}
        {assign var="show_dealer_price" value=0}
    {/if}
{/if}

{assign var="tree_days_ago" value='-3 day'|strtotime}
{assign var="tree_days_ago" value=$tree_days_ago|date_format:"%Y-%m-%d %H:%M:%S"}						
{if $item->getCreatedDate()>$tree_days_ago}
    {assign var="new_item" value=true}			
{else}
    {assign var="new_item" value=false}			
{/if}


<div class="avo_style_searchItemBox" id="search_item_unit_container_{$item->getId()}">
    <div class="avo_style_imageBox">
        <a href="{$SITE_PATH}/item/{$item->getId()}"  id="item_picture^{$item->getId()}" class="search_item_title" onclick="javascript:void(0);">
            <img src="{$ns.itemManager->getItemImageURL($item->getId(), $item->getCategoriesIds(), '150_150', 1)}" />             
            {if $new_item == true}
                <img src="{$SITE_PATH}/img/newproduct_32_32.png" style="position: absolute;right:0;top:0"/>
            {/if}
        </a>
        {if ($ns.userLevel===$ns.userGroupsAdmin)}
            <a class="button glyph siv_add_picture avo_style_table_left_topBtn" item_id="{$item->getId()}">...</a>
        {/if} 
    </div>

    <div class="avo_style_infoBox">

        <div class="avo_style_aboutBox">
            <div class="avo_style_aboutBox1">
                <a href="{$SITE_PATH}/item/{$item->getId()}" onclick="return false;" 
                   class="search_item_title" id="item^{$item->getId()}" title="{if ($item->getDisplayName()|strlen)>150}{$item->getDisplayName()|substr:0:150}...{else}{$item->getDisplayName()}{/if}" >

                    {if ($item->getDisplayName()|strlen)>150}
                        {$item->getDisplayName()|substr:0:150}...
                    {else}
                        {$item->getDisplayName()}
                    {/if}
                </a>
                {if $item->getBrand()} <span class="avo_styleItemBrandBox"> by {$item->getBrand()} </span> {/if}     
            </div>

            <div  class="avo_style_aboutBox2">
                {assign var='item_model' value=$item->getModel()}
                {if !empty($item_model)}{$ns.langManager->getPhraseSpan(106)}: {$item_model} <br/> {/if}                
                {if $isAdminOrCompanyOrDealer}
                    <br/>
                    <span title="{$ns.langManager->getPhrase(271)}: {$item->getCompanyPhones()|replace:',':'&#13;&#10;'}" style="{if ($item->getIsCompanyOnline())}color:green;{/if}"
                          class="translatable_attribute_element" attribute_phrase_id="`271`: {$item->getCompanyPhones()|replace:',':'&#13;&#10;'}" attribute_name_to_translate="title">{$ns.langManager->getPhraseSpan(66)}: {$item->getCompanyName()}</span>
                {/if}
            </div>
            <div class="avo_style_aboutBox3">
                {if $smarty.now|date_format:"%Y-%m-%d">$item->getItemAvailableTillDate()}
                    {if ($ns.userLevel!=$ns.userGroupsGuest)}
                        {if $item->getIsCompanyOnline()}
                            <a item_id="{$item->getId()}" id="check_item_availability_button_{$item->getId()}" href="javascript:void(0);" class="small button checkitemavailabilitybutton avo_style_checkBtn">
                            	 {$ns.langManager->getPhraseSpan(86)} 
                        	 </a>
                        	 <div class="clear"></div>
                             {$ns.langManager->getPhraseSpan(526)} {$ns.langManager->getCmsVar('pcstore_sales_phone_number')}
                        {else}
                            {$ns.langManager->getPhraseSpan(525)}<br/>{$ns.langManager->getCmsVar('pcstore_sales_phone_number')}
                        {/if}
                    {/if}

                {else}


                    <span class="avo_style_instockItem translatable_attribute_element" title="{$ns.langManager->getPhrase(250)}" attribute_phrase_id="250" attribute_name_to_translate="title">
                        {$ns.langManager->getPhraseSpan(83)} 			
                        {if $new_item == true}
                            <span style="color:#BB0000">{$ns.langManager->getPhraseSpan(559)}</span>
                        {else}
                            {if $item->getUpdatedDate() && $item->getUpdatedDate() != "0000-00-00 00:00:00"}
                                {$ns.langManager->getPhraseSpan(453)}: {$item->getUpdatedDate()|date_format:"%d/%m/%Y"}
                            {/if}
                        {/if}
                    </span>
                {/if}

            </div>
        </div>
        {if $item->getVatPrice()>0} 
            {assign var = "showvatprice" value = "true"}
        {/if}					    
        <div class="avo_style_priceBox">
            <div class="avo_style_leftBox">
                <span class="avo_style_price_box"> 
                    {if $isAdminOrCompanyOrDealer && $show_dealer_price == 1}
                        {if $item->getDealerPriceAmd()>0}
                            {$item->getDealerPriceAmd()|number_format} Դր.
                        {else}
                            ${$item->getDealerPrice()|number_format:1}
                        {/if}

                    {else}
                        {assign var="price_in_amd" value=$ns.itemManager->exchangeFromUsdToAMD($item->getCustomerItemPrice())}
                        <span style="color:gray;"><span class="avo_style_priceTitle">{$ns.langManager->getPhraseSpan(588)}:</span> <span style="text-decoration: line-through">{$item->getListPriceAmd()|number_format} Դր.</span></span></br>
                        <span style="color:gray" class="avo_style_priceTitle">{$ns.langManager->getPhraseSpan(88)}:</span> <span class="avo_style_BasePrice">{$price_in_amd|number_format} Դր.</span>
                        {if $showvatprice == "true"}				       
                            <span  title="{$ns.langManager->getPhrase(277)}"
                                  class="translatable_attribute_element" attribute_phrase_id="277" attribute_name_to_translate="title">
                                {if $isAdminOrCompanyOrDealer && $show_dealer_price == 1}
                                    {if $item->getVatPriceAmd()>0}
                                        ({$item->getVatPriceAmd()|number_format} Դր.)
                                    {else}
                                        (${$item->getVatPrice()|number_format:1})
                                    {/if}	
                                {else}
                                    {assign var="price_in_amd" value=$ns.itemManager->exchangeFromUsdToAMD($item->getCustomerVatItemPrice())}
                                    <span>({$price_in_amd|number_format} Դր.)</span>				                
                                {/if}
                            </span>
                        {/if}
                        <br>

                        {math equation="100-x*100/y" x=$price_in_amd y=$item->getListPriceAmd() assign="list_price_discount"}
                        <span style="color:gray" class="avo_style_priceTitle">{$ns.langManager->getPhraseSpan(589)}:</span> {$item->getListPriceAmd()-$price_in_amd|number_format} ({$list_price_discount|number_format}%)                           
                    {/if} 
                </span>
                {if $ns.userLevel == $ns.userGroupsAdmin && $show_dealer_price == 1}
                    <span class="item_price_style" style="padding-left:10px;float: left;font-size: 14px">				
                        {assign var="price_in_amd" value=$ns.itemManager->exchangeFromUsdToAMD($item->getCustomerItemPrice())}				
                        {$price_in_amd|number_format} Դր.
                    </span>
                {/if}		


            </div>
            {if !$isAdminOrCompanyOrDealer || $ns.userLevel == $ns.userGroupsAdmin}
                <div class="avo_style_rightBox">

                    <span class="avo_style_price_box" style="padding-left:2px;font-size: 12px;" title="{$ns.langManager->getPhrase(417)}"
                          class="translatable_attribute_element" attribute_phrase_id="417" attribute_name_to_translate="title">
                        {math  equation="round(creditAmount/(1-commission/100)*monthlyInterestRatio/(1- (1/pow(1+monthlyInterestRatio, credit_months))))"				
								creditAmount = $price_in_amd
								commission = $ns.defaultSupplierCommission
								monthlyInterestRatio = $ns.defaultCreditInterestMonthlyRatio
								credit_months= $ns.defaultSelectedCreditMonths
								assign="credit_monthly_amd"}
                        <span id="item_monthly_credit_amount^{$item->getId()}" >{$credit_monthly_amd|number_format}</span> Դր.
                        <input id="item_price_amd_for_credit^{$item->getId()}" type="hidden" value="{$price_in_amd}"/>
                    </span>
                    <select id="cho_selected_credit_months" name="cho_selected_credit_months"
                            onkeyup="this.blur();
                                        this.focus();" name='region' class="cmf-skinned-select cmf-skinned-text credit_months_select" style="width: 90px;margin-left: 5px;" item_id="{$item->getId()}">

                        {foreach from=$ns.creditPossibleMonthsValues item=value key=key}
                            <option value="{$value}" {if $ns.defaultSelectedCreditMonths == $value}selected="selected"{/if} class="translatable_element" phrase_id="{$value} `183`">{$ns.langManager->getPhrase("`$value` `183`")}</option>

                        {/foreach}
                    </select>

                </div>
            {/if} 
        </div>

        <div class="avo_style_clear"></div>
        <div class="avo_style_addToBtnBox">
            {if ($ns.userLevel!=$ns.userGroupsGuest)}		
                {if not ($smarty.now|date_format:"%Y-%m-%d">$item->getItemAvailableTillDate())}			
                    {if $ns.userLevel==$ns.userGroupsUser and not $item->getIsDealerOfThisCompany()}
                        <a href="javascript:void(0);" class="orderbutton translatable_attribute_element" id="order_item_button^{$item->getId()}" title="{$ns.langManager->getPhrase(284)}" 
                           attribute_phrase_id="284" attribute_name_to_translate="title">{$ns.langManager->getPhrase(284)}</a>
                    {/if}
                {/if}
            {else}			
                <a href="javascript:void(0);" class="f_login_to_order">{$ns.langManager->getPhrase(85)}</a>
            {/if}
        </div>
        {if $ns.userLevel==$ns.userGroupsAdmin}
            <a class="copy_company_items_links button avo_style_table_right_topBtn" item_id="{$item->getId()}" 
               style="{if $ns.copied_item_id == $item->getId()}visibility: hidden;{/if}" href="javascript:void(0);">Copy</a>
            <a class="post_on_list_am button avo_style_table_right_bottomBtn" item_id="{$item->getId()}" href="javascript:void(0);">List.am &gt;&gt;</a>

        {/if}
    </div>
</div>



