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
<section id="side">
<?php $barname = $GLOBALS["barname"]; ?>
<?php if( strlen($barname) > 0 ) : ?>
   <?php if( is_dynamic_sidebar('sidebar-'.$barname) ) : ?>
      <?php dynamic_sidebar('sidebar-'.$barname) ?>
   <?php endif; ?>
<?php endif; ?>
</section> <!-- #side -->
