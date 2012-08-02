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
<?php get_header() ?>
<section id="body" class="clearfix">
  <section id="main">
    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
    <span>Viewed <?=increPostViews(get_the_ID());?> Times</span>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <?php if ( is_front_page() ) { ?>
      <h2 class="entry-title"><?php the_title(); ?></h2>
      <?php } else { ?>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php } ?>
      
      <div class="entry-content">
        <?php the_content(); ?>
        <?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:' ), 'after' => '</div>' ) ); ?>
        <?php edit_post_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>
      </div><!-- .entry-content -->
    </div><!-- #post-## -->
    <?php comments_template( '', true ); ?>
    <?php endwhile; // end of the loop. ?>
  </section>
</section>
<?php get_footer() ?>
