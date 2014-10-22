
<form id='cu_form' name='cu_form'>
    <p>
    <div style="line-height: 20px;width: 130px;float: left">
        <label for="cu_user_email_field" >{$ns.langManager->getPhraseSpan(47)}:</label>
    </div>
    {if $ns.userLevel== $ns.userGroupsGuest}
        <input name="cu_user_email_field" id="cu_user_email_field" type="email"  style="width: 230px"/>
    {else}
        <input name="cu_user_email_field" id="cu_user_email_field" type="email"  readonly="readonly" value="{$ns.customer_email}" style="width: 230px"/>
    {/if}
</p>

<p>
    <textarea name="cu_user_message_field" id="cu_user_message_field" style="width: 100%;height: 140px"></textarea>
</p>
<div>
    <span style="color:#004B91; font-weight: bold;"> Or call us: {$ns.pcstore_contact_us_phone_number} </span>

</div>		
<div style="text-align: left; line-height: 20px;">
    <img src="{$SITE_PATH}/img/skype_logo.png" style="float: left;"/>
    <span style="color: #004B91; font-size: 14px; font-weight: bold">&nbsp; pcstore.am</span>
</div>


</form>

