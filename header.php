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

$prefix = get_template_directory_uri();
$barname = null;
if( is_single() ) {
  $title = single_post_title();
  $barname = "single";
}else if( is_home() || is_front_page() ) {
  $title = get_bloginfo('name') . '|' . get_bloginfo('description');
  $barname = "landing";
}else if( is_search() ) {
  $title = sprintf( 'Search Results for %s - %d ', wp_specialchars($s), get_page_number() );
  $barname = "search";
}else if( is_404() ) {
  $title = get_bloginfo('name') . ' | 404 - Not found';
}else {
  $title = get_bloginfo('name') . wp_title('|') . get_page_number();
}
/* Exported names */
$GLOBALS["barname"] = $barname;

if( is_singular() ) {
  wp_enqueue_script( 'comment-reply' );
}

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
    //$out .= '</ul>';
  }
}

$navmenu_opts = array(
		      'container_id'=>'nav-bar1',
		      'container_class'=>'nav-bar-horizontal',
		      'menu_class'=>'clearfix'
		      );


function output_cat_nav_menu() {
  global $navmenu_opts;
  $out = "";
  $k = new TopMenuWalker(4);
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
<!DOCTYPE html>
<html <?php language_attributes() ?>>
  <head>
    <meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>" charset="<?php bloginfo('charset') ?>"/>
    <title><?=$title?></title>
    <link rel="alternative" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="News"/>
    <link rel="alternative" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="Comments"/>
    <link rel="pingback" href="<?php bloginfo('pingback_url') ?>"/>
    <!-- Theme Style Loading -->
    <link href="<?php bloginfo('stylesheet_url') ?>" rel="stylesheet"/> <!-- Main Stylesheet -->
    <?php if( is_single() ) : ?><link rel="stylesheet" href="<?=$prefix?>/single.css"/><?php endif; ?>
    <!-- Script Loading -->
    <script type="text/javascript" src="<?=$prefix?>/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?=$prefix?>/utils.js"></script>    
    <script type="text/javascript" src="<?=$prefix?>/header.js"></script>    
    <?php wp_head(); ?>
  </head>
  <body id="page">
    <div id="page-bg">
      <div class="seg1"></div>
      <div class="seg2"></div>
      <div class="seg3"></div>
    </div>
    <header>
      <a id="top-banner" href="<?=site_url()?>">
	<img src="http://placehold.it/960x125"/>
      </a>
      <nav id="top-nav">
	<a id="branding" href="<?=site_url()?>">
	  <img alt="<?=get_bloginfo('name')?>" src="http://placehold.it/200x32"/>
	</a>
  <?php wp_nav_menu( array_merge( array("theme_location"=>"primary_menu",'walker'=>new TopMenuWalker(4),"fallback_cb"=>'output_cat_nav_menu'), $navmenu_opts) ); ?>
	<div id="search-box">
	  <?php get_search_form(); ?>
	</div>
	<div style="clear:both"></div>
      </nav>
      <nav class="nav-bar-horizontal clearfix" id="nav-bar1-expand"></nav>
  <?php wp_nav_menu( array_merge( array("theme_location"=>"secondary_menu",'walker'=>new TopMenuWalker(1000),"fallback_cb"=>'output_page_nav_menu'), $navmenu_opts) ); ?>
      <div class="info-bar">
	Hey I just met you, and this is crazy... but the server's down, for maintance baby
      </div>
    </header>
