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

?>
<?php get_header() ?>
<section id="body">
  <section id="main">
    <h1 class="page-title"><?php printf( __( 'Search Results for: %s'), '<span>' . wp_specialchars(get_search_query()) . '</span>' ); ?></h1>
    <?php if( have_posts() ) : $count = 0; ?>
    <?php while( have_posts() ) : $count++; the_post() ?>
    <div class="promo-story clearfix">
      <a href="<?php echo get_permalink(get_the_ID())?>"><img src="<?php echo get_post_thumb( get_the_ID() )?>" class="promo-img" style="width:70px;height:70px;"/></a>
      <h1><a href="<?php echo get_permalink(get_the_ID())?>" class="promo-title"><?php echo get_the_title()?></a></h1>
      <div class="promo-desc"><?php echo get_the_excerpt()?></div>
    </div>
    <?php endwhile; ?>
    <nav>
      <?php previous_posts_link(__("Last Page"),10); ?>
      <?php if( $count == get_meta_option('misc_opts','search_ppp') ) next_posts_link(__("Next Page"),10); ?>
    </nav>
    <?php else: ?>
    <div class="error">No search results!</div>
    <?php endif; ?>
  </section>
  <?php get_sidebar() ?>
  <div style="clear:both"></div>
</section>
<?php get_footer() ?>
