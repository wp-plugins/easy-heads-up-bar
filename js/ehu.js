// hello
jQuery(document).ready(function($) {
	var data = {
		action: 'ehu_show_bar',
		home: ehu_is_home_pg
	};
	jQuery.post(ajaxurl, data, function(response) {
	    jQuery('body').prepend(response);
	    if(ehu_animate === 'toggle'){
	      jQuery('#ehu_bar').css('display', 'none');
	      jQuery('#ehu_bar').slideToggle('fast');
	    }
	    
	});
});