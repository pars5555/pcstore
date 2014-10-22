<div id="ajax_loader"></div>
<link href="{$SITE_PATH}/img/logo_150x91.png" rel="image_src"/>

<div id = "heder_div" class="avo_style_headerBox">

    <div class="avo_style_headerInnerBox">       
        <a href="/" style="float: left;" ><img src="{$SITE_PATH}/img/logo_150x91{if $ns.langManager->getCmsVar('cristmas_logo_enable')==1}-cristmas{/if}.png"/></a>

        <a href="http://www.imusic.am" style="float: left;"  target="_blank"><img src="{$SITE_PATH}/img/imusic_logo_150_91.png" /></a>
        <a href="javascript:void(0);">
            <div id="daily_deal_container" style="width:450px; height:91px;float: left">
                {nest ns=deals}
            </div>

            <div id="hader_banner_container" style="width:450px; height:91px;float: left;display:none">
                <img  src="{$SITE_PATH}/img/header_banner_sale.png" />
            </div>

        </a>

        {if ($ns.user)}
            <div id="shopping_cart_link" style="float:right;cursor: pointer;font-size: 24px;font-weight: bold; position: relative">
                <img style="float:left" src="{$SITE_PATH}/img/checkout/shopping_cart.png"/>					
                <div id= "shopping_cart_item_count" style="margin-left: 6px; position: absolute;width: 100%; margin-top: 20px;text-align: center;color: lightsalmon;
                     text-shadow:-2px -2px 0 #000,2px -2px 0 #000,-2px 2px 0 #000,2px 2px 0 #000;">{if $ns.customer_cart_items_count>0}{$ns.customer_cart_items_count}{else}0{/if}</div>
            </div>
        {/if}


        {* site languages *}
        <div style="position:fixed;z-index:12000;right:0">
            <div class="flag_image" style="margin-top: 5px;" >
                <a href="{$SITE_PATH}/am" onclick="javascript:void(0);" id="set_language_am_link"><img  src="{$SITE_PATH}/img/language_flages/am_s.png" /></a>
            </div>

            <div class="flag_image" style="margin-top: 5px;">
                <a href="{$SITE_PATH}/en" onclick="javascript:void(0);" id="set_language_en_link"><img src="{$SITE_PATH}/img/language_flages/en_s.png" /></a>
            </div>

            <div class="flag_image" style="margin-top: 5px;">
                <a href="{$SITE_PATH}/ru" onclick="javascript:void(0);" id="set_language_ru_link"><img src="{$SITE_PATH}/img/language_flages/ru_s.png" /></a>
            </div>
        </div>


    </div>

</div>
