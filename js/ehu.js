jQuery(document).ready(function($) {
	var $ehuBar = $('div#ehu-bar');
	var $ehuBarLinks = $('div#ehu-bar a');
	// Check the bar is here
	if (typeof $ehuBar.html() !== 'undefined') {
		$ehuBar.remove();
		$linkColor = $ehuBar.attr('data-bar-link-color');
		$barLocatoin = $ehuBar.attr('data-bar-location');
		
		if ($barLocatoin=='top') {
			$ehuBar.prependTo('body');
		}else{
					$ehuBar.appendTo('body');
		};
		$ehuBarLinks.css({'color':$linkColor});

		// close button
		var $dhuCloseButton = $('#ehu-close-button');
		if (typeof $dhuCloseButton.html() !== 'undefined') {
			var $dhuCloseButtonPosition = $dhuCloseButton.position();
			
			// Open button
			var $dhuOpenButton = $('#ehu-open-button');
			if ($barLocatoin=='top') 
			{
				$dhuOpenButton.css({
					'top': $dhuCloseButtonPosition.top,	
				});
			}else{
				$dhuOpenButton.css({
					'bottom': $dhuCloseButtonPosition.bottom,	
				})
			};
			$dhuOpenButton.css({
				'right': $dhuCloseButtonPosition.right,	
			});

			// hide action
			$dhuCloseButton.click(function() {
			  $( this ).parent().slideUp( "100", function() {
			    	$dhuOpenButton.css({
							'visibility': 'visible'	
						});
						// Set Cookie
						ehuCreateCookie('ehuBarStatus','hidden', 7);
			  });
			});

			// hide action
			$dhuOpenButton.click(function() {
				// Unset Cookie
				ehuEraseCookie('ehuBarStatus');
				$dhuOpenButton.css({'visibility': 'hidden'	});
			  $ehuBar.slideDown( "slow", function() {
					if ($barLocatoin=='top') 
					{
						window.scrollTo(0,0);
					}else{
						window.scrollTo(0,$dhuCloseButtonPosition.top);
					};
			  });
			});

			// Check the Cookie
			var $ehuCookie = ehuReadCookie('ehuBarStatus');

			if($ehuCookie == 'hidden') {
				$dhuCloseButton.trigger( "click" );
			}

		}; // end check for button
	}; // end Check for bar
});

// Cookie script care of these great guys http://www.quirksmode.org/js/cookies.html

function ehuCreateCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function ehuReadCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function ehuEraseCookie(name) {
	ehuCreateCookie(name,"",-1);
}

// EOF