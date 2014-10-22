{if ($ns.selected_component_id && $ns.selected_component_id == $item->getId()) || 
		($ns.selected_components_ids_array && in_array($item->getId(), $ns.selected_components_ids_array))}
{assign var="item_is_selected" value=true}
{/if}

	{assign var="show_dealer_price" value=1}
	{if $ns.userLevel==$ns.userGroupsAdmin}
		{if $ns.admin_price_group != 'admin'}
			{assign var="show_dealer_price" value=0}
		{/if}
	{/if}

	{if ($ns.multi_count_selection_item)}
		{assign var = "max_count" value = $ns.componentLoad->getComponentMaxPossibleCount($item)}
		{assign var = "selected_component_count" value = $ns.componentLoad->getSelectedItemCount($item)}

		{if $max_count == $selected_component_count}
			{assign var = "component_limit_over" value=true}
		{/if}
	{/if}

	{if ($item->getPccItemCompatible()== 0)}
		{assign var="error_message"  value=$ns.pcmm->getItemAllNotCompatibleReasonsMessages($item, $ns.componentIndex)}
	{/if}

	{if ($ns.userLevel== $ns.userGroupsCompany || $ns.userLevel==$ns.userGroupsAdmin) || $item->getIsDealerOfThisCompany()==1}
		{assign var="isAdminOrCompanyOrDealer" value=true}
	{/if}

	<div class="{$ns.componentName|lower}_pcc_listing_item {if $error_message}tooltip yellow-tooltip{/if}" style="position: relative; width: 100%;height:50px;background:{if ($item->getPccItemCompatible()==1)}#EEFFEE{else}#FFBBBB{/if};padding-top: 2px;border-bottom: 1px solid  #C4C4C4" >	

		{if $error_message}	
			<div class="tooltip_baloon" style="word-wrap: break-word;white-space: normal">	
				<img width="48" height="48" alt="Error" src="../../img/Critical.png" style="float: left;margin-left: -50px;margin-top: -20px">	
				<span style="float:left; font-size: 16px;padding-bottom: 20px;font-style: italic">{$ns.langManager->getPhraseSpan(370)}</span>
				<span style="float:left;white-space: normal;">{$error_message}</span></div>	 	 
			{/if}

		<div style="height: 100%;float: left;width: 40px">		

			{if !$ns.multiselect_component}
				{if  $item_is_selected}
					<a id="remove_selected_{$ns.componentName|lower}_component" href="javascript:void(0);">
						<div class="pcc_remove_selected_item_icon"></div>
					</a>
				{/if}			
				<input id="{$ns.componentName|lower}_radio_{$item->getId()}" item_id="{$item->getId()}" class="{$ns.componentName|lower}_radios" type="radio"  name="{$ns.componentName|lower}_radio_button_group"
					   style="margin-left:{if  $item_is_selected}5px{else}12px{/if} ;margin-top:13px;float:left"
				{if  $item_is_selected}	checked="checked"	{/if}/>		
		{else}	
			{if !$component_limit_over || $item_is_selected}	
				<input id="{$ns.componentName|lower}_checkbox_{$item->getId()}" item_id="{$item->getId()}" class="{$ns.componentName|lower}_checkboxes" type="checkbox" style="margin-left:12px;margin-top:13px;"			
				{if $item_is_selected} checked="checked" {/if}/>
		{/if}		
	{/if}
</div>
<div style="float: left;width: 50px; height: 50px;position: relative">
	<div class="item_image" style="float: left">			
		<img src="{$ns.itemManager->getItemImageURL($item->getId(), $item->getCategoriesIds(),'30_30', 1 , true)}" />
	</div>
</div>
<div style="float: left;height: 50px; width:380px; position: relative; ">
	<div style="float: left;height: 50px;overflow:hidden;">		

		<a item_id="{$item->getId()}" onclick="javascript:return false;" class="search_item_title {$ns.componentName|lower}_titles"	
		   style="word-wrap: break-word;display:block;">
			{if ($item->getDisplayName()|strlen)>100}
				{$item->getDisplayName()|substr:0:100}...
			{else}
				{$item->getDisplayName()}
			{/if}
		{if $item->getBrand()} <span style="color :#333;font-size: 11px;"> by {$item->getBrand()} </span> {/if}		

		{if $isAdminOrCompanyOrDealer == true}
			<span title="{$ns.langManager->getPhrase(271)}: {$item->getCompanyPhones()|replace:',':'&#13;&#10;'}" style="color :#006600;font-size: 11px;"
				  class="translatable_attribute_element" attribute_phrase_id="`271`: {$item->getCompanyPhones()|replace:',':'&#13;&#10;'}" attribute_name_to_translate="title"> 
				{$ns.langManager->getPhraseSpan(66)}: <span style="color :#000">{$item->getCompanyName()} </span></span> 
			{/if}

	</a>


</div>
</div>
{if $isAdminOrCompanyOrDealer && $item->getVatPrice()>0} 
	{assign var = "showvatprice" value=true}
{/if}	
<div style="float: left;height: 50px;width: 95px;position: relative">
	<div style="width: 100%;height:50px;float: left;
		 line-height:{if $showvatprice || !$isAdminOrCompanyOrDealer ||  ($ns.userLevel==$ns.userGroupsAdmin && $show_dealer_price == 0)}22px{else}45px{/if};">	

		<span class="item_price_style" style="padding-left:2px;float: left;"> 
			{if $isAdminOrCompanyOrDealer && $show_dealer_price == 1}
				${$item->getDealerPrice()|number_format:1}						
			{else}						
				<span style="font-size: 14px;color: #666;text-decoration: line-through;">
					{assign var="price_in_amd" value=$ns.itemManager->exchangeFromUsdToAMD($item->getCustomerItemPrice())}
					{$price_in_amd|number_format} Դր.
				</span>
				</br>						

				{if !$isAdminOrCompanyOrDealer ||  ($ns.userLevel==$ns.userGroupsAdmin && $show_dealer_price == 0)}					
					{math equation="1 - x/100" x=$ns.pc_configurator_discount assign="discountParam"}
					<span title="{$ns.pc_configurator_discount}% {$ns.langManager->getPhrase(285)}"
						  class="translatable_attribute_element" attribute_phrase_id="{$ns.pc_configurator_discount}% `285`" attribute_name_to_translate="title">							
						{$price_in_amd*$discountParam|number_format} Դր.
					</span>									
				{/if}

			{/if} 
			{if $showvatprice && !($ns.userLevel==$ns.userGroupsAdmin && $show_dealer_price == 0)}					
				<div style="clear: both"> </div>
				<span class="item_price_style" style="padding-left:2px;float: left;color:#006600" title="{$ns.langManager->getPhrase(277)}"
					  class="translatable_attribute_element" attribute_phrase_id="277" attribute_name_to_translate="title"> 
					${$item->getVatPrice()|number_format:1}
				</span>
			{/if}


	</div>		
</div>
{if ($ns.multi_count_selection_item) && $ns.selected_components_ids_array && in_array($item->getId(), $ns.selected_components_ids_array)}	
	<div style="width:65px; height: 50px;float: left;">	

		<span style = "margin-top:18px; float: left;color: #000">X</span>		
		<select id="{$ns.componentName|lower}_select_count_{$item->getId()}" class="{$ns.componentName|lower}_select_count" item_id="{$item->getId()}" style="width: 45px;margin-top: 12px;float: left;margin-left: 10px;">
			{section name=spid start=1 loop=$max_count+1 step=1}
				{assign var="index" value=$smarty.section.spid.index}		
				<option value="{$index}" 			
						{if $selected_component_count == $index} 
							selected="selected"
						{/if}>
					{$index}</option>
				{/section}

		</select>	
	</div>
{/if}

</div>



