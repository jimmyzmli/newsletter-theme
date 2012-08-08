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

ini_set('display_errors', 'On');

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
  'name'=>'Front Page Sidebar',
  'id' => 'sidebar-front-page',
  'description' => __('The sidebar for the landing page')
));
register_sidebar( array(
  'name'=>'Single Post Sidebar',
  'id' => 'sidebar-single',
  'description' => __('The sidebar for a single post view')
));
register_sidebar( array(
  'name'=>'Archive (Category, Search) Sidebar',
  'id' => 'sidebar-archive',
  'description' => __('Sidebar for archive views')
));

register_nav_menu( 'primary_menu', 'The main menu at top' );
register_nav_menu( 'secondary_menu', 'A secondary menu' );


add_image_size( 'slideshow', 450, 260 , false );
add_image_size( 'featured_thumb', 70, 70, true );

function theme_custom_widget_init() {
  register_widget("FeaturedWidget");
  register_widget("TwitterWidget");
}

/* Template name remapping */

$tname_map = array(
  'post' => 'single',
  'page' => 'page',
  'category' => 'archive',
  'search'=>'archive'
);
$current_template = null;

add_filter( 'template_include', create_function('$t','global $current_template; $current_template = basename($t); remove_filter("template_include",__FUNCTION__); return $t;'), 1000 );

function get_theme_template_name( $name = null ) {
  global $current_template, $tname_map;

  if( $name === null ) $name = $current_template;

  if( strtolower(end(explode('.',$name))) == 'php' )
    $name = substr($name,0,-4);

  if( isset($tname_map[$name]) ) $name = $tname_map[$name];

  return $name;
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
  global $wpdb;

  $str = str_replace('$','\$',json_encode($str));
  $rplcmnts = array( "wp_" => $wpdb->prefix, '\${q}' => '$q' );
  foreach( $rplcmnts as $t=>$r ) $str = str_replace($t, $r, $str);
  return create_function( '$q', 'return '.$str.';' );
}

function get_featured_posts( $args ) {
  global $wpdb;
  static $f_join = null;
  $f_orderby = null; $f_where = null; 

  $custom_def = array( 'rollup_days' => 1, 'exclude_posts' => "" );
  $args = wp_parse_args( $args, $custom_def );
  extract( $args, EXTR_PREFIX_ALL, 'p' );

  /* Ignore stupid cases */
  if( $p_numberposts <= 0 ) return array();

  /* Parse Special Arguments */
  if( isset($p_category) && !is_numeric($p_category) && is_string( $p_category ) )
    $args['category'] = get_cat_ID( $p_category );
  $p_exclude_posts = array_filter( explode(',',$p_exclude_posts), create_function('$a','return strlen(trim($a)) > 0;') );
  
  /* We don't want our custom keys to be mixed up with the ones we pass to get_posts() */
  foreach( array_keys($custom_def) as $k ) if( isset($args[$k]) ) unset($args[$k]);

  /* Create our lambdas */
  if( is_array($p_exclude_posts) && count($p_exclude_posts) > 0 && !is_callable($f_where) )
    $f_where = create_query_function( '${q} AND post_ID NOT IN ('.$wpdb->escape(implode(',',$p_exclude_posts)).')' );
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
  if( is_callable($f_where) ) add_filter('posts_where',$f_where);

  $args = array_merge( array( 'post_type'=>'post', 'post_status'=>'publish', 'suppress_filters' => false ), $args );
  $posts = get_posts( $args );    

  if( is_callable($f_where) ) remove_filter('posts_where',$f_where);
  remove_filter('posts_orderby', $f_orderby);
  remove_filter('posts_join', $f_join);

  return $posts;

}

/*
  Here's a little form generating function for (simple) widget forms
  Form is in the format of:
  array( 'field_name' => array('label'=>'Label Text', 'type'=>'Input Type', 'default'=>'Default value') )
  The user of this function have no power over the styling.
  There is _no_garentee_ that the DOM will stay the same.
*/
function widget_form( &$widget, &$c, $form ) {
  static $script_loaded = false;
  /* Load Script Dependencies */
  if( !$script_loaded ) {
    $script_loaded = true;    
    wp_enqueue_script('farbtastic');
    wp_enqueue_style('farbtastic');    
    print
<<<HTML
    <script type="text/javascript">jQuery(function(\$){\$('.cw-color-picker').each(function(){\$(this).farbtastic('#' + \$(this).attr('rel'));});});</script>
HTML;
  }
  
  foreach( $form as $k=>$info ) {
    extract( $info, EXTR_PREFIX_ALL, 'p' );
    if( !isset($c[$k]) && isset($p_default) ) $c[$k] = $p_default;
    printf( "<label for=\"%1\$s\">$p_label</label><br/><input type=\"$p_type\" id=\"%1\$s\" name=\"%2\$s\" value=\"%3\$s\" %4\$s/><br/>",
	    $widget->get_field_id($k),
	    $widget->get_field_name($k),
	    esc_attr( is_array($c[$k]) ? implode(':',$c[$k]) : $c[$k] ),
	    $p_type == 'checkbox' && $c[$k]===true ? 'checked="checked"' : ''
    );

    /* Special cases */
    if( $p_type == 'colour' ) {
      printf( '<div class="cw-color-picker" rel="%1$s" onload="jQuery(this).farbtastic(\'#%1$s\');"></div>', $widget->get_field_id($k) );
    }
    
  }
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
  if( isset($_REQUEST['preview']) ) return $count;  
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







/**
   Prints out Custom header structure.
   With the help of header.js, it creates the header of the theme.
**/

class TopMenuWalker extends Walker_Nav_Menu {
  private $i = 0, $limit = 1;
  public function __construct( $n ) {
    $this->limit = $n-1;
  }
  function start_el( &$out, $item, $depth ) {
    if( $depth == 0 ) $this->i++;
    if( $depth == 0 && $this->i == $this->limit+1 )
      $out .= '<li id="nav-expand-btn"><a href="#">More</a></li>';    
    $out .= sprintf( '<li%3$s><a href="%2$s">%1$s</a>',
		     esc_attr($item->title),
		     esc_attr($item->url),
		     ($this->i > $this->limit && $depth == 0) ?
		     ' style="display:none" class="hidden-nav-section" ' : '');
  }

  function start_lvl( &$out, $depth = 0 ) {
    $out .= sprintf('<ul%s>', ($depth >= 0) ? ' class="nav-dropmenu" ' : '') ;
  }

  function end_lvl( &$out, $depth = 0 ) {
    $out .= '<div style="clear:both"></div>'.'</ul>';
  }
}

function output_cat_nav_menu() {
  global $navmenu_opts;
  $out = "";
  $k = new TopMenuWalker(6);
  $header_cat = get_all_category_ids();
  
  $k->start_lvl( $out, -1 );
  foreach( $header_cat as $i=>$cat_ID ) {
    $item = new stdClass;
    $c =  get_category( $cat_ID );
    $item->url = get_category_link($cat_ID);
    $item->title = $c->name;
    $k->start_el( $out, $item, 0 );
    $k->end_el( $out, $item, 0 );    
  }
  $k->end_lvl( $out, -1 );
  print '<div class="nav-bar-horizontal" id="nav-bar1"><ul class="clearfix">'.$out.'</ul></div>';
}

function output_page_nav_menu() {
  $out = "";
  $k = new TopMenuWalker(10000);
  $pagelist = get_pages();
  $k->start_lvl( $out, -1 );
  foreach( $pagelist as $i=>$p ) {
    $item = new stdClass;
    $item->url = get_permalink( $p->ID );
    $item->title = $p->post_title;
    $k->start_el( $out, $item, 0 );
    $k->end_el( $out, $item, 0 );    
  }
  $k->end_lvl( $out, -1 );
  print '<div class="nav-bar-horizontal" id="nav-bar2">'.$out.'</div>';
}



?>
