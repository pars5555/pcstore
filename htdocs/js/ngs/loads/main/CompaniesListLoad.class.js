ngs.CompaniesListLoad = Class.create(ngs.AbstractLoad, {
    map_markers: null,
    map_info_windows: null,
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "companies_list";
    },
    getMethod: function() {
        return "POST";
    },
    getContainer: function() {
        return "hp_companies_list_tab";
    },
    getName: function() {
        return "companies_list";
    }, beforeLoad: function() {
        $('global_modal_loading_div').style.display = 'block';
    },
    afterLoad: function() {
        var thisInstance = this;
        ngs.UrlChangeEventObserver.setFakeURL("/companies");
        $('global_modal_loading_div').style.display = 'none';
        jQuery('#cl_tabs').tabs();        
        if ($('add_company_button')) {
            $('add_company_button').onclick = this.onAddCompanyButtonClicked.bind(this);
        }
        var company_offers_save_buttons = $$("#icl_list_container_table .company_offers_save_buttons");
        this.addCompanyOffersChangedButtonsClickHandlers(company_offers_save_buttons);
        var receive_sms_from_checkboxes = $$("#icl_list_container_table .f_receive_sms_from_checkboxes");
        this.addReceiveSmsFromCheckboxesClickHandler(receive_sms_from_checkboxes);
        if ($('f_show_only_last_hours_select'))
        {
            $('f_show_only_last_hours_select').onchange = this.onShowOnlyLastHoursSelectChanged.bind(this);
        }

        jQuery('.price_scroll_right_a').click(function() {
            jQuery('#price_files_content_' + jQuery(this).attr('company_id')).animate({
                marginLeft: '-=100px'}, 300);
            if (jQuery(this).attr('loaded') != 1) {
                jQuery('body').append(jQuery("<div id='tmp_zip_prices_container' style='display:none'></div>"));
                ngs.load('company_zipped_prices_more', {'company_id': jQuery(this).attr('company_id')});
                jQuery(this).attr('loaded', 1);
            }
        });
        jQuery('.price_scroll_left_a').click(function() {
            if (parseInt(jQuery('#price_files_content_' + jQuery(this).attr('company_id')).css('margin-left')) <= -100) {
                jQuery('#price_files_content_' + jQuery(this).attr('company_id')).animate({
                    marginLeft: '+=100px'}, 300);
            }
        });
        jQuery('.service_price_scroll_right_a').click(function() {
            jQuery('#service_price_files_content_' + jQuery(this).attr('service_company_id')).animate({
                marginLeft: '-=100px'}, 300);
            if (jQuery(this).attr('loaded') != 1) {
                jQuery('body').append(jQuery("<div id='tmp_zip_prices_container' style='display:none'></div>"));
                ngs.load('service_company_zipped_prices_more', {'service_company_id': jQuery(this).attr('service_company_id')});
                jQuery(this).attr('loaded', 1);
            }
        });
        jQuery('.service_price_scroll_left_a').click(function() {
            if (parseInt(jQuery('#service_price_files_content_' + jQuery(this).attr('service_company_id')).css('margin-left')) <= -100) {
                jQuery('#service_price_files_content_' + jQuery(this).attr('service_company_id')).animate({
                    marginLeft: '+=100px'}, 300);
            }
        });
        this.initGMaps();
        jQuery(".company_gmap_pin").click(function() {
            thisInstance.map_info_windows.each(function(infoWindow) {
                infoWindow.close();
            });
            var company_id = jQuery(this).attr('company_id');
            thisInstance.map_markers['company_' + company_id].each(function(marker) {
                google.maps.event.trigger(marker, 'click');
            });
            jQuery(window.self).scrollTop(0);
        });
        jQuery(".service_company_gmap_pin").click(function() {
            thisInstance.map_info_windows.each(function(infoWindow) {
                infoWindow.close();
            });
            var company_id = jQuery(this).attr('service_company_id');
            thisInstance.map_markers['service_company_' + company_id].each(function(marker) {
                google.maps.event.trigger(marker, 'click');
            });
            jQuery(window.self).scrollTop(0);
        });
        jQuery("#cl_search_in_prices").keypress(function(e) {
            if (e.which === 13) {
                var searchText = jQuery(this).val();
                ngs.load("companies_list", {'search_text': searchText});
            }
        });
        jQuery('.add_service_company_buttons').click(function() {
            var service_company_id = jQuery(this).attr('service_company_id');
            var access_key = jQuery('#service_company_access_key_input_' + service_company_id).val();
            ngs.action("add_service_company_action", {
                "access_key": access_key,
                "service_company_id":service_company_id
            });

        });
    },
    initGMaps: function()
    {
        this.map_markers = {};
        this.map_info_windows = new Array();
        var thisInstance = this;
        if (typeof window.google === 'undefined' || typeof window.google.maps === 'undefined') {
            return false;
        }
        var lat = typeof CMS_VARS.companies_list_map_center_lat !== 'undefined' ? CMS_VARS.companies_list_map_center_lat : 40.19;
        var lng = typeof CMS_VARS.companies_list_map_center_lng !== 'undefined' ? CMS_VARS.companies_list_map_center_lat : 44.52;
        var zoom = typeof CMS_VARS.companies_list_map_default_zoom !== 'undefined' ? CMS_VARS.companies_list_map_default_zoom : 14;
        var myLatlng = new google.maps.LatLng(lat, lng);
        var mapOptions = {
            zoom: zoom,
            zoomControl: true,
            panControl : true,
            scrollwheel: false,
            center: myLatlng,
            height: '300px',
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById('cl_gmap'), mapOptions);
        var styleArray = [
            {
                featureType: "all",
                stylers: [
                    {saturation: -80}
                ]
            }, {
                featureType: "road.arterial",
                elementType: "geometry",
                stylers: [
                    {hue: "#00ffee"},
                    {saturation: 50}
                ]
            }, {
                featureType: "poi.business",
                elementType: "labels",
                stylers: [
                    {visibility: "off"}
                ]
            }
        ];
        map.setOptions({styles: styleArray});

        map.setOptions({minZoom: 3, maxZoom: 19});
        var all_companies_dtos_to_array = jQuery.parseJSON(jQuery('#all_companies_dtos_to_array_json').val());
        var all_companies_branches_dtos_to_array = jQuery.parseJSON(jQuery('#all_companies_branches_dtos_to_array_json').val());
        all_companies_branches_dtos_to_array.each(function(cbdto) {

            var companyDto = thisInstance.findCompanyById(all_companies_dtos_to_array, cbdto.company_id);
            var cname = 'unknown company';
            if (typeof companyDto !== 'undefined')
            {
                cname = companyDto.name;
            }
            var k = (parseInt(companyDto.rating) + 30) / 100 / 2;
            var markerWidth = 100;
            var markerHeight = 100;
            var latlng = new google.maps.LatLng(cbdto.lat, cbdto.lng);
            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                title: cname + ', ' + cbdto.street,
                icon: {
                    url: 'img/google_map_pin.png',
                    size: new google.maps.Size(markerWidth * k, markerHeight * k),
                    scaledSize: new google.maps.Size(markerWidth * k, markerHeight * k)
                }
            });
            if ('company_' + companyDto.id in thisInstance.map_markers) {
                thisInstance.map_markers['company_' + companyDto.id].push(marker);
            } else
            {
                thisInstance.map_markers['company_' + companyDto.id] = [marker];
            }
            var logoPath = SITE_PATH + '/images/small_logo/' + cbdto.company_id + '/logo.png';
            var priceLogoPath = SITE_PATH + '/img/file_types_icons/xls_icon.png';
            var pricePath = SITE_PATH + '/price/last_price/' + cbdto.company_id;
            var infowindow = new google.maps.InfoWindow({
                content: '<div style="min-width:180px;min-height:70px"><img src="' + logoPath + '" /><br>' + cname + ', ' + cbdto.street +
                        '</div><div style="margin:5px"><a href="' + pricePath + '">' + '<img src="' + priceLogoPath + '" />' + ngs.LanguageManager.getPhraseSpan(10) + '</a></div>'
            });
            thisInstance.map_info_windows.push(infowindow);
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(map, this);
            });
        });
        var all_service_companies_dtos_to_array = jQuery.parseJSON(jQuery('#all_service_companies_dtos_to_array_json').val());
        var all_service_companies_branches_dtos_to_array = jQuery.parseJSON(jQuery('#all_service_companies_branches_dtos_to_array_json').val());
        all_service_companies_branches_dtos_to_array.each(function(scbdto) {

            var serviceCompanyDto = thisInstance.findCompanyById(all_service_companies_dtos_to_array, scbdto.service_company_id);
            var scname = 'unknown company';
            if (typeof serviceCompanyDto !== 'undefined')
            {
                scname = serviceCompanyDto.name;
            }
            var markerWidth = 15;
            var markerHeight = 15;
            var latlng = new google.maps.LatLng(scbdto.lat, scbdto.lng);
            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                title: scname + ', ' + scbdto.street,
                icon: {
                    url: 'img/google_map_pin_blue.png',
                    size: new google.maps.Size(markerWidth, markerHeight),
                    scaledSize: new google.maps.Size(markerWidth, markerHeight)
                }
            });
            if ('service_company_' + serviceCompanyDto.id in thisInstance.map_markers) {
                thisInstance.map_markers['service_company_' + serviceCompanyDto.id].push(marker);
            } else
            {
                thisInstance.map_markers['service_company_' + serviceCompanyDto.id] = [marker];
            }
            var logoPath = SITE_PATH + '/images/sc_small_logo/' + scbdto.service_company_id + '/logo.png';
            var infowindow = new google.maps.InfoWindow({
                content: '<div style="min-width:140px;min-height:70px"><img src="' + logoPath + '" /><br>' + scname + ', ' + scbdto.street

            });
            thisInstance.map_info_windows.push(infowindow);
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(map, this);
            });
        });
    },
    findCompanyById: function(all_companies_dtos_to_array, cid)
    {
        for (var dto_index in all_companies_dtos_to_array) {
            if (all_companies_dtos_to_array[dto_index].id == cid) {
                return all_companies_dtos_to_array[dto_index];
            }
        }
    },
    onShowOnlyLastHoursSelectChanged: function()
    {
        var show_only_hours = $('f_show_only_last_hours_select').value;
        ngs.load("companies_list", {'show_only_last_hours_selected': show_only_hours});
    },
    addReceiveSmsFromCheckboxesClickHandler: function(elements_array) {
        for (var i = 0; i < elements_array.length; i++) {
            elements_array[i].onclick = this.onReceiveSmsFromClick.bind(this);
        }
    },
    onReceiveSmsFromClick: function() {
        var receive_sms_from_checkboxes = $$("#icl_list_container_table .f_receive_sms_from_checkboxes");
        var companiesIds = new Array();
        for (var i = 0; i < receive_sms_from_checkboxes.length; i++) {
            var checkbox = receive_sms_from_checkboxes[i];
            if (checkbox.checked) {
                var companyId = checkbox.id.substr(checkbox.id.indexOf("^") + 1);
                companiesIds.push(companyId);
            }
        }
        ngs.action('set_company_interested_companies_ids_action', {"companies_ids": companiesIds.join(',')});
    },
    addCompanyOffersChangedButtonsClickHandlers: function(elements_array) {

        for (var i = 0; i < elements_array.length; i++) {
            var company_id = elements_array[i].id.substr(elements_array[i].id.indexOf("^") + 1);
            elements_array[i].onclick = this.onCompanyOfferSaveButtonClick.bind(this, company_id);
        }
    },
    onCompanyOfferSaveButtonClick: function(companyId) {
        var offers = $("company_offers_textarea^" + companyId).value;
        ngs.action('change_company_offers_action', {
            'company_id': companyId,
            'offers': offers
        });
    },
    onAddCompanyButtonClicked: function() {
        var access_key = prompt(ngs.LanguageManager.getPhrase(516), "");
        if (access_key != null && access_key.strip().length > 0) {
            ngs.action("add_company_action", {
                "access_key": access_key
            });
        }
    },
    onLoadDestroy: function()
    {
        jQuery("#" + this.getContainer()).html("");
    }
});
