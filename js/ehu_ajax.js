  (function($){ 
       var ehuid = 0;
       
       
       $('.onoff').click(function() {
         if ($(this).hasClass('on')) {
           $(this).removeClass('on').addClass('off');
           ehuid = $(this).attr('id').split("-",1);
            $('td.'+ehuid).removeClass('ehu_bar_on').addClass('ehu_bar_off');
            // do some Ajax here
            
            var data = {
               action:  'ehuAjax',
               id:       ehuid,
               dowhat:   'deactivate'
             };
             // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
             $.post(ajaxurl, data, function(response) {
                // alert('Got this from the server: ' + response);
             });
            
            
         }else{
           $(this).removeClass('off').addClass('on');
           ehuid = $(this).attr('id').split("-",1);
           $('td.'+ehuid).removeClass('ehu_bar_off').addClass('ehu_bar_on');
           // do some Ajax here
           
           var data = {
              action:  'ehuAjax',
              id:       ehuid,
              dowhat:   'activate'
            };
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.post(ajaxurl, data, function(response) {
               // alert('Got this from the server: ' + response);
            });
           
         };
       });
       
       $('.delete').click(function(){
           ehuid = $(this).attr('id').split("-",1);
           
           if(disp_confirm()==true)
           {
             var data = {
                 action:  'ehuAjax',
                 id:       ehuid,
                 dowhat:   'delete'
               };
             $.post(ajaxurl, data, function(response) {
                // alert('Got this from the server: ' + response);
             });
             
             $('td.'+ehuid).fadeOut('slow');
             // do some Ajax here
             
           }
           
         });
       
  })(jQuery);
  
  
  function disp_confirm()
  {
  var r=confirm("Are you sure? \nThis can't be undone.")
  if (r==true)
    {
    return true;
    }
  else
    {
    return false;
    }
  }