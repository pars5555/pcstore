<div style="text-align: center">
    <a href="#" onclick="
            var sharer = '//www.facebook.com/sharer/sharer.php?s=100&p[url]=' +
                    encodeURIComponent(location.href) + '&p[images][0]=' + '{$SITE_PATH}/img/icon_pc.png' +
                    '&p[title]=PcStore PC configurator' + '&p[summary]=';
            window.open(sharer,
                    'fb-share-dialog',
                    'width=626,height=436');
            return false;">	
        <img src="{$SITE_PATH}/img/social/facebook-share.png"/>
    </a>
</div>
<div style="clear:both"> </div>

{foreach from=$ns.selected_components item=item name=sc}
    {if $item}
        {if !($item|is_array)}			
            {include file="$TEMPLATE_DIR/main/pc_configurator/pcc_totcalc_same_components_row.tpl" count=1 item=$item}				
        {else}
            {assign var="groupedSameItems" value=$ns.pccm->groupSameItemsInSubArrays($item)}				
            {foreach from=$groupedSameItems item=subarray}
                {include file="$TEMPLATE_DIR/main/pc_configurator/pcc_totcalc_same_components_row.tpl" count=$subarray|@count item=$subarray.0}			
            {/foreach}
        {/if}			
    {/if}
{/foreach}


<div style="clear:both"> </div>

{*} PC build fee {*}
<div class="pcc_total_calc_item_price_row" title="{$ns.langManager->getPhrase(351)}"
     class="translatable_attribute_element" attribute_phrase_id="351" attribute_name_to_translate="title">
    <span class="pcc_total_calc_item_price_row_item_title">{$ns.langManager->getPhraseSpan(320)}*</span>
    <span class="pcc_total_calc_item_price_row_item_price" style="{if $ns.pc_build_fee_amd==0}color:#008800{/if}"> 
        {if $ns.pc_build_fee_amd>0}
            {$ns.pc_build_fee_amd} Դր.
        {else}
            {$ns.langManager->getPhraseSpan(289)}
        {/if} </span>
</div>

<div style="width: 100%;float: left;margin-top: 30px;">
    <span style="float:left; color: black;font-size: 12px;overflow: hidden;padding:5px">{$ns.langManager->getPhraseSpan(261)}</span>
    {if $ns.total_usd>0}
        <span style="float:right;margin-right:10px; color: #BB0000;font-size: 12px;font-weight: bold;padding:5px; border-top: 2px dashed #666666;"> ${$ns.total_usd|number_format:1} </span>
    {/if}
    {if $ns.total_amd>0 || $ns.pc_build_fee_amd>0}
        <span style="float:right;margin-right:10px; color: #BB0000;font-size: 12px;font-weight: bold;padding:5px; border-top: 2px dashed #666666;"> {$ns.total_amd+$ns.pc_build_fee_amd|number_format:0} Դր.</span>
    {/if}
    {if $ns.total_amd==0 && $ns.total_usd==0}
        <span style="float:right;margin-right:10px; color: #BB0000;font-size: 12px;font-weight: bold;padding:5px;"> 0  Դր. </span>
    {/if}
</div>

{if $ns.total_amd>0}
    <div style="width: 100%;float: left;">	

        <span style="float:left; color: black;font-size: 16px;overflow: hidden;height:auto;padding:5px">{$ns.langManager->getPhraseSpan(285)} {$ns.pc_configurator_discount}%</span>

        {if $ns.total_usd>0}
            <span style="float:right;margin-right:10px; color: #000000;font-size: 16px;font-weight: bold;padding:5px; border-top: 2px dashed #666666;"> ${$ns.total_usd|number_format:1} </span>
        {/if}
        {if $ns.total_amd>0 || $ns.pc_build_fee_amd>0}
            <span style="float:right;margin-right:10px; color: #000000;font-size: 16px;font-weight: bold;padding:5px; border-top: 2px dashed #666666;"> {$ns.grand_total_amd|number_format:0} Դր.</span>
        {/if}
        {if $ns.total_amd==0 && $ns.total_usd==0}
            <span style="float:right;margin-right:10px; color: #000000;font-size: 16px;font-weight: bold;padding:5px;"> 0  Դր. </span>
        {/if}
    </div>
{/if}

<div style="width: 100%;float: left;">
    {if $ns.ready_to_order == "true"}
        {if ($ns.userLevel!=$ns.userGroupsGuest)}
            {if $ns.configurator_mode_edit_cart_row_id > 0}
                <a href="javascript:void(0);" id="pcc_footer_order_button"  class= "button1 green" style="float:right;margin:10px;">
                    {$ns.langManager->getPhraseSpan(43)}
                </a>				
            {else}
                <a href="javascript:void(0);" id="pcc_footer_order_button"  style="float:right;padding: 10px;color: #008800;font-size: 16px;line-height: 30px"> 
                    <img style="float:left" src="{$SITE_PATH}/img/add-to-cart.png">
                    <div style="float: left;margin-left: 5px;">
                        {$ns.langManager->getPhraseSpan(284)}
                    </div> 
                </a>
            {/if}            
        {else}
            <a class="large button1 gray" id="pcc_footer_login_order_button" href="javascript:void(0);"
               style="word-wrap: break-word;display:block;float:right;margin:10px"> {$ns.langManager->getPhraseSpan(85)} </a>
        {/if}
         <a href="javascript:void(0);" id="pcc_print_button"  style="float:left;padding: 10px;color: #008800;font-size: 16px;line-height: 30px"> 
                    <img style="float:left" src="{$SITE_PATH}/img/print.png">
                    <div style="float: left;margin-left: 5px;">
                        {$ns.langManager->getPhraseSpan(629)}
                    </div> 
                </a>
    {/if}
</div>
<div style="clear:both"></div>
<div id="pcc_credit_calculation_container">
    {nest ns = pcc_credit_calculation}
</div>	