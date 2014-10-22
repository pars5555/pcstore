
<div style="width: 100%;height: 100%;">
    <div style="clear:both; min-height:50px" id="is2_header_container" >
        <div style="clear:both" id="is2_header_content">
            <button id="is2_select_all" class="button glyph" style="float:left">Select All</button>
            <button id="is2_select_none" class="button glyph" style="float:left">Select None</button>
            <div id="is2_set_category_for_selection" style="float:left;border: 1px solid gray;padding:5px">
                <select id="is2_selected_price_item_root_category" onkeyup="this.blur();
                        this.focus();" class="cmf-skinned-select cmf-skinned-text">
                    {html_options values=$ns.firstLevelCategoriesIds output=$ns.firstLevelCategoriesNames}
                </select>
                <button class="button glyph" id="is2_selected_price_items_sub_categories_button">...</button>
                <input type="hidden" id="is2_selected_price_items_sub_categories_ids" value=""/>
            </div>
        </div>

    </div>
    {*price new items table*}
    <table style="width: 100%;background-color: #ddd" id="ii_table_view">
        <thead>	
        <th>

        </th>
        {foreach from=$ns.columnNames key=dtoFieldName item=columnTitle name=columnNamesForeach}
            <th {if $dtoFieldName=='displayName'}style="min-width:400px"{/if}>
                {$columnTitle}
            </th>
        {/foreach}
        <th>
            root category
        </th>		
        <th>
            spec
        </th>		
        <th>
            <button id="f_find_all_simillar_items">fine all simillar items</button>
        </th>		
        <th>
            Picture
        </th>		

        </thead>
        <tbody style="line-height: 14px">
            {foreach from=$ns.priceRowsDtos item=rowDto}
                <tr ii_table_pk_value="{$rowDto->id}">
                    <td>
                        <input type="checkbox" class="is2_include_row" pk_value="{$rowDto->id}"  
                               {if isset($ns.new_items_row_ids)} 
                                   {if $rowDto->id|in_array:$ns.new_items_row_ids}
                                       checked="checked"
                                   {/if}							   
                               {/if}/>
                    </td>
                    {foreach from=$ns.columnNames key=dtoFieldName item=columnTitle name=columnNamesForeach}

                        {if $dtoFieldName=="warrantyMonths"}
                            {assign var=originalFieldName value="originalWarranty"} 
                        {else}
                            {assign var=cap value=$dtoFieldName|capitalize}
                            {assign var=originalFieldName value="original`$cap`"} 
                        {/if}		

                        <td dtoFieldName="{$dtoFieldName}" dtoOriginalFieldName="{$originalFieldName}" 
                            style='max-width:200px; overflow: hidden;text-overflow: ellipsis;'>							
                            <span class="editable_cell" id="ii_table_editable_span_{$rowDto->getId()}_{$dtoFieldName}" dtoFieldName='{$dtoFieldName}' pk_value="{$rowDto->id}"
                                  style="width:100%;">{$rowDto->$dtoFieldName|default:"empty"}</span><br>
                            <span style="color:#888">{$rowDto->$originalFieldName}</span>
                        </td>

                    {/foreach}
                    <td >							

                        <select id="is2_price_item_root_category_{$rowDto->getId()}" pk_value="{$rowDto->getId()}" onkeyup="this.blur();
                                this.focus();" name="item_root_category" class="cmf-skinned-select cmf-skinned-text price_items_roots_categories_selects" style="width:100px;float: left">
                            {html_options values=$ns.firstLevelCategoriesIds  selected=$rowDto->getRootCategoryId() output=$ns.firstLevelCategoriesNames}
                        </select>
                        <button class="is2_sub_categories_button" pk_value="{$rowDto->getId()}" style="float:left">...</button>
                        <input type="hidden" pk_value="{$rowDto->getId()}" id="is2_price_item_sub_categories_ids_{$rowDto->getId()}" class="price_items_sub_categories_hiddens" value="{$rowDto->getSubCategoriesIds()}"/>
                        <div style="margin-left:10px;float:left">
                            <input type="checkbox" class="is2_cat_checkbox" pk_value="{$rowDto->getId()}"/>
                        </div>
                    </td>

                    <td>							
                        <button class="is2_spec_button" pk_value="{$rowDto->getId()}">spec</button>
                        <textarea style="display: none" pk_value="{$rowDto->getId()}" id="is2_item_short_spec_{$rowDto->getId()}">{$rowDto->getShortSpec()}</textarea>
                        <textarea style="display: none" pk_value="{$rowDto->getId()}" id="is2_item_full_spec_{$rowDto->getId()}">{$rowDto->getFullSpec()}</textarea>
                    </td>
                    <td>
                        <input type="text" id="is2_simillar_item_search_text_{$rowDto->getId()}" class="is2_simillar_item_search_texts" pk_value="{$rowDto->getId()}" value="{$rowDto->getSupposedModel()}"/>
                        <button class="is2_find_simillar_items_button" id="is2_find_simillar_items_button_{$rowDto->getId()}" pk_value="{$rowDto->getId()}">load</button><br>
                        <select class="is2_simillar_items_select" style="max-width:150px" id="simillar_items_select_{$rowDto->getId()}" pk_value="{$rowDto->getId()}"></select>                                                        
                    </td>
                    <td>
                        <a class="button blue f_upload_photo_button" row_id="{$rowDto->getId()}" type="submit">upload</a>
                        <img id="is2_item_picture_{$rowDto->getId()}" src="" alt="" style="max-width: 100px;max-height: 60px"/>
                        <form class="picture_form" target="is2_upload_target" enctype="multipart/form-data" method="post" action="{$SITE_PATH}/dyn/admin/do_import_steps_actions_group" style="width:0; height:0;visibility: none;border:none;">        
                            <input type="file" class="item_picture"  id="item_picture_{$rowDto->getId()}" name="item_picture" accept="image/*" style="display:none">
                            <input type="hidden" name="action" value="upload_new_item_picture"/>
                            <input type="hidden" name="row_id" value="{$rowDto->getId()}"/>
                        </form>
                       
                    </td>

                </tr>

            {/foreach}

        </tbody>
    </table>
             <iframe name="is2_upload_target" style="width:0;height:0;border:0px solid #fff;display: none;"></iframe>
</div>
<input type="hidden" id="is1_company_id" value="{$ns.company_id}" />
<input type="hidden" id="is1_used_columns_indexes_array" value="{$ns.used_columns_indexes_array}" />