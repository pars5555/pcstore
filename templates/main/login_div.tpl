<form id = "ld_login_form" style="float: right;margin-top:2px">		
	<input placeholder="{$ns.langManager->getPhrase(3)}"  type="email"  class="translatable_attribute_element" attribute_phrase_id="3" attribute_name_to_translate="placeholder" id="user_email_input_field" style="width:150px"/>
	<input placeholder="{$ns.langManager->getPhrase(4)}" type="password" class="translatable_attribute_element" attribute_phrase_id="4" attribute_name_to_translate="placeholder" id="user_pass_input_field" style="width:100px"/>
	<button  class="button blue"  id = "login_button" ><span class="glyph key"></span>{$ns.langManager->getPhraseSpan(1)}</button>
</form>
<a id = "login_registration" href="{$SITE_PATH}/registration" onclick="javascript:void(0);" style="font-weight: bold;float: right;margin: 7px;">{$ns.langManager->getPhraseSpan(5)}</a>
<a id = "login_forgot_password" href="javascript:void(0);" style="float: right;margin: 7px;">{$ns.langManager->getPhraseSpan(6)}</a>
