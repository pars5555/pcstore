<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {include file="$TEMPLATE_DIR/main/util/headerControls.tpl"}
    </head>
    <body class="winter_background avo_style">
        <div id="fb-root"></div>
        <div class="center">
            <input type="hidden" id="initialLoad" name="initialLoad" value="main" />
            <input type="hidden" id="contentLoad" value="{$ns.contentLoad}" />
            <input id = "customer_ping_pong_timeout_seconds" type="hidden"  value="{$ns.customer_ping_pong_timeout_seconds}"/>			
            {include file="$TEMPLATE_DIR/main/util/header.tpl"}
            {if $ns.under_construction == 1}
                <div id="f_site_in_under_constraction">
                    <h1 style="text-align: center;background: white">Under Construction</h1>
                </div>
            {else}
                {nest ns=content}
            {/if}
            {include file="$TEMPLATE_DIR/main/util/footer.tpl"}
        </div>
        {if ($ns.user_activation)}
        <input id = "user_activation_element_id" type="hidden" value="{$ns.user_activation}" />
    {/if}
        {if ($ns.open_register_page)}
        <input id = "open_register_page" type="hidden" value="1" />
    {/if}
    <input type="hidden" id="win_uid" value="{$ns.winUid}" />   
</body>
</html>