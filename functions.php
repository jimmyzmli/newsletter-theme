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

defined("ABSPATH") || exit;

/* Register Admin stuff */
require_once("meta.php");

/* Custom Widget integerated with the theme */
require_once("widgets/featured_widget.php");
require_once("widgets/twitter_widget.php");

$theme = 'newsletter';
load_theme_textdomain( $theme, TEMPLATEPATH, '/languages' );
$locale = get_locale();
$locale_file = TEMPLATEPATH . '/languages/$locale.php';

if( is_readable($locale_file) )
  require_once( $locale_file );

/* Theme Supports */
//add_theme_support('post-thumbnails');
add_theme_support( 'custom-header' );

/* Add setting pages */
add_action( 'widgets_init', 'theme_custom_widget_init' );

register_sidebar( array(
  'id' => 'sidebar-front-page',
  'description' => __('The sidebar for the landing page')
));
register_sidebar( array(
  'id' => 'sidebar-single',
  'description' => __('The sidebar for a single post view')
));


register_nav_menu( 'primary_menu', 'The main menu at top' );
register_nav_menu( 'secondary_menu', 'A secondary menu' );


add_image_size( 'slideshow', 450, 260 , false );
add_image_size( 'featured_thumb', 70, 70, true );

function theme_custom_widget_init() {
  register_widget("FeaturedWidget");
  register_widget("TwitterWidget");
}

/* Add to WP-Admin post listing */

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
  These functions create custom queries to select "featured" posts.
  The algorithm is simple: For all posts this N-days, arrange by views
*/
function create_query_function( $str ) {
  return create_function( '$q',
			  'global $wpdb;'.
			  '$rplcmnts = array( "wp_" => $wpdb->prefix, "\${q}" => $q );'.
			  '$str = '.json_encode("".$str).';'.
			  'foreach( $rplcmnts as $t=>$r ) $str = str_replace($t, $r, $str);'.
			  /*'var_dump($str);'.*/
			  'return $str;'
  );
}

function get_featured_posts( $args ) {
  static $f_join = null;
  $f_orderby = null; 

  $custom_def = array( 'rollup_days' => 1 );
  $args = wp_parse_args( $args, $custom_def );
  extract( $args, EXTR_PREFIX_ALL, 'p' );  

  /* Ignore stupid cases */
  if( $p_numberposts <= 0 ) return array();

  /* Parse Special Arguments */
  if( isset($p_category) && !is_numeric($p_category) && is_string( $p_category ) )
    $args['category'] = get_cat_ID( $p_category );
  
  /* We don't want our custom keys to be mixed up with the ones we pass to get_posts() */
  foreach( array_keys($custom_def) as $k ) if( isset($args[$k]) ) unset($args[$k]);

  /* Create our lambdas */
  if( !is_callable( $f_orderby ) )
    $f_orderby = create_query_function(
      'YEAR(wp_posts.post_date_gmt),'.
      sprintf('DAYOFYEAR(wp_posts.post_date_gmt)-MOD(DAYOFYEAR(wp_posts.post_date_gmt),%d) desc,', intval($p_rollup_days)).
      'cast(wp_postmeta.meta_value as SIGNED) desc,'.
      '${q}'
    );
  if( !is_callable( $f_join ) )
    $f_join = create_query_function( '${q} LEFT JOIN wp_postmeta on (wp_postmeta.meta_key=\'post_views_count\' and wp_postmeta.post_id=wp_posts.ID)' );

  add_filter('posts_orderby', $f_orderby);
  add_filter('posts_join', $f_join);

  $args = array_merge( array( 'post_type'=>'post', 'suppress_filters' => false ), $args );
  $posts = get_posts( $args );    

  remove_filter('posts_orderby', $f_orderby);
  remove_filter('posts_join', $f_join);

  return $posts;

}

function get_page_number() {
  $p = get_query_var('paged');
  if( $p ) {
    printf( ' | %s%s', __('Page', $theme), $p );
  }
}

/*
  This is the very simple Category-Tiles tiling system implemented for WP.
  Format (In JSON): [ {id: cat_ID, tiles: [{tileI},{tileII},] }, ]
*/
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

/* COUNT POST VIEWS */

/* function to display number of posts. */
function getPostViews($postID){
  $count_key = 'post_views_count';
  $count = get_post_meta($postID, $count_key, true);
  if($count==''){
    delete_post_meta($postID, $count_key);
    add_post_meta($postID, $count_key, '0');
    return 0;
  }
  return $count;
}

/* function to count views. */
function increPostViews($postID) {
  $count_key = 'post_views_count';
  $count = get_post_meta($postID, $count_key, true);
  if($count==''){
    $count = 1;
    delete_post_meta($postID, $count_key);
    add_post_meta($postID, $count_key, "$count");
  }else{
    $count++;
    update_post_meta($postID, $count_key, $count);
  }
  return $count;
}




?>
