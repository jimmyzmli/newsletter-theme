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

function nws_get_title() {
  if( is_single() ) {
    single_post_title();
  }else if( is_home() || is_front_page() ) {
    bloginfo('name');
    print ' | ';
    bloginfo('description');
    get_page_number();
  }else if( is_search() ) {
    printf( 'Search Results for %s - ', wp_specialchars($s) );
    get_page_number();
  }else if( is_404() ) {
    bloginfo('name');
    print ' | 404 - Not found';
  }else {
    bloginfo('name');
    wp_title('|');
    get_page_number();
  }
}

if( is_singular() ) {
  wp_enqueue_script( 'comment-reply' );
}

?>
<!DOCTYPE html>
<html <?php language_attributes() ?>>
  <head>
    <link href="<?php bloginfo('stylesheet_url') ?>" rel="stylesheet"/>
    <meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>" charset="<?php bloginfo('charset') ?>"/>
    <title><?php nws_get_title(); ?></title>
    <?php wp_head() ?>
    <link rel="alternative" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="News"/>
    <link rel="alternative" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="Comments"/>
    <link rel="pingback" href="<?php bloginfo('pingback_url') ?>"/>
  </head>
  <body>
    <header>
      <div id="top-banner">
	<img src="noimg"/>
      </div>
      <nav id="top-nav">
	<div id="branding">
	  <?php bloginfo('name') ?>
	</div>
	<ul class="nav-bar-horizontal">
	  <li>News</li>
	  <li>News</li>
	  <li>News</li>
	  <li>More...</li>
	</ul>
	<div id="search-box">
	  <input type="text"/>
	</div>
	<div style="clear:both"></div>
      </nav>
      <div class="info-bar">
	Hey I just met you, and this is crazy... but the server's down, for maintance baby
      </div>
    </header>