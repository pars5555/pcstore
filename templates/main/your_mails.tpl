
<div id = "mails_body_div" style="width: 100%;">
    <div id = "mails_navigation_container" style=" width:{$ns.mailsNavigationBarWidth-10}px;float:left; position: relative;min-height: 800px;">                
         
        <div id = "mails_navi_toolbar_div" style="width: 100%;height: 50px;border-bottom: 1px solid #aaa;">
           
            <a class="button blue" id="ym_compose_email" style="margin: 10px 20px;height:22px" href="javascript:void(0);">{$ns.langManager->getPhraseSpan(479)}...</a>
            
         </div>

        <div style="color: #000;font-size: 18px;font-weight: bold;">
            <div style="padding: 5px 30px"><a href="javascript:void(0);" id="ym_inbox">{$ns.langManager->getPhraseSpan(474)} (<span id="customer_inbox_unread_email_count_span">{$ns.unread_mails_count}</span>)</a></div>
            <div style="padding: 5px 30px"><a href="javascript:void(0);" id="ym_sent">{$ns.langManager->getPhraseSpan(473)} </a></div>
            <div style="padding: 5px 30px"><a href="javascript:void(0);" id="ym_trash">{$ns.langManager->getPhraseSpan(472)} </a></div>
        </div>
    </div>
    <div id = "mails_main_container" style="float: left; width: {$ns.wholePageWidth-$ns.mailsNavigationBarWidth-1}px;position: relative;min-height: 800px;">    
        <form autocomplete="off"/>
            {nest ns=mails_main_bar}
        </form>
    </div>
    <div style="clear: both"></div>
</div>
    <input type="hidden" id="active_folder_to_show" value="{$ns.active_folder_to_show}"/>