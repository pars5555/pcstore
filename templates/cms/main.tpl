<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {include file="$TEMPLATE_DIR/cms/util/headerControls.tpl"}
    </head>
    <body class="admin">
        <div class="center">
            <input type="hidden" id="initialLoad" value="cms_main" />
            <input type="hidden" id="contentLoad" value="cms_{$ns.contentLoad}" />
            <div id="ajax_loader"></div>
            {include file="$TEMPLATE_DIR/cms/util/header.tpl"}            
            {nest ns=content}
            {include file="$TEMPLATE_DIR/cms/util/footer.tpl"}
        </div>
    </body>
</html>