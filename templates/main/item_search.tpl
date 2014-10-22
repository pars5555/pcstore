{if isset($ns.selected_item_id)}
    {nest ns=item_large_view}		
{else}
    <div id='item_search_overlay_div' class="modal_loading_div"></div>

    <div id="item_search_header_and_toolbar_container" class="avo_style_item_search">
        <div id = "search_header_bar" class="avo_style_search_header_bar" style="width: 100%;">

            <div style="float:left;margin-top: 10px">
                {if ($ns.companiesIds|@count > 1)}
                    <label for="selected_company_id" style="font-size: 12px;float: left;margin: 5px 0 0 10px">{$ns.langManager->getPhraseSpan(115)}: </label>
                {/if}
                <select onkeyup="this.blur();
                        this.focus();" class="cmf-skinned-select cmf-skinned-text" name='selected_company_id' id='selected_company_id' autocomplete="off"
                        style="width:100px;margin-left:5px;float: left;	{if not ($ns.companiesIds|@count > 1)}display:none;{/if}">

                    {foreach from=$ns.companiesIds item=value key=key}
                        {if ($key == 0)}
                            <option value="{$value}" {if $ns.selectedCompanyId == 0}selected="selected"{/if} class="translatable_element" phrase_id="153">{$ns.companiesNames[$key]}</option>
                        {else}
                            <option value="{$value}" {if $ns.selectedCompanyId == $value}selected="selected"{/if} >{$ns.companiesNames[$key]}</option>

                        {/if}
                    {/foreach}
                </select>
            </div>

            <button id="item_seach_button" class="avo_style_btn" style="margin: 8px 10px 0 0;float: right;height: 32px;">
                {$ns.langManager->getPhraseSpan(91)}
            </button>
            <input name="search_item_text_field"  id="search_item_text_field" type="text" value="{$ns.search_text}" autocomplete="off" class="avo_style_text_field"/>

        </div>

        <div id = "search_toolbar_container" class="avo_style_search_toolbar_container">

            <div class="avo_style_price_range_div">

                <div class="avo_style_price_range_block">

                    <label for="search_item_price_range_min" style="margin: 5px 0 0 10px">{$ns.langManager->getPhraseSpan(88)}: </label>
                    <input name="search_item_price_range_min" class="avo_style_input box"  id="search_item_price_range_min" type="text" value="{$ns.search_item_price_range_min_value}" autocomplete="off" style="width: 70px;"/>
                    <label for="search_item_price_range_max" style=" margin: 5px"> {$ns.langManager->getPhraseSpan(185)}</label>
                    <input name="search_item_price_range_max" class="avo_style_input box"  id="search_item_price_range_max" type="text" value="{$ns.search_item_price_range_max_value}" autocomplete="off" style="width: 70px;"/>
                    <span style="margin: 5px 30px 0 2px;"> Դր.</span>

                </div>

                <div class="avo_style_price_range_block">    
                    <label class="avo_style_Vat" for="search_item_show_only_vat_items">{$ns.langManager->getPhraseSpan(378)}</label>
                    <input {if isset($ns.show_only_vat_items)}checked="checked"{/if} name="search_item_show_only_vat_items"  id="search_item_show_only_vat_items" type="checkbox" autocomplete="off" style="margin-top: 5px"/>
                </div>


                <div class="avo_style_price_range_block">
                    <label for="selected_company_id" style="margin-top: 5px">{$ns.langManager->getPhraseSpan(116)}: </label>
                    <select onkeyup="this.blur();
                            this.focus();" class="cmf-skinned-select cmf-skinned-text" name='sort_by_select' id='sort_by_select' style="width:100px;margin-right:5px;">

                        {foreach from=$ns.sort_by_values item=value key=key}
                            <option value="{$value}" {if $ns.selected_sort_by_value == $value}selected="selected"{/if} class="translatable_element" phrase_id="{$ns.sort_by_display_names_phrase_ids_array[$key]}">{$ns.sort_by_display_names[$key]}</option>
                        {/foreach}

                    </select>
                </div>
            </div>
            {if ($ns.userLevel===$ns.userGroupsAdmin)}
                <div id="f_admin_search_toolbox" class="avo_style_price_range_div" >
                    <div class="avo_style_price_range_block">
                        <label for="no_picture_items_only">no picture items: </label>
                        <input type="checkbox" id="no_picture_items_only" value='1' {if $ns.show_only_non_picture_items==1}checked{/if}/>
                    </div>
                    <div class="avo_style_price_range_block">
                        <label for="no_short_spec_items_only">no short spec items: </label>
                        <input type="checkbox" id="no_short_spec_items_only" value='1' {if $ns.show_only_no_short_spec_items==1}checked{/if}/>
                    </div>
                    <div class="avo_style_price_range_block">
                        <label for="no_full_spec_items_only">no full spec items: </label>
                        <input type="checkbox" id="no_full_spec_items_only" value='1' {if $ns.show_only_no_full_spec_items==1}checked{/if}/>
                    </div>

                </div>
            {/if} 

            <div class="avo_style_result_count_box">
                {$ns.langManager->getPhraseSpan(114)}
                <span id="total_search_result_items_count" class="avo_style_result_count"></span>
                {$ns.langManager->getPhraseSpan(113)}

            </div>

        </div>
        <div style="clear: both"></div>
    </div>
    <div style="clear: both"></div>

    <div id = "search_body_div" style="width: 100%;font-size: 12px">
        <div id = "search_body_categories_container" class="avo_search_body_categories_container" style="min-height: 1px;min-width: {$ns.searchLeftBarWidth}px;width:{$ns.searchLeftBarWidth}px;float:left; position: relative;background-color: #FFF">
            {include file="$TEMPLATE_DIR/main/item_search_categories.tpl"}
        </div>
        <div id = "item_search_result_container" style="float: left; width: {$ns.wholePageWidth-$ns.searchLeftBarWidth-10}px;position: relative">
            {include file="$TEMPLATE_DIR/main/item_search_result.tpl"}
        </div>
        <div style="clear: both"></div>
    </div>

    <div id = "check_item_availability_dialog" style="display:none">	
        <div style="padding: 15px;">
            <label for="keep_anonymous_checkbox" style="line-height: 20px;margin-right: 5px;">{$ns.langManager->getPhraseSpan(528)}</label>
            <input id ="keep_anonymous_checkbox" name ="keep_anonymous_checkbox" type="checkbox"/>
        </div>
    </div>
    <input id ="total_search_result_items_count_value" type="hidden" value="{$ns.totalItemsRowsCount}"/>
    <div style="clear: both"></div>
{/if}


