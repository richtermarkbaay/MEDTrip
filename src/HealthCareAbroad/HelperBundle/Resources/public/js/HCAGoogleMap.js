var HCAGoogleMap = {
	tableId: 4442675,
	lat: '',
	lng: '',
	map: null,
	layer: null,
	latLngString: '',
	marker: null,
	geocoder: null,
	address: 'Washington, United States',
	defaultAddress: 'Washington, United States', // City and Country Address
	mapCanvasElem: document.getElementById('map_canvas'),
	mapOnChangeCallback: null,

	initialize: function(params) {
		
		HCAGoogleMap.setParams(params);

		var options = { zoom: 15, disableDefaultUI: true, mapTypeId: google.maps.MapTypeId.ROADMAP };

		HCAGoogleMap.map = new google.maps.Map(HCAGoogleMap.mapCanvasElem, options);
		HCAGoogleMap.geocoder = new google.maps.Geocoder();
		HCAGoogleMap.geocoder.geocode({'address': HCAGoogleMap.address}, HCAGoogleMap.geocoderCallback);
	},
	
	setParams: function(params) {
		if(typeof params.mapCanvasElem != 'undefined') {
			HCAGoogleMap.mapCanvasElem = params.mapCanvasElem;
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
	
	geocoderCallback: function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			HCAGoogleMap.map.setCenter(results[0].geometry.location);
	        HCAGoogleMap.lat = results[0].geometry.location.lat();
	        HCAGoogleMap.lng = results[0].geometry.location.lng();		        
	        HCAGoogleMap.latLngString = HCAGoogleMap.lat + "," + HCAGoogleMap.lng;
	        
	        HCAGoogleMap.setMarker(results[0].geometry.location);
	        HCAGoogleMap.setLayer();

	        google.maps.event.addListener(HCAGoogleMap.marker, 'click', HCAGoogleMap.toggleBounce);

		} else {
			HCAGoogleMap.geocoder.geocode({ 'address': HCAGoogleMap.defaultAddress}, function(){});
		}
	
	    if(HCAGoogleMap.mapOnChangeCallback) {
	    	HCAGoogleMap.mapOnChangeCallback();
	    }
	},
	
	setMarker: function(markerPosition) {
        if (HCAGoogleMap.marker) {
        	HCAGoogleMap.marker.setMap(null);
        }

        HCAGoogleMap.marker = new google.maps.Marker({
            map: HCAGoogleMap.map,
            position: markerPosition,
            draggable:true,
            zoom: 15,
            animation: google.maps.Animation.DROP
        });
	},
	
	setLayer: function() {

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
	
	updateMap: function(address) {
		HCAGoogleMap.address = address;
		HCAGoogleMap.geocoder.geocode({ 'address': address}, HCAGoogleMap.geocoderCallback);
	},

	toggleBounce: function() {
		if (HCAGoogleMap.marker.getAnimation() != null) {
			HCAGoogleMap.marker.setAnimation(null);
		} else {
			HCAGoogleMap.marker.setAnimation(google.maps.Animation.BOUNCE);
		}		
	}
}