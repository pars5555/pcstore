{if $ns.item}
    <link href="{$ns.itemManager->getItemImageURL($ns.item->getId(),$ns.item->getCategoriesIds(), '400_400', 1)}" rel="image_src" />
    <meta property="og:image" content="{$ns.itemManager->getItemImageURL($ns.item->getId(),$ns.item->getCategoriesIds(), '400_400', 1)}" />    
    <img src="{$ns.itemManager->getItemImageURL($ns.item->getId(),$ns.item->getCategoriesIds(), '400_400', 1)}" style="display: none"/>    

    {if ($ns.userLevel== $ns.userGroupsCompany || $ns.userLevel==$ns.userGroupsAdmin) || $ns.item->getIsDealerOfThisCompany()==1}
        {assign var="isAdminOrCompanyOrDealer" value='true'}
    {/if}
{/if}
{assign var="tree_days_ago" value='-3 day'|strtotime}
{assign var="tree_days_ago" value=$tree_days_ago|date_format:"%Y-%m-%d %H:%M:%S"}						
{if isset($ns.item) && $ns.item->getCreatedDate()>$tree_days_ago}
    {assign var="new_item" value=true}			
{else}
    {assign var="new_item" value=false}			
{/if}


{assign var="show_dealer_price" value=1}
{if $ns.userLevel==$ns.userGroupsAdmin}
    {if $ns.admin_price_group != 'admin'}
        {assign var="show_dealer_price" value=0}
    {/if}
{/if}

<div style="float: left; width: 100%; height: 40px; position: relative; border-bottom: 1px dashed rgb(201, 201, 201);">
    <a id="back_to_search_result_link" style="height: 100%;line-height: 38px;float: left;" href="javascript:void(0);"> 
        <img style="float: left;" src = "{$SITE_PATH}/img/back.png"  alt="document"/> 
        <span style="color:#004B91;font-size: 14px;float: left"> &nbsp; {$ns.langManager->getPhraseSpan(181)} </span> </a>

    {if $ns.item}
        <div style="float: left;margin-left: 20px;margin-top: 12px">
            <span style="font-size: 16px;font-weight: bold;color: #004B91;float: left">Category:</span>
            <span style="font-size: 14px;color: #000;float:left;padding-left: 10px;">{$ns.itemManager->getItemCategoriesPathToString($ns.item)}</span>
        </div>
    {/if}
</div>
<div class="clear"></div>
<div id= "item_lagre_view_content_container" class="avo_style_detail_block">
    {if $ns.item}
        <div class="avo_style_detailBox">
            <div class="avo_style_leftContent">
                <div id="gallery">
                    {*item picture*}
                    <div class="avo_style_detail_imageBox">
                        <a href="{$ns.itemManager->getItemImageURL($ns.item->getId(),$ns.item->getCategoriesIds(), '800_800', 1)}"  rel="lightbox[plants]"> 
                            <img src="{$ns.itemManager->getItemImageURL($ns.item->getId(),$ns.item->getCategoriesIds(), '150_150', 1)}" /> </a>
                            {if $new_item == true}
                            <img src="{$SITE_PATH}/img/newproduct_64_64.png" style="position: absolute;right:0;top:0"/>
                        {/if}
                    </div>
                    <div class="clear"></div>
                    <div style="float: left;height: 70px;width: 180px;padding:10px"> 
                        {section name=images_counter start=2 loop=$ns.itemPicturesCount+1 step=1}					
                            <div style="float:left;border: 2px solid #666; border-radius: 5px;width: 30px;height: 30px;margin:2px;" >
                                <a href="{$ns.itemManager->getItemImageURL($ns.item->getId(),$ns.item->getCategoriesIds(), '800_800', $smarty.section.images_counter.index)}">						
                                    <img src="{$ns.itemManager->getItemImageURL($ns.item->getId(),$ns.item->getCategoriesIds(), '30_30', $smarty.section.images_counter.index)}" alt="" />
                                </a>
                            </div>
                        {/section}
                    </div>	
                </div>
            </div>  

            <div class="avo_style_rightContent">
                {* item title, price, vat price, company, add to cart button ,.... *}		

                {* item title*}		
                <span class="item_title_large_view"> 
                    {$ns.item->getDisplayName()}{if $ns.item->getBrand()} <span style="color :#333;font-size: 11px;"> by {$ns.item->getBrand()} </span> {/if} 
                </span>		
                <div class="clear"> </div>

                {* warranty, price, company*}

                {*warranty*}
                <div style="line-height:30px; float:left;width: 90px;">
                    {$ns.langManager->getPhraseSpan(82)}:
                </div>
                <div style="line-height:30px; float:left; ">
                    <span style="color: green;font-size: 14px;">{$ns.item->getWarranty()} {if $ns.item->getWarranty()|lower!='lifetime'}{$ns.langManager->getPhraseSpan(183)}{/if} </span>
                </div>

                <div class="clear"></div>
                {*price*}				

                {if $ns.item->getVatPrice()>0} 
                    {assign var = "showvatprice" value = "true"}
                {/if}                
                <div class="large_view_item_price_style" style="line-height:30px; float:left; ">				
                    {if $isAdminOrCompanyOrDealer && $show_dealer_price == 1}
                        {if $ns.item->getDealerPriceAmd()>0}
                            {$ns.item->getDealerPriceAmd()|number_format} Դր.
                        {else}
                            ${$ns.item->getDealerPrice()|number_format:1}
                        {/if}


                    {else}
                        {assign var="price_in_amd" value=$ns.itemManager->exchangeFromUsdToAMD($ns.item->getCustomerItemPrice())}                        
                        <span style="color:gray;">List price: <span style="text-decoration: line-through">{$ns.item->getListPriceAmd()} Դր.</span></span></br>
                        <span style="color:gray">{$ns.langManager->getPhraseSpan(88)}:</span> <span>{$price_in_amd|number_format} Դր.</span></br>
                        {math equation="100-x*100/y" x=$price_in_amd y=$ns.item->getListPriceAmd() assign="list_price_discount"}
                        <span style="color:gray">you save:</span> {$ns.item->getListPriceAmd()-$price_in_amd|number_format} ({$list_price_discount|number_format}%)                           
                    {/if} 

                    {if $showvatprice == "true"}
                        <br>
                        <span style="color:gray;">VAT price: </span>
                        <span title="{$ns.langManager->getPhrase(277)}"
                              class="translatable_attribute_element" attribute_phrase_id="277" attribute_name_to_translate="title"> 
                            {if $isAdminOrCompanyOrDealer && $show_dealer_price == 1}
                                {if $ns.item->getVatPriceAmd()>0}
                                    ({$ns.item->getVatPriceAmd()|number_format} Դր.)
                                {else}
                                    (${$ns.item->getVatPrice()|number_format:1})
                                {/if}	
                            {else}
                                {assign var="price_in_amd" value=$ns.itemManager->exchangeFromUsdToAMD($ns.item->getCustomerVatItemPrice())}
                                (<span>{$price_in_amd|number_format} Դր.</span>)
                            {/if}	
                        </span>
                    {/if}

                </div>
                <div class="clear"></div>
                {if ($ns.userLevel== $ns.userGroupsCompany || $ns.userLevel==$ns.userGroupsAdmin ||
					($ns.userLevel==$ns.userGroupsUser &&  $ns.item->getIsDealerOfThisCompany()))}

                <div style="line-height:30px; float:left;width: 90px;">
                    <span >{$ns.langManager->getPhraseSpan(66)}:</span>
                </div>
                <div style="line-height:30px; float:left;">
                    <span title="{$ns.langManager->getPhrase(271)}: {$ns.item->getCompanyPhones()|replace:',':'&#13;&#10;'}" style="font-size: 14px; {if ($ns.item->getIsCompanyOnline())}color:green;{/if}"
                          class="translatable_attribute_element" attribute_phrase_id="`271`: {$ns.item->getCompanyPhones()|replace:',':'&#13;&#10;'}" attribute_name_to_translate="title">{$ns.item->getCompanyName()}</span>
                </div>					
            {/if}
            <div class="clear"></div>
            <div>		
                {if ($ns.userLevel!=$ns.userGroupsGuest)}
                    {if $smarty.now|date_format:"%Y-%m-%d">$ns.item->getItemAvailableTillDate()}
                        {if $ns.item->getIsCompanyOnline()}
                            <a id="large_view_check_item_availability_button" href="javascript:void(0);" class="small button"
                               style="height:20px;line-height: 20px">{$ns.langManager->getPhraseSpan(86)}</a>
                            <br/>{$ns.langManager->getPhraseSpan(526)} {$ns.langManager->getCmsVar('pcstore_sales_phone_number')}
                        {else}
                            <span style="font-size: 14px;">{$ns.langManager->getPhraseSpan(525)}   {$ns.langManager->getCmsVar('pcstore_sales_phone_number')}</span>
                        {/if}
                    {else}
                        <span class="large_view_item_instock_style" title="{$ns.langManager->getPhrase(250)}" style="color: #060"
                              class="translatable_attribute_element" attribute_phrase_id="250" attribute_name_to_translate="title">{$ns.langManager->getPhraseSpan(83)}
                            {if $new_item == true}
                                <br/>
                                <span style="color:#BB0000">{$ns.langManager->getPhraseSpan(559)}</span>
                            {else}
                                {if $ns.item->getUpdatedDate() && $ns.item->getUpdatedDate() != "0000-00-00 00:00:00"}
                                    <br/>
                                    {$ns.langManager->getPhraseSpan(453)}: {$ns.item->getUpdatedDate()|date_format:"%d/%m/%Y"}
                                {/if}
                            {/if}

                        </span>
                    {/if}
                {/if}
            </div>
            <div class="space"></div>

            <div style="float: left;width: 240px;">
                {if ($ns.userLevel!=$ns.userGroupsGuest)}
                    {if !($smarty.now|date_format:"%Y-%m-%d">$ns.item->getItemAvailableTillDate())}
                        {if $ns.userLevel==$ns.userGroupsUser && !$ns.item->getIsDealerOfThisCompany()}
                            <a href="javascript:void(0);" class="orderbutton translatable_attribute_element" id="large_view_add_to_cart_button" title="{$ns.langManager->getPhrase(284)}" 					   
                               attribute_phrase_id="284" attribute_name_to_translate="title">
                                <img src="{$SITE_PATH}/img/add-to-cart.png" style="float:left;margin: 1px;float: left"/>
                                <div style="font-size: 14px;color:#060;font-weight: bold;margin: 10px">{$ns.langManager->getPhraseSpan(284)}</div>
                            </a>
                        {/if}
                    {/if}
                {else}
                    <div class="avo_style_addToBtnBox">
                        <a href="javascript:void(0);" id="login_to_order_link">{$ns.langManager->getPhrase(85)}</a>
                    </div>
                {/if}
            </div>

            <div class="clear"></div>
            {if $ns.userLevel==$ns.userGroupsAdmin}	
                {assign var="rootCategoryId" value=$ns.itemManager->getItemRootCategoryId($ns.item)}
                <select onkeyup="this.blur();
                        this.focus();" id = "item_root_category_select"  class="cmf-skinned-select cmf-skinned-text" style="width:170px;float: left">
                    {html_options values=$ns.firstLevelCategoriesIds selected=$rootCategoryId output=$ns.firstLevelCategoriesNames}
                </select>

                <button id="f_change_item_sub_categories" class="button gray">...</button>
                <input type="hidden" id="ilw_sub_categories" value="{$ns.item->getCategoriesIds()}"/>
            {/if}
            <div class="clear"></div>
            <div style="float: left;margin-top: 20px">		
                <span style="font-size: 14px;color: #000;float:left;
                      word-wrap: break-word;display:block;white-space: normal">
                    {foreach from=$ns.itemPropertiesHierarchy key= k item=sp}
                        <span style="color: #308ECF">{$k} :</span> {', '|implode:$sp}</br>
                    {/foreach}	
                </span>

            </div>
        </div>	    	
    </div>
    <div class="clear"></div>	

    {if $ns.item->getShortDescription() || $ns.userLevel==$ns.userGroupsAdmin}
        <div class="avo_style_centerBlock">
            <div class="large_view_short_description_header_style">
                Short Description
            </div>			
            <div  style="padding:10px 0 0 0;font-size: 14px;color: #000">
                {if $ns.userLevel==$ns.userGroupsAdmin}					
                    <button id="save_item_short_spec_button" class="button gray">Save</button>
                    <textarea id="item_short_spec_textarea" style="resize:vertical;text-align: left; width:96%;padding: 10px 2%;border: 1px solid #c9c9c9;height: 150px">{$ns.item->getShortDescription()}</textarea>
                {else}
                    {$ns.item->getShortDescription()}
                {/if}
            </div>

        </div>
    {/if}

    <div class="space"></div>	

    {if $ns.item->getFullDescription() || $ns.userLevel==$ns.userGroupsAdmin}
        <div class="avo_style_centerBlock">

            <div class="large_view_short_description_header_style">
                Specification
            </div>	

            <div  style="padding:10px 0 10px 0;font-size: 14px;color: #000">
                {if $ns.userLevel==$ns.userGroupsAdmin}					
                    <button id="save_item_spec_button" class="button gray">Save</button>
                    <textarea id="item_spec_editable_div" style="text-align: left; width:96%;padding: 10px 2%;min-height: 150px">{$ns.item->getFullDescription()}</textarea>
                {else}
                    {$ns.item->getFullDescription()}
                {/if}
            </div>
        </div>	
    {/if}



    <input id="selected_large_view_item_id" type="hidden" value="{$ns.item->getId()}"/>
    {else}
        <div style="width: 100%; height: 100%;text-align: center;font-size: 18px;">

            {$ns.langManager->getPhraseSpan(300)}
        </div>
        {/if}

        </div>





