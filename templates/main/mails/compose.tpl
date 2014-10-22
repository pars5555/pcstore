<div id = "mails_bar_toolbar" style="width: 100%;height: 50px;border-bottom: 1px solid #aaa;">
    <a class="button blue" style="margin: 10px; height:22px" href="javascript:void(0);" id="ym_send_email">{$ns.langManager->getPhraseSpan(48)}</a>    
    <a class="button" style="margin: 10px; height:22px" href="javascript:void(0);" id="ym_cancel_email">{$ns.langManager->getPhraseSpan(49)}</a>    
</div>
<div id = "mails_compose_body" style="width: 100%;">
    <div style="padding:10px">
        <span>To: </span>
        <div type="text" id="cm_to_emails" style="width: 100%;border:1px solid #aaa;min-height:40px;
             max-height: 100px;   overflow-y: auto;background: #fff
             "></div>
        <a href="javascript:void(0);" id="mails_clear_all_contacts" class="button" style="height:22px;margin-top:5px" >{$ns.langManager->getPhraseSpan(498)}</a>
    </div>
    <div style="padding:10px">
		<span>{$ns.langManager->getPhraseSpan(463)}: </span>
		<input type="text" id="cm_subject" style="width: 100%" value="{if !empty($ns.email_subject)}{$ns.email_subject}{/if}"/>
    </div>
	<div style="padding:10px">
		<textarea type="text" id="cm_body" name="cm_body" class="emailsTinyMCEEditor" style="width: 100%;height: 500px">{if !empty($ns.email_body)}<br/><br/><hr><hr>{$ns.email_body}{/if}</textarea>
    </div>
	{if !empty($ns.email_to)}
		<input type="hidden" id="cm_email_to" value="{$ns.email_to}"/>
		<input type="hidden" id="cm_email_to_name" value="{$ns.email_to_name}"/>
		<input type="hidden" id="cm_email_to_type" value="{$ns.email_to_type}"/>
	{/if}
</div>
