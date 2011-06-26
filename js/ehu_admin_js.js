jQuery(document).ready(function() {
  var ehu_message = jQuery("input#ehu_message").attr("value");
  jQuery('#ehu_pre_txt').text(ehu_message);

  jQuery("#ehu_message").keyup(function() {
    ehu_message = jQuery("input#ehu_message").attr("value");
    jQuery('#ehu_pre_txt').text(ehu_message);
  });

  jQuery("#ehu_message").focusout(function() {
    ehu_message = jQuery("input#ehu_message").attr("value");
    jQuery('#ehu_pre_txt').text(ehu_message);
  });
 
  var ehu_link_text = jQuery("input#ehu_link_text").attr("value");
  jQuery('#ehu_pre_link').text(ehu_link_text);
  
  jQuery("#ehu_link_text").keyup(function() {
    ehu_link_text = jQuery("input#ehu_link_text").attr("value");
    jQuery('#ehu_pre_link').text(ehu_link_text);
  });
  
  jQuery("#ehu_link_text").focusout(function() {
    ehu_link_text = jQuery("input#ehu_link_text").attr("value");
    jQuery('#ehu_pre_link').text(ehu_link_text);
  });
  
  jQuery('#ehu_message').limit('90','#ehu_message_charsLeft');
  jQuery('#ehu_link_text').limit('25');
  
  jQuery('#textColor').ColorPicker({
  			onSubmit: function(hsb, hex, rgb, el) {
  				jQuery(el).val(hex);
  				jQuery(el).ColorPickerHide();
  			},
  			onBeforeShow: function () {
  				jQuery(this).ColorPickerSetColor(this.value);
  			},
  			onChange: function ( hsb, hex, rgb,el ) {
  				jQuery('#textColor').attr('value','#' + hex);
  				jQuery('#textColor').css('backgroundColor', '#' + hex);
  				jQuery('#ehu_pre_txt').css('color', '#' + hex);
  			}
  })
  .bind('keyup', function(){
  			jQuery(this).ColorPickerSetColor(this.value);
  });
  
  jQuery('#linkColor').ColorPicker({
  			onSubmit: function(hsb, hex, rgb, el) {
  				jQuery(el).val(hex);
  				jQuery(el).ColorPickerHide();
  			},
  			onBeforeShow: function () {
  				jQuery(this).ColorPickerSetColor(this.value);
  			},
  			onChange: function ( hsb, hex, rgb,el ) {
  				jQuery('#linkColor').attr('value','#' + hex);
  				jQuery('#linkColor').css('backgroundColor', '#' + hex);
  				jQuery('#ehu_pre_link').css('color', '#' + hex);
  			}
  })
  .bind('keyup', function(){
  			jQuery(this).ColorPickerSetColor(this.value);
  });
  
  jQuery('#bgColor').ColorPicker({
  			onSubmit: function(hsb, hex, rgb, el) {
  				jQuery(el).val(hex);
  				jQuery(el).ColorPickerHide();
  			},
  			onBeforeShow: function () {
  				jQuery(this).ColorPickerSetColor(this.value);
  			},
  			onChange: function ( hsb, hex, rgb,el ) {
  				jQuery('#bgColor').attr('value','#' + hex);
  				jQuery('#bgColor').css('backgroundColor', '#' + hex);
  				jQuery('#ehupreview_bar').css('backgroundColor', '#' + hex);
  			}
  })
  .bind('keyup', function(){
  			jQuery(this).ColorPickerSetColor(this.value);
  });
  
   var bgColor    =   jQuery('#bgColor').attr('value');
   var linkColor  =   jQuery('#linkColor').attr('value');                                                                     
   var textColor  =   jQuery('#textColor').attr('value');                                                                      
   
  jQuery('#bgColor').css('backgroundColor',   bgColor   );
  jQuery('#linkColor').css('backgroundColor', linkColor );
  jQuery('#textColor').css('backgroundColor', textColor );
  
  jQuery('#ehupreview_bar').css('backgroundColor', bgColor  );
  jQuery('#ehu_pre_txt').css('color',               textColor);
  jQuery('#ehupreview_bar a').css('color',         linkColor);
  
  
});


jQuery(function()
  {
		var dates = jQuery( "#ehu_start_date, #ehu_end_date" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3,
			onSelect: function( selectedDate ) {
				var option = this.id == "ehu_start_date" ? "minDate" : "maxDate",
					instance = jQuery( this ).data( "datepicker" ),
					date = jQuery.datepicker.parseDate(
						instance.settings.dateFormat ||
						jQuery.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	
	(function($){ 
       $.fn.extend({  
           limit: function(limit,element) {
  			var interval, f;
  			var self = $(this);

  			$(this).focus(function(){
  				interval = window.setInterval(substring,100);
  			});

  			$(this).blur(function(){
  				clearInterval(interval);
  				substring();
  			});

  			substringFunction = "function substring(){ var val = $(self).val();var length = val.length;if(length > limit){$(self).val($(self).val().substring(0,limit));}";
  			if(typeof element != 'undefined')
  				substringFunction += "if($(element).html() != limit-length){$(element).html((limit-length<=0)?'0':limit-length);}"

  			substringFunction += "}";

  			eval(substringFunction);

  			substring();

          } 
      }); 
  })(jQuery);
  
  (function($){ 
    $("#custom_colors_tab").click(function() {
     // Act on the event
     $("#styles_tab").removeClass('active_tab');
     $("#custom_colors_tab").addClass('active_tab');
     $("#styles").addClass('hideTab');
     $("#custom-colors").removeClass('hideTab');
    });
    $("#styles_tab").click(function() {
     // Act on the event
     $("#custom_colors_tab").removeClass('active_tab');
     $("#styles_tab").addClass('active_tab');
     $("#custom-colors").addClass('hideTab');
     $("#styles").removeClass('hideTab');
    });
    
    /* preset styles */
    $('.nightClub').click(function() {
      $('#bgColor').attr('value','#000000').css('backgroundColor', '#000000');
      $('#linkColor').attr('value','#00ff73').css('backgroundColor', '#00ff73');
      $('#textColor').attr('value','#FFF').css('backgroundColor', '#FFF');

      $('#ehupreview_bar').css('backgroundColor', '#000000');
      $('#ehu_pre_txt').css('color', '#FFF');
      $('#ehupreview_bar a').css('color', '#00ff73');
    });
    $('.coffeeCream').click(function() {
      //Act on the event
      $('#bgColor').attr('value','#6D3712').css('backgroundColor', '#6D3712');
      $('#linkColor').attr('value','#FFDA82').css('backgroundColor', '#FFDA82');
      $('#textColor').attr('value','#EDE3CB').css('backgroundColor', '#EDE3CB');
     
      $('#ehupreview_bar').css('backgroundColor', '#6D3712');
      $('#ehu_pre_txt').css('color', '#EDE3CB');
      $('#ehupreview_bar a').css('color', '#FFDA82');
      
    });
    $('.atTheBeach').click(function() {
      // Act on the event
      $('#bgColor').attr('value','#00A0B0').css('backgroundColor', '#00A0B0');
      $('#linkColor').attr('value','#f7ff00').css('backgroundColor', '#f7ff00');
      $('#textColor').attr('value','#fff').css('backgroundColor', '#fff');
     
      $('#ehupreview_bar').css('backgroundColor', '#00A0B0');
      $('#ehu_pre_txt').css('color', '#FFF');
      $('#ehupreview_bar a').css('color', '#f7ff00');
      
    });
    $('.springbok').click(function() {
      // Act on the event
      $('#bgColor').attr('value','#006400').css('backgroundColor', '#006400');
      $('#linkColor').attr('value','#E8B663').css('backgroundColor', '#E8B663');
      $('#textColor').attr('value','#FFF').css('backgroundColor', '#FFF');
     
      $('#ehupreview_bar').css('backgroundColor', '#006400');
      $('#ehu_pre_txt').css('color', '#FFF');
      $('#ehupreview_bar a').css('color', '#E8B663');
      
    });
      
  })(jQuery);