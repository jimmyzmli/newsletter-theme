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
  $catID = wp_get_post_categories( get_the_ID() );
  $catID = $catID[0];
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
  var slidesInfo=<?php echo json_encode($slides)?>;
  jQuery(function($) {
      $(".slider").jslides( slidesInfo, {start:1} );
  });
</script>
<section id="body">
  <section id="main">
    <section id="top-promo" class="slider"></section>
    <?php foreach( $cats as $i=>$c ) : ?>
    <section class="news-promo">
      <h2><?php echo $c->name?></h2>
      <?php foreach( $c->tileInfo['tiles'] as $j=>$tile ) : $i = 0; ?>
      <section class="news-tile-<?php echo get_size_name($tile['size'])?> clearfix" style="height:<?php echo 110+intval($tile['height'])*30?>px">
	<?php
		global $post;
		foreach( get_featured_posts( array( 'category'=>$c->cat_ID, 'numberposts'=>intval($tile['height']) ) ) as $k=>$post ) :
		setup_postdata($post); $i++;
		$imgh = round(1/$tile['size'] * 260);
	?>
	<section class="promo-story">
	  <div class="promo-title">
	    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
          </div>
	  <section class="promo-desc" <?php echo ($i==1?'style="padding-bottom: '.$imgh.'px;"':'');?>>
		<?php if( $i==1 ) : ?>
	   	<img src="<?php echo get_post_thumb(get_the_ID())?>" class="promo-img" style="height:<?php echo $imgh ?>PX;"/>
		<?php endif; ?>
	  	<?php the_excerpt() ?>
	  </section>
	</section>
        <?php endforeach; wp_reset_postdata(); ?>
      </section>
      <?php endforeach; ?>
      <div style="clear:both"></div>
    </section>
    <?php endforeach; ?>
    <div style="clear:both"></div>
  </section> <!-- #main -->
  <?php get_sidebar() ?>
<div style="clear:both"></div>
</section> <!-- #body -->
<div style="clear:both"></div>
</section>
<?php get_footer() ?>
