<div id="item_search_categories_modal_loading_div" class="modal_loading_div"></div>
{if $ns.category_id > 0}
	<div class="avo_style_ItemWrapperCatBox">
		<div class="avo_style_ItemBox">
			<a href="javascript:void(0);" category_id="0">{$ns.langManager->getPhraseSpan(130)}</a>
		</div>
		{assign var="index" value=0}
		{if isset($ns.category_path)}		
			{foreach from=$ns.category_path item=parent_category_dto name=fi}			
				{assign var="index" value=$smarty.foreach.fi.index}
				<div style="width:100%;margin: 5px 0 5px {$index*15+30}px" >
					<a href="{$ns.itemSearchManager->getUrlParams('cid', $parent_category_dto->getId())}" category_id="{$parent_category_dto->getId()}"
					   style="padding: 5px; color: #558AB8;font-size: 14px;">{$parent_category_dto->getDisplayName()}</a>
				</div>
			{/foreach}
		{/if}
		{if $ns.category_id > 0}	
			{assign var="index" value=$index+1}
			<div class="avo_style_ItemCategoryBox" style="padding-left: {$index*15+30}px;" >
				<span style="padding: 5px; color: #558AB8;font-size: 14px;font-weight: bold">{$ns.category_dto->getDisplayName()}</span>
			</div>	
		{/if}
	</div>	
{/if}


{if $ns.itemsCategoryMenuView}
	{$ns.itemsCategoryMenuView->display(false)}
{/if}
{if ($ns.properties_views && $ns.properties_views |@count>0)}
	<div class="avo_style_category_property_view_container" id="category_property_view_container">
		{foreach from=$ns.properties_views item=property_view}
			{$property_view->display()}
		{/foreach}	
	</div>
{/if}

{if $ns.category_id > 0}
	<input type="hidden" id="search_selected_category_id" value="{$ns.category_id}"/>
{/if}