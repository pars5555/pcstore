
<!--***************** don't touch to elements ids and class names, it uses in JS  *****************-->
<div id = "company_item_availability_check_dialog_template"  class="company_item_check_dialog_template">
	<div class="blue-box-header" >
		<!--don't change the position of this header div, it uses in JS-->
		<h1>Header Text</h1><!--don't change the h1, it uses in JS-->
	</div>
	<div id="item_title_div" style="padding: 5px;word-wrap: break-word;">
		{$ns.langManager->getPhraseSpan(16)}					
		</div>
	
	<div style="padding-left: 10px;">
		<div style="line-height: 20px;">
		<input type="radio" name="item_availability_selection" value="2days" />
		{$ns.langManager->getPhraseSpan(17)}
		
		</div>
		
		<div style="line-height: 20px;">
		<input type="radio" name="item_availability_selection" value="1week" />		
		{$ns.langManager->getPhraseSpan(18)}
		
		</div>
		<div style="line-height: 20px;">
		<input type="radio" name="item_availability_selection" value="not" />
		{$ns.langManager->getPhraseSpan(19)}
		
		</div>

	</div>
	<div style="text-align: center;position:absolute; bottom:0px; width:100%;height:30px; padding-bottom: 10px;">
		<a id="reply_item_availability_button" href="javascript:void(0);" class="small button"> {$ns.langManager->getPhraseSpan(476)} </a>
		<!--this A element should be the only A element on this dialog, it uses in JS-->
	</div>
</div>