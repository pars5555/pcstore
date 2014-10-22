ngs.HomePageLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "home_page";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "content";
    },
    getName: function() {
        return "home_page";
    },
    afterLoad: function() {
        jQuery("#hp_tabs_container a").click(function(e) {
            if (e.button === 0 && e.ctrlKey) {
                jQuery('#hp_main_tabs').tabs("disable");
                ngs.preventTabClick = true;
                window.open('/' + jQuery(this).attr('page_url'), '_blank');
                e.stopPropagation();
                e.preventDefault();
                return false;
            } else
            {
                jQuery('#hp_main_tabs').tabs("enable");
                ngs.preventTabClick = false;
            }
        });

        $('global_modal_loading_div').style.display = 'none';

        jQuery('#hp_main_tabs').tabs();

        ngs.activeLoadName = jQuery('#active_load_name').val();
        jQuery("#hp_main_tabs").tabs('select', '#hp_' + ngs.activeLoadName + '_tab');
        this.initTabs();

        window.onunload = this.onWindowClose.bind(this);


        if ($('shopping_cart_link')) {
            $('shopping_cart_link').onclick = this.onShoppingCartLinkClicked.bind(this);
        }

        if ($('companies_list')) {
            $('companies_list').onclick = this.onCompanyListTabClicked.bind(this);
        }
        if ($('item_search')) {
            $('item_search').onclick = this.onItemSearchTabClicked.bind(this);
        }
        if ($('pc_configurator')) {
            $('pc_configurator').onclick = this.onPcConfiguratorTabClicked.bind(this);
        }
        if (jQuery('#user_name_profile_open_a').length > 0)
        {
            jQuery('#user_name_profile_open_a').click(function() {
                if (jQuery('#hp_company_profile_tab').length > 0) {
                    jQuery("#hp_main_tabs").tabs('select', '#hp_company_profile_tab');
                    ngs.load('company_profile', {});
                }
                if (jQuery('#hp_service_company_profile_tab').length > 0) {
                    jQuery("#hp_main_tabs").tabs('select', '#hp_service_company_profile_tab');
                    ngs.load('service_company_profile', {});
                }
                if (jQuery('#hp_your_profile_tab').length > 0) {
                    jQuery("#hp_main_tabs").tabs('select', '#hp_your_profile_tab');
                    ngs.load('your_profile', {});
                }
                return false;
            });
        }


        if ($('footer_links_content')) {
            ngs.nestLoad("footer_links_content", {});
        }

        if (typeof ngs.activeLoadName !== 'undefined' && ngs.activeLoadName !== "")
        {
            ngs.nestLoad(ngs.activeLoadName, {});
        }

        if (!$('user_id')) {
            ngs.nestLoad("login", {});
            if ($('nest_user_registration_load')) {
                ngs.nestLoad('user_registration', {});
            }
        }

        if (jQuery('#pcstore_camera_dialog_href').length > 0)
        {
            jQuery('#pcstore_camera_dialog_href').click(function() {
                jQuery('<div id="f_pcstore_camera_load_container">').dialog(
                        {
                            resizable: true,
                            width: 400,
                            height: 300,
                            modal: true,
                            close: function() {
                                jQuery(this).remove();
                            }
                        });
                ngs.load('pcstore_camera_1', {});



            });
        }
        if (jQuery('#admin_config_dialog_href').length > 0)
        {
            jQuery('#admin_config_dialog_href').click(function() {
                jQuery("<form id='f_admin_config_root_pass_form' autocomplete='off'>root password: <input type='password' id='f_admin_config_root_pass'/></form>").dialog({
                    resizable: false,
                    title: ngs.LanguageManager.getPhrase(483),
                    modal: true,
                    buttons: [{
                            text: ngs.LanguageManager.getPhrase(485),
                            'class': "dialog_default_button_class translatable_search_content",
                            phrase_id: 485,
                            id: "f_admin_config_root_pass_form_submit_button",
                            click: function() {
                                var root_pass = jQuery('#f_admin_config_root_pass').val();
                                var rootPassMd5 = jQuery().crypt({
                                    method: "md5",
                                    source: root_pass
                                });
                                if (rootPassMd5 === jQuery('#root_pass_md5').val()) {
                                    jQuery('<div id="system_config_container_created_in_js" style="overflow:hidden"></div>').dialog({
                                        width: 1200,
                                        height: 600,
                                        resizable: true,
                                        modal: true,
                                        title: "System Config",
                                        buttons: {
                                            "Close": function() {
                                                jQuery(this).remove();
                                            }
                                        },
                                        close: function() {
                                            jQuery(this).remove();
                                        }
                                    }

                                    );
                                    ngs.load('system_config', {});
                                }
                                jQuery(this).remove();
                            }
                        },
                        {
                            text: ngs.LanguageManager.getPhrase(49),
                            'class': "translatable_search_content",
                            phrase_id: 49,
                            click: function() {
                                jQuery(this).remove();
                            }
                        }
                    ],
                    close: function() {
                        jQuery(this).remove();
                    },
                    open: function(event, ui) {
                        jQuery(this).parent().attr('phrase_id', 483);
                        jQuery(this).parent().addClass('translatable_search_content');
                    }
                });
                jQuery('#f_admin_config_root_pass_form').submit(function() {
                    jQuery('#f_admin_config_root_pass_form_submit_button').trigger('click');
                    return false;
                });

            });
        }
        if (jQuery('#scroll_top_div').length > 0)
        {
            jQuery('#scroll_top_div').click(function() {
                jQuery(window.self).scrollTop(0);
            });
        }
        jQuery('#sound_on_off').click(function() {
            if (jQuery(this).hasClass('sound_on_float_div')) {

                jQuery(this).removeClass('sound_on_float_div').addClass('sound_off_float_div');
                ngs.action('sound_on_action', {'on': 0});
                ngs.sound_on = 0;
            } else {
                jQuery(this).removeClass('sound_off_float_div').addClass('sound_on_float_div');
                ngs.action('sound_on_action', {'on': 1});
                ngs.sound_on = 1;
            }

        });

        //admin price group
        if (jQuery('#admin_price_group').length > 0) {
            jQuery('#admin_price_group').change(function() {
                ngs.action('set_price_group_action', {'price_group': jQuery(this).val()});
            });
        }
        if (jQuery('#filter_email_addresses_href').length > 0) {
            jQuery('#filter_email_addresses_href').click(function() {
                var emailsCounterHTML = '<div id="afe_emails_count" style="text-align:center;;width:100%;height:50px;"></div>';
                var textareaHTML = '<div style="position:absolute; top:60px;left:10px;right:10px;bottom:10px"><textarea style="width:100%;height:100%;border:1px solid gray" id="admin_filter_email_textarea"></textarea></div.';
                var contentHTML = '<div>' + emailsCounterHTML + textareaHTML + '</div>';
                ngs.DialogsManager.actionOrCancelDialog('Filter', 'admin_filter_email_button', false, 'admin_filter_email_cancel_button', 'Filter Emails', contentHTML, function() {
                    var emails = jQuery('#admin_filter_email_textarea').val();
                    ngs.action('admin_group_actions', {'action': 'filter_emails', 'emails': emails});
                }, true, 500, 400);

            });
        }

        if (jQuery('#send_sms_from_pcstore').length > 0) {
            jQuery('#send_sms_from_pcstore').click(function() {

                ngs.load('admin_send_sms', {});

            });
        }

        this.initNewsletterForm();


    },
    initNewsletterForm: function() {
        jQuery('#newsletter_subscription_form').submit(function() {
            var email = jQuery('#fotter_newsletter_subscription_input').val();
            ngs.action('add_newsletter_subscriber_action', {'email': email});
            return false;
        });

    },
    initTabs: function() {
        var thisInstance = this;
        jQuery('#hp_main_tabs').tabs("enable");
        jQuery("#hp_tabs_container a").click(function() {
            if (!ngs.preventTabClick) {
                var loadName = jQuery(this).attr('load_name');
                ngs.load(loadName, {});
                thisInstance.destroyPreviousLoad(loadName);
            } else
            {
                jQuery('#hp_main_tabs').tabs("enable");
            }
        });


    },
    onWindowClose: function() {
    },
    destroyPreviousLoad: function(tabName) {
        if (typeof ngs.activeLoadName !== 'undefined') {
            var load = ngs.loadFactory.getLoad(ngs.activeLoadName);
            if (typeof load.onLoadDestroy === 'function') {
                load.onLoadDestroy();
            }
        }
        ngs.activeLoadName = tabName;
    },
    onShoppingCartLinkClicked: function() {
        ngs.load("shopping_cart", {});
    }
});
