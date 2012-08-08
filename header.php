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

if( is_single() ) {
  $title = single_post_title("",false);
}else if( is_home() || is_front_page() ) {
  $title = get_bloginfo('name') . '|' . get_bloginfo('description');
}else if( is_search() ) {
  $title = sprintf( 'Search Results for %s - %d ', wp_specialchars($s), get_page_number() );
}else if( is_category() ) {
  $title = wp_title('',false);
}else if( is_404() ) {
  $title = get_bloginfo('name') . ' | 404 - Not found';
}else if( is_page() ) {
  $title = get_bloginfo('name') . '|' . get_bloginfo('description');
}else {
  $title = get_bloginfo('name') . wp_title('|',false) . get_page_number();
}

if( is_singular() ) {
  wp_enqueue_script( 'comment-reply' );
}

$tname = get_theme_template_name();

$navmenu_opts = array(
		      'container_id'=>'nav-bar1',
		      'container_class'=>'nav-bar-horizontal',
		      'menu_class'=>'clearfix'
		      );

?>
<!DOCTYPE html>
<html <?php language_attributes() ?>>
  <head>
    <!-- Meta tags -->
    <meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>" charset="<?php bloginfo('charset') ?>"/>
    <title><?php echo $title?></title>
    <!-- Miscellaneous Wordpress stuff -->
    <link rel="alternative" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="News"/>
    <link rel="alternative" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="Comments"/>
    <link rel="pingback" href="<?php bloginfo('pingback_url') ?>"/>
    <!-- Theme Style Loading -->
    <link href="<?php bloginfo('stylesheet_url') ?>" rel="stylesheet"/> <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo $prefix?>/slideshow.css"/>
    <?php if( file_exists(get_template_directory()."/".get_theme_template_name().".css") ) : ?>
    <link rel="stylesheet" href="<?php echo $prefix.'/'.get_theme_template_name().'.css'?>"/>
    <?php endif; ?>
    <!-- Script Loading -->
    <script type="text/javascript" src="<?php echo $prefix?>/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $prefix?>/js/jquery-slides.min.js"></script>
    <?php if( get_meta_option('misc_opts', 'marquee_info_bar') ) : ?><script type="text/javascript" src="<?php echo $prefix?>/js/jquery-marquee.min.js"></script><?php endif; ?>
    <script type="text/javascript" src="<?php echo $prefix?>/js/weather.js"></script>
    <script type="text/javascript" src="<?php echo $prefix?>/utils.js"></script>
    <script type="text/javascript" src="<?php echo $prefix?>/header.js"></script>
    <!-- Dnymically generated scripts/styles -->
    <script type="text/javascript">
      /* Generate JS based on settings */
      jQuery(function($) {
	  <?php if( get_meta_option('misc_opts', 'marquee_info_bar') ) : ?>
	  $(".info-bar").marquee();
	  <?php endif; ?>
	  <?php if( get_meta_option('misc_opts', 'show_weather_bar') ) : ?>
	      
	  var loc = get_weather( function( loc ) {
	      $(".weather-bar")
		  .append( $("<span>").text( loc.city + ", " + loc.region ).css("margin-right", "10px") )
		  .append( $("<span>").text( loc.country ).css("margin-right", "10px") )
		  .append( $("<span>").html( loc.temp + "&deg;C" + "/" + loc.humidity + "% humidity" ) );	      
	  });
	  <?php endif; ?>
	      
      });
    </script>
    <style type="text/css">
      #page { background: <?php echo get_meta_option("misc_opts","bg_colour") ?>; }
      
      #top-nav,
      header .nav-bar-horizontal a,
      header .nav-bar-horizontal {
	  background: <?php echo get_meta_option("misc_opts","menu_colour"); ?>;      
	  color: <?php echo get_meta_option("misc_opts","menu_font_colour"); ?>;      
      }

      #side .widgettitle {
	  background: <?php echo get_meta_option('misc_opts','widgettitle_bg') ?>;
      }

      #side li.widget {
	  background: <?php echo get_meta_option('misc_opts','widget_bg') ?>;
      }

      <?php
        $fs = get_meta_option('misc_opts','tiles_font_size');
        $lc = get_meta_option('misc_opts','tiles_lines_per_post');
      ?>
      #main .tile .promo-desc {
	  font-size: <?php echo $fs ?>px;
	  line-height: <?php echo $fs ?>px;
	  height: <?php echo $fs*$lc ?>px;
      }

      #main .news-promo {
	  background: <?php echo get_meta_option('misc_opts','tiles_bg_colour') ?>;
      }

      #main .tile .promo-title a {
	  background: <?php echo get_meta_option('misc_opts','tiles_title_bg'); ?>;
      }

      #main .tile .promo-title a:hover {
	  background: <?php echo get_meta_option('misc_opts','tiles_title_hover_bg') ?>;
      }      
      
      /* Custom styles */
      <?php
	 global $allowed_custom_styles;
	 echo get_meta_style( 'global' );
	 if( isset($allowed_custom_styles[$tname]) ) echo get_meta_style( $tname );
      ?>
      
    </style>
    <?php wp_head(); ?>
  </head>
  <body id="page">
    <div id="page-bg">
      <div class="seg1"></div>
      <div class="seg2"></div>
      <div class="seg3"></div>
    </div>
    <header>
      <?php if( ($himg=get_header_image())!="" || ($clr=get_header_textcolor())!="blank" ) : ?>
      <a id="top-banner" href="<?php echo site_url()?>">
	<?php if( $himg != "" ) : ?><img src="<?php echo $himg?>" class="site-header-img" alt="<?php bloginfo('name') ?>"/><?php endif; ?>
	<?php if( $himg == "" && $clr!="blank" ) : ?><div style="color:#<?php echo $clr ?>" class="site-header-text"><?php bloginfo('name') ?></div><?php endif; ?>
      </a>
      <?php endif; ?>            
      <nav id="top-nav">
<!--
	<a id="branding" href="<?php echo site_url()?>">
	  <img alt="<?php echo get_bloginfo('name')?>" src="http://placehold.it/200x32"/>
	</a>
-->
  <?php wp_nav_menu( array_merge( array("theme_location"=>"primary_menu",'walker'=>new TopMenuWalker(6),"fallback_cb"=>'output_cat_nav_menu'), $navmenu_opts) ); ?>
	<div id="search-box">
	  <?php get_search_form(); ?>
	</div>
	<div style="clear:both"></div>
      </nav>
      <nav class="nav-bar-horizontal clearfix" id="nav-bar1-expand"></nav>
  <?php wp_nav_menu( array_merge( array("theme_location"=>"secondary_menu",'walker'=>new TopMenuWalker(1000),"fallback_cb"=>'output_page_nav_menu'), $navmenu_opts) ); ?>
      <div class="info-bar">
	<span style="color:white;background:red;"><?php echo get_meta_option('misc_opts','global_msg') ?></span>
        <span class="weather-bar" style="background:gainsboro"></span>
      </div>
    </header>
