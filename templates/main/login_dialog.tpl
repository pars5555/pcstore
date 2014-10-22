<div id= "ld_login_dialog">
	<div id="ld_error_msg" style="color:red;margin: 5px"></div>
	<form id="ldlg_login_form" autocomplete="off">		
		<p>
			<label for="ld_email_input">{$ns.langManager->getPhraseSpan(3)}</label>
			<input placeholder="{$ns.langManager->getPhrase(3)}"  class="translatable_attribute_element" attribute_phrase_id="3" attribute_name_to_translate="placeholder" type="text" id="ld_email_input" style="width:150px" value="{$ns.login}"/>
		</p>
		<p>
			<label for="ld_email_input">{$ns.langManager->getPhraseSpan(4)}</label>
			<input placeholder="{$ns.langManager->getPhrase(4)}"  class="translatable_attribute_element" attribute_phrase_id="4" attribute_name_to_translate="placeholder" type="password" id="ld_password_input"  style="width:150px"/>
		</p>
		<input type="submit" style="height: 0px; width: 0px; border: none; padding: 0px;" hidefocus="true" value="" name=""/>
	</form>
	<div style="clear: both"></div>
	<div>
		<a id = "login_dialog_forgot_password" href="javascript:void(0);" >{$ns.langManager->getPhraseSpan(6)} </a>
		
	</div>
	
		<a id = "register_new_user_link" href="javascript:void(0);" style="color:green;font-size: 16px;position: absolute;left: 10px;bottom: 10px">{$ns.langManager->getPhraseSpan(5)} </a>
		
	
</div>
