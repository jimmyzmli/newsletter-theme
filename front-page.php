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
?>
<?php

$opts = get_option("layout_opts");
$opts = is_array($opts) ? $opts : array();
$layout = is_array( $opts['layout'] ) ? $opts['layout'] : array();

$cats = get_cats($layout);

$posts = get_posts( array('meta_key' => 'post_banner_image') );
$slidelist = get_option( 'slide_opts' );
$slidelist = is_array($slidelist) ? $slidelist : array();

foreach( $posts as $i=>$post )
    if( in_array( $post->ID, $slidelist ) ) unset($posts[$i]);

$slides = array();
foreach( $slidelist as $i=>$postID ) {
  $post = get_post( $postID );
  $p = new stdClass;
  $catID = wp_get_post_categories( get_the_ID() )[0];
  $catID = is_numeric($catID) && $catID >= 0 ? $catID : 0;
  $catName = get_category( $catID )->name;
  
  setup_postdata( $post );
  $p->img = get_post_meta_img($postID,'slideshow');
  $p->post_ID = $postID;
  $p->title = get_the_title();
  $p->cat = $catName;
  $p->desc = get_the_excerpt();

  array_push( $slides, $p );
}
?>
<?php get_header(); ?>
<script type="text/javascript">
  var slidesInfo=<?=json_encode($slides)?>;
  jQuery(function($) {
      $(".slider").jslides( slidesInfo, {start:1} );
  });
</script>
<section id="body">
  <section id="main">
    <section id="top-promo" class="slider"></section>
    <?php foreach( $cats as $i=>$c ) : ?>
    <section class="news-promo">
      <h2><?=$c->name?></h2>
      <?php foreach( $c->tileInfo['tiles'] as $j=>$tile ) : ?>
      <section class="news-tile-<?=get_size_name($tile['size'])?>" style="height:<?=intval($tile['height'])*150?>px">
	<?php foreach( get_posts_for_cat($c->cat_ID,1) as $k=>$post ) : setup_postdata($post) ?>
	<section class="promo-story">
	  <img src="<?=get_the_post_thumbnail($p->id, array(300,200))?>" class="promo-img"/>
	  <div class="promo-title">
	    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
          </div>
	  <section class="promo-desc"><?php the_excerpt() ?></section>
	</section>
        <?php endforeach; wp_reset_postdata(); ?>
      </section>
      <?php endforeach; ?>
      <div style="clear:both"></div>
    </section>
    <?php endforeach; ?>
    <div style="clear:both"></div>
  </section> <!-- #main -->

  <section id="side">
    <section id="about">
      <h2>About <?=bloginfo('name')?></h2>
      <div class="desc">About here BLAH BLAH BLAH BLAH BLAH</div>
    </section>
    <?php get_sidebar() ?>
  </section> <!-- #side -->
<div style="clear:both"></div>
</section> <!-- #body -->
<div style="clear:both"></div>
</section>
<?php get_footer() ?>
