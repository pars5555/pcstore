ngs.MainLoad = Class.create(ngs.AbstractLoad, {
    customer_ping_pong_timeout_milis: null,
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
        var cppts = $('customer_ping_pong_timeout_seconds');
        if (cppts && parseInt(cppts.value) >= 1) {
            this.customer_ping_pong_timeout_milis = parseInt(cppts.value) * 1000;
        }
    },
    getUrl: function() {
        return "main_site";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "content";
    },
    getName: function() {
        return "main";
    },
    afterLoad: function() {

        var thisInstance = this;
        jQuery('#contact_us_link').click(function() {
            ngs.DialogsManager.actionOrCancelDialog(48, 'f_contact_us_send_button', false, null, 46, '<div id="f_contactus_container_dialog"></div>', function() {
                thisInstance.onCuSendButtonClicked();
            });
            ngs.load("contact_us", {});
        });

        this.initLangFlagsActions();
        if (jQuery("#f_site_in_under_constraction").length > 0)
        {
            return;
        }

        ngs.UrlChangeEventObserver.initialize(this.onLocationChange);

        if ($('user_activation_element_id')) {
            var ret = $('user_activation_element_id').value;
            if (ret === 'just activated') {
                ngs.DialogsManager.closeDialog(514, "<div>" + ngs.LanguageManager.getPhraseSpan(523) + "</div>", 280, function() {
                    window.location.href = "/";
                });
            } else if (ret === 'already activated') {
                ngs.DialogsManager.closeDialog(483, "<div>" + ngs.LanguageManager.getPhraseSpan(524) + "</div>", 280, function() {
                    window.location.href = "/";
                });
            }

        }

        if (!this.customer_ping_pong_timeout_milis || this.customer_ping_pong_timeout_milis < 1000) {
            ngs.DialogsManager.closeDialog(583, "<div>" + 'System Error: Customer time out milis can not be less that 1 second!' + "</div>");

        } else {
            ngs.nestLoad("home_page", {});
            ngs.action('ping_pong_action', {"win_uid": jQuery("#win_uid").val()});
            setTimeout(this.ping.bind(this), this.customer_ping_pong_timeout_milis);
        }

        ngs.nestLoad('deals', {});

        if ($('user_email_input_field')) {
            $('user_email_input_field').focus();
        }

        if (typeof CMS_VARS.snowing !== 'undefined' && CMS_VARS.snowing != 0)
        {
            startSnow();
        }

        this.startHeaderBannerSliding();

        this.initSocialLogout();

    },
    initSocialLogout: function() {
        jQuery('#f_sociallogoutbutton').click(function() {
            if (typeof gapi.auth !== 'undefined') {
                gapi.auth.signOut();
            }
            if (typeof IN !== 'undefined' && typeof IN.User !== 'undefined') {
                IN.User.logout();
            }
            if (typeof FB !== 'undefined') {
                FB.getLoginStatus(function(response) {
                    if (response && response.status === 'connected') {
                        FB.logout(function(response) {
                        });
                    }
                });
            }
            return true;
        });
    },
    initLangFlagsActions: function() {
        jQuery('#set_language_am_link').click(function() {
            ngs.action('set_language_action', {'l': 'am'});
            ngs.LanguageManager.changeSiteLanguage('am');
            return false;
        });
        jQuery('#set_language_en_link').click(function() {
            ngs.action('set_language_action', {'l': 'en'});
            ngs.LanguageManager.changeSiteLanguage('en');
            return false;
        });
        jQuery('#set_language_ru_link').click(function() {
            ngs.action('set_language_action', {'l': 'ru'});
            ngs.LanguageManager.changeSiteLanguage('ru');
            return false;
        });
    },
    onCuSendButtonClicked: function() {
        var email_field = jQuery('#cu_user_email_field').val();
        var msg_field = jQuery('#cu_user_message_field').val();
        ngs.action('contact_us_action', {
            'email': email_field,
            'msg': msg_field
        });
    },
    startHeaderBannerSliding: function() {
        //setTimeout(this.switchHeaderBanner.bind(this), 5000);
    },
    switchHeaderBanner: function() {
        if (jQuery('#hader_banner_container').is(':visible'))
        {
            jQuery('#hader_banner_container').fadeOut(500, function() {
                jQuery('#daily_deal_container').fadeIn(500);
            });
        } else
        {
            jQuery('#daily_deal_container').fadeOut(500, function() {
                jQuery('#hader_banner_container').fadeIn(500);
            });
        }
        setTimeout(this.switchHeaderBanner.bind(this), 5000);
    },
    onLocationChange: function(oldURL, newURL) {
        var nt = "";
        var ot = "";
        if (newURL) {
            nt = newURL.strip();
        }
        if (oldURL) {
            ot = oldURL.strip();
        }

        if (ngs.UrlChangeEventObserver.urlChangedByProgram === false) {

            //todo implement the same functionallity without reloading
            if (ot === "" && nt.length > 0) {
                window.location.reload();
                return;
            }
            if (ot.length > 0 && nt.length > 0) {
                window.location.reload();
                return;
            }
        }

    },
    ping: function() {
        if (!this.customer_ping_pong_timeout_milis || this.customer_ping_pong_timeout_milis < 5000) {
            ngs.DialogsManager.closeDialog(583, "<div>" + 'System Error: Customer time out milis can not be less that 5 second!' + "</div>");
        } else {
            ngs.action('ping_pong_action', {"win_uid": jQuery("#win_uid").val()});
            setTimeout(this.ping.bind(this), this.customer_ping_pong_timeout_milis);
        }
    },
    getCookie: function(c_name)
    {
        var i, x, y, ARRcookies = document.cookie.split(";");
        for (i = 0; i < ARRcookies.length; i++)
        {
            x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
            y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
            x = x.replace(/^\s+|\s+$/g, "");
            if (x == c_name)
            {
                return unescape(y);
            }
        }
    }
});
