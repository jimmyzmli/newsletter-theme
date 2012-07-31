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

function get_posts_for_cat( $cat, $n ) {
  if( !is_numeric($cat) )
    if( is_string( $cat ) ) 
      $cat = get_cat_ID( $cat_name );
  if( is_numeric($cat) ) {
    $args = array(
		  'category' => intval($cat),
		  'numberposts' => $n,
		  'orderby' => 'post_view_count',
		  'order' => 'ASC'
		  );
    $posts = get_posts( $args );
    return $posts;
  }else {
    return array();
  }
}

class FeaturedWidget extends WP_Widget {
  private static $INFO = array(
			       'classname'=>'FeaturedWidget',
			       'description'=>'Displays Top Featured Posts'
			       );
    
  function __construct() {
    parent::__construct( self::$INFO['classname'], 'Featured', self::$INFO );
  }
  
  function form( $c ) {
    $c = wp_parse_args( (array)$c, array( 'show'=>3 ) );
    printf('<label for="%1$s">Number of posts shown:</label>'.
	   '<input type="widefat" id="%1$s" name="%1$s" value="%2$s"/>',
	   $this->get_field_id('show'), attribute_escape($c['show']) );
  }

  function update( $new_c, $old_c ) {
    $c = $old_c;
    $c['show'] = $new_c['show'];
    return $c;
  }

  function widget( $args, $c ) {
    ?>
    <section class="featured">
      <h2>Featured</h2>
      <?php foreach( get_posts_for_cat('General',2) as $i=>$post ) : setup_postdata($post) ?>
      <article class="promo-story">
	<img src="http://placehold.it/69x69" class="promo-img"/>
	<section class="promo-title">
	  <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
	</section>
	<section class="promo-desc">
	  <?php the_excerpt() ?>
	</section>
	<div style="clear:both"></div>
      </article>
      <?php endforeach; wp_reset_postdata(); ?>
    </section>
   <?php
  }
  
}

?>

<?php
/*
<section class="updates">
      <h2>Updates</h2>
      <article class="promo-story">
	<section class="promo-author">JzL</section>
	<section class="promo-date">Mon Jul 23</section>		
	<section class="promo-desc">TWITT TWITT. PFFTOOOOT.</section>
      </article>
      <article class="promo-story">
	<section class="promo-author">JzL</section>
	<section class="promo-date">Mon Jul 23</section>		
	<section class="promo-desc">TWITT TWITT. PFFTOOOOT.</section>
      </article>
    </section>

    <section class="survey-bar">
      <h2>Surveys</h2>
      ABCDEFG
    </section>
*/
?>
