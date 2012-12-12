<?php
/*
Plugin Name: Easy Heads Up Bar
Plugin URI: http://www.beforesite.com/plugins/easy-heads-up-bar
Description: An Easy to use notification (heads up) bar for your WordPress website with a linked call to action
Version: 0.4
Author: Greenweb
Author URI: http://www.greenvillweb.us 
*/

/**
 * Copyright (c) 2011 Greenville Web Design. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

  // ehu_ is the Easy Heads Up Bar prefix 
  
  if (!function_exists ('add_action')){
  	header('Status: 403 Forbidden');
  	header('HTTP/1.1 403 Forbidden');
  	exit();
  }
   
  $ehu_plugin_loc         = plugin_dir_url( __FILE__ );
  $ehu_plugname           = "Easy Heads Up Bar";
  $ehu_plug_shortname     = "easy_heads_up_bar";
  $ehu_the_web_url        = get_bloginfo('url');
  $ehu_the_blog_name      = get_bloginfo('name');
  $ehu_the_default_email  = get_bloginfo('admin_email');
  
  // check for ssl 
  if ( preg_match( '/^https/', $ehu_plugin_loc ) && !preg_match( '/^https/', get_bloginfo('url') ) )
  	$ehu_plugin_loc = preg_replace( '/^https/', 'http', $ehu_plugin_loc );
  
  define( 'EHU_FRONT_URL',      $ehu_plugin_loc );
  
  define( 'EHU_URL',            plugin_dir_url(__FILE__) );
  define( 'EHU_PATH',           plugin_dir_path(__FILE__) );
  define( 'EHU_BASENAME',       plugin_basename( __FILE__ ) );
  
  define( 'EHU_NAME',           $ehu_plugname );
  define( 'EHU_S_NAME',         $ehu_plug_shortname );
  define( 'EHU_VERSION',        '1.0' );
  define( 'EHU_PREFIX' ,        "ehu_");
  
  // WP_BLOG_NAME & WP_URL is somthing I'ld like to see in WordPress
  // heck they may just add them so lets add the -> if ! defined statment
  if ( ! defined('WP_BLOG_NAME') )
  	define( 'WP_BLOG_NAME', $ehu_the_blog_name );
  if ( ! defined('WP_URL') )
  	define( 'WP_URL', $ehu_the_web_url );
  
  include 'lib.php';
  
  $ehu_run = new EhuDB();
  
  register_activation_hook( __FILE__, 'ehu_activate' );

  function ehu_activate()
  {
    
  }
  