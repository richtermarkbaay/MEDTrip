/**
 * Google Map js
 * @author Chaztine Blance
 */

var tableId = 4442675; // imported from 3835940
		  var geocoder = null;
		  var map = null;
		  var layer = null;
		  var marker = null;
		  var address = 'Manila, Philippines';

		 $('.addressFields').live('blur', function(e) {
            if (e.which == 17){
            	  e.preventDefault();
            }
            var selectedCity = document.getElementById('institution_profile_form_city');
			var selectedCountry = document.getElementById('institution_profile_form_country');
			var address = document.getElementById('institution_profile_form_building').value + " " + document.getElementById('institution_profile_form_steet').value + "," + selectedCity.options[selectedCity.selectedIndex].innerHTML + "," + selectedCountry.options[selectedCountry.selectedIndex].innerHTML;
		    
			geocoder.geocode( { 'address': address}, geocoderCallback ); 
        });
		 
		 $('.slectAdressFields').live('change', function(e) {
			 
		    var selectedCity = document.getElementById('institution_profile_form_city');
			var selectedCountry = document.getElementById('institution_profile_form_country');
			var address = document.getElementById('institution_profile_form_building').value + " " + document.getElementById('institution_profile_form_steet').value + "," + selectedCity.options[selectedCity.selectedIndex].innerHTML + "," + selectedCountry.options[selectedCountry.selectedIndex].innerHTML;
		    
			geocoder.geocode( { 'address': address}, geocoderCallback ); 
		 });
		 
		  function initialize() {
		    var myOptions = {
		      zoom: 15,
		      disableDefaultUI: true,
		      mapTypeId: google.maps.MapTypeId.ROADMAP
		    }
		    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		    geocoder = new google.maps.Geocoder();
		    geocoder.geocode( { 'address': address}, geocoderCallback);
		  }
		
		function geocoderCallback (results, status) {
		      if (status == google.maps.GeocoderStatus.OK) {
		        map.setCenter(results[0].geometry.location);
		        var lat = results[0].geometry.location.lat();
		        var lng = results[0].geometry.location.lng();		        
		        var point2 = lat + ", " + lng; 

		        document.getElementById("institution_profile_form_coordinates").value = point2; 
		        
		        if (marker) marker.setMap(null);
		
		        marker = new google.maps.Marker({
		            map: map, 
		            position: results[0].geometry.location,
		            draggable:true,
	                zoom: 15,
	                animation: google.maps.Animation.DROP
	                   
		        });
		        
		        if (!layer) 
		        {
		            layer = new google.maps.FusionTablesLayer({
		              query: {
		                select: 'geometry',
		                from: tableId,
		                where: 'ST_INTERSECTS(geometry, CIRCLE(LATLNG(' + lat + ', ' + lng + '),1))',
		                limit: 1
		              }
		            });
		            layer.setMap(map);
		        } 
		        else 
		        { 
		           layer.setOptions({
		              query: {
		                select: 'geometry',
		                from: tableId,
		                where: 'ST_INTERSECTS(geometry, CIRCLE(LATLNG(' + lat + ', ' + lng + '),1))',
		                limit: 1
		              }
		            });
		        }
		        google.maps.event.addListener(marker, 'click', toggleBounce);
		      } else { // if not found map, use defaul address
		    	  var address = document.getElementById('institution_profile_form_city').value + "," + document.getElementById('institution_profile_form_country').value;
				    geocoder.geocode( { 'address': address}, geocoderCallback ); 
		      }
		};
		
		  function toggleBounce() {

	          if (marker.getAnimation() != null) {
	            marker.setAnimation(null);
	          } else {
	            marker.setAnimation(google.maps.Animation.BOUNCE);
	          }
	        }
