<div id= "today_deal_container" style="background: white;width: 100%;height:100%;;position: relative" {if $ns.todayItem} item_id="{$ns.todayItem->getId()}" {/if}>	
    {if $ns.todayItem}	

		{*item picture*}
		<div style="float: left;width: 70px">
			<div class="item_image_60_60" style="float: left;">
				<a href="{$SITE_PATH}/item/{$ns.todayItem->getId()}" class="f_today_deal_open_item_large_view_link"> 
					<img src="{$ns.itemManager->getItemImageURL($ns.todayItem->getId(),$ns.todayItem->getCategoriesIds(), '60_60', 1)}" /> 
				</a>
			</div>				
		</div>


		<div style="margin-left: 80px;font-size: 12px;position: relative">
			<a href="{$SITE_PATH}/item/{$ns.todayItem->getId()}" class="f_today_deal_open_item_large_view_link"> 
				<div id="td_item_display_name_div" style="padding: 5px 0;color: #000;">
					{$ns.todayItem->getDisplayName()}
				</div>			
			</a>

			<div style="color: #000;">
				<div style="line-height:30px;float:left">
					{$ns.langManager->getPhraseSpan(88)}:
				</div>
				<div style="line-height:15px;float:left;margin-left: 10px;white-space: nowrap">
					<span style="text-decoration: line-through;">
						{assign var="ususal_amd_price" value=$ns.todayItem->getListPriceAmd()}
						{$ususal_amd_price|number_format} Դր.
					</span>
					<br />
					<span style="font-size: 16px;color:#A00">												
						{assign var="deal_amd_price" value=$ns.today_deal_fixed_price}							
						{$deal_amd_price|number_format} Դր.																					
					</span>

				</div>	
				{if $smarty.now|date_format:"%Y-%m-%d"<=$ns.todayItem->getItemAvailableTillDate()}
					<div id="countdown" class="countdownHolder" style="float:left"></div>
					<div style="float:left;">
						{if ($ns.userLevel!=$ns.userGroupsGuest && $ns.userLevel!=$ns.userGroupsAdmin && $ns.userLevel!=$ns.userGroupsCompany)}
							<a href="javascript:void(0);" class="orderbutton translatable_attribute_element" id="today_deal_add_to_cart_button" title="{$ns.langManager->getPhrase(284)}" 					   
							   attribute_phrase_id="284" attribute_name_to_translate="title" item_id="{$ns.todayItem->getId()}">
								<img src="{$SITE_PATH}/img/add-to-cart.png" style="float:left;margin: 1px;float: left"/>
							</a>
						{else}
							<a href="javascript:void(0);" title="{$ns.langManager->getPhrase(85)}" 
							   class="translatable_attribute_element" attribute_phrase_id="85" attribute_name_to_translate="title">
								<img class="grayscale" src="{$SITE_PATH}/img/add-to-cart.png" style="float:left;margin: 1px"/>
							</a>
						{/if}
					</div>
				{else}
					<div style="float:left;font-size: 18px;color: #666;padding-left:20px ">
						{$ns.langManager->getPhraseSpan(19)}
					</div>
				{/if}

			</div>
		</div>				
		<div style="color: #000;line-height: 15px;position: absolute;left:0;bottom:0;right:0;text-align: center;margin-bottom: 5px;" title="{$ns.langManager->getPhrase(552)}"
			 class="translatable_attribute_element" attribute_phrase_id="552" attribute_name_to_translate="title">
			<span >{$ns.langManager->getPhraseSpan(551)}*:</span>
			<span style="font-size: 18px;color:#A00">{$ns.today_deal_promo_code}</span>
            
		</div>
            <img title="{$ns.langManager->getPhrase(602)}" 
                 class="translatable_attribute_element" attribute_phrase_id="602" attribute_name_to_translate="title"
                 style="position: absolute;right: 0;bottom: 0; width: 30px" src="{$SITE_PATH}/img/help.png"/>
		<input type="hidden" id="today_deal_seconds_to_end" value="{$ns.today_deal_seconds_to_end}"/>

	{else}

	{/if}

</div>	
