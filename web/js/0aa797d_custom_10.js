jQuery(document).ready(function($) {
//MISC
$('#footer .widget ul li.cat-item').parent().addClass('cat_w_list');

if ($('#footer .widget ul.cat_w_list > li').length %2 != 0){
    $('#footer .widget ul li.cat-item').slice(-1).css('border-bottom', 'none');
}else{
    $('#footer .widget ul li.cat-item').slice(-2).css('border-bottom', 'none');
}

$('.cat_article_img .video_icon_img').append('<span class="ca_video_icon" />');

//Hovers images
$('.cat_article_img img, .widget .flickr_badge_wrapper a, .news_box .recent_news_img, .mom_posts_images img, ul.blog_posts_widget li img, .lates_video_item img, .sidebar .ads125 img, .related_image, .nb2_next2_img img').hover( function() {
    $(this).stop().animate({opacity:0.8},{queue:false,duration:200});  
   }, function() {
     $(this).stop().animate({opacity:1},{queue:false,duration:200});  
    });


// Social icon hover
  if(jQuery.browser.msie){
     if(parseFloat(jQuery.browser.version) < 9){
        //Versions of IE less than 8
     }
     else{
       //code for versions of IE at least 8 (currently 8 and 9)
     }
  }
  else{
$('.bottom_bar ul.social_icons li a').hover( function() {
    $(this).stop().animate({opacity:0.6},{queue:false,duration:200});  
   }, function() {
     $(this).stop().animate({opacity:1},{queue:false,duration:200});  
    });
}


// Latest Video
$('.lates_video_item a ').hover( function() {
    $(this).find('span.video_icon, span.slide_icon').stop().animate({left:0},{queue:false,duration:200});  
   }, function() {
     $(this).find('span.video_icon, span.slide_icon').stop().animate({left:'-44px'},{queue:false,duration:200});  
    });

// News Box Hover
$('.recent_news_img a').hover( function() {
    $(this).find('.nb_video_icon, .nb_slide_icon, .nb_article_icon ').stop().animate({left:0},{queue:false,duration:200});  
   }, function() {
     $(this).find('.nb_video_icon, .nb_slide_icon, .nb_article_icon ').stop().animate({left:'-44px'},{queue:false,duration:200});  
    });

// Cat Box Hover
$('.cat_img a').hover( function() {
    $(this).find('.ca_video_icon, .ca_slide_icon, .ca_article_icon').stop().animate({left:0},{queue:false,duration:200});  
   }, function() {
     $(this).find('.ca_video_icon, .ca_slide_icon, .ca_article_icon').stop().animate({left:'-44px'},{queue:false,duration:200});  
    });


// Images Tip
  $('.mom_posts_images div a').tipsy({gravity: 's', fade: true});


//scroll to top
$('.scrollTo_top').hide();
	$(window).scroll(function () {
		if( $(this).scrollTop() > 100 ) {
			$('.scrollTo_top').fadeIn(300);
		}
		else {
			$('.scrollTo_top').fadeOut(300);
		}
	});

	$('.scrollTo_top a').click(function(){
		$('html, body').animate({scrollTop:0}, 500 );
		return false;
	});

//news ticker
    $("ul#ticker01").liScroll();

//tabbed widget
$("ul.tabbed_nav").tabs(".tabbed_container .tabbed_content", {effect: 'fade', fadeOutSpeed: 400});


/************************************
 * Shortcodes/framework
**************************************/
//dropdown
	jQuery("#navigation ul.nav a, .top_bar ul.top_nav a").removeAttr('title');
	jQuery(" #navigation ul.nav ul, .top_bar ul.top_nav ul").css({display: "none"}); // Opera Fix
	jQuery("#navigation ul.nav li, .top_bar ul.top_nav li").each(function()
		{	
	var jQuerysubmeun = jQuery(this).find('ul:first');
	jQuery(this).hover(function()
		{	
	jQuerysubmeun.stop().css({overflow:"hidden", height:"auto", display:"none", paddingTop:0}).slideDown(250, function()
		{
	jQuery(this).css({overflow:"visible", height:"auto"});
		});	
		},
	function()
		{	
	jQuerysubmeun.stop().slideUp(250, function()
		{	
	jQuery(this).css({overflow:"hidden", display:"none"});
			});
		});	
	});

// buttons
$('.single_article_content .button_left:last').after('<div class="clear" />');
$('.single_article_content .button_right:last').after('<div class="clear" />');

//External Links
  $("a[href^=http]").each(function(){
      if(this.href.indexOf(location.hostname) == -1) {
         $(this).attr({
            target: "_blank",
            title: ""
         });
      }
   });
  

//tip
  $('.tip_s').tipsy({gravity: 's'});
  $('.tip_n').tipsy({gravity: 'n'});
  $('.tip_e').tipsy({gravity: 'e'});
  $('.tip_w').tipsy({gravity: 'w'});

// Tabs
	$(".custom_tabs").tabs(".custom_tabs_content", {
		tabs: "li",
		effect:'fade'
	});
	
	$(".custom_tabs2").tabs(".custom_tabs2_content", {
		tabs: "li",
		effect:'fade'

	});

//Accordion
        
        $(".accordion").tabs(".accordion .acc_content", {tabs: 'h2.acc_title', effect: 'slide', initialIndex: null});
	$('.accordion .acc_title:first-child').addClass('current');
	$('.accordion .acc_title.current').next('div.acc_content').css('display', 'block');
//toggle
jQuery("h4.toggle").click(function () {
	$(this).next(".toggle_content").slideToggle();
	$(this).toggleClass("active_toggle");
	$(this).parent().toggleClass("toggle_var");
});

$("h4.toggle_min").click(function () {
	$(this).next(".toggle_content_min").slideToggle();
	$(this).toggleClass("active_toggle_min");
});

//Testimonials
	$('.test_content').append('<span class="t-arrow"></span>');
//Flickr
$('.single_article_content .flickr_badge_wrapper div img, a.lightbox_img img').hover( function() {
    $(this).stop().animate({opacity:0.7},{queue:false,duration:200});  
   }, function() {
     $(this).stop().animate({opacity:1},{queue:false,duration:200});  
    });
//Images
	$("a.lightbox_img").prettyPhoto({deeplinking: false});
	$(".gallery .gallery-item .gallery-icon a").attr('rel', 'prettyPhoto[mom_gal]');
	$(".gallery .gallery-item .gallery-icon a").prettyPhoto({deeplinking: false});
	$(".mom_gallery .gallery-item .gallery-icon a").prettyPhoto({deeplinking: false});

//fitvids
    $(".single_article_content").fitVids();

});