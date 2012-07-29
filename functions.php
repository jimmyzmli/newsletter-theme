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

include("featured_widget.php");

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
add_action( 'widgets_init', 'theme_custom_widget_init' );
register_sidebar( array(
			'id' => 'sidebar-landing',
			'description' => __('The sidebar for the landing page')
			));

function theme_options_init() {
  register_setting( 'layout_opts', 'layout_opts', 'validate_layout_opts' );
}

function theme_custom_widget_init() {
  register_widget("FeaturedWidget");
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


?>
