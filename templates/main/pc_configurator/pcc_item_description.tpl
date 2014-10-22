{if $ns.item}
<div id="pcc_item_description_inner_container" style="width: 100%;border-top: 1px solid">


	<div style="width: 100%;position: relative;">
		<div id="gallery" style="float: left;height: 180px;width: 180px;position: relative;text-align: center">
			{if $ns.item_id|is_array}		
			<div class="item_image_150_150">
				<a href="{$ns.itemManager->getItemImageURL(0, $ns.item.0->getCategoriesIds(), '800_800', 1)}"  rel="lightbox[plants]">
					 <img src="{$ns.itemManager->getItemImageURL(0, $ns.item.0->getCategoriesIds(), '150_150', 1)}" /> </a>
			</div>		
			{else}
			<div class="item_image_150_150">
				<a href="{$ns.itemManager->getItemImageURL($ns.item->getId(), $ns.item->getCategoriesIds(), '800_800', 1)}"  rel="lightbox[plants]">
					 <img src="{$ns.itemManager->getItemImageURL($ns.item->getId(), $ns.item->getCategoriesIds(), '150_150', 1)}" /> </a>
			</div>			
			{/if}
			
			
		</div>

	
		<div style="float: left;width: 100%;">
			{if ($ns.item  and (not ($ns.item|is_array)))}
			<a href="{$SITE_PATH}/item/{$ns.item->getId()}" target="_blank">	  						
					<span style="padding:10px;color:black;word-wrap: break-word;display:block;white-space: normal;font-size: 14px">{$ns.item->getDisplayName()}				
					{if $ns.item->getBrand()} 
							<span style="color :#333;font-size: 11px;"> ({$ns.item->getBrand()}) </span> 
					{/if}
					- {$ns.langManager->getPhraseSpan(82)}: {$ns.item->getWarranty()} {if $ns.item->getWarranty()|lower!='lifetime'}{$ns.langManager->getPhraseSpan(183)}{/if} -
					 <span class="large_view_item_price_style" style="font-size: 14px">				
					{if ($ns.userLevel == $ns.userGroupsCompany || $ns.userLevel==$ns.userGroupsAdmin) || $ns.item->getIsDealerOfThisCompany()==1}
					${$ns.item->getDealerPrice()|number_format:1}
					{else}
					{assign var="price_in_amd" value=$ns.itemManager->exchangeFromUsdToAMD($ns.item->getCustomerItemPrice())}
					{$price_in_amd|number_format} Դր.
					{/if} </span>
					 </span>			
					</a>
			{else}
					{foreach from=$ns.item item=_item}
					<a href="{$SITE_PATH}/item/{$_item->getId()}" target="_blank">	  						
					<span style="padding:10px;color:black;word-wrap: break-word;display:block;white-space: normal;font-size: 14px">{$_item->getDisplayName()}				
					{if $_item->getBrand()} 
							<span style="color :#333;font-size: 11px;"> ({$_item->getBrand()}) </span> 
					{/if}
					- {$ns.langManager->getPhraseSpan(82)}: {$_item->getWarranty()} {if $_item->getWarranty()|lower!='lifetime'}{$ns.langManager->getPhraseSpan(183)}{/if} -
					 <span class="large_view_item_price_style" style="font-size: 14px"> 
					{if ($ns.userLevel == $ns.userGroupsCompany || $ns.userLevel==$ns.userGroupsAdmin) || $_item->getIsDealerOfThisCompany()==1}
					${$_item->getDealerPrice()|number_format:1}
					{else}
					{assign var="price_in_amd" value=$ns.itemManager->exchangeFromUsdToAMD($_item->getCustomerItemPrice())}
					{$price_in_amd|number_format} Դր.
					{/if} </span>
					 </span>			
					</a>
					{/foreach}
						
			{/if}
		</div>
		<div style="clear:both"></div>
	</div>


	
	
</div>
{/if}
{if ($ns.item  and (not ($ns.item|is_array)))}
{section name=images_counter start=2 loop=$ns.item_pictures_count+1 step=1}
<a href="{$SITE_PATH}/images/item_800_800/{$ns.item_id}/{$smarty.section.images_counter.index}/picture.jpg"  rel="lightbox[plants]" style="display: none"></a>
{/section}
{/if}
<div style="clear:both"></div>
