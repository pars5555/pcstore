<div style="width: 100%;height: 100%;" oncontextmenu="return false;" id="is3_container_div">
    <button id="is3_select_all" class="button glyph">Select All</button>
    <button id="is3_select_none" class="button glyph">Select None</button> 
    {*stock items table*}
    <table style="width: 100%;background-color: #ddd" id="ii_table_view">
        <thead>
        <th>
            change
        </th>

        <th>
            info
        </th>

        {foreach from=$ns.columnNames key=dtoFieldName item=columnTitle}
            <th {if $dtoFieldName=='displayName'}style="min-width:400px"{/if} class="is3_popup_menu_th" fieldName="{$dtoFieldName}">
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
                    <tr style="height:60px">
                        <td>
                            <input type="checkbox" class="is3_include_row"  
                                   row_id="{$rowDto->getId()}" {if in_array($rowDto->getId(),$ns.changedRowsIds)}checked{/if}/>
                        </td>
                        <td>
                            stock<br>price<br>original price
                        </td>
                        {foreach from=$ns.columnNames key=dtoFieldName item=columnTitle}
                            {if $dtoFieldName=="warrantyMonths"}
                                {assign var=fieldName value="warranty"} 
                                {assign var=originalFieldName value="originalWarranty"} 
                            {else}
                                {assign var=fieldName value=$dtoFieldName}
                                {assign var=cap value=$dtoFieldName|capitalize}
                                {assign var=originalFieldName value="original`$cap`"} 
                            {/if}					


                            <td fieldName="{$dtoFieldName}" id="is3_editable_td_{$dtoFieldName}_{$rowDto->id}" pk_value="{$rowDto->id}" 
                                stockItemValue = "{$correspondingStockItemDto->$fieldName}" class="is3_popup_menu_td {$dtoFieldName}" 
                                style='max-width:100px; overflow: hidden;text-overflow: ellipsis;
                                {assign var=rowId value=$rowDto->getId()}	
                                {if $ns.changedFields.$rowId.$fieldName == 1}color:magenta;{/if}'>
                                {$correspondingStockItemDto->$fieldName|default:"empty"}<br>
                                <span class="editable_cell" dtoFieldName='{$dtoFieldName}' pk_value="{$rowDto->id}"
                                      id="ii_table_editable_span_{$rowDto->id}_{$dtoFieldName}">{$rowDto->$dtoFieldName|default:"empty"}</span>
                                <br>
                                {$rowDto->$originalFieldName|default:"empty"}
                            </td>
                        {/foreach}
                        <td>							
                            <button class="is3_spec_button" pk_value="{$rowDto->getId()}">spec</button>                            
                            <textarea style="display: none" pk_value="{$rowDto->getId()}" id="is3_item_short_spec_{$rowDto->getId()}">{$rowDto->getShortSpec()}</textarea>
                            <textarea style="display: none" pk_value="{$rowDto->getId()}" id="is3_item_full_spec_{$rowDto->getId()}">{$rowDto->getFullSpec()}</textarea>
                        </td>
                         <td>		
                             <input type="text" id="is3_simillar_item_search_text_{$rowDto->getId()}" class=is3_simillar_item_search_texts" pk_value="{$rowDto->getId()}"/>
                             <button class="is3_find_simillar_items_button" id="is3_find_simillar_items_button_{$rowDto->getId()}" pk_value="{$rowDto->getId()}">load</button><br>
                             <select class="is3_simillar_items_select" style="max-width:150px" id="simillar_items_select_{$rowDto->getId()}" pk_value="{$rowDto->getId()}"></select>                                                        
                        </td>
                    </tr>				
                {/if}
            {/foreach}

        </tbody>
    </table>	
</div>
<ul id="is3_popup_menu" style="position:fixed;display: none"> 
    <li><a id="is3_take_stock_value" href="javascript:void(0);">Take Stock Value</a></li>
    <li><a id="is3_menu_delete" href="javascript:void(0);">Delete</a></li>
</ul>