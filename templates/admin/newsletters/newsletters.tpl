<textarea type="text" id="sc_newsletter_html"  class="" style="width: 100%;height: 300px"></textarea>
<div style="text-align: center;clear: both">
    <a href="javascript:void(0);" class="button" id="sc_send_newsletter_email" style="float:right;margin: 5px">Send</a>
    <div style="float:right;margin-right: 5px;padding: 7px">
        <input type="checkbox" id="sc_test_checkbox" >
        <label for="sc_test_checkbox" >send to test account only</label>    
        <div style="margin:10px">
            <input type="checkbox" id="send_to_all_registered_users" {if $ns.include_all_active_users ==1}checked{/if}/>
            <label for="send_to_all_registered_users" style="font-weight: bold">Include All Active Users Emails</label>
        </div>

    </div>

    <a href="javascript:void(0);" class="button" id="sc_save_newsletter" style="float:left;margin: 5px">save</a>
    <a href="javascript:void(0);" class="button" id="sc_open_newsletter" style="float:left;margin: 5px">load...</a>
    <a href="javascript:void(0);" class="button" id="sc_manage_newsletters" style="float:left;margin: 5px">manage</a>
    <div style="color:red;font-size: 16px;padding: 10px" id="nl_newsletter_title"></div>
    <div style="clear: both"></div>
</div>


