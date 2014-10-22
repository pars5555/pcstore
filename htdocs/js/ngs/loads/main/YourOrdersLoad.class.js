ngs.YourOrdersLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "your_orders";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "hp_" + this.getName() + "_tab";
    },
    getName: function() {
        return "your_orders";
    },
    beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {
        ngs.UrlChangeEventObserver.setFakeURL("/orders");
        $('global_modal_loading_div').style.display = 'none';

        var collapse_expande_buttons = $$("#yo_table_container .collapse_expande_buttons");
        this.addCollapseExpandeButtonsClickHandler(collapse_expande_buttons);

        var order_status_seletes = $$("#yo_table_container .f_orders_status");
        this.addOrderStatusChangedHandlers(order_status_seletes);

        if ($('f_show_only_select')) {
            $('f_show_only_select').onchange = this.onShowOnlyOrdersChanged.bind(this);
        }

        var thisObject = this;
        var handler = StripeCheckout.configure({
            key: CMS_VARS['stripe_publishable_key'],
            image: '/img/logo_150x91.png',
            token: function(token) {
                ngs.action('confirm_stripe_payment', {'stripeToken': token.id, 'order_id': thisObject.order_id_be_be_paied_by_stripe});
            }
        });

        jQuery('.credit_card_paynow_btn').click(function(e) {
            var orderId = jQuery(this).attr('order_id');
            thisObject.order_id_be_be_paied_by_stripe = orderId;
            var totalAmount = jQuery(this).attr('total_amount');
            var user_email = jQuery(this).attr('user_email');

            handler.open({
                name: 'Pcstore',
                description: 'Order Number ' + orderId,
                amount: totalAmount * 100,
                email: user_email
            });
            e.preventDefault();
        });
        this.initPrintInvoiceBtn();
    },
    initPrintInvoiceBtn: function() {
        var thisInstance = this;
        jQuery('#print_invoice_btn').click(function() {
            var orderId = jQuery(this).attr('order_id');
            var iframe = "<div><iframe style='width:100%;height:100%;'  scrolling='no' id='print_invoice_iframe' src='//"+SITE_URL+"/print_invoice?order_id=" + orderId + "'></iframe></div>";
            jQuery(iframe).dialog({
                resizable: true,
                title: ngs.LanguageManager.getPhraseSpan(630),
                modal: true,
                width:800,
                height:600,
                buttons: {
                    "Action": {
                        text: ngs.LanguageManager.getPhrase(629),
                        'class': "dialog_default_button_class translatable_search_content",
                        phrase_id: 629,
                        click: function() {
                            thisInstance.printFrame("print_invoice_iframe");
                        }
                    },
                    "Cancel": {
                        text: ngs.LanguageManager.getPhrase(49),
                        'class': "translatable_search_content",
                        phrase_id: 49,
                        click: function() {
                            jQuery(this).remove();
                        }
                    }
                },
                close: function() {
                    jQuery(this).remove();
                },
                open: function(event, ui) {
                    jQuery(this).parent().attr('phrase_id', 630);
                    jQuery(this).parent().addClass('translatable_search_content');
                }
            });
        });
    },
    onShowOnlyOrdersChanged: function() {
        var value = $('f_show_only_select').value;
        $('global_modal_loading_div').style.display = 'block';
        ngs.load('your_orders', {
            'show_only': value
        });
    },
    addOrderStatusChangedHandlers: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var order_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
            elements_array[i].onchange = this.onOrderStatusChanged.bind(this, order_id);
        }
    },
    onOrderStatusChanged: function(order_id) {
        var changeStatus = true;
        var status = $('f_order_status_select^' + order_id).value;
        var cencel_reason = '';
        if (status == 2) {
            var cencel_reason = prompt("Please enter the cancelateion reason!", "");
            if (cencel_reason == null) {
                changeStatus = false;
            }
        }
        $('global_modal_loading_div').style.display = 'block';
        ngs.action('change_order_status_action', {
            'order_id': order_id,
            'status': status,
            'cencel_reason': cencel_reason,
            'only_refresh': changeStatus ? 0 : 1
        });
    },
    addCollapseExpandeButtonsClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            var id = elements_array[i].id;
            var ind = elements_array[i].id.substr(id.indexOf("^") + 1);
            elements_array[i].onclick = this.onCollapseExpandeButtonClicked.bind(this, ind, id[0] == 'o' ? 'order' : 'bundle', elements_array[i]);
        }
    },
    onCollapseExpandeButtonClicked: function(id, orderOrBundle, element) {
        if (element.hasClassName('order_expand_button')) {
            new Effect.BlindDown(orderOrBundle + '_container_' + id, {
                duration: 0.3
            });
            element.removeClassName('order_expand_button');
            element.addClassName('order_collapse_button');
        } else {
            new Effect.BlindUp(orderOrBundle + '_container_' + id, {
                duration: 0.3
            });
            element.removeClassName('order_collapse_button');
            element.addClassName('order_expand_button');
        }

    },
    onLoadDestroy: function()
    {
        jQuery("#" + this.getContainer()).html("");
    }
});
