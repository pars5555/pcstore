<div id = "mails_bar_toolbar" style="width: 100%;height: 50px;">
    <a class="button gray" style="margin: 10px;height:22px;visibility: hidden" href="javascript:void(0);" id="ym_delete_email">{$ns.langManager->getPhraseSpan(475)}</a>
    <a class="button blue" style="margin: 10px;height:22px;visibility: hidden" href="javascript:void(0);" id="ym_reply_email">{$ns.langManager->getPhraseSpan(476)}</a>
    <a class="button blue" style="margin: 10px;height:22px;visibility: hidden" href="javascript:void(0);" id="ym_forward_email">{$ns.langManager->getPhraseSpan(477)}</a>
</div>
<div id = "mails_inbox_body" style="width: 100%;background: #fff">
    <div id = "mails_resizable" class="ui-widget-content" style="height:200px">
		<div style="position: absolute; left:0px; top:0px;width:100%;height:100%; overflow-y:auto">
			{foreach from=$ns.emails item=email name=cl} 
				<div class="emails_row_container" id="email_row_container_{$email->getId()}"  email_id="{$email->getId()}" style="{if $email->getReadStatus()==0}font-weight: bold;{/if}">
					<div class="grid_3" style="float: left;padding-top: 3px"><input type="checkbox" row="{$smarty.foreach.cl.index}" class="f_emails_row_checkboxes" id="email_checkbox_{$email->getId()}"/></div>
					<div class="grid_20" style="float: left"><span style="padding: 7px 0;overflow: hidden;white-space:nowrap;text-overflow:ellipsis; display:inline-block;width: 100%">{$email->getCustomerTitle()}</span></div>	
					<div class="grid_30" style="float: left"><span style="padding: 7px 0;overflow: hidden;white-space:nowrap;text-overflow:ellipsis; display:inline-block;width: 100%">{$email->getSubject()}</span></div>
					<div class="grid_20" style="float: left;text-align: center"><span style="padding: 7px 0;overflow: hidden;white-space:nowrap;text-overflow:ellipsis;display:inline-block;width: 100%">{$email->getDatetime()}</span></div>
					<div style="clear: both"> </div>
				</div>
			{/foreach}

			<div style="clear: both"> </div>
		</div>
    </div>

	<div id="mails_bottom_component" class="mails_bottom_component">
		<div style="text-align:center">{$ns.langManager->getPhraseSpan(480)}</div>
    </div>

</div>
