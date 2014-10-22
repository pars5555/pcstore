<div style="position: relative;overflow: auto;">
	<div>
		{foreach from=$ns.firstLevelCategories item=category name=catlist}
			{$category->getDisplayName()}<br />
			
		{/foreach}
	</div>
</div>
