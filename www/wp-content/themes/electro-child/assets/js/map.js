'use strict';

/* ========================================================================
 * Omega: map.js
 * Map Shortcode Javascript file
 * ========================================================================
 * Copyright 2014 Oxygenna LTD
 * ======================================================================== */

/* jshint camelcase: false */

/* global jQuery: false, google: false, alert: false */

jQuery(document).ready(function($) {

    $('.google-map').each(function() {
        var mapDiv = $(this);
        var mapData = window[mapDiv.attr('id')];

        function createMap() {
            var style = [{
                'stylers': [{
                    'saturation': -100
                }]
            }];

            var options = {
                maxZoom: ~~mapData.map_maxZoom,
                zoom: ~~mapData.map_zoom,
                scrollwheel: mapData.map_scrollwheel,
                draggable: mapData.map_scrollable === 'on',
                mapTypeId: google.maps.MapTypeId[mapData.map_type],
                panControl: mapData.map_panControl,
                streetViewControl: mapData.map_streetViewControl,
                zoomControl: mapData.map_zoomControl,
                fullscreenControl: mapData.map_fullscreenControl,
                mapTypeControl: mapData.map_mapTypeControl,
            };
            //console.log(mapData);
            if(mapData.map_style === 'blackwhite') {
                options.styles = style;
            }

            return new google.maps.Map(mapDiv[0], options);
        }

        // create map
        var map = createMap();

        // create bounds in case we dont have center map coordinates
        // every time a marker is added we increase the bounds
        var bounds = new google.maps.LatLngBounds();
        function addMarker(position, index) {
            if(mapData.marker === 'show') {
                var image = {
                    url: mapData.markerURL,
                    size: new google.maps.Size(30, 48),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(15, 48)
                };

                var marker = new google.maps.Marker({
                    position: position,
                    icon:image,
                    map: map
                });

                // extend bounds to encase new marker
                bounds.extend(position);

                // add label popup to marker
                if (mapData.label[index] !== undefined) {
                    var infoWindow = new google.maps.InfoWindow({
                        content: mapData.label[index]
                    });
                    google.maps.event.addListener(marker, 'click', function(e) {
                        infoWindow.open(map, this);
                    });
                }

            }
        }

        // centre map

        var centerMapWithCoordinates = !mapData.auto_center;
        if(centerMapWithCoordinates) {
            if (mapData.center_latlng !== undefined) {
                var center_lat_lng = mapData.center_latlng.split(',');
                var center_map = new google.maps.LatLng(center_lat_lng[0], center_lat_lng[1]);
                map.setCenter(center_map);
            }
            else {
                console.log('You have not set any coordinates for the map to be centered at.');
            }
        }


        // create markers
        if(mapData.address) {
            // lookup addresses
            var markerAddressCount = 0;
            $.each(mapData.address, function(index, address) {
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({ 'address': address}, function(results, status) {
                    if(status === google.maps.GeocoderStatus.OK) {
                        if(undefined !== results[0]) {
                            var location = results[0].geometry.location;
                            var position = new google.maps.LatLng(location.lat(), location.lng());
                            addMarker(position, index);
                        }

                        // increment count so we can keep track of all markers loaded
                        markerAddressCount++;
                        // if all markers are loaded then fit map
                        if(!centerMapWithCoordinates && markerAddressCount === mapData.address.length) {
                            map.fitBounds(bounds);
                            //console.log(bounds);
                        }
                    }
                    else {
                        console.log('Geocode was not successful for the following reason: ' + status);
                    }
                });
            });
        }else if(undefined !== mapData.latlng) {
            for(var i = 0; i < mapData.latlng.length; i++) {
                var coordinates = mapData.latlng[i].split(',');
                var position = new google.maps.LatLng(coordinates[0], coordinates[1]);
                addMarker(position, i);
            }
            if(!centerMapWithCoordinates) {
                map.fitBounds(bounds);
            }
        }

        var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
            this.setZoom(~~mapData.map_zoom);
            setTimeout(function(){
                google.maps.event.removeListener(boundsListener);
            }, 1000);
        });
    });
});