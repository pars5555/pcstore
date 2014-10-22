<div style="margin-bottom: 10px;color:red" id="ss_error_message">
</div>
<div style="margin-bottom: 10px">
    <label for="ss_gateway">Gateway:</label>
    <select id="ss_gateway"  onkeyup="this.blur();
            this.focus();" class="cmf-skinned-select cmf-skinned-text" >
        {html_options options=$ns.smsGatewaysNames selected=$ns.defaultSmsGateway}
    </select>
</div>
<div style="margin-bottom: 10px">
    <label for="ss_phone_number">Phone number:</label>
    <input type="text" id="ss_phone_number"/>
</div>
<div>
    <label for="ss_message">Message:</label>
    <div style="clear:both"></div>
    <textarea id="ss_message" style="width:100%;min-height: 150px;border: 1px solid gray"></textarea>
</div>