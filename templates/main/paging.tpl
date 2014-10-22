{if $ns.pageCount>1}
	<div class="navigation_pagination" id="f_pageingBox">
		<div id="pageBox" class="pagination">

			{if $ns.page > 1}	
				{assign var="pg" value=$ns.page-1}				
				<a id="f_prev" 	 rel="prev"		   
				   href="{$SITE_PATH}/search?{$ns.itemSearchManager->getUrlParams('spg', $pg)}"  class="prev-btn navbutton">&lt;&lt; {$ns.langManager->getPhraseSpan(151)}</a>
			{else}
				<span class="prev-btn disabled">&lt;&lt; {$ns.langManager->getPhraseSpan(151)}</span>
			{/if}

			{if $ns.pStart+1>1}
				<a class="f_pagenum" id="tplPage_1" href="{$SITE_PATH}/search?{$ns.itemSearchManager->getUrlParams('spg', 1)}">1</a>
				{if $ns.pStart+1>2}
					<span>...</span>
				{/if}
			{/if}

			{section name=pages loop=$ns.pEnd start=$ns.pStart}
				{assign var="pg" value=$smarty.section.pages.index+1}				
				{if $ns.page != $pg}		
					
					<a class="f_pagenum" id="tplPage_{$pg}"
					   href="{$SITE_PATH}/search?{$ns.itemSearchManager->getUrlParams('spg', $pg)}">{$pg}</a>
				{else}
					<span class="current">{$pg}</span>
				{/if}
			{/section}

			{if $ns.pageCount > $ns.pEnd}
				{if $ns.pageCount > $ns.pEnd + 1}
					<span >...</span>
				{/if}
				<a class="f_pagenum" id="tplPage_{$ns.pageCount}"
				    href="{$SITE_PATH}/search?{$ns.itemSearchManager->getUrlParams('spg', $ns.pageCount)}">{$ns.pageCount}</a>
			{/if}

			{if $ns.page == $ns.pageCount}
				<span class="next-btn disabled">{$ns.langManager->getPhraseSpan(152)} &gt;&gt;</span>
			{else}
				{assign var="pg" value=$ns.page+1}				
				<a id="f_next"  rel="next"
				   href="{$SITE_PATH}/search?{$ns.itemSearchManager->getUrlParams('spg', $pg)}"
				   class="next-btn navbutton">{$ns.langManager->getPhraseSpan(152)} &gt;&gt;</a>
			{/if}


		</div>
		<input type="hidden" id="f_curPage" value="{$ns.page}">
		<input type="hidden" id="f_pageCount" value="{$ns.pageCount}">
	</div>
{/if}