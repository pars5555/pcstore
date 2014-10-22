<div style="padding:20px" >
    {if $ns.page_loaded == 0}
        <div style="text-align: center;">
        <button id="im_load_page_button">Load Page</button>
        </div>
    {else}
        <div style='width:100%;height: 50px'>
            Company
            <select id="im_selected_company" class="cmf-skinned-select cmf-skinned-text">
                {html_options values=$ns.companyIds selected=$ns.selected_company_id output=$ns.companyNames}
            </select>
            Include Hidden
            <input type="checkbox" id="im_include_hiddens" value="1" {if $ns.include_hiddens==1}checked{/if} style="margin-right: 10px"/>
            Model is empty
            <input type="checkbox" id="im_empty_model" value="1" {if $ns.empty_model==1}checked{/if} style="margin-right: 10px"/>
            Short spec is empty
            <input type="checkbox" id="im_empty_short_spec" value="1" {if $ns.empty_short_spec==1}checked{/if} style="margin-right: 10px"/>
            Full spec is empty
            <input type="checkbox" id="im_empty_full_spec" value="1" {if $ns.empty_full_spec==1}checked{/if} style="margin-right: 10px"/>
            Pictures Count
            <select id="im_pictures_count" >
                <option value='any' {if $ns.pictures_count==='any'}selected{/if}>Any</option>                    
                <option value='0'{if $ns.pictures_count=='0'}selected{/if}>0</option>
                <option value='1'{if $ns.pictures_count=='1'}selected{/if}>1</option>
                <option value='2'{if $ns.pictures_count=='2'}selected{/if}>2</option>
                <option value='3'{if $ns.pictures_count=='3'}selected{/if}>3</option>
                <option value='4'{if $ns.pictures_count=='4'}selected{/if}>4</option>
                <option value='5'{if $ns.pictures_count=='5'}selected{/if}>5</option>
            </select>
        </div>
            <div style="position: absolute; top:50px;bottom: 0;left:0;right: 0 ;overflow: auto">
            <table style="width: 100%;background-color: #ddd">
                <thead>
                <th>Title</th>
                <th>Model</th>
                <th>Short Spec</th>
                <th>Pictures Count</th>
                <th>Same Items</th>
                </thead>
                <tbody style="line-height: 14px">
                    {foreach from=$ns.items item=item}
                        <tr>
                            <td style="max-width: 250px;text-overflow: ellipsis;overflow: hidden" title="{$item->getDisplayName()}">{$item->getDisplayName()}</td>
                            <td style="max-width: 100px;text-overflow: ellipsis;overflow: hidden" title="{$item->getModel()}">{$item->getModel()}</td>
                            <td style="max-width: 200px;text-overflow: ellipsis;overflow: hidden" title="{$item->getShortDescription()}">{$item->getShortDescription()}</td>
                            <td style="max-width: 200px;text-overflow: ellipsis;overflow: hidden">{$item->getPicturesCount()}</td>
                            <td style="max-width: 200px;text-overflow: ellipsis;overflow: hidden">
                                <select id="most_simillar_items_select_{$item->getId()}" style="max-width:250px" class="same_items_select" item_id="{$item->getId()}">
                                </select>
                                <br>
                                <button style="visibility:hidden" id ="im_copy_item_pictures_button_{$item->getId()}" class="im_copy_item_pictures_buttons" item_id="{$item->getId()}">
                                    <- Pic
                                </button>
                                <button style="visibility:hidden" id ="im_copy_item_short_spec_button_{$item->getId()}" class="im_copy_item_short_spec_buttons" item_id="{$item->getId()}">
                                    <-Short Spec
                                </button>
                                    <br>
                                <button style="visibility:hidden" id ="im_copy_item_full_spec_button_{$item->getId()}" class="im_copy_item_full_spec_buttons" item_id="{$item->getId()}">
                                    <-Full Spec
                                </button>
                                <button style="visibility:hidden" id ="im_copy_item_model_button_{$item->getId()}" class="im_copy_item_model_buttons" item_id="{$item->getId()}">
                                    <-Model
                                </button>
                                    <br>
                                <button class="im_find_simillar_items_button" item_id="{$item->getId()}" >Find</button>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    {/if}
</div>
