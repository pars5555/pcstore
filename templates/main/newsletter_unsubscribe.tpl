<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {include file="$TEMPLATE_DIR/main/util/headerControls.tpl"}
    </head>
    <body class="winter_background">
        <div id="fb-root"></div>
        <div class="center">           
            {include file="$TEMPLATE_DIR/main/util/header.tpl"}
            <div id = "home_page_content_container" class="header_div_style" style="width:{$ns.wholePageWidth}px;background: #EFEFEF;position: relative;text-align: center;min-height: 500px;padding: 30px 0;">
                {if $ns.error_message}
                    <div style="font-size: 16px;color: #A00;padding: 10px">{$ns.error_message}</div>
                {/if}
                {if $ns.success_message}
                    <div style="font-size: 16px;color: #0A0;padding: 10px">{$ns.success_message}</div>
                {/if}
                <h1>Unsubscribe from Pcstore.am newsletter recipient list!</h1>    
                <div style="clear:both;margin:10px">
                    <form action="/dyn/main/do_delete_newsletter_subscriber">
                        <label for="unsubscribe_email_input">Email: </label>
                        <input name="email" id="unsubscribe_email_input" type="email" style="width: 250px"/>
                        <div style="clear:both;margin-top: 10px"></div>
                        <button type="submit" >Unsubscribe</button>
                    </form>

                </div>
            </div>
        </div>        

    </body>
</html>