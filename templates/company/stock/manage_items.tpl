<div class="avo_style_manage_items_container">
	{* Select Company*}
	<div class="avo_style_uploadTopBox avo_style_manageBox">
	    <div style="display:{if ($ns.userLevel === $ns.userGroupsAdmin)}block{else}none{/if};">
	        <div class="avo_style_titleBox">
	            <label for="company_selector">Select company:</label>
	        </div>
	        <select onkeyup="this.blur();
	                this.focus();" id ='mi_select_company' 
	                class="cmf-skinned-select cmf-skinned-text" name='company_selector' style="margin-right: 20px;float: left;" >
	            {html_options values=$ns.allCompaniesIds selected=$ns.selectedCompanyId output=$ns.allCompaniesNames}
	        </select>
	        {if ($ns.userLevel === $ns.userGroupsAdmin)}
	            <button id="hidden_plus_days_button" class="button glyph" style="float: left;">
	                Auto-adjust items date
	            </button>
	        {/if}
	
	    </div>
	    <button  id="add_new_item_button" class="button blue" style="float:left;margin-left: 30px">
	        {$ns.langManager->getPhraseSpan(96)}...
	    </button>
	
	    {if $ns.userLevel === $ns.userGroupsAdmin} 		
	        <button  id="paste_item_button" class="button blue" style="float: left;margin-left: 10px;{if !isset($ns.copied_item_id)}visibility:hidden{/if}" title="Paste {$ns.copied_item_id}">
	            Paste
	        </button>
	            <button  id="import_price_button" class="button gray" style="float:left;margin-left: 10px" disabled="true">
	            import...
	        </button>
	    {/if}
	
	    <a href="{$SITE_PATH}/company_item_export/{$ns.selectedCompanyId}" style="float: right;margin-right: 10px;color:green"> Export Stock 
	        <img src="{$SITE_PATH}/img/document.png" style="vertical-align: middle;"/> 
	    </a>
	
	</div>
	
	<div class="clear"></div>		
	
	{* Company Stock items listing*}
	
	<div  class="avo_style_uploadWrapperBox" id = "f_company_items_table_container">
	    <form>
	        <table cellspacing="0" style="width:100%;">
	            <thead>
	                <tr>
	
	                    <th>{$ns.langManager->getPhraseSpan(108)}</th>
	                    <th>{$ns.langManager->getPhraseSpan(109)}</th>
	                        {if ($ns.userLevel === $ns.userGroupsAdmin)}
	                        <th>Spec</th>
	                        {/if}
	                    <th>$</th>
	                    <th>{$ns.langManager->getPhraseSpan(499)} $</th>
	                    <th>Դր.</th>
	                    <th>{$ns.langManager->getPhraseSpan(499)} Դր.</th>
	                    <th>{$ns.langManager->getPhraseSpan(111)}
	                        {if ($ns.userLevel === $ns.userGroupsAdmin)}
	                            <a id="mi_hide_all_items" href="javascript:void(0);">X</a></th>
	                        {/if}
	                    </th>
	                    <th>{$ns.langManager->getPhraseSpan(112)}</th>
	                        {if ($ns.userLevel === $ns.userGroupsAdmin)}
	                        <th>Sort				
	                            <a id="mi_reset_all_indexes" href="javascript:void(0);">X</a>
	                        </th>
	                    {/if}
	                </tr>
	            </thead>
	            <tbody>
	                {foreach from=$ns.company_items item=item name=ci}
	                    {if ($item->getHidden()==1) && !isset($first_invisible_meet)}
	                        <tr><td><td>----------hidden items---------</td></td></tr>
	                        {assign var='first_invisible_meet' value=1}
	                    {/if}
	                    <tr class="{if $smarty.foreach.ci.index % 2 == 1}avo_style_blueTableBox{/if}">
	
	                        <td>
	                            <div style="cursor: pointer;" class="mi_item_default_pictures" item_id="{$item->getId()}">
	                                <img src="{if $item->getImage2()!=''}data:image/jpeg;base64,{$item->getImage2()}{else}{$ns.itemManager->getItemDefaultImageByCategoriesIds($item->getCategoriesIds(), '60_60')}{/if}" />
	                            </div>
	                        </td>
	                        <td id="item_display_name_td_field_{$item->getId()}" class="avo_style_table_titleBox {if $item->getHidden()==1 || $ns.itemManager->isItemAvailable($item) == 0}inactive{/if}">
	                            <span style="white-space: normal;word-wrap: break-word" id="f_item_display_name_in_table_{$item->getId()}" class="f_item_display_name_in_table">{$item->getDisplayName()}</span>
	                            {if $ns.userLevel === $ns.userGroupsAdmin || $ns.userLevel === $ns.userGroupsCompany}
	                                <input type="text" id="f_item_inline_display_name_input_{$item->getId()}" style="display: none;width: 300px;" />
	                            {/if}
	                        </td>
	                        {if ($ns.userLevel === $ns.userGroupsAdmin)}
	                            <td>
	                                <button class="mi_item_spec_button button" item_id="{$item->getId()}">spec</button>
	                            </td>
	                        {/if}
	                        <td style="vertical-align: middle;">
	                            <span id="f_item_dealer_price_in_table_{$item->getId()}" class="f_item_dealer_price_in_table">{$item->getDealerPrice()|number_format:2}</span>
	                            {if $ns.userLevel === $ns.userGroupsAdmin || $ns.userLevel === $ns.userGroupsCompany}
	                                <input type="text" id="f_item_inline_price_input_{$item->getId()}" style="display: none;width: 50px;" />
	                            {/if}
	                        </td>
	
	                        <td style="vertical-align: middle;">
	                            <span id="f_item_vat_price_in_table_{$item->getId()}" class="f_item_vat_price_in_table">{$item->getVatPrice()|number_format:2}</span>
	                            {if $ns.userLevel === $ns.userGroupsAdmin || $ns.userLevel === $ns.userGroupsCompany}
	                                <input type="text" id="f_item_inline_vat_price_input_{$item->getId()}" style="display: none;width: 50px;" />
	                            {/if}
	                        </td>
	
	                        <td style="vertical-align: middle;">
	                            <span id="f_item_dealer_price_amd_in_table_{$item->getId()}" class="f_item_dealer_price_amd_in_table">{$item->getDealerPriceAmd()}</span>
	                            {if $ns.userLevel === $ns.userGroupsAdmin || $ns.userLevel === $ns.userGroupsCompany}
	                                <input type="text" id="f_item_inline_price_amd_input_{$item->getId()}" style="display: none;width: 50px;" />
	                            {/if}
	                        </td>
	
	                        <td style="vertical-align: middle;">
	                            <span id="f_item_vat_price_amd_in_table_{$item->getId()}" class="f_item_vat_price_amd_in_table">{$item->getVatPriceAmd()}</span>
	                            {if $ns.userLevel === $ns.userGroupsAdmin || $ns.userLevel === $ns.userGroupsCompany}
	                                <input type="text" id="f_item_inline_vat_price_amd_input_{$item->getId()}" style="display: none;width: 50px;" />
	                            {/if}
	                        </td>
	                        <td style="vertical-align: middle;">
	                            <input id="hide_company_item_checkbox_{$item->getId()}" class="hide_company_item_checkboxs"   type="checkbox"		
	                                   {if $item->getHidden()== '1'} checked="checked" {/if} 	/> </td>
	
	
	                        <td style="vertical-align: middle;">
	                            <a id='edit_company_item_link_{$item->getId()}'  class="edit_company_items_links button" href="javascript:void(0);">{$ns.langManager->getPhraseSpan(112)}</a>
	                            {if ($ns.userLevel === $ns.userGroupsAdmin)}
	                                <br/>
	                                <a id='remove_company_item_link_{$item->getId()}' class="remove_company_items_links button"  href="javascript:void(0);">{$ns.langManager->getPhraseSpan(71)}</a>
	                            {/if}
	                        </td>
	
	                        {if ($ns.userLevel === $ns.userGroupsAdmin)}
	                            <td style="vertical-align: middle;">
	                                <span id="f_item_price_order_index_in_table_{$item->getId()}" class="f_item_price_order_index_in_table">{$item->getOrderIndexInPrice()}</span>
	                                <input type="text" id="f_item_inline_price_order_index_input_{$item->getId()}" style="display: none;width: 50px;" />
	                            </td>
	                        {/if}
	
	
	                    </tr>
	
	                {/foreach}
	            </tbody>
	        </table>
	    </form>
	</div>
</div>	

<div id="f_mi_add_edit_container"></div>