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


class FeaturedWidget extends WP_Widget {
  private static $INFO = array(
    'classname'=>'FeaturedWidget',
    'description'=>'Displays Top Featured Posts'
  );
    
  function __construct() {
    parent::__construct( self::$INFO['classname'], 'Featured Widget', self::$INFO );
  }
  
  function form( $c ) {
    $p_show = wp_parse_args( (array)$c, array( 'show'=>3 ) );
    if( strlen($this->errmsg) > 0 ) printf('<p style="color:red">%s</p>',$this->errmsg);
    printf('<label for="%1$s">Number of posts shown:</label>'.
	   '<input type="text" id="%1$s" name="%2$s" value="%3$d"/>',
	   $this->get_field_id('show'), $this->get_field_name('show'), esc_attr($p_show['show']) );
  }

  function update( $new_c, $old_c ) {
    $c = $old_c;
    $this->errmsg = "";
    if( is_numeric($new_c['show']) )
      if( $new_c['show'] >= 0 && $new_c['show'] <= 30 )
	$c['show'] = $new_c['show'];
      else
	$this->errmsg = "Post number in unreasonable bounds";
    else $this->errmsg = 'Not a numeric value';
    return $c;
  }

  function widget( $args, $c ) {
    extract( $args, EXTR_OVERWRITE );
    extract( $c, EXTR_PREFIX_ALL, 'p' );
    if( $p_show <= 0 ) return;
  ?>
<section class="featured">
  <h2><?=$before_title.(empty($title)?"Featured":$title).$after_title?></h2>
  <?php
    echo $before_widget;
    global $post;
    foreach( get_featured_posts( array( 'numberposts'=>$p_show, 'rollup_days'=>1 ) ) as $i=>$post ) {
      setup_postdata($post);
      $thumb = get_post_meta_img(get_the_ID(),'featured_thumb');
      foreach( wp_get_post_categories(get_the_ID()) as $cat_ID )
	$thumb = strlen($thumb) > 0 ? $thumb : get_cat_meta_img($cat_ID,'featured_thumb');
    ?>
      <article class="promo-story">
	<img src="<?=$thumb?>" class="promo-img"/>
	<section class="promo-title">
	  <a href="<?php the_permalink()?>"><?=get_the_title()?></a>
	</section>
	<section class="promo-desc">
	  <?php the_excerpt() ?>
	</section>
	<div style="clear:both"></div>
      </article>
   <?php } wp_reset_postdata(); ?>
    </section>
   <?php
    echo $after_widget;
  }
  
}

?>
