<div id= "fl_forgot_login_dialog">
	<div id="fl_forgot_error_msg" style="color:red;margin: 5px"></div>
	<form id="fl_forgot_form" autocomplete="off" >		
		<p>
			<label for="ld_email_input">{$ns.langManager->getPhraseSpan(3)}</label>
			<input placeholder="{$ns.langManager->getPhrase(3)}"  class="translatable_attribute_element" attribute_phrase_id="3" attribute_name_to_translate="placeholder"  type="text" id="fl_email_input" style="width:150px" value="{$ns.login}"/>
		</p>
		<input type="submit" style="height: 0px; width: 0px; border: none; padding: 0px;" hidefocus="true" value="" name=""/>
		<p>
			{$ns.langManager->getPhraseSpan(355)}
			</p>
	</form>	
</div>
