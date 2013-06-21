var HCAGoogleMap = {
	tableId: 4442675,
	lat: '',
	lng: '',
	zoom: 15,
	map: null,
	layer: null,
	marker: null,
	latLngString: '',
	geocoder: new google.maps.Geocoder(),
	address: 'Washington, United States',
	defaultAddress: 'Washington, United States', // City and Country Address
	mapCanvasElem: document.getElementById('map_canvas'),
	mapOnChangeCallback: function(){},

	initialize: function(params) {
		HCAGoogleMap._setParams(params);
		var mapOptions = { zoom: HCAGoogleMap.zoom, disableDefaultUI: true, mapTypeId: google.maps.MapTypeId.ROADMAP };

		if(HCAGoogleMap.latLngString) {
			mapOptions.center = new google.maps.LatLng(HCAGoogleMap.lat, HCAGoogleMap.lng);  
			HCAGoogleMap.map = new google.maps.Map(HCAGoogleMap.mapCanvasElem, mapOptions);

	        HCAGoogleMap._setMarker(HCAGoogleMap.map.getCenter());
	        google.maps.event.addListener(HCAGoogleMap.marker, 'click', HCAGoogleMap._onMarkerClick);
	        google.maps.event.addListener(HCAGoogleMap.marker, 'dragend', HCAGoogleMap._onMarkerDragend); 
	        google.maps.event.addListener(HCAGoogleMap.map, 'resize', HCAGoogleMap._onMapResize);

		} else {
			HCAGoogleMap.map = new google.maps.Map(HCAGoogleMap.mapCanvasElem, mapOptions);
			HCAGoogleMap.geocoder.geocode({'address': HCAGoogleMap.address}, HCAGoogleMap._geocoderCallback);
		}
	},

	// Private Functions
	_setParams: function(params) {
		if(typeof params.mapCanvasElem != 'undefined') {
			HCAGoogleMap.mapCanvasElem = params.mapCanvasElem;
		}

		if(typeof params.latLngString != 'undefined') {
			HCAGoogleMap.latLngString = params.latLngString;
			coordinates = HCAGoogleMap.latLngString.split(',');
			HCAGoogleMap.lat = coordinates[0];
	        HCAGoogleMap.lng = coordinates[1];
		}

		if(typeof params.coordinates != 'undefined') {
			HCAGoogleMap.zoom = params.zoom;			
		}

		if(typeof params.address != 'undefined') {
			HCAGoogleMap.address = params.address;			
		}

		if(typeof params.defaultAddress != 'undefined') {
			HCAGoogleMap.defaultAddress = params.defaultAddress;
		}

		if(typeof params.mapOnChangeCallback != 'undefined') {
			HCAGoogleMap.mapOnChangeCallback = params.mapOnChangeCallback;
		}	
	},
	
	_geocoderCallback: function(results, status) {		
		//console.log('setGeocoderMap');
		if (status == google.maps.GeocoderStatus.OK) {
			latlngObj = results[0].geometry.location;
	        HCAGoogleMap.lat = results[0].geometry.location.lat();
	        HCAGoogleMap.lng = results[0].geometry.location.lng();		        
	        HCAGoogleMap.latLngString = HCAGoogleMap.lat + "," + HCAGoogleMap.lng;
			HCAGoogleMap.map.setCenter(latlngObj);
	        HCAGoogleMap._setMarker(latlngObj);
	        HCAGoogleMap._setLayer();

	        google.maps.event.addListener(HCAGoogleMap.marker, 'click', HCAGoogleMap._onMarkerClick);
	        google.maps.event.addListener(HCAGoogleMap.marker, 'dragend', HCAGoogleMap._onMarkerDragend); 
	        google.maps.event.addListener(HCAGoogleMap.map, 'resize', HCAGoogleMap._onMapResize);

		} else {
			HCAGoogleMap.geocoder.geocode({ 'address': HCAGoogleMap.defaultAddress}, function(){});
		}

		HCAGoogleMap.mapOnChangeCallback();

	},

	_setMarker: function(markerPosition) {
        if (HCAGoogleMap.marker) {
        	HCAGoogleMap.marker.setMap(null);
        }

        HCAGoogleMap.marker = new google.maps.Marker({
            map: HCAGoogleMap.map,
            position: markerPosition,
            draggable:true,
            zoom: HCAGoogleMap.zoom,
            animation: google.maps.Animation.DROP
        });
	},
	
	_setLayer: function() {
		var options = {
			query: {
	            select: 'geometry',
	            from: HCAGoogleMap.tableId,
	            where: 'ST_INTERSECTS(geometry, CIRCLE(LATLNG(' + HCAGoogleMap.latLongString + '),1))',
	            limit: 1
			}
		};
		if (!HCAGoogleMap.layer) {
        	HCAGoogleMap.layer = new google.maps.FusionTablesLayer(options);
        	HCAGoogleMap.layer.setMap(HCAGoogleMap.map);
        } else { 
        	HCAGoogleMap.layer.setOptions(options);
        }
	},

	_onMarkerClick: function() {
		if (HCAGoogleMap.marker.getAnimation() != null) {
			HCAGoogleMap.marker.setAnimation(null);
		} else {
			HCAGoogleMap.marker.setAnimation(google.maps.Animation.BOUNCE);
		}
	},

	_onMarkerDragend: function(location) { 
	    HCAGoogleMap.lat = location.latLng.lat();
	    HCAGoogleMap.lng = location.latLng.lng();		        
	    HCAGoogleMap.latLngString = HCAGoogleMap.lat +','+ HCAGoogleMap.lng;

	    setTimeout(function(){
	    	HCAGoogleMap.map.panTo(location.latLng);
	    }, 1000);

	    HCAGoogleMap.mapOnChangeCallback();
	},

	_onMapResize: function() {
    	setTimeout(function(){
    		HCAGoogleMap.map.panTo(HCAGoogleMap.marker.getPosition());
    	}, 100);
	},
	// End of Private Functions


	// Public Functions
	updateMap: function(address) {
		//console.log('updateGeocoderMap');
		HCAGoogleMap.address = address;
		HCAGoogleMap.geocoder.geocode({ 'address': address}, HCAGoogleMap._geocoderCallback);
		google.maps.event.trigger(HCAGoogleMap.map, 'resize');
	},
}