
jQuery(document).ready(function() {


		$(document).ready(function(){

/***************************************************
		  		   // Portfolio on mouseover opactiy
***************************************************/	

if( jQuery.hasOwnProperty("prettyPhoto") ){

$(".lightbox").prettyPhoto({
animation_speed	: 'normal',
theme			: 'pp_default',
social_tools	: ''
});

}

});

//prettyPhoto END	

/***************************************************
		  		//	Parallex start
***************************************************/
	
$(window).load(function() {
$('#menu').localScroll({offset:-50,duration:2000});
$('.logo').localScroll({offset:-50,duration:1000});
$('.more_bg').localScroll({offset:-50,duration:1000});
$('.circle_uparrow').localScroll({offset:-50,duration:1000});
$('.circle_downarrow').localScroll({offset:-50,duration:1000});
$('#right_nav').localScroll({offset:-50,duration:1500});
	
	//.parallax(xPosition, speedFactor, outerHeight) options:
	//xPosition - Horizontal position of the element
	//inertia - speed to move relative to vertical scroll. Example: 0.1 is one tenth the speed of scrolling, 2 is twice the speed of scrolling
	//outerHeight (true/false) - Whether or not jQuery should use it's outerHeight option to determine when a section is in the viewport
	
	$('#home').parallax("50%", 0.3); // Backgrounds
	$('#home_static').parallax("50%", 0.3); // Backgrounds
	$('#home_superslide').parallax("50%", 0.3); // Backgrounds
	$('#home_sequence').parallax("50%", 0.3); // Backgrounds
	$('#services').parallax("50%", 0.3); // Backgrounds	

	
})

<!-- Parallex end -->


/***************************************************
		  		//	Preloader Script
***************************************************/

$(window).load(function() {
  $('#preloader').fadeOut(300, function() {
    $('body').css('overflow','visible');
    $(this).remove();
  });
});

	(function() {

		var settings = {
				button      : '#back_to_top',
				text        : 'Back to Top',
				min         : 200,
				fadeIn      : 400,
				fadeOut     : 400,
				scrollSpeed : 800,
				easingType  : 'easeInOutExpo'
			},
			oldiOS     = false,
			oldAndroid = false;

		// Detect if older iOS device, which doesn't support fixed position
		if( /(iPhone|iPod|iPad)\sOS\s[0-4][_\d]+/i.test(navigator.userAgent) )
			oldiOS = true;

		// Detect if older Android device, which doesn't support fixed position
		if( /Android\s+([0-2][\.\d]+)/i.test(navigator.userAgent) )
			oldAndroid = true;
	
		$('body').append('<a href="#" id="' + settings.button.substring(1) + '" title="' + settings.text + '">' + settings.text + '</a>');

		$( settings.button ).click(function( e ){
				$('html, body').animate({ scrollTop : 0 }, settings.scrollSpeed, settings.easingType );

				e.preventDefault();
			});

		$(window).scroll(function() {
			var position = $(window).scrollTop();

			if( oldiOS || oldAndroid ) {
				$( settings.button ).css({
					'position' : 'absolute',
					'top'      : position + $(window).height()
				});
			}

			if ( position > settings.min ) 
				$( settings.button ).fadeIn( settings.fadeIn );
			else 
				$( settings.button ).fadeOut( settings.fadeOut );
		});

	})();

	
<!--  (Back to Top)-->

});