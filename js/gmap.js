/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

(function ($) {
    $( document ).ready(function() {
        async function initMap(gmap, gdata) {

            const { Map } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

            const position = { lat: parseFloat(gdata.lat), lng: parseFloat(gdata.lng) };
            $(gmap).empty().removeClass('d-none');
            let map = new Map(gmap, {
                center: position,
                zoom: parseInt(gdata.zoom),
                mapTypeId: gdata.type,
                scrollwheel: parseInt(gdata.mousescroll) !== 0,
                disableDefaultUI: parseInt(gdata.show_controllers) !== 1,
                mapId: "DEMO_MAP_ID",
            });
            // The marker, positioned at Uluru
            const marker = new AdvancedMarkerElement({
                map: map,
                position: position,
                title: gdata.title,
            });

            if (gdata.infowindow !== '') {
                const infowindow = new google.maps.InfoWindow({
                    content: '<h5>'+gdata.title+'</h5>'+'<div>'+gdata.infowindow+'</div>',
                    ariaLabel: gdata.title,
                });

                // Add a click listener for each marker, and set up the info window.
                marker.addListener("click", () => {
                    infowindow.open({
                        anchor: marker,
                        map,
                    });
                });
            }

            if (gdata.locations.length) {
                gdata.locations.forEach(location => {
                    const _marker = new AdvancedMarkerElement({
                        map: map,
                        position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
                        title: location.address,
                    });

                    const _infowindow = new google.maps.InfoWindow({
                        content: location.address,
                        ariaLabel: location.address,
                    });

                    // Add a click listener for each marker, and set up the info window.
                    _marker.addListener("click", () => {
                        _infowindow.open({
                            anchor: _marker,
                            map,
                        });
                    });
                });
            }
        }
        Array.from(document.querySelectorAll('.as-gmap')).forEach(function(gmap) {
            initMap(gmap, JSON.parse($(gmap).text()));
        });
    });
}(jQuery));