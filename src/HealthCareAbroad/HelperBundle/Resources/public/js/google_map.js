/**
 * Google Map js
 * @author Chaztine Blance
 */
 var GoogleMap = {
		
	tableId: 4442675,
	lat: '',
	lng: '',
	map: null,
	layer: null,
	latLngString: '',
	marker: null,
	geocoder: null,
	mapCanvasElem: document.getElementById('map_canvas'),
	mapOnChangeCallback: null,
	recursion: 0,
	defaultAddress: 'Washington, United States', // City and Country Address
	
    inputs: {
        'selectedCity': '', 
        'selectedCountry':'',
        'inputCoordinates': '',
        'selectedBuilding': '',
        'selectedStreet' : '',
    },
    
    setInputs: function(_val){
        this.inputs = _val;
        
        return this;
    },
	setParams: function() {
		if(GoogleMap.inputs.selectedCity.options){
			GoogleMap.address = GoogleMap.inputs.selectedBuilding.value + " " + GoogleMap.inputs.selectedStreet.value + "," + GoogleMap.inputs.selectedCity.options[GoogleMap.inputs.selectedCity.selectedIndex].innerHTML + "," + GoogleMap.inputs.selectedCountry.value;
		}
	},
	initialize: function() {
		
		GoogleMap.setParams();

		var options = { zoom: 15, disableDefaultUI: true, mapTypeId: google.maps.MapTypeId.ROADMAP };

		GoogleMap.map = new google.maps.Map(GoogleMap.mapCanvasElem, options);
		GoogleMap.geocoder = new google.maps.Geocoder();
		GoogleMap.geocoder.geocode({'address': GoogleMap.address}, GoogleMap.geocoderCallback);
		
	},
	geocodePosition: function(pos) {
		GoogleMap.geocoder.geocode({ latLng: pos }, 
		function(responses) {
			if (responses && responses.length > 0) {
				latLngString = responses[0].geometry.location.lat() + "," + responses[0].geometry.location.lng();
				GoogleMap.inputs.inputCoordinates.value = latLngString;
			} 
		});
	},
	geocoderCallback: function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			GoogleMap.map.setCenter(results[0].geometry.location);
			GoogleMap.lat = results[0].geometry.location.lat();
			GoogleMap.lng = results[0].geometry.location.lng();		        
			GoogleMap.latLngString = GoogleMap.lat + ", " + GoogleMap.lng;
			
			GoogleMap.inputs.inputCoordinates.value = GoogleMap.latLngString;
			
			GoogleMap.setMarker(results[0].geometry.location);
			GoogleMap.setLayer();
	        google.maps.event.addListener(GoogleMap.marker, 'click', GoogleMap.toggleBounce);
	        google.maps.event.addListener(GoogleMap.marker, 'dragend', function() {
		          GoogleMap.geocodePosition(GoogleMap.marker.getPosition());
		    });
	        google.maps.event.trigger(GoogleMap.map, 'resize');
	        if(GoogleMap.mapOnChangeCallback) {
	        	GoogleMap.mapOnChangeCallback();
	        }
	        
//	        GoogleMap.recursion++;

		} else {
			GoogleMap.geocoder.geocode({ 'address': GoogleMap.defaultAddress}, GoogleMap.geocoderCallback);
		}
	},
	
	setMarker: function(markerPosition) {
        if (GoogleMap.marker) {
        	GoogleMap.marker.setMap(null);
        }
        var latlng = new google.maps.LatLng(GoogleMap.inputs.inputCoordinates.value);
        GoogleMap.marker = new google.maps.Marker({
            map: GoogleMap.map,
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
	            from: GoogleMap.tableId,
	            where: 'ST_INTERSECTS(geometry, CIRCLE(LATLNG(' + GoogleMap.latLongString + '),1))',
	            limit: 1
			}
		};

		if (!GoogleMap.layer) {
			GoogleMap.layer = new google.maps.FusionTablesLayer(options);
			GoogleMap.layer.setMap(GoogleMap.map);
        } else { 
        	GoogleMap.layer.setOptions(options);
        }
	},
	
	updateMap: function(address) {
		if(address){
			GoogleMap.address = address;
		}else{
		GoogleMap.setParams();
		}
		GoogleMap.geocoder.geocode({ 'address': GoogleMap.address}, GoogleMap.geocoderCallback);
	},
	
	toggleBounce: function() {
		if (GoogleMap.marker.getAnimation() != null) {
			GoogleMap.marker.setAnimation(null);
		} else {
			GoogleMap.marker.setAnimation(google.maps.Animation.BOUNCE);
		}		
	},
};
 
// (function($){
	 
//	 $('.addressFields').live('blur', function(e) {
//         if (e.which == 17){
//         	  e.preventDefault();
//         }
//     	GoogleMap.setParams();
//        GoogleMap.geocoder.geocode( { 'address': GoogleMap.address}, GoogleMap.geocoderCallback ); 
//     });
//		 
//	 $('.selectAdressFields').live('change', function(e) {
//		GoogleMap.setParams();
//		GoogleMap.geocoder.geocode( { 'address': GoogleMap.address}, GoogleMap.geocoderCallback ); 
//	 });
	 
// })(jQuery);