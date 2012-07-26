<?php
/*
	Copyright (c) 2012 Jimmy Li (JzL)

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
$theme = 'newsletter';
load_theme_textdomain( $theme, TEMPLATEPATH, '/languages' );
$locale = get_locale();
$locale_file = TEMPLATEPATH . '/languages/$locale.php';

if( is_readable($locale_file) )
  require_once( $locale_file );

/* Theme Supports */
add_theme_support('post-thumbnails');

/* Add setting pages */
add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

function theme_options_init() {
  register_setting( 'layout_opts', 'layout_opts', 'validate_layout_opts' );
}

function validate_layout_opts($opts) {
  $old = get_option('layout_opts');
  $layout = json_decode($opts['layout'],true);
  $hlayout = json_decode($opts['hidden_layout'],true);
  $opts = &$old;
  
  if( $layout !== null ) $opts['layout'] = $layout;
  if( $hlayout !== null ) $opts['hidden_layout'] = $hlayout;
  
  return $opts;
}

function theme_options_add_page() {
  //add_utility_page( "Theme Options", "Theme Options", "edit_theme_options", "theme_utility_menu", "theme_options_do_page", "none");
  add_menu_page( __( 'Theme Options', $theme ), __( "Theme Options", $theme), 'edit_theme_options', 'theme_main_options', "theme_main_menu_render" );
  add_submenu_page( "theme_main_options", __("Layout",$theme), __("Layout",$theme), "edit_theme_options", "theme_layout_options", "theme_layout_menu_render");
}

function theme_main_menu_render() {
  echo "HI";
}

function theme_layout_menu_render() {
  include "layout_setting.php";
}

function get_page_number() {
  $p = get_query_var('paged');
  if( $p ) {
    printf( ' | %s%s', __('Page', $theme), $p );
  }
}

/* [ {id: cat_ID, tiles: [{tileI},{tileII},] }, ] */
function get_cats( $layout ) {
  $cats = get_categories( array('hide_empty'=>0) );
  $m = array();
  foreach( $layout as $i=>$c ) {
    foreach( $cats as $j=>$cc ) {
      if( $cc->cat_ID == $c['id'] ) {
	$cc->tileInfo = $c;
	array_push( $m, $cc );
	unset( $cats[$j] );
	break;
      }
    }
  }
  return $m;
}

function cat_inverse() {
  $args = func_get_args();
  $idlst = get_all_category_ids();
  $m = array();
  foreach( $args as $n=>$a )
    if( is_array($a) ) {
      foreach( $a as $i=>$c ) {
	$k = array_search( $c->cat_ID, $idlst );
	if( $k >= 0 ) unset( $idlst[$k] );
      }
    }
  foreach( $idlst as $i=>$id ) {
    $c = get_category( $id );
    $c->tileInfo = array( 'tiles' => array() );
    array_push( $m, $c );
  }
  return $m;
}

function &cat_kill_dup( &$a ) {
  $done = array();
  foreach( $a as $i=>$c )
    if( in_array($c->cat_ID,$done) )
      unset($a[$i]);
    else
      array_push($done,$c->cat_ID);
  return $a;
}

function get_size_name($n) {
  return $n==1 ? "full" : ($n==2 ? "half" : ($n==4 ? "quart" : "any"));
}

/* POST VIEWS COUNT */
/* Add it to a column in WP-Admin */
add_filter('manage_posts_columns', 'posts_column_views');
add_action('manage_posts_custom_column', 'posts_custom_column_views',5,2);
function posts_column_views($defaults){
    $defaults['post_views'] = __('Views');
    return $defaults;
}
function posts_custom_column_views($column_name, $id){
	if($column_name === 'post_views'){
        echo getPostViews(get_the_ID());
    }
}

/*
  View Counter System
  Thanks to wp-snippets.com
  source: http://wp-snippets.com/post-views-without-plugin/
*/
/* function to display number of posts. */
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}

/* function to count views. */
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

/*
add_filter('manage_posts_columns', 'posts_column_views');
add_action('manage_posts_custom_column', 'posts_custom_column_views',5,2);
function posts_column_views($defaults){
    $defaults['post_views'] = __('Views');
    return $defaults;
}
function posts_custom_column_views($column_name, $id){
if($column_name === 'post_views'){
        echo getPostViews(get_the_ID());
    }
}
*/


/*
  Portable Layout Management System (PLMS)
  CopyRight JzL (c) 2012
  All rights Reserved
  
  Data structure for layout:

  Unit : <"em" | "px" | "%">
  
  box : {
    type : <"root" | "tile" | "box">
    w: <n>
    h: <n>
    uw: <x>    #Unit for w
    uh: <x>    #Unit for h
    children: [ box, box, box, ... ]
  }

  #Wordpress specific types
  box.type == "tile" : {
    func: <"cat" | "widget">
    id : <n> #An ID to identify the resource involved    
  }

*/

function print_layout_html( $box, &$p = array() ) {
  static $ignore_prop = array( "w", "h", "children" );

  $class = is_string($p['box_class']) ? $p['box_class'] : "box";
  $size = ""; $margin = "";
    
  $tabc = &$p['tab_count'];
  $tabc = is_numeric($tabc) && $tabc > 0 ? $tabc : 0;
  $tabstr = str_repeat("\t", $tabc++);
  
  /* Check if box data is valid */
  if( is_array($box) ) {
    if( !is_string($box['type']) || strlen($box['type']) <= 0 )
      $box['type'] = "box";
    extract( $box, EXTR_PREFIX_ALL, "b" );
  } else {
    return;
  }

  /* Set size if not container */
  if( count($b_children) == 0 ) {
    $size = sprintf('width:%s;height:%s', $b_w, $b_h);
  }

  /* Set classname */
  if( $b_type != "box" ) {
    $c_name = $b_type."_class";
    if( is_string($p[$c_name]) )
      $class = "$class ".$p[$c_name];
  }

  /* Print out element */
  if( $b_type != "root" ) {
    ob_start();
    printf("$tabstr<section class='$class' style='$size' />\n");
    foreach( $box as $k=>$v ) {
      /* And each property */
      if( in_array($k,$ignore_prop) || is_array($v) ) continue;
      printf("$tabstr\t<input type='hidden' name='%s' value='%s'/>\n", $k, $v);
    }
    /* Custom callbacks to display whatever else is needed */
    $c_name = $b_type."_cb";
    $r = false;
    if( is_callable($p[$c_name]) ) {
      $r = $p[$c_name]( $box, $p );
      if( is_string($r) ) print $r;
      print "\n";
    }
    /* Output buffered info */
    if( $r !== false ) {
      ob_end_flush();
    }
  }

  /* Process Children */
  if( is_array( $b_children ) )
    foreach( $b_children as $i=>$c ) {
      print_layout_html( $c, $p );
    }

  /* Terminate element */
  printf("%s</section>\n", str_repeat("\t", --$tabc));
}

function layout_map( $layout, $cb, $args = array() ) {
  if( is_callable($cb) ) {
    array_unshift($args,$layout);
    calL_user_func_array($cb,$args);
    array_shift($args);
  } else {
    return;
  }

  if( is_array( $layout['children'] ) )
    foreach( $layout['children'] as $i=>$k )
      layout_map( $k, $cb, $args );
}

function get_empty_cat_ids( $layout, &$list = null ) {
  if( $list === null ) $list = get_all_category_ids();
  layout_map( $layout, create_function( '$a,&$b',
					'$i = array_search( $a["id"], $b );'.
					'if( $a["type"] == "tile" &&'.
					'    $a["func"] = "cat" &&'.
					'    is_numeric($i) && $i >= 0 ) {'.
					'  unset( $b[$i] );}'
					), array(&$list) );
  return $list;
}


?>
