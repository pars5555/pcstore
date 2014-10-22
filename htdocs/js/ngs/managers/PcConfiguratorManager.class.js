ngs.PcConfiguratorManager = {
    componentsIndex: null,
    selectedComponentsArray: null,
    lastClickedComponentIndex: 0,
    init: function(ajaxLoader) {
        this.ajaxLoader = ajaxLoader;
        this.selectedComponentsArray = new Array();
        this.componentsIndex = {
            CASE: 1,
            CPU: 2,
            MB: 3,
            COOLER: 4,
            RAM: 5,
            HDD: 6,
            SSD: 7,
            OPT: 8,
            MONITOR: 9,
            VIDEO: 10,
            POWER: 11,
            KEY: 12,
            MOUSE: 13,
            SPEAKER: 14
        };
    },
    caseSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.CASE, item_id);
    },
    mbSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.MB, item_id);
    },
    ramSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.RAM, item_id);
    },
    cpuSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.CPU, item_id);
    },
    hddSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.HDD, item_id);
    },
    coolerSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.COOLER, item_id);
    },
    optSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.OPT, item_id);
    },
    monitorSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.MONITOR, item_id);
    },
    graphicsSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.VIDEO, item_id);
    },
    powerSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.POWER, item_id);
    },
    keySelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.KEY, item_id);
    },
    mouseSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.MOUSE, item_id);
    },
    speakerSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.SPEAKER, item_id);
    },
    ssdSelected: function(item_id) {
        this.onComponentOrTabChanged(this.componentsIndex.SSD, item_id);
    },
    getBackendSelectedComponentsIdsArray: function() {
        var selected_components_ids_array = new Array();
        for (var i = 1; i <= 14; i++) {
            var el = $('selected_component_' + i);
            selected_components_ids_array[i] = el.value;
        }
        return selected_components_ids_array;
    },
    onComponentAfterLoad: function(componentIndex, loadParams, componentKey) {
        $('global_modal_loading_div').style.display = 'none';
        if (loadParams.load_more) {
            var ih = $('pcc_components_container').innerHTML;
            $('pcc_components_container').remove();
            //removed temporary div
            var realContainer = $('last_pcc_components_container');
            realContainer.id = 'pcc_components_container';
            $('component_selection_container').innerHTML += ih;
        } else {
            if (typeof loadParams.take_in_count_search_text !== 'undefined' && loadParams.take_in_count_search_text == 1) {
                $('pcc_components_container').id = "component_selection_container";
                var realContainer = $('last_pcc_components_container');
                realContainer.id = 'pcc_components_container';
            }
        }
        var load_more_button = $('load_more_button');
        if (load_more_button) {
            load_more_button.onclick = this.onLoadMoreComponents.bind(this, componentKey);
        }

        var selected_components_ids_array = this.getBackendSelectedComponentsIdsArray();
        for (var i = 1; i <= 14; i++) {
            this.selectedComponentsArray[i] = selected_components_ids_array[i];
        }
        var selected_item_id = this.selectedComponentsArray[componentIndex];
        if (selected_item_id instanceof Array) {
            selected_item_id = selected_item_id.join(',');
        }
        ngs.load('pcc_item_description', {
            "item_id": selected_item_id
        });
        var params = this.getSelectedComponentsParam(null, null);
        var p = params;
        if ($('configurator_mode_edit_cart_row_id')) {
            var configurator_mode_edit_cart_row_id = parseInt($('configurator_mode_edit_cart_row_id').value);
            if (configurator_mode_edit_cart_row_id > 0) {
                p = mergeObjects(params, {
                    "configurator_mode_edit_cart_row_id": configurator_mode_edit_cart_row_id
                });
            }
        }
        $('pcc_components_container_overlay').style.display = 'none';
        if (jQuery('#pcc_footer_order_button').length > 0) {
            jQuery('#pcc_footer_order_button').css({'visibility': 'visible'});
        }
        if (jQuery('#pcc_print_button').length > 0) {
            jQuery('#pcc_print_button').css({'visibility': 'visible'});
        }
        ngs.UrlChangeEventObserver.setFakeURL('?' + jQuery.param(params));
        ngs.load('pcc_total_calculations', p);

        var thisInstance = this;
        jQuery('#pcc_search_component_text').bind('change cut paste keyup', function(e) {

            if (thisInstance.search_component_timer) {
                window.clearTimeout(thisInstance.search_component_timer);
            }
            var component_index = jQuery('#pcc_select_component_inner_container').attr('component_index');
            thisInstance.search_component_timer = window.setTimeout(function() {
                ngs.PcConfiguratorManager.onSearchComponentChanged(component_index, jQuery('#pcc_search_component_text').val());
            }, 1000);
        });

        jQuery('#pccsc_go_to_next_component').click(function() {
            var nextComponentIndex = parseInt(jQuery(this).attr('component_index')) + 1;
            jQuery('#component_icon_a_link_' + nextComponentIndex).trigger('click');
        });
        jQuery('#pccsc_go_to_prev_component').click(function() {
            var prevComponentIndex = parseInt(jQuery(this).attr('component_index')) - 1;
            jQuery('#component_icon_a_link_' + prevComponentIndex).trigger('click');
        });
    },
    refreshComponentPage: function() {
        if (this.lastClickedComponentIndex > 0) {
            this.onComponentOrTabChanged(this.lastClickedComponentIndex);
        }
        var params = this.getSelectedComponentsParam(null, null);
        ngs.action('pcc_after_component_changed_action', params);
        ngs.load('pcc_total_calculations', params);
        ngs.load('pcc_select_case', params);
    },
    reset: function()
    {
        this.lastClickedComponentIndex = 0;
        var params = this.getSelectedComponentsParam(null, null);
        ngs.action('pcc_after_component_changed_action', params);
    },
    /**
     * componentIndex  is based 0 (0=>case)
     */
    onComponentOrTabChanged: function(componentIndex, item_id) {
        this.lastClickedComponentIndex = componentIndex;
        $('pcc_components_container_overlay').style.display = 'block';

        var params = this.getSelectedComponentsParam(null, null);
        if (typeof item_id !== 'undefined') {
            if (item_id && item_id instanceof Array) {
                item_id = item_id.join(',');
            }
            this.selectedComponentsArray[componentIndex] = item_id;

            var params = this.getSelectedComponentsParam(componentIndex, item_id);
            ngs.action('pcc_after_component_changed_action', params);
        }

        if (jQuery('#pcc_footer_order_button').length > 0) {
            jQuery('#pcc_footer_order_button').css({'visibility': 'hidden'});
        }
        if (jQuery('#pcc_print_button').length > 0) {
            jQuery('#pcc_print_button').css({'visibility': 'hidden'});
        }

        var loadName = this.getComponentLoadName(componentIndex);
        ngs.load(loadName, params);



    },
    getComponentLoadName: function(componentIndex)
    {
        switch (parseInt(componentIndex)) {
            case this.componentsIndex.CASE:
                return 'pcc_select_case';
            case this.componentsIndex.CPU:
                return 'pcc_select_cpu';
            case this.componentsIndex.MB:
                return 'pcc_select_mb';
            case this.componentsIndex.COOLER:
                return 'pcc_select_cooler';
            case this.componentsIndex.RAM:
                return 'pcc_select_ram';
            case this.componentsIndex.HDD:
                return 'pcc_select_hdd';
            case this.componentsIndex.SSD:
                return 'pcc_select_ssd';
            case this.componentsIndex.OPT:
                return 'pcc_select_opt';
            case this.componentsIndex.MONITOR:
                return 'pcc_select_monitor';
            case this.componentsIndex.VIDEO:
                return 'pcc_select_graphics';
            case this.componentsIndex.POWER:
                return 'pcc_select_power';
            case this.componentsIndex.KEY:
                return 'pcc_select_keyboard';
            case this.componentsIndex.MOUSE:
                return 'pcc_select_mouse';
            case this.componentsIndex.SPEAKER:
                return 'pcc_select_speaker';
        }
        return null;
    },
    /**
     *Returns selected components ids, or if selected component is more that one then join the component ids and put in the corresponding component object key.
     * @param {Object} componentIndex last selected component index
     * @param {Object} item_id last selected item id
     */
    getSelectedComponentsParam: function(componentIndex, item_id) {
        var ret = {
            "case": this.selectedComponentsArray[this.componentsIndex.CASE],
            "mb": this.selectedComponentsArray[this.componentsIndex.MB],
            "rams": this.selectedComponentsArray[this.componentsIndex.RAM],
            "cpu": this.selectedComponentsArray[this.componentsIndex.CPU],
            "hdds": this.selectedComponentsArray[this.componentsIndex.HDD],
            "ssds": this.selectedComponentsArray[this.componentsIndex.SSD],
            "cooler": this.selectedComponentsArray[this.componentsIndex.COOLER],
            "opts": this.selectedComponentsArray[this.componentsIndex.OPT],
            "monitor": this.selectedComponentsArray[this.componentsIndex.MONITOR],
            "graphics": this.selectedComponentsArray[this.componentsIndex.VIDEO],
            "power": this.selectedComponentsArray[this.componentsIndex.POWER],
            "keyboard": this.selectedComponentsArray[this.componentsIndex.KEY],
            "mouse": this.selectedComponentsArray[this.componentsIndex.MOUSE],
            "speaker": this.selectedComponentsArray[this.componentsIndex.SPEAKER]
        };
        if (componentIndex !== null)
        {
            ret.last_selected_component_type_index = componentIndex;
        }
        if (item_id !== null)
        {
            ret.last_selected_component_id = item_id;
        }
        return ret;
    },
    /**
     *Returns flat (one level) array of selected items ids, note that only
     */
    getSelectedComponentsIdsJoinWithComma: function() {
        var scids = this.selectedComponentsArray.join(',');
        var flat_ids = scids.split(',');
        var exclude_not_selected_components = new Array();
        for (var i = 0; i < flat_ids.length; i++) {
            if (parseInt(flat_ids[i]) > 0) {
                exclude_not_selected_components.push(flat_ids[i]);
            }
        }
        return exclude_not_selected_components.join(',');
    },
    getBackendComponentId: function(elementId) {

        var selected_component = $(elementId).value;
        if (selected_component.indexOf(',') == -1) {

            var selected_component_id = 0;
            if (selected_component.strip().length > 0 && parseInt(selected_component) > 0) {
                selected_component_id = parseInt(selected_component);
            }
            return selected_component_id;
        } else {

            var selected_components = $(elementId).value;
            selected_components = selected_components.split(',');

            var selected_components_ids = new Array();
            for (var i = 0; i < selected_components.length; i++) {
                var selected_component = selected_components[i];
                var selected_component_id = 0;
                if (selected_component.strip().length > 0 && parseInt(selected_component) > 0) {
                    selected_component_id = parseInt(selected_component);
                }
                if (selected_component_id > 0)
                    selected_components_ids.push(selected_component_id);
            }
            var selected_component_id = selected_components_ids.length > 1 ? selected_components_ids : selected_components_ids.length === 1 ? selected_components_ids[0] : 0;
            if (selected_component_id && selected_component_id instanceof Array) {
                selected_component_id = selected_component_id.join(',');
            }
            return selected_component_id;
        }
    },
    onLoadMoreComponents: function(componentKey) {
        $('pcc_components_container_overlay').style.display = 'block';
        $('load_more_div').remove();
        $('pcc_components_container').id = "last_pcc_components_container";
        $('load_more_hidden_div').id = 'pcc_components_container';
        $('pcc_components_container').style.display = "none";

        var params = this.getSelectedComponentsParam(null, null);
        var params = this.getSelectedComponentsParam(null, null);
        var searchText = jQuery('#pcc_search_component_text').val();
        var offset = $$("#pcc_select_component_inner_container ." + componentKey + "_pcc_listing_item").length;
        jQuery.extend(params, {'search_component': searchText, "take_in_count_search_text": 1, "load_more": true,
            'loaded_items_count': offset});
        ngs.load('pcc_select_' + componentKey, params);
    },
    onSearchComponentChanged: function(componentKeyIndex, searchText) {

        $('pcc_components_container_overlay').style.display = 'block';
        $('pcc_components_container').id = "last_pcc_components_container";
        $('component_selection_container').id = 'pcc_components_container';

        var params = this.getSelectedComponentsParam(null, null);
        params = mergeObjects(params, {
            "take_in_count_search_text": 1,
            "search_component": searchText
        });
        var loadName = this.getComponentLoadName(componentKeyIndex);
        ngs.load(loadName, params);

    }
};
