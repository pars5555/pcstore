<form style="line-height: 24px">
	<div style="clear: both;margin:5px">
		{$ns.langManager->getPhraseSpan(572)}
		<select  style="float:right" id="nms_server_select" class="cmf-skinned-select cmf-skinned-text">
			{html_options options=$ns.all_email_servers_options} 
		</select>
		<div style="clear: both"></div>
	</div>
		
	<div style="clear: both;margin:5px">
		{$ns.langManager->getPhraseSpan(21)}
		<input style="float:right" type="text" id="nms_login"/>
		<div style="clear: both"></div>
	</div>
	<div style="clear: both;margin:5px">
		{$ns.langManager->getPhraseSpan(4)}
		<input style="float:right" type="password" id="nms_password"/>
		<div style="clear: both"></div>
	</div>

</form>