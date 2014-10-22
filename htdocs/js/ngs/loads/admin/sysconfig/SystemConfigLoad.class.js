ngs.SystemConfigLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin_sysconfig", ajaxLoader);
    },
    getUrl: function() {
        return "system_config";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "system_config_container_created_in_js";
    },
    getName: function() {
        return "system_config";
    },
    afterLoad: function() {
        jQuery('#sc_system_config_tabs').tabs();
        ngs.nestLoad("admin_actions_view", {});
        ngs.nestLoad("table_view", {});
        ngs.nestLoad("gallery_view", {});
        ngs.nestLoad("search_statistics_view", {});
        ngs.nestLoad("admin_user_management", {});
        ngs.nestLoad("items_management", {});
        ngs.nestLoad("companies_management", {});
        ngs.nestLoad("customer_alerts_after_login", {});
        jQuery("#sc_send_newsletter_email").click(function() {
            tinyMCE.activeEditor.save();
            var bodyHTML = jQuery('#sc_newsletter_html').val();
            
            if (jQuery('#sc_test_checkbox').prop('checked'))
            {                
                var contentHtml = "<div><label for='sc_test_email_account'>Email </label><input type='email' id='sc_test_email_account'/></div>";
                ngs.DialogsManager.closeDialog('email address', contentHtml, 'send', function() {
                    jQuery('#sc_send_newsletter_email').css('visibility', 'hidden');
                    jQuery('#f_admin_config_newsletter_view_container').block({message: 'processing...'});
                    var testEmailAccount = jQuery('#sc_test_email_account').val();
                    ngs.action("send_newsletter_action", {
                    'include_all_active_users': jQuery('#send_to_all_registered_users').is(':checked'),
                    "email_body_html": bodyHTML,
                    'test': 1,
                    'test_email': testEmailAccount
                });
                }, false);
            } else {                
                jQuery('#sc_send_newsletter_email').css('visibility', 'hidden');
                jQuery('#f_admin_config_newsletter_view_container').block({message: 'processing...'});
                ngs.action("send_newsletter_action", {
                    'include_all_active_users': jQuery('#send_to_all_registered_users').is(':checked'),
                    "email_body_html": bodyHTML,
                    'test': 0,
                    'test_email': ''
                });
            }
        });


       
        jQuery("#sc_manage_newsletters").click(function() {
            jQuery('<div id="sys_config_manage_newsletters_container"></div>').dialog({
                resizable: true,
                width: 530,
                height: 350,
                modal: true,
                buttons: {
                    "delete": {
                        text: 'delete',
                        id: "mn_delete_newsletter_button",
                        click: function() {

                        }
                    },
                    "cancel": {
                        text: 'cancel',
                        click: function() {
                            jQuery(this).dialog("close");
                        }
                    }

                },
                close: function() {
                    jQuery(this).remove();
                }
            });

            ngs.load("manage_newsletters", {});
        });
        jQuery("#sc_open_newsletter").click(function() {
            jQuery('<div id="sys_config_open_newsletter_container"></div>').dialog({
                resizable: true,
                width: 530,
                height: 350,
                modal: true,
                buttons: {
                    "open": {
                        text: 'open',
                        'class': "dialog_default_button_class",
                        id: 'on_open_newsletter_button',
                        click: function() {
                        }
                    },
                    "cancel": {
                        text: 'cancel',
                        click: function() {
                            jQuery(this).dialog("close");
                        }
                    }

                },
                close: function() {
                    jQuery(this).remove();
                }
            });
            ngs.load("open_newsletter", {});
        });

        jQuery("#sc_save_newsletter").click(function() {
            jQuery('<div id="sys_config_save_newsletter_container"></div>').dialog({
                resizable: true,
                width: 530,
                height: 350,
                modal: true,
                buttons: {
                    "save": {
                        text: 'save',
                        'class': "dialog_default_button_class",
                        id: "sn_save_button",
                        click: function() {

                        }
                    },
                    "cancel": {
                        text: 'cancel',
                        click: function() {
                            jQuery(this).dialog("close");
                        }
                    }

                },
                close: function() {
                    jQuery(this).remove();
                }
            });
            ngs.load("save_newsletter", {});
        });



        this.initTinyMCE("textarea#sc_newsletter_html");
    }   
});