<div id="cart_messages_container" style="color: #FF3333;font-size: 12px;height:50px;overflow: auto">
	<div style="width: 100%;height:100%;">
		{foreach from=$ns.customerMessages item=customerMessage}
			<span style="text-overflow: ellipsis;overflow: hidden;width: 100%;white-space: nowrap;float: left;padding: 3px 0 3px 0">{$customerMessage}</span>		
		{/foreach}			
	</div>
</div>
