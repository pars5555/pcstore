{* author information for google search *}



<a id="btn_google_plus" style="visibility: hidden;position: fixed" title="Google Plus" href="//plus.google.com/115174553400657734338?rel=author" target="_blank"></a>

<a href="//www.facebook.com/pages/Pcstoream/101732636647846" target="_blank">
    <div class="fb_left_float_div"></div>
</a>
{if ($ns.userLevel === $ns.userGroupsAdmin)}
    {*config for admin only*}   
    <input type="hidden" value="{$ns.root_pass_md5}" id="root_pass_md5"/>
    <a id="admin_config_dialog_href" href="javascript:void(0);" style="position: fixed;right: 0;top:90px">
        <img src="{$SITE_PATH}/img/system_config.png"/>
    </a>
    <a id="pcstore_camera_dialog_href" href="javascript:void(0);" style="position: fixed;right: 0;top:250px">
        <img src="{$SITE_PATH}/img/camera.png"/>
    </a>
    <a id="filter_email_addresses_href" href="javascript:void(0);" style="position: fixed;left: 0;top:250px">
        <img src="{$SITE_PATH}/img/email_address_logo.png"/>
    </a>
    <a id="send_sms_from_pcstore" href="javascript:void(0);" style="position: fixed;left: 0;bottom:150px">
        <img style="width:70px" src="{$SITE_PATH}/img/sms.png"/>
    </a>

    {*price grouop for admin only*}
    <select style="position: fixed;left:0;top:0" id="admin_price_group">
        <option {if $ns.admin_price_group=='admin' || $ns.admin_price_group==''}selected{/if} value="admin">Admin</option>
        <option {if $ns.admin_price_group=='customer'}selected{/if} value="customer">Customer</option>
        <option {if $ns.admin_price_group=='vip'}selected{/if} value="vip">Vip</option>
    </select>
{/if}
<div id="global_modal_loading_div" class="modal_loading"></div>
<input id = "active_load_name" type="hidden"  value="{$ns.active_load_name}"/>
{if $ns.userId} 
    <input id = "user_id" type="hidden"  value="{$ns.userId}"/>
{/if}
<input id = "user_level" type="hidden"  value="{$ns.userLevel}"/>

<div id = "alerts_fixed_footer_div" style="position:fixed; bottom:0; right:0;height:200px;overflow: hidden;z-index: 200;"></div>


<div id="shopping_cart_container"></div>
<div id="global_empty_container"></div>
<div id="global_empty_container1"></div>
<input id = "password_regexp" type="hidden"  value="{$ns.passRegexp}"/>
<div id = "home_page_content_container" class="avo_style_wrapper header_div_style">
    <div id = "content_header_div" class="avo_style_wrapperHeader">
        <div style="margin:3px 0; float:left">
            <div style="font-size: 14px;;float:left;margin-top: 8px">{$ns.langManager->getPhraseSpan(87)} </div>
            <img src="{$SITE_PATH}/img/telephone.png" style="vertical-align: middle;;float:left;margin: 0 10px"/>
            <div style="color:#BB0000;font-size: 16px;float:left">{$ns.salesPhone1}</br>{$ns.salesPhone}</div>
        </div>
        {if $ns.user}
            <div style="float:right;color: #0000AA;">
                <a href = "{$SITE_PATH}/dyn/main/do_logout" class="button1" style="border-radius: 0 0 0 8px;" {if $ns.userLoginType!=='pcstore'}id = 'f_sociallogoutbutton'{/if}>{$ns.langManager->getPhraseSpan(65)}</a>
            </div>
            {if $ns.user->getName()|strlen>0}
                <div style="float:right;margin:5px 0 0 10px; padding: 0 10px 0 10px">
                    <span style="color:#000000;font:Times;font-size: 16px;"> {$ns.langManager->getPhraseSpan(386)} <a id="user_name_profile_open_a" href="javascript:void(0);">{$ns.user->getName()}</a>!</span>
                </div>
            {/if}
            {if $ns.userLevel === $ns.userGroupsUser}

                <div style="float:right;margin:5px 0 0 10px; padding: 0 10px 0 10px;cursor: pointer" title="{$ns.langManager->getPhrase(439)}"
                     class="translatable_attribute_element" attribute_phrase_id="439" attribute_name_to_translate="title">
                    <span style="color:#000000;font:Times;font-size: 16px;">{$ns.langManager->getPhraseSpan(434)}: {$ns.user->getPoints()} Դր.</span> 					 					 

                </div>
            {/if}

            {* facebook likes*}
            <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FPcstoream%2F101732636647846%3Fref%3Dtn_tnmn&amp;send=false&amp;layout=button_count&amp;width=180&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=604055496277705" scrolling="no" frameborder="0"  allowTransparency="true"
                    style="border:none; overflow:hidden; width:135px; height:21px;float: right;margin-top: 3px"></iframe>		

            {if $ns.userLevel === $ns.userGroupsUser && false}
                <a id="facebook_request_friends" style="float: right;margin: 2px 5px 0 0" href="javascript:void(0);">
                    <img src="{$SITE_PATH}/img/facebook_globe.png"/>
                </a>						
            {/if}					
        {else}
            <div class="user_login_div_style" id ="user_login_container">
                {nest ns=login}                
            </div>
            <div style="clear: both"></div>

            {* social login buttons*}
            <div style="float: right;">
                <img id="facebookLoginBtn" style="width: 35px;cursor: pointer" src="{$SITE_PATH}/img/social/fb.png"/>
                <img id="linkedinLoginBtn" style="width: 35px;cursor: pointer" src="{$SITE_PATH}/img/social/in.png"/>
                <span id="googleLoginBtn">    
                    <img style="width: 35px;cursor: pointer"  src="{$SITE_PATH}/img/social/gp.png"/>
                </span>
                {*<span id="yahooLoginBtn"></span>*}
            </div>

            {* facebook likes*}
            <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FPcstoream%2F101732636647846%3Fref%3Dtn_tnmn&amp;send=false&amp;layout=button_count&amp;width=180&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=604055496277705" scrolling="no" frameborder="0"  allowTransparency="true"
                    style="border:none; overflow:hidden; width:135px; height:21px;float: right;margin-top: 3px"></iframe>
            {if $ns.userLevel === $ns.userGroupsUser || $ns.userLevel === $ns.userGroupsGuest && false}
                <a id="facebook_request_friends" style="float: right;margin: 2px 5px 0 0" href="javascript:void(0);">
                    <img src="{$SITE_PATH}/img/facebook_globe.png"/>
                </a>						
            {/if}					

        {/if}
    </div>
    <div style="clear: both"></div>

    <div id = "content_body_div" class="avo_style_contentBox">
        <div id = "tab_header_div" style="float:left;width:100%">
            {if !$ns.static_body_content}		
                <div id="hp_main_tabs" class="avo_style_main_tabs">
                    <ul id="hp_tabs_container" class="avo_style_tab">
                        {foreach from=$ns.tabTitles key=k item=title}									
                            <li>
                                <a href="#hp_{$k}_tab" onclick='return false;' load_name="{$k}" id="tab_link_{$k}" page_url="{$ns.load_url.$k}">
                                    {$title}
                                 {if $k=='pc_configurator'}
                                        <img src="{$SITE_PATH}/img/pc_configurator/pc.png" style="vertical-align: middle"/>
                                {/if}</a>
                            </li>			
                        {/foreach}
                    </ul>
                    {foreach from=$ns.tabTitles key=k item=title}	
                        <div id="hp_{$k}_tab">
                            {if $ns.active_load_name == $k}
                                {nest ns = active_load_to_nest}
                            {/if}
                        </div>
                    {/foreach}                   
                </div>                           

            {/if}


        </div>
        <div style="clear: both"></div>	

        {if $ns.static_body_content}		
            {nest ns = footer_links_content}
        {/if}

        <div style="clear: both"></div>	

    </div>

</div>
{if $ns.show_registration_dialog == 1}
    <input type="hidden" id="nest_user_registration_load" value="1"/>
    {nest ns = user_registration}
{/if}

<div id = "scroll_top_div" class="scroll_top_bottom_right_float_div" title="{$ns.langManager->getPhrase(564)}"
     class="translatable_attribute_element" attribute_phrase_id="564" attribute_name_to_translate="title" >
</div>

<div id = "sound_on_off" class="{if isset($ns.user) && $ns.user->getSoundOn()==1}sound_on_float_div{else}sound_off_float_div{/if}">
</div>