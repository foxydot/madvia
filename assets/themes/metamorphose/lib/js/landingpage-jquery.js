jQuery(document).ready(function($) {	
	var numwidgets = $('.landing-page-footer div.widget').length;
	$('.landing-page-footer').addClass('cols-'+numwidgets);
});