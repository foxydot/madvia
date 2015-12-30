jQuery(document).ready(function($) {
	var numwidgets = $('.footer-widgets-1 div.widget').length;
	$('.footer-widgets-1').addClass('cols-'+numwidgets);
	$('.footer-widgets-1 .widget,.footer-widgets-1 .widget .widget-wrap').equalHeights();
	
	$('.carousel').carousel();
	var elem = $('#_' + window.location.hash.replace('#', ''));
    if(elem) {
         $.scrollTo(elem.left, elem.top);
    }
	$('#menu-primary-links>.menu-item>a').click(function(){
	    var scrollId = $(this).attr('href').replace('/','');
        $.scrollTo($(scrollId),{duration: 2000,easing:'swing',}); 
	});
});