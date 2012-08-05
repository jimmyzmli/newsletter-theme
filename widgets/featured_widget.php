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

class FeaturedWidget extends WP_Widget {
  private static $INFO = array(
    'classname'=>'featured_widget',
    'description'=>'Displays Top Featured Posts'
  );
    
  function __construct() {
    parent::__construct( self::$INFO['classname'], 'Featured Widget', self::$INFO );
  }
  
  function form( $c ) {
    if( strlen($this->errmsg) > 0 ) printf('<p style="color:red">%s</p>',$this->errmsg);
    widget_form( $this, $c, array(
      'show'=>array('default'=>3,'type'=>'number','label'=>'Number of posts you wish to show'),
      'rollup_days'=>array('default'=>1,'type'=>'number','label'=>'Round down date to nearest N days'),
      'lines'=>array('default'=>4,'type'=>'number','label'=>"Number of lines to show"),
      'font_size'=>array('default'=>16,'type'=>'number','label'=>'Font Size')
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
    /* Check font/line count */
    if( is_numeric($n_lines) && $n_lines >= 0 ) $c['lines'] = $n_lines; else $this->errmsg = "Teach me how to show negative number of lines, please.";
    if( is_numeric($n_font_size) && $n_font_size >= 3 ) $c['font_size'] = $n_font_size; else $this->errmsg = "Font too small";
    return $c;
  }

  function widget( $args, $c ) {
    extract( $args, EXTR_OVERWRITE );
    extract( $c, EXTR_PREFIX_ALL, 'p' );
    if( $p_show <= 0 ) return;
    echo $before_widget;
  ?>
<section class="featured">
  <?php echo $before_title.(empty($title)?"Featured":$title).$after_title?>
  <?php    
    global $post;
    foreach( get_featured_posts( array( 'numberposts'=>$p_show, 'rollup_days'=>$p_rollup_days ) ) as $i=>$post ) {
      setup_postdata($post);
      $thumb = get_post_thumb( get_the_ID() );
    ?>
      <article class="promo-story">
	<img src="<?php echo $thumb?>" class="promo-img"/>
	<section class="promo-title">
	  <a href="<?php the_permalink()?>"><?php echo get_the_title()?></a>
	</section>
	<section class="promo-desc" style="font-size:<?php echo $p_font_size?>px;line-height:<?php echo $p_font_size?>px;height:<?php echo $p_lines*($p_font_size+0.25)?>px;">
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
