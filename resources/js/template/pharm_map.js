class googleMap {
    constructor() {
        this.options = {
            zoom: 6,
            mapTypeControl: false,
            center: {
                lat: 49.038442,
                lng: 31.451323
            },
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        this.icon = null;
        this.init();
    }

    init() {
        map = new google.maps.Map(domEntityMaps, this.options);
        bounds = new google.maps.LatLngBounds();
        domEntityMaps.style.height = "650px";
        this.locations();
        this.mapMarkerIcon();
        this.markers();
        domEntityCity.addEventListener('change', this.changeCity);
        for (var i = 0; i < ar_markers.length; i++) {
            bounds.extend(ar_markers[i].getPosition());
        }
        map.setCenter(bounds.getCenter());
        map.fitBounds(bounds);
        map.panToBounds(bounds);
        map.setZoom(map.getZoom());
        if (map.getZoom() > 15) {
            map.setZoom(15);
        }
        allCities[0].coordinates.lat = map.getCenter().lat();
        allCities[0].coordinates.lng = map.getCenter().lng();
        allCities[0].bounds = bounds;
        domEntity.classList.remove('uk-hidden');
    }

    mapMarkerIcon() {
        this.icon = {
            url: mark_icon,
            scaledSize: new google.maps.Size(30, 30),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(20, 40)
        }
    };

    markers() {
        let arMapMarkers = mapMarkers;
        if (arMapMarkers != undefined && Array.isArray(arMapMarkers)) {
            for (let i = 0; i < arMapMarkers.length; i++) {
                this.addMarker(arMapMarkers[i]);
            }
        }
    }

    addMarker(properties) {
        var marker = new google.maps.Marker({
            icon: this.icon,
            position: properties.coordinates,
            map: map
        });
        ar_markers.push(marker);
        if (properties.info) {
            let infoWindow = new google.maps.InfoWindow({
                content: properties.info,
                maxWidth: 300
            });
            marker.addListener('click', function () {
                if (lastOpenInfoWindow) lastOpenInfoWindow.close();
                infoWindow.open(map, marker);
                lastOpenInfoWindow = infoWindow;
            });
        }
    }

    locations() {
        for (let [key, value] of Object.entries(citiesMarkers)) {
            var b = new google.maps.LatLngBounds();
            for (let [key1, value2] of Object.entries(value)) {
                b.extend(value2.coordinates);
            }
            allCities[key].coordinates.lat = b.getCenter().lat();
            allCities[key].coordinates.lng = b.getCenter().lng();
            allCities[key].bounds = b;
        }
    }

    changeCity() {
        cityId = this.options[domEntityCity.selectedIndex].value;
        map.setCenter(allCities[cityId].bounds.getCenter());
        map.fitBounds(allCities[cityId].bounds);
        map.panToBounds(allCities[cityId].bounds);
        map.setZoom(map.getZoom());
        if (map.getZoom() > 15) {
            map.setZoom(15);
        }
    }
}

function initGoogleMap() {
    new googleMap();
}

class googleMap {
    constructor() {
        this.options = {
            zoom: 15,
            mapTypeControl: false,
            center: {
                lat: parseFloat(marker.lat),
                lng: parseFloat(marker.lng)
            },
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        this.icon = null;
        this.init();
    }

    init() {
        map = new google.maps.Map(domEntityMaps, this.options);
        domEntityMaps.style.height = "650px";
        this.mapMarkerIcon();
        new google.maps.Marker({
            icon: this.icon,
            position: {
                lat: parseFloat(marker.lat),
                lng: parseFloat(marker.lng)
            },
            map: map
        });
    }

    mapMarkerIcon() {
        this.icon = {
            url: mark_icon,
            scaledSize: new google.maps.Size(30, 30),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(20, 40)
        }
    }
}


class googleMap {
    constructor() {
        this.options = {
            zoom: 6,
            mapTypeControl: false,
            center: {
                lat: 49.038442,
                lng: 31.451323
            },
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        this.icon = null;
        this.init();
    }

    init() {
        map = new google.maps.Map(domEntityMap, this.options);
        bounds = new google.maps.LatLngBounds();
        domEntityMap.style.height = "650px";
        this.mapMarkerIcon();
        this.markers();
        for (var i = 0; i < ar_markers.length; i++) {
            bounds.extend(ar_markers[i].getPosition());
        }
        map.setCenter(bounds.getCenter());
        map.fitBounds(bounds);
        map.panToBounds(bounds);
        map.setZoom(map.getZoom());
        if (map.getZoom() > 15) {
            map.setZoom(15);
        }
    }

    mapMarkerIcon() {
        this.icon = {
            url: mark_icon,
            scaledSize: new google.maps.Size(30, 30),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(20, 40)
        }
    };

    markers() {
        if (mapMarkers != undefined && Array.isArray(mapMarkers)) {
            for (let i = 0; i < mapMarkers.length; i++) {
                this.addMarker(mapMarkers[i]);
            }
        }
    }

    addMarker(properties) {
        var marker = new google.maps.Marker({
            icon: this.icon,
            position: properties.coordinates,
            map: map
        });
        ar_markers.push(marker);
        if (properties.info) {
            let infoWindow = new google.maps.InfoWindow({
                content: properties.info,
                maxWidth: 300
            });
            marker.addListener('click', function () {
                if (lastOpenInfoWindow) lastOpenInfoWindow.close();
                infoWindow.open(map, marker);
                lastOpenInfoWindow = infoWindow;
            });
        }
    }
}