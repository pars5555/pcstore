ngs.AdminGroupActionsAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "admin", ajaxLoader);
    },
    getUrl: function() {
        return "do_admin_group_actions";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status === "ok") {
            switch (this.params.action)
            {
                case "delete_user":
                    ngs.load('admin_user_management', {});
                    break;
                case "delete_all_unnecessary_items_pictures":
                    if (data.removed_items_ids.length > 0)
                    {
                        var removedItemsIds = data.removed_items_ids.split(',');
                        ngs.DialogsManager.closeDialog('Attention', "<div>" + removedItemsIds.length + " items' pictures removed!</div>", 'ok');
                    } else {
                        ngs.DialogsManager.closeDialog('Attention', "<div>There is no picture to remove!</div>", 'ok');
                    }
                    break;
                case "get_item_spec":
                    jQuery('#mi_item_short_spec_input').val(data.short_spec);
                    tinymce.activeEditor.setContent(data.full_spec, {format : 'raw'});
                    break;
                case "filter_emails":
                    jQuery('#afe_emails_count').html('total emails count: ' + data.count);
                    jQuery('#admin_filter_email_textarea').val(data.emails);
                    jQuery('#admin_filter_email_button').attr('disabled', false);
                    jQuery('#admin_filter_email_cancel_button').attr('disabled', false);
                    break;
                case "delete_price_values_column":
                    ngs.load("import_price", {"sheet_index": this.params.sheet_index, 'company_id': this.params.company_id});
                    break;
                case "is_price_values_ready":
                    if (data.ready == 1) {
                        jQuery('#import_price_button').prop('disabled', false);
                        jQuery('#import_price_button').removeClass('gray');
                    } else {
                        var company_id = this.params.company_id;
                        setTimeout(function() {
                            if (jQuery('#import_price_button').length > 0) {
                                ngs.action('admin_group_actions', {'action': "is_price_values_ready", "company_id": company_id});
                            }
                        }, 1000);

                    }
                    break;
                case "delete_customer_amessage_after_login":
                    ngs.load('customer_alerts_after_login', {});
                    break;
                case "preview_customer_message":
                    var message = {'id': data.id, 'title_formula': data.title_formula, 'message_formula': data.message_formula, 'type': data.type, 'showed_count': data.showed_count, 'shows_count': data.shows_count};
                    ngs.PingPongAction.prototype.openAfterLoginCustmerMessagesDialogSinlgeMessage(message, true);
                    break;
                case 'delete_old_hidden_items':
                    ngs.DialogsManager.closeDialog('Attention', "<div>" + data.count + " items' removed!</div>", 'ok');
                    break;
                case 'update_companies_price_text':
                    ngs.DialogsManager.closeDialog(534, "<div>" + 'Updated!' + "</div>");
                    break;
                case 'deploy_latest_pcstore_changes':
                    ngs.DialogsManager.closeDialog(534, "<div>" + data.message + "</div>");
                    break;
            }
        } else
        {
            ngs.DialogsManager.closeDialog(583, "<div>" + data.message + "</div>");
        }
    }
});
