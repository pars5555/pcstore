ngs.DealsLoad = Class.create(ngs.AbstractLoad, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "deals";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "daily_deal_container";
    },
    getName: function() {
        return "deals";
    },
    afterLoad: function() {
        $('global_modal_loading_div').style.display = 'none';

        jQuery('#td_item_display_name_div').ellipsis({row: 2});

        jQuery('.f_today_deal_open_item_large_view_link').click(function() {
            if (ngs.activeLoadName === 'item_search') {
                ngs.load('item_large_view', {'item_id': jQuery('#today_deal_container').attr('item_id')});
                return false;
            }
        });


        if (jQuery('#today_deal_seconds_to_end').length > 0)
        {
            var dealSecondsToEnd = parseInt(jQuery('#today_deal_seconds_to_end').val());


            var now = (new Date()).getTime();
            var ts = now + dealSecondsToEnd * 1000;
            jQuery('#countdown').countdown({
                timestamp: ts,
                showDays: false,
                callback: function(days, hours, minutes, seconds, timerHandler) {
                    if (days === 0 && hours === 0 && minutes === 0 && seconds === 0)
                    {
                        if (typeof timerHandler !== 'undefined')
                        {
                            //just to make sure taht timout cleared
                            window.clearTimeout(timerHandler);
                        }
                        ngs.load('deals', {});
                    }
                }
            });
        }
        if (jQuery('#today_deal_add_to_cart_button').length > 0) {
            jQuery('#today_deal_add_to_cart_button').click(function() {
                ngs.action("add_to_cart_action", {
                    "item_id": jQuery(this).attr("item_id")
                });
            });
        }
    }

});