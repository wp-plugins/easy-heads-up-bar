<?php
  
  	class EhuDB
  	{
        function EhuDB()
        {
         //NB Always set wpdb globally!
        	global $wpdb;
        	$plugin_prefix  = EHU_PREFIX;
        	$ehu_data       = $wpdb->prefix . $plugin_prefix . "data";
        	$ehu_stats      = $wpdb->prefix . $plugin_prefix . "stats";
        	
        	if (!$this->ehu_check_table_existance($ehu_data)) 
        	          $this->install_ehu_tables('data',$ehu_data);
        	if (!$this->ehu_check_table_existance($ehu_stats)) 
          	        $this->install_ehu_tables('stats',$ehu_stats);
          	        
          // Menu
          add_action('admin_init', array( $this, 'ehu_admin_init' ) );
      		add_action('admin_menu',  array( $this,'ehu_options_pages'  ) );
          // Ajax
          if(is_admin())
            add_action('wp_ajax_ehuAjax', array($this,'ehu_ajax'));
          
          // Front End
          if(!is_admin())
            add_action('init', array( $this, 'ehu_frontend_init' ) );
          
         
           
            
          add_action('wp_head', array( $this, 'ehu_frontend_js' ) );
          add_action('wp_ajax_nopriv_ehu_show_bar', array($this,'ehu_fe_show_bar'));
          add_action('wp_ajax_ehu_show_bar', array($this,'ehu_fe_show_bar'));
                    
        } // End Construct
        
        // =============
        // = Front End =
        // =============
        function ehu_frontend_init()
        {
          if(!is_admin())
            wp_register_script('ehuJs', EHU_URL.'js/ehu.js', array('jquery',),EHU_VERSION,true);
          if(!is_admin())
            wp_enqueue_script( 'ehuJs' );
        }
        
        function ehu_frontend_js()
        {
          $ehu_script = "var ajaxurl = '".admin_url('admin-ajax.php')."';";
          $ehu_script .= "var ehu_animate='toggle';";
          
          if ( is_front_page() ) {  // This is a homepage
            $ehu_script .= "var ehu_is_home_pg = '1';";
          } else {  // This is not a homepage
            $ehu_script .= "var ehu_is_home_pg = '0';";
          }
          
          echo("<script type=\"text/javascript\">$ehu_script</script>");
         
        }
        
        function ehu_fe_show_bar($i=1)
        {
          if($i>20){ exit; } // this is so we don't have ab infinite loop if there are no records
          $today = date("m/d/Y");
          
          if ( $_POST['home'] == '1' ) {
            $xtra = " AND show_where != 'interior'";
          }else{
            $xtra = " AND show_where != 'home'";
          }
          
          $xtra .= " ORDER BY RAND() LIMIT 0,1";
          $data = $this->ehu_bars_data('all',$xtra); 
          if(!$data) // check of data
          {
           exit;
          }else{ // show data
            //results
            $show_where = $data['show_where'];
            $start_date = $this->ehu_convert_dates($data['start_date']);
            $end_date   = $this->ehu_convert_dates($data['end_date']);
            
          // ===============
          // = Check dates =
          // ===============  
                if ($start_date != NULL ) {
                  if(!$this->ehu_compare_str_dates($start_date,$today,"g")){
                     $this->ehu_fe_show_bar(++$i);
                     exit;
                  }else{
                    #echo " SD Good To GO "; // debug
                  }
                }  // END Check Start Dates  
                
                if ($end_date != NULL ) {
                  if(!$this->ehu_compare_str_dates($end_date,$today,"l")){
                     $this->ehu_remove_expired($data['ehu_id']);
                     $this->ehu_fe_show_bar(++$i);
                     exit;
                  }else{
                    #echo " ED Good To GO "; // debug
                  }
                }  // END Check End Dates
            
            $ehu_id         =  $data['ehu_id'];
            $ehu_message    =  $data['message'];
            $ehu_link_text  =  $data['link_text'];
            $ehu_link_url   =  $data['link_url'];
            $ehu_options    =  json_decode($data['options'],true);
            
            $ehu_bgColor    =  $ehu_options['bgColor'];
            $ehu_textColor  =  $ehu_options['textColor'];
            $ehu_linkColor  =  $ehu_options['linkColor'];
            
            $ehu_bar_style  = "background-color: $ehu_bgColor;";
            $ehu_bar_style .= "color: $ehu_textColor;font-size: 14px;";
            $ehu_bar_style .= "font-family: Verdana,Geneva;";
            $ehu_bar_style .= "color: $ehu_textColor;";   
            $ehu_bar_style .= "border-bottom: 4px solid #FFFFFF;";
            $ehu_bar_style .= "box-shadow: 0 1px 5px 0 rgba(0, 0, 0, 0.5);";
            $ehu_bar_style .= "padding: 0px; text-align:center;";
            $ehu_bar_style .= "width: 100%;z-index: 1000;";
            $ehu_bar_style .= "font-weight: normal;height: 30px;line-height: 30px;";
            $ehu_bar_style .= "margin: 0;overflow: visible;position: relative;";    
                    
            $ehu_link_style = "color:$ehu_linkColor;";
            
            // open in new window
            $ehu_o_n_window = (isset($ehu_options['winTarget'])) ? $ehu_options['winTarget'] : 0 ;
            $ehu_target = ($ehu_o_n_window == 1 ) ? "target='_blank'" : "" ;
            $ehu_b_html  = '<div id="ehu_bar" style="'.$ehu_bar_style.'">';
            if( $ehu_message !='' || $ehu_message != null )
              $ehu_b_html .= '<span id="ehu_txt">'.$ehu_message.'</span>&nbsp;';
            if( ($ehu_link_text !='' || $ehu_link_text != null) && ($ehu_link_url !='' || $ehu_link_url != null)  )
              $ehu_b_html .= '<a id="ehu_link" style="'.$ehu_link_style.'" href="'.$ehu_link_url.'" '.$ehu_target.'>'.$ehu_link_text.'</a>';
            
            $ehu_b_html .= '</div>';
            
            echo($ehu_b_html);
            die('');
          }        
        } // End fun ehu_fe_show_bar()

        
        // Register ehu stylesheet
        function ehu_admin_init()
        {
		  		wp_register_script('ehuAdminJs', EHU_URL.'js/ehu_admin_js.js', array('jquery',),EHU_VERSION,true);
		  		wp_register_script('jquery-ui-datepicker', EHU_URL.'js/ui.datepicker.js', array('jquery-ui-core'),EHU_VERSION,true);
		  		wp_register_script('s-colorpicker', EHU_URL.'js/colorpicker.js', array('jquery'),EHU_VERSION,true);
		  		wp_register_script('ehuAdminAJAX', EHU_URL.'js/ehu_ajax.js', array('jquery'),EHU_VERSION,true);
		  		
		  		wp_register_style( 's-colorpicker', EHU_URL . 'css/colorpicker.css', false, EHU_VERSION,'all' );					
		  		wp_register_style( 'jquery-ui-datepicker', EHU_URL . 'css/ui.datepicker.css', false, EHU_VERSION,'all' );
		  		wp_register_style( 'ehuStylesheet', EHU_URL . 'css/stylesheet.css', false, EHU_VERSION,'all' );
		  							
        	$page = (isset($_GET['page'])) ? $_GET['page'] : false ;
          if ($page==false) return;
        	$ehu_page = explode("_", $page);
        	if( $ehu_page[0] == "ehu" )
        	{
        		if (is_admin() && isset($_GET['page']) && $_GET['page'] == 'ehu_newbar_pg') 
            {
              wp_enqueue_script('ehuAdminJs');
      				wp_enqueue_script('jquery-ui-datepicker');
      				wp_enqueue_script('s-colorpicker');
      				wp_enqueue_style('s-colorpicker');
      				wp_enqueue_style('jquery-ui-datepicker');
            }
        		wp_enqueue_style( 'ehuStylesheet' );
        		
        		// Ajax 
        		if (is_admin() && isset($_GET['page']) && $_GET['page'] == 'ehu_options_pg') {
      				wp_enqueue_script('ehuAdminAJAX');
        		}
  	  			
            
        	}
        
           // =============
           // = Ajax Hook =
           // =============
        
        }
     
        // Add menu to admin page
        function ehu_options_pages()
        {
        	add_menu_page(
        		EHU_NAME." &rsaquo; ".__('Options Page'), 
        		"Heads Up Bar", 'administrator', 
        		'ehu_options_pg', 
        		array( $this,'ehu_options_pg'  ),
        		( EHU_URL.'images/heads_up.png' )
        	);
        	add_submenu_page( 
           'ehu_options_pg', 
           EHU_NAME." &rsaquo; ".__('Bar Editor'), 
           __('Bar Editor'), 'administrator', 
           'ehu_newbar_pg', 
         	 array($this,'ehu_newbar_pg')
         	);
     
        }
        
        function ehu_options_pg()
        {
        	if (!current_user_can('manage_options'))  {
        		wp_die( __('You do not have sufficient permissions to access this page.') );
        	}
        	echo '<div class="wrap ehu">';
        	echo '<h2>'.EHU_NAME.' ~ '.__('General Settings').'</h2>';
        	
        	$the_data=$this->ehu_list_bars();
          $ehu_tb =  '<div class="tablenav top">
                        <span class="ehu-label">Bar Manager</span>
                        <a href="'.$_SERVER['PHP_SELF'].'?page=ehu_newbar_pg" class="right button-secondary">Add Heads Up Bar</a>
                      </div>';
          if( !$the_data )
          {
            echo $ehu_tb.'<div class="no-ehu-bars">You have no Heads up Bars add one now</div>';
          } //end if 
            else
          {
            echo $ehu_tb.$the_data;
          }
            echo ('<div class="ui-1-1">');
              
              echo ('<div class="left ui-1-3 ehu-info-box">');
                echo ('<h3 class="ehu-rss-title">'.__('Plugins News from BeforeSites').'</h3>');
        	      $this->ehu_rss();
        	      echo ( $this->ehu_hire_me() );
              echo ('</div>');
              
              echo ('<div class="left ui-1-3 ehu-info-box" style="margin-left:15px;">');
                #echo ('<h3 class="ehu-rss-title">'.__('Plugins News from BeforeSites').'</h3>');
        	      $this->ehu_donate();
              echo ('</div>');         
           
              echo ('<br class="css-c-f" />');
           echo ('</div>'); // end self promo
                        
        	echo ('</div>');
        }
        
        // New & edit Bar
        function ehu_newbar_pg()
        {
          if (!current_user_can('manage_options'))  {
        		wp_die( __('You do not have sufficient permissions to access this page.') );
        	}
     
          echo '<div class="wrap ehu" id="add_new_ehubar_page">';
        	echo '<h2>'.EHU_NAME.' ~ '.__('Add New').'</h2>';

          $ehu_save = (isset($_POST['save'])) ? $_POST['save'] : null ;
        	if (!isset($_POST['ehu_id']) && $ehu_save=="Save")
          {
            if (!$this->ehu_update_bar_data()) {
              echo "<div class='ehu_updated ehu_error'>".__("Update Failed")."</div>";
            }
            else
            {
              echo '<div class="ehu_updated">'
                    .__('New Bar Created, add another below if you like, or go back to the 
                    <a href="admin.php?page=ehu_options_pg">management screen</a>').'</div>';
            }
          }
          elseif(isset($_POST['ehu_id']) && $_POST['save']=="Save"){
            
            if (!$this->ehu_update_bar_data()) {
              echo "<div class='ehu_updated ehu_error'>".__("Update Failed")."</div>";
            }
            else
            {
               echo '<div class="ehu_updated">'
                      .__('Update has been made. Continue editing, or go back to the 
                      <a href="admin.php?page=ehu_options_pg">management screen</a>').'</div>';
            }
            
          }
          
          
          $get_ehu_id = null;
          if( isset($_GET['ehu_id']) ) {
            $ehu_id         = $_GET['ehu_id'];
            $ehu_bars_data  = $this->ehu_bars_data($ehu_id);
            $get_ehu_id     = "&ehu_id=$ehu_id";
            if(!$ehu_bars_data) wp_die("NO DATA");
          }
          
          echo('<form action="'.$_SERVER['PHP_SELF'].'?page=ehu_newbar_pg'.$get_ehu_id.'" method="post" id="add_new_ehubar_form"> ');
          if(!isset($_GET['ehu_id']) ) 
          {     
            // Form label
             $ehu_bar_title = __('New Bar Title:');
           // defaults for for Form
             $title			    =		__("Heads Up Bar");
             $message		    =		__("Easy Head Up Bar Text Add Your Message Here");
             $link_text 	  =		__("Link text");
             $link_url		  =		__("http://www.beforesite.com");
             $winTarget     =   0;
             $start_date	  =		'';
             $end_date	    =		'';
             $show_where	  =		__("home");
             $bgColor       =   "#DDDDDD";
             $textColor     =   "#333333";
             $linkColor     =   "#0092cc";
             $is_home       =   '';
             $is_int        =   '';
             $is_all        =   '';
            
            
            if($show_where=="home")       $is_home = "selected";
            if($show_where=="interior")   $is_int  = "selected";
            if($show_where=="all")        $is_all  = "selected";
     
            $show_where_selected  = '<option value="home"     '.$is_home.'>Home Page/Front Page</option>';
            $show_where_selected .= '<option value="interior" '.$is_int.'>Interior Page</option>';
            $show_where_selected .= '<option value="all"      '.$is_all.'>All Pages</option>';
          }
          else
          {
           // Form label
           $ehu_bar_title = __('Editing:');
           
           // defaults for for Form
           $title			  =	 $ehu_bars_data['title'];
           $message		  =	 $ehu_bars_data['message'];
           $link_text 	=	 $ehu_bars_data['link_text'];
           $link_url		=	 $ehu_bars_data['link_url'];
           $start_date	=	 $this->ehu_convert_dates($ehu_bars_data['start_date']);
           $end_date	  =	 $this->ehu_convert_dates($ehu_bars_data['end_date']);
           $show_where	=	 $ehu_bars_data['show_where'];
           $options     =  json_decode($ehu_bars_data['options'],true);
          
           

           $bgColor     =  $options['bgColor'];
           $textColor   =  $options['textColor'];
           $linkColor   =  $options['linkColor'];
           $is_home     =  "";
           $is_int      =  "";
           $is_all      =  "";
     
           if($show_where=="home")       $is_home = "selected";
           if($show_where=="interior")   $is_int  = "selected";
           if($show_where=="all")        $is_all  = "selected";
     
             $show_where_selected  = '<option value="home"     '.$is_home.'>Home Page/Front Page</option>';
             $show_where_selected .= '<option value="interior" '.$is_int.'>Interior Page</option>';
             $show_where_selected .= '<option value="all"      '.$is_all.'>All Pages</option>'; 
                   
          echo('<input type="hidden" name="ehu_id" value="'.$ehu_id.'" />');
          }
          

          // set up Prviewer's HTML
          $ehu_preview ='
            <span class="ehu-label">Preview</span>
    	      <div id="ehupreview">
              <div id="ehupreview_bar">
                <span id="ehu_pre_txt"></span>&nbsp;<a id="ehu_pre_link">link</a>
              </div>
    	      </div>
          '; // end Preview
          
          // TODO Clean this up!
          $win_target = (isset($options['winTarget'])) ? $options['winTarget'] : null ;
          
          // output page
          echo('
                  		    <div class="ui-1-1 ehu_bar_title">
                  		      <label for="ehu_title">'.$ehu_bar_title.'</label>           
                            <input type="text" 	name="title"       value="'.$title.'" id="ehu_title"/>
                            <br class="css-c-f" />
                          </div>
                          '.$ehu_preview.'            
                          
                          <label for="ehu_message">message, <em>limited to 85 Characters</em></label>
                    	       <input type="text" 			name="message"     value="'.$message.'" id="ehu_message"/><div id="ehu_message_charsLeft">0</div>
                          <div class="ui-1-1">
                            <div class="left ui-2-1">
                    	        <label for="ehu_link_text">     link text</label>       
                              <input type="text" 	name="link_text"   value="'.$link_text.'" id="ehu_link_text"/>
                            </div>
                            <div class="left ui-2-1">
                    	        <label for="ehu_link_url">      link url <em>'.__("Don't forget the http:// or https://").'</em></label>        
                              <input type="text" 	name="link_url"    value="'.$link_url.'" id="ehu_link_url"/>
                            </div>
                          </div>
                          <div class="ui-1-1">
                            <label>Open in new window:  <input type="checkbox" value="1" name="options[winTarget]" '.$win_target.'> </label>
                          </div>
                          <div class="ui-1-1">
                            <div class="ui-2-1 left">
                    	        <div class="ui-2-1 left">  
                    	          <label for="ehu_start_date">   start date</label>      
                                <input type="text" name="start_date"  value="'.$start_date.'" id="ehu_start_date"/>
                    	        </div>
                    	        <div class="ui-2-1 left">
                    	          <label for="ehu_end_date">     end date</label>        
                                <input type="text" name="end_date" value="'.$end_date.'" id="ehu_end_date"/>
                    	        </div>
                    	        <br class="css-c-f" />
                    	          <label for="show_where">Show bar on </label>
                                <select name="show_where" id="ehu_show_where">
                                  '.$show_where_selected.'
                                </select>
                            </div>
     
                            <div class="ui-2-1 left">
                                <ul class="tabs">
                                    <li><a href="#styles" class="active_tab" id="styles_tab">Heads Up Syles</a></li>
                                    <li><a href="#custom-colors" id="custom_colors_tab">Custom Color</a></li>
                                </ul>
                                <br class="css-c-f" />
                	              <div class="tab" id="styles">
                                  <a href="#nightClub" class="nightClub">Night Club</a>
                                  <a href="#coffeeCream" class="coffeeCream">Coffee Cream</a>
                                  <a href="#atTheBeach" class="atTheBeach">At The Beach</a>
                                  <a href="#springbok" class="springbok">Go Springboks!</a>
                                  <br class="css-c-f" />
                                </div>
                                <div class="tab hideTab" id="custom-colors">
                                  <span class="left"><label for="bgColor">Bar Color</label>        <input type="text" id="bgColor"   class="colors" name="options[bgColor]"   value="'.$bgColor.'" /></span>
                                  <span class="left"><label for="textColor">Text Color</label>     <input type="text" id="textColor" class="colors" name="options[textColor]" value="'.$textColor.'" /></span>
                                  <span class="left"><label for="linkColor">Link Color</label>     <input type="text" id="linkColor" class="colors" name="options[linkColor]" value="'.$linkColor.'" /></span>
                                  <br class="css-c-f" />                
                                </div>
                            </div>
     
                          </div>
                          <br class="css-c-f" />
                    	    <input type="submit" name="save" class="button-secondary" value="Save" id="ehu_save"/>
                  	');
                  	
        	echo('</form>');
        	
        	echo '</div>';
          
        }
        
        // =================
        // = EHU DATA BASE =
        // =================
        function ehu_bars_data($ehu_id=0,$extra=NULL) // function is used by editor and front end 
        {
         if($ehu_id===0 OR $ehu_id===NULL){
           wp_die("NO RECORD WAS REQUESTED");
         }
          global $wpdb;          
         	$ehu_where     =  "WHERE ehu_id = $ehu_id";
         	if($ehu_id =='all')
              $ehu_where     =  "WHERE active = 1 $extra";
          $plugin_prefix  =   EHU_PREFIX;
          $ehu_data       =   $wpdb->prefix . $plugin_prefix . "data";
          $sql            =  "SELECT * FROM $ehu_data $ehu_where";
          
          $result         =   mysql_query($sql);
          if (mysql_num_rows($result) == 0) return false;
          if($result) 
          {
              while ( $row = mysql_fetch_assoc($result) )
              {
          	  	$return['ehu_id']         =   $row['ehu_id'];
          	  	$return['title']          =   $row['title'];
          	  	$return['message']        =   $row['message'];
          	  	$return['start_date']     =   $row['start_date'];
          	  	$return['end_date']       =   $row['end_date'];
          	  	$return['show_where']     =   $row['show_where'];
          	  	$return['link_url']       =   $row['link_url'];
          	  	$return['link_text']      =   $row['link_text'];
          	  	$return['options']        =   $row['options'];
          	  	$return['active']         =   $row['active'];
              }
            return $return;
          }else{
            return false;
          }
        
        }
     
        function ehu_list_bars()
        {
         global $wpdb;
     
         	$order_by     =  "ORDER BY ehu_id DESC";
          $plugin_prefix  =   EHU_PREFIX;
          $ehu_data       =   $wpdb->prefix . $plugin_prefix . "data";
          $sql            =  "SELECT * FROM $ehu_data $order_by";
          $result         =   mysql_query($sql);
          if (mysql_num_rows($result) == 0) return false;
          if($result) 
          {
              $table = '<table border="0" cellpadding="0" cellspacing="0" class="ehu-list-table">';
              $table_head     =   "<thead>
                                    <tr>                           
          	                           <th>"  .__("Bar Title"). "</th>             
                                       <th>"  .__("Dates").     "</th>           
                                       <th>"  .__("Location").  "</th>           
                                       <th>"  .__("Active").    "</th>           
                                       <th>"  .__("Remove").    "</th>           
                                     </tr>
                                     </thead>";
                
              $table         .=   $table_head;
              
              while ( $row = mysql_fetch_assoc($result) )
              {
          	  	
          	  	$id             =   $row['ehu_id'];
          	  	$title          =   $row['title'];
          	  	$message        =   $row['message'];
          	  	
          	  	$start_date     =   $this->ehu_convert_dates($row['start_date']);          	  	  
          	  	$end_date       =   $this->ehu_convert_dates($row['end_date']);
          	  	
          	  	$show_where     =   $row['show_where'];	
          	  	$active         =   $row['active'];
          	  	$created_by     =   $row['created_by'];
          	    if($active == 1 ){
          	      $active = "on";
          	      $active_title = __("Deactivate");
          	    }elseif($active == 0){
          	      $active= "off";
          	      $active_title = __("Activate");
          	    }
          	  	if( $start_date != null && $end_date != null ){
          	  	  $start_date   =   $start_date . __(" thru");
          	  	}elseif($start_date == null && $end_date != null){
          	  	   $start_date   =   __("Bar Expires");
          	  	}elseif($start_date != null && $end_date == null){
             	  	   $start_date   =   __("Bar Starts on ").$start_date;
             	  	}
          	  	
          	  	$table         .=   "\n<tr>";
                  $table       .=   "\n<td class='ehu_bar_$active $id'><a href='admin.php?page=ehu_newbar_pg&amp;ehu_id=$id' title='$message'>$title</a></td>";
        	  	    $table       .=   "\n<td class='ehu_bar_$active $id'>$start_date $end_date</td>";
        	  	    $table       .=   "\n<td class='ehu_bar_$active $id'>Display on $show_where</td>";
        	  	    $table       .=   "\n<td class='ehu_bar_$active $id'><a href='#' id='$id-isactive' class='onoff $active' title='$active_title'>$active</a></td>";
        	  	    $table       .=   "\n<td class='ehu_bar_$active $id'><a href='#' id='$id-del' class='delete'>".__("Delete")."</a></td>";
          	  	$table         .=   "\n</tr>";
              }   
            $table .= "</table>";
            return $table;       
          
          }else {
            return false;
          }
        
        }
 	     
        function ehu_update_bar_data()
        {
          global $current_user;
         
          global $wpdb;
          $plugin_prefix  =   EHU_PREFIX;
          $ehu_data       =   $wpdb->prefix . $plugin_prefix . "data";
          
          $ehu_id         =   "NULL";
          $title          =   "";
          $message        =   "";
          $link_text      =   "";
          $link_url       =   "";
          $start_date     =   "";
          $end_date       =   "";
          $show_where     =   "";
          $options        =   "";
          $active         =   1;
          $created_by     =   $current_user->user_login;
          
          
          if ($_POST['save']=="Save") 
          {
            foreach($_POST AS $key => $value) 
            { 
              ${$key} = $value;
                if(${$key} !=="save" && $value !="")
                {
                    if(!is_array($value)){
                       ${$key} = $value;
                    }else{
                      ${$key} = json_encode($value);
                    } //end if
                }
               
            } // end for each
            
          }
         
          if ( !isset($_POST['ehu_id']) && $_POST['save']=="Save" )
          {
            $start_date = $this->ehu_convert_dates_mysql($start_date);
            $end_date   = $this->ehu_convert_dates_mysql($end_date);
            
            $sql = "INSERT INTO $ehu_data 
                    ( 
                      `ehu_id` ,
                      `title` ,
                      `message` ,
                      `link_text` ,
                      `link_url` ,
                      `start_date` ,
                      `end_date` ,
                      `show_where` ,
                      `options` ,
                      `active` ,
                      `created_by`
                     ) 
                    VALUES 
                    (
                       $ehu_id,
                      '$title',
                      '$message',
                      '$link_text',
                      '$link_url',
                      '$start_date',
                      '$end_date',
                      '$show_where',
                      '$options',
                       $active,
                      '$created_by'
                    )";
            $result =  mysql_query($sql);
            if(!$result){
              return false;
            }else{
              return true;
            }
          } // end  if ($_POST['save']=="Save")  
          elseif(isset($_POST['ehu_id']) && $_POST['save']=="Save")
          {
            $start_date = $this->ehu_convert_dates_mysql($start_date);
            $end_date   = $this->ehu_convert_dates_mysql($end_date);
            $sql = "UPDATE $ehu_data
             SET 
              `ehu_id`        =     $ehu_id,
              `title`         =    '$title',
              `message`       =    '$message',
              `link_text`     =    '$link_text',
              `link_url`      =    '$link_url',
              `start_date`    =    '$start_date',
              `end_date`      =    '$end_date',
              `show_where`    =    '$show_where',
              `options`       =    '$options',
              `active`        =     $active,
              `created_by`    =    '$created_by'
             WHERE ehu_id=$ehu_id";
             $result =  mysql_query($sql);
             if(!$result){
               return false;
             }else{
               return true;
             }
          }
        }
        
        function ehu_remove_expired($ehu_id)
        {
          // clean out the expired ones
          global $wpdb;
          $plugin_prefix  =   EHU_PREFIX;
          $ehu_data       =   $wpdb->prefix . $plugin_prefix . "data";
          $sql = "UPDATE $ehu_data SET `active`= 0 WHERE ehu_id=$ehu_id";
          $result =  mysql_query($sql);
        }
        
        // Lets check if the database has our table:
        function ehu_check_table_existance($new_table)
        {
          //NB Always set wpdb globally!
          global $wpdb;
     
          foreach ($wpdb->get_col("SHOW TABLES",0) as $table ){
           	if ($table == $new_table){
           		return true;
           	}
           }
          return false;
        }
        
        // End ehu_check_table_existance
        function install_ehu_tables($tb,$tb_name)
        {
          //NB Always set wpdb globally!
        	global $wpdb;
        
          //Table structure
          $ehu_data_sql = "CREATE TABLE $tb_name (
            ehu_id smallint(16) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            message varchar(255) NOT NULL,
            link_text varchar(255) NOT NULL,
            link_url varchar(255) NOT NULL,
            start_date varchar(255) NOT NULL,
            end_date varchar(255) NOT NULL,
            show_where varchar(255) NOT NULL,
            options longtext NOT NULL,
            active tinyint(1) NOT NULL,
            created_by varchar(255) NOT NULL,
            UNIQUE KEY ehu_id (ehu_id)
          ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
     
          $ehu_stats_sql = "CREATE TABLE $tb_name (
            id smallint(16) NOT NULL AUTO_INCREMENT,
            ehu_id smallint(16) NOT NULL,
            date varchar(255) NOT NULL,
            timestamp TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            actions varchar(255) NOT NULL, #impressions or clicks
            user_info longtext NOT NULL,
            UNIQUE KEY id (id)
          ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
          
          if($tb==='data')
            $ehu_sql = $ehu_data_sql;
          if($tb==='stats')
            $ehu_sql = $ehu_stats_sql;
     
          mysql_query($ehu_sql);
     
        } //end install_ehu_tables
        
        // =============
        // = AJAX  =====
        // =============
        function ehu_ajax()
        {
          if (!current_user_can('manage_options'))  {
        		wp_die( __('You do not have sufficient permissions to access this page.') );
        	}
         
          global $wpdb;
          $plugin_prefix  =   EHU_PREFIX;
          $ehu_data       =   $wpdb->prefix . $plugin_prefix . "data";
          
          
          $ehu_id = $_POST['id']['0'];
          $dowhat = $_POST['dowhat'];
          if( !isset($ehu_id) OR !isset($dowhat) ) die();
          if ($dowhat!="delete") {
            if ($dowhat=="activate")    $active = 1;
            if ($dowhat=="deactivate")  $active = 0;
            # code...
            $sql = "UPDATE $ehu_data SET `active`=$active WHERE ehu_id=$ehu_id";
            
          }
          if ($dowhat==="delete") {
            $sql = "DELETE FROM  $ehu_data WHERE ehu_id=$ehu_id";
          }
          
          $result =  mysql_query($sql);
          if(!$result){ return false; }else{  return true; }
          
        	die();
        }

        // ========================
        // = Self Promo Functions =
        // ========================
        function ehu_rss($ehu_feed='http://feeds.feedburner.com/EasySignUpPluginNews')
        {
          include_once(ABSPATH . WPINC . '/feed.php');

          // Get a SimplePie feed object from the specified feed source.
          $rss = fetch_feed($ehu_feed);// http://feeds.feedburner.com/easysignup
          if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly 
              // Figure out how many total items there are, but limit it to 5. 
              $maxitems = $rss->get_item_quantity(5); 

              // Build an array of all the items, starting with element 0 (first element).
              $rss_items = $rss->get_items(0, $maxitems); 
          endif;
          ?>
              <ul id="ehu-rss">
                  <?php if ($maxitems == 0) echo '<li>No items.</li>';
                  else
                  // Loop through each feed item and display each item as a hyperlink.
                  foreach ( $rss_items as $item ) : ?>
                  <li>
                      <a href='<?php echo $item->get_permalink(); ?>'
                      title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
                      <?php echo $item->get_title(); ?></a>
                  </li>
                  <?php endforeach; ?>
              </ul>
              <ul>
                <li>
                  <a href="http://feeds.feedburner.com/EasySignUpPluginNews" rel="alternate" type="application/rss+xml"><img src="http://www.feedburner.com/fb/images/pub/feed-icon16x16.png" alt="" style="vertical-align:middle;border:0"/></a>&nbsp;<a href="http://feeds.feedburner.com/EasySignUpPluginNews" rel="alternate" type="application/rss+xml">Subscribe in a reader</a></p>
                </li>
              </ul>
          <?php
          
        }
        
        function ehu_hire_me()
        {
           $return =  '<ul id="hire-rew"> 
            	          <li>
              	        <strong>Like this Plugin?</strong> 
              	        You can hire me<br>
              	        <strong><a href="http://www.greenvilleweb.us/services/?ref=ehu_plugin" title="Need WordPress Design? Themes and Plugins">Click Here For a List of Services</a></strong></li>
                       </ul>';
           
           return $return;
        }
        
        //Admin UI widgets/panels
      	function ehu_donate()
      	{ 
      		  #if( class_exists( 'EsuStyle' )) return;
      			?>
      			<!-- DONATE -->
      			  <h3><?php _e('Donate $5, $10, $20'); ?></h3> 
              <div class="ui-p-10">
      			    <ul>
      			      <li>This plugin has cost me many hours of work, if you use it, please donate a token of your appreciation!</li> 
      			      <li>
      			      	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" />
      							<input type="hidden" name="cmd" value="_s-xclick" />
      							<input type="hidden" name="hosted_button_id" value="EVDZXAPFS243J" />
      							<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donateCC_LG.gif" 
      							border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />
      							<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/scr/pixel.gif" width="1" height="1" />
      							</form>
      			      </li> 
      			    </ul>
      			  </div>
      			<!-- END DONATE -->
      		<?php 
      		}
        
        // ====================
        // = HELPER FUNCTIONS =
        // ====================
        function ehu_compare_str_dates($date_1,$date_2,$operation="g")
        {
         
          $date_1  = strtotime($date_1); 
          $date_2  = strtotime($date_2); 
          
          if($operation==='g') // greater then or == to 0
          {
             if ($date_1 <= $date_2) 
             {
              return true;
             } else {
             return false;
             }
          }
          if($operation==='l') // lesser then or == to 0
          {
              if ($date_1 >= $date_2)
              {
                
                #die("$date_1 <= $date_2");
                return true;
              } else {
                return false;
              }
           }
         
         
        }
        
        function ehu_convert_dates($date)
        {
          $date=trim($date);
          if ( $date ==="0000-00-00" || trim($date) =="" || $date == NULL || $date == null ) return null;
          
          $array = explode("-",$date);
          $date = $array['1']."/".$array['2']."/".$array['0'];
          return $date;
        }
        
        function ehu_convert_dates_mysql($date)
        {
        $date=trim($date);
        if($date==null || $date=='') return $date; 
        $array = explode("/",$date);
        $mysql_date = $array['2']."-".$array['0']."-".$array['1'];
        return $mysql_date;
        }
      
    } // End Class 