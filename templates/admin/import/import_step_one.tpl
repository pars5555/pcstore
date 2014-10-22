<div style="width: 100%;height: 100%;" oncontextmenu="return false;" id="is1_container_div">
    {*stock items table*}

    <div style="width: 100%;position:absolute;left:0;top:0;height:50px;text-align: center">
        items simillarity aceptable percent: <select id="is1_aceptable_simillarity_percent"  onkeyup="this.blur();
               this.focus();" class="cmf-skinned-select cmf-skinned-text" >
            {html_options values=$ns.acepableItemSimillarityPercentOptions  selected=$ns.acepableItemSimillarityPercent output=$ns.acepableItemSimillarityPercentOptions}
        </select>%	
        <br>
        {$ns.matched_price_items_count} matched items!
        <br>
        {$ns.unmatched_price_items_count} new items!
    </div>
    <div style="width: 40%;position:absolute;left:0;top:50px;">
        <div style="text-align: center;font-size: 32px">Stock</div>
        <table style="background-color: #ddd" id="istock_table_view">
            <thead>

            <th>

            </th>
            {foreach from=$ns.columnNames key=dtoFieldName item=columnTitle}

                <th style="{if $dtoFieldName=="displayName"}width:300px{else}width:40px{/if}">
                    {$columnTitle}
                </th>
            {/foreach}

            </thead>
            <tbody style="line-height: 14px">
                {*stock items list matched to price items*}
                {foreach from=$ns.priceRowsDtos item=rowDto}
                    {assign var=correspondingStockItemId value=$rowDto->getMatchedItemId()}
                    {if isset($correspondingStockItemId)}
                        {assign var=correspondingStockItemDto value=$ns.stockItemsDtosMappedByIds.$correspondingStockItemId}
                        <tr istock_table_pk_value="{$correspondingStockItemId}" style="height:60px">
                            <td style="width:50px">							
                            </td>

                            {foreach from=$ns.columnNames key=dtoFieldName item=columnTitle}
                                {if $dtoFieldName=="warrantyMonths"}
                                    {assign var=fieldName value="warranty"} 
                                {else}
                                    {assign var=fieldName value=$dtoFieldName} 
                                {/if}
                                <td dtoFieldName="{$fieldName}" 
                                    style='max-width:100px; height: 100px;white-space: normal'>
                                    {$correspondingStockItemDto->$fieldName}<br>&nbsp

                                </td>
                            {/foreach}
                        </tr>
                    {else}
                        <tr style="height:60px">
                            {foreach from=$ns.columnNames key=dtoFieldName item=columnTitle}							
                                <td>
                                    &nbsp<br>&nbsp
                                </td>
                            {/foreach}			
                        </tr>
                    {/if}
                {/foreach}

                {*unmatched stock items list to price items*}
                {foreach from=$ns.unmatchedCompanyItems item=stockItemDto}

                    <tr style="height:60px">
                        <td>
                            <button class="ii_link_source_button" pk_value="{$stockItemDto->getId()}">link<br>source</button>
                        </td>
                        {foreach from=$ns.columnNames key=dtoFieldName item=columnTitle}
                            {if $dtoFieldName=="warrantyMonths"}
                                {assign var=fieldName value="warranty"} 
                            {else}
                                {assign var=fieldName value=$dtoFieldName} 
                            {/if}
                            <td dtoFieldName="{$fieldName}"
                                style='max-width:100px;height: 100px;white-space: normal'>
                                {$stockItemDto->$fieldName}<br>&nbsp

                            </td>
                        {/foreach}
                    </tr>

                {/foreach}


            </tbody>
        </table>
    </div>

    {*price items table*}
    <div style="width: 60%;position: absolute;left:40%;top:50px;">
        <div style="text-align: center;font-size: 32px">Price</div>
        <table style="background-color: #ddd" id="ii_table_view">
            <thead>

            <th>link/unlink
            </th>
            {foreach from=$ns.columnNames key=dtoFieldName item=columnTitle name=columnNamesForeach}

                <th style="{if $dtoFieldName=="displayName"}width:300px{else}width:40px{/if}">
                    {$columnTitle}
                </th>

            {/foreach}		
            </thead>
            <tbody style="line-height: 14px">
                {foreach from=$ns.priceRowsDtos item=rowDto}
                    {assign var=correspondingStockItemId value=$rowDto->getMatchedItemId()}
                    {if isset($correspondingStockItemId)}
                        {assign var=correspondingStockItemDto value=$ns.stockItemsDtosMappedByIds.$correspondingStockItemId}
                    {/if}
                    <tr ii_table_pk_value="{$rowDto->id}" style="height:60px">
                        <td style="text-align: center;width:50px">
                            {if $rowDto->getMatchedItemId()>0}
                                <button class="is1_unbind_item" price_item_id="{$rowDto->getId()}">X</button>
                            {else}
                                <button class="ii_link_target_button" pk_value="{$rowDto->getId()}">link<br>target
                                </button>
                            {/if}
                        </td>
                        {foreach from=$ns.columnNames key=dtoFieldName item=columnTitle name=columnNamesForeach}

                            {if $dtoFieldName=="warrantyMonths"}
                                {assign var=originalFieldName value="originalWarranty"} 
                            {else}
                                {assign var=cap value=$dtoFieldName|capitalize}
                                {assign var=originalFieldName value="original`$cap`"} 
                            {/if}
                            <td dtoFieldName="{$dtoFieldName}" dtoOriginalFieldName="{$originalFieldName}" pk_value="{$rowDto->id}" cellValue = "{$rowDto->$dtoFieldName}" originalCellValue = "{$rowDto->$originalFieldName}" class="is1_popup_menu_td" 
                                style='max-width:200px; height: 100px;white-space: normal'>							
                                <span class="editable_cell" id="ii_table_editable_span_{$rowDto->getId()}_{$dtoFieldName}" dtoFieldName='{$dtoFieldName}' pk_value="{$rowDto->id}" 
                                      style='width:100%;
                                      {if !($rowDto->getMatchedItemId()>0)}color:red{/if}		
                                      {if isset($correspondingStockItemDto) && $correspondingStockItemDto->$dtoFieldName|regex_replace:"#\s+#":""|lower!=
												$rowDto->$dtoFieldName|regex_replace:"#\s+#":""|lower}color:magenta;{/if}'>{$rowDto->$dtoFieldName}</span>
                                {*
                                <br>
                                <span style="color:#888">{$rowDto->$originalFieldName}</span>	
                                *}
                            </td>
                        {/foreach}
                    </tr>
                {/foreach}

            </tbody>
        </table>
    </div>


    <ul id="is1_popup_menu" style="position:fixed;display: none"> 
        <li><a id="is1_menu_copy" href="javascript:void(0);">Copy</a></li>
        <li><a id="is1_menu_copy_original" href="javascript:void(0);">Copy Original Value</a></li>
        <li><a id="is1_menu_paste" href="javascript:void(0);">Paste</a></li>
        <li><a id="is1_menu_delete" href="javascript:void(0);">Delete</a></li>
    </ul>
</div>
<input type="hidden" id="is1_used_columns_indexes_array" value="{$ns.used_columns_indexes_array}" />

