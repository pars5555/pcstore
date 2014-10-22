{if isset($ns.description_tag_value)}
    {assign var="pagedesc" value=$ns.description_tag_value}
{else}
    {assign var="pagedesc" value="Armenian PC stores group. All Armenian PC stores' items in one palce!!!"}
{/if}
{if isset($ns.keywords_tag_value)}
    {assign var="pagekeywords" value=$ns.keywords_tag_value}
{else}
    {assign var="pagekeywords" value=$ns.langManager->getCmsVar('google_pcstore_homepage_keywords')}
{/if}
{if isset($ns.title_tag_value)}
    {assign var="pagetitle" value=$ns.title_tag_value}
{else}
    {assign var="pagetitle" value=$ns.langManager->getCmsVar('google_pcstore_homepage_title')}
{/if}

<meta NAME="description" content="{$pagedesc}">
<meta NAME="keywords" content="{$pagekeywords}">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="Naghashyan Framework {$VERSION}" />

<meta name="alexaVerifyID" content="9ac30mbahn5Zs0fcldjgrl5GA_M"/>
<title>{$pagetitle}</title>

<!-- NGS Theme Styles -->
<link rel="stylesheet" type="text/css" href="{$SITE_PATH}/css/main/style.css?{$VERSION}" />
<link rel="shortcut icon" href="{$SITE_PATH}/img/pcstorefavicon.ico" />

<script type="text/javascript">
    {literal}
        var ngs = {};
        ngs.reloadPage = function()
        {
            window.location.reload(true);
        };
    {/literal}
    {if isset($ns.user) && $ns.user->getSoundOn()==1}
        ngs.sound_on = 1;
    {else}
        ngs.sound_on = 0;
    {/if}
        var site_PROTOCOL = "{$SITE_PROTOCOL}";
        var SITE_PATH = "{$SITE_PATH}";
        var SITE_URL = "{$SITE_URL}";
        var ALL_PHRASES = {$ns.all_phrases_dtos_mapped_by_id};
        var CMS_VARS = {$ns.cms_vars};
</script>
<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="//checkout.stripe.com/checkout.js"></script>                           
<script type="text/javascript"  src="{$SITE_PATH}/js/lib/prototype.js" ></script>
<script type="text/javascript"  src="{$SITE_PATH}/js/lib/jquery/jquery-2.1.0.js" ></script>
{if ($ns.userLevel === $ns.userGroupsAdmin)}
    <script type="text/javascript"  src="{$SITE_PATH}/js/lib/jquery/jquery-migrate-1.0.0.js" ></script>
    <script type="text/javascript" src="{$SITE_PATH}/js/lib/elfinder-2.0-rc1/js/elfinder.min.js"></script>
{/if}
<script type="text/javascript" src="{$SITE_PATH}/js/lib/tinymce4/tinymce/js/tinymce/tinymce.js"></script>

<script type="text/javascript">
    jQuery.noConflict();
</script>

<script type="text/javascript" src="{$SITE_PATH}/js/out/ngs.js?4_2_6"></script>
<script type="text/javascript" src="{$SITE_PATH}/js/out/ngs_loads.js?4_2_6"></script>
<script type="text/javascript" src="{$SITE_PATH}/js/out/ngs_util.js?4_2_6"></script>
<script type="text/javascript" src="{$SITE_PATH}/js/out/ngs_actions.js?4_2_6"></script>
<script type="text/javascript" src="{$SITE_PATH}/js/out/ngs_manager.js?4_2_6"></script>


{* facebook login setup *}
<script type="text/javascript"  src="//connect.facebook.net/en_US/sdk.js"></script>
{* linkedin login setup *}
<script type="text/javascript" src="//platform.linkedin.com/in.js">
    api_key: 75ys1q9fcupeqq
    authorize: true
</script> 
{* yahoo login setup *}
{*<script src="//yui.yahooapis.com/3.17.2/build/yui/yui-min.js"></script>*}
{* google pluse login setup *}
<script type="text/javascript" src="https://apis.google.com/js/client:plusone.js?onload=render"></script>
<meta name="google-signin-clientid" content="1035369249-j8j8uc4oacruo2iefonhdj1q0csjb9sj.apps.googleusercontent.com"/>                                             
<meta name="google-signin-scope" content="https://www.googleapis.com/auth/plus.login  https://www.google.com/m8/feeds" />
<meta name="google-signin-requestvisibleactions" content="http://schemas.google.com/AddActivity" />
<meta name="google-signin-cookiepolicy" content="single_host_origin" />
<meta name="google-signin-callback" content="googleLoginCallback" />

<script src="https://apis.google.com/js/client.js"></script>


{if $ns.langManager->getCmsVar('dev_mode') != 'on'}

    {literal}
        <script>
    (function(i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function() {
            (i[r].q = i[r].q || []).push(arguments);
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m);
    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
    ga('create', 'UA-50076679-1', 'pcstore.am');
    ga('send', 'pageview');

        </script>
    {/literal}
{/if}
