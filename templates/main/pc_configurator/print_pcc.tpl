{foreach from=$ns.selected_components item=item name=sc}
    {if isset($item)}
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
{$ns.langManager->getPhraseSpan(320)}: 
{if $ns.pc_build_fee_amd>0}
    {$ns.pc_build_fee_amd} Դր.
{else}
    {$ns.langManager->getPhraseSpan(289)}
{/if} 
<div style="clear:both"> </div>
total:
{if $ns.total_usd>0}
    ${$ns.total_usd|number_format:1} 
{/if}
<span style="{if $ns.total_amd>0}text-decoration: line-through{/if}">
    {if $ns.total_amd>0 || $ns.pc_build_fee_amd>0}
        {$ns.total_amd+$ns.pc_build_fee_amd|number_format:0} Դր.
    {/if}
</span>
<div style="clear:both"> </div>
{if $ns.total_amd>0}

    {$ns.langManager->getPhraseSpan(285)} {$ns.pc_configurator_discount}%

    {if $ns.total_usd>0}
        ${$ns.total_usd|number_format:1} 
    {/if}
    {if $ns.total_amd>0 || $ns.pc_build_fee_amd>0}
        {$ns.grand_total_amd|number_format:0} Դր.
    {/if}
{/if}
