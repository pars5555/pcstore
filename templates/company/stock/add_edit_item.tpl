<div id="mi_edit_item_dialog">
    {* Item Edit Form *}
    <form name ="create_item_form" id="create_item_form" target="mi_upload_target" enctype="multipart/form-data" method="post"
          action="{$SITE_PATH}/dyn/company_item/do_add_change_item"
          style="position: relative;width:100%;height:100%;overflow-y: hidden">

        <input id="mi_submit_form_button" type="submit" style="display: none"/>

        <div style="position: relative;float: left;padding-right: 10px;width:390px;">
            <p>
            <div style="line-height:20px;float: left;width:100px;text-align: left;padding-left: 10px;">
                <label for="item_title">{$ns.langManager->getPhraseSpan(97)}:</label>
            </div>
            <input name='item_title' id="item_title"  type="text" value="{$ns.item_title}"  style="width:280px;"/>
            </p>

            <p>

            <div style="position:relative; line-height:20px;float: left;width:100px;text-align: left;padding-left: 10px;">
                <label for="item_availability">{$ns.langManager->getPhraseSpan(98)}:</label>
            </div>
                     <select  onkeyup="this.blur();
                    this.focus();" class="cmf-skinned-select cmf-skinned-text" name='item_availability' id='item_availability' style="width:80px;"
                     {if ($ns.item_id)}title="Item is available till {$ns.item_available_till_date} (included)"{/if}>
                {html_options values=$ns.item_availability_options_values  selected=$ns.item_availability_selected  output=$ns.item_availability_options_names}
            </select>
            <label for="check_availability" style="margin-left: 10px;">{$ns.langManager->getPhraseSpan(99)}</label>
            <input type="checkbox" value="1" {if ($ns.item_available == '0')}checked="checked"{/if} id="check_availability" name="check_availability" style="margin-left: 5px"/>
            {if ($ns.item_id)}
                <label for="unchange_item_date" style="margin-left: 10px;">{$ns.langManager->getPhraseSpan(379)}</label>
                <input type="checkbox" checked="checked" value="1" id="unchange_item_date" name="unchange_item_date" style="margin-left: 5px"/>
            {/if}
            </p>

            <p>

            <div style="line-height:20px;float: left;width:180px;text-align: left;padding-left: 10px;">
                <label for="item_price_sort_index">{$ns.langManager->getPhraseSpan(276)}:</label>
            </div>
            <input id="item_price_sort_index" name="item_price_sort_index" type="text" min="0" value="{if $ns.order_index_in_price>0}{$ns.order_index_in_price}{else}0{/if}" style="float: left;width: 60px;margin-left: 10px;"/>

            </p>
            <p>
            <div style="line-height:20px;float: left;width:100px;text-align: left;padding-left: 10px;">
                <label for="item_brand">{$ns.langManager->getPhraseSpan(101)}:</label>
            </div>
            <input name='item_brand' id="item_brand"  type="text" value="{$ns.item_brand}" style="width:140px;"/>
            </p>
            <div style="clear: both"></div>
        </div>
        <div style="position: relative; float: left;padding-right: 10px;width: 350px;">
            <p>
            <div style="line-height:20px;float: left;width:120px;text-align: left;padding-left: 10px;">
                <label for="item_price">{$ns.langManager->getPhraseSpan(88)} $:</label>
            </div>
            <input name='item_price'  id='item_price'  type="text" value="{$ns.dealer_price}" style="width:50px;" />
            <label for="item_vat_price" style="margin-left:5px; ">{$ns.langManager->getPhraseSpan(499)} $:</label>
            <input name='item_vat_price'  id='item_vat_price'  type="text" value="{$ns.vat_price}" style="width:50px;" />				
            </p>		
            <p>
            <p>
            <div style="line-height:20px;float: left;width:120px;text-align: left;padding-left: 10px;">
                <label for="item_price">{$ns.langManager->getPhraseSpan(88)} Դր:</label>
            </div>
            <input name='item_price_amd'  id='item_price_amd'  type="text" value="{$ns.dealer_price_amd}" style="width:50px;" />
            <label for="item_vat_price_amd" style="margin-left:5px; ">{$ns.langManager->getPhraseSpan(499)} Դր:</label>
            <input name='item_vat_price_amd'  id='item_vat_price_amd'  type="text" value="{$ns.vat_price_amd}" style="width:50px;" />				
            </p>		
            <p>
            <div style="line-height:20px;float: left;width:120px;text-align: left;padding-left: 10px;">
                <label for="warranty_period">{$ns.langManager->getPhraseSpan(104)}:</label>
            </div>
                    <select onkeyup="this.blur();
                this.focus();" class="cmf-skinned-select cmf-skinned-text" name='warranty_period' id='warranty_period' style="width:140px" >
                {html_options values=$ns.item_warranty_options  selected=$ns.selected_warranty_option  output=$ns.item_warranty_options}
            </select>
            </p>

            <p>
            <div style="line-height:20px;float: left;width:120px;text-align: left;padding-left: 10px;">
                <label for="item_root_category">{$ns.langManager->getPhraseSpan(105)}:</label>
            </div>
                    <select onkeyup="this.blur();
                this.focus();" id = "item_root_category"  name="item_root_category" class="cmf-skinned-select cmf-skinned-text" style="width:170px;float: left">
                {html_options values=$ns.firstLevelCategoriesIds selected=$ns.selectedRootCategoryId output=$ns.firstLevelCategoriesNames}
            </select>
            <button id="select_sub_categories_button" class="button glyph" title="{$ns.langManager->getPhrase(126)}" style="margin-left: 5px;"	
                    class="translatable_attribute_element" attribute_phrase_id="126" attribute_name_to_translate="title">
                ...
            </button>
            </p>
            <p>

            <div style="line-height:20px;float: left;width:120px;text-align: left;padding-left: 10px;">
                <label for="item_model">{$ns.langManager->getPhraseSpan(106)}:</label>
            </div>
            <input name='item_model' id="item_model"  type="text" value="{$ns.item_model}" style="width:140px;"/>

            </p>
            <div style="clear: both"></div>
        </div>		
        <div style="position: relative;float: left;width: 230px;overflow: auto">
            <div id="item_pictures_container_div" style="height: 110px;overflow: auto;padding-left: 8px;padding-top: 3px;">

                {if ($ns.item_pictures_count>0)}
                    {section name=item_picture_id start=0 loop=$ns.item_pictures_count step=1}
                        <div class="item_image" id="item_image_div^{$smarty.section.item_picture_id.index+1}">
                            <img src="{$SITE_PATH}/images/item_30_30/{$ns.item_id}/{$smarty.section.item_picture_id.index+1}/picture.jpg?{$smarty.now}" />
                            <div id="remove_item_picture^{$smarty.section.item_picture_id.index+1}" class="remove_item_picture_x" style="position:absolute; right: 0px;top: 0px;font-size: 14px;padding-right:2px;background: white;cursor: pointer;">
                                x
                            </div>
                        </div>
                    {/section}
                {/if}

            </div>
            <div style="position: absolute;bottom: 0px;width: 100%;height:40px;">
                <div style="margin-top: 5px;">
                    <div style="line-height:18px;float: left;width:140px;text-align: left;padding-left: 10px;float: left">
                        <label for="add_picture">{$ns.langManager->getPhraseSpan(107)}:</label>
                    </div>
                    <input name = "add_picture" id = "add_picture" type="checkbox" style="float: left"/>
                    <button id ="select_picture_button" class="button glyph"  style="margin-left: 10px;float: left">
                        ...
                    </button>
                </div>

            </div>
            <div style="clear: both"></div>
        </div>
        <div style="clear: both"></div>
        <div id="item_description_input_dialog_container" style="bottom: 0">			
            <div style="clear: both"></div>
            <label for="short_description_for_display" >{$ns.langManager->getPhraseSpan(102)}:</label>			
            <div style="clear: both"></div>
            <textarea name="short_description_for_display" id="short_description_for_display" maxlength="1000"  style="width:100%;height:20%; resize: none;float: left">{$ns.short_description}</textarea>																		
            <div style="clear: both"></div>
            <label for="full_description_for_display" >{$ns.langManager->getPhraseSpan(103)}:</label>		
            <div style="clear: both"></div>
            <textarea class="item_full_description_textarea"  name = "full_description" style="bottom:0;position:absolute;top:300px;width:100%;min-height:200px">{$ns.full_description}</textarea>
        </div>

        <input type="hidden" value="{$ns.sub_categories_ids}" id="selected_sub_categories_ids" name="selected_sub_categories_ids" />		
        <input type="hidden" id='item_categories_names'  name='item_categories_names'/>
        <input type="hidden" id="item_removed_pictures_ids" name="item_removed_pictures_ids"/>	
        <input type="hidden" id="company_id" name="company_id" value="{$ns.company_id}"/>
        <input type="hidden" id="selected_item_id" name="selected_item_id" value="{$ns.item_id}"/>		

        <input id="mi_file_input" name="item_picture" type="file" accept="image/*" style="display:none"/>
        <iframe id="mi_upload_target" name="mi_upload_target" style="width:0;height:0;border:0px solid #fff;display: none;" ></iframe>
    </form>
</div>
