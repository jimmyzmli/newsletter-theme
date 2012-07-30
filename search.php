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
<section id="body">
  <section id="main">
    <h1 class="page-title"><?php printf( __( 'Search Results for: %s'), '<span>' . get_search_query() . '</span>' ); ?></h1>
    <?php while( have_posts() ) : the_post() ?>
    <h1><a href="<?=get_permalink(get_the_ID())?>"><?=get_the_title()?></a></h1>
    <?php endwhile; ?>
  </section>
  <section id="side">
  </section>
  <div style="clear:both"></div>
</section>
<?php get_footer() ?>
