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
    if( strlen($this->errmsg) > 0 ) printf('<p style="color:red">%s</p>',$this->errmsg);
    widget_form( $this, $c, array(
      'show'=>array('default'=>3,'type'=>'number','label'=>'Number of posts you wish to show'),
      'rollup_days'=>array('default'=>1,'type'=>'number','label'=>'Round down date to nearest N days')
    ));
  }

  function update( $new_c, $old_c ) {
    $c = $old_c;
    $this->errmsg = "";
    extract( $new_c, EXTR_PREFIX_ALL, 'n' );
    /* Check post number */
    if( is_numeric($n_show) )
      if( $n_show >= 0 && $n_show <= 30 )
	$c['show'] = $n_show;
      else
	$this->errmsg = "Post number in unreasonable bounds";
    else $this->errmsg = 'Not a numeric value';
    /* Check rollup day */
    if( is_numeric($n_rollup_days) && $n_rollup_days >= 1 )
      $c['rollup_days'] = $n_rollup_days;
    else
      $this->errmsg = "Can only round to nearest postive integer day";
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
    foreach( get_featured_posts( array( 'numberposts'=>$p_show, 'rollup_days'=>$p_rollup_days ) ) as $i=>$post ) {
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
