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
<?php global $ma; ?>
<div class="my_meta_control metabox">
  <?php $mb->the_field('show_comments'); ?>
  <p>
    <table>
      <tr>
	<td><label for="<?php echo $mb->get_the_name()?>" style="width:300px">Show Comment Section</label></td>
	<td><input type="checkbox" id="<?php echo $mb->get_the_name()?>" name="<?php echo $mb->get_the_name()?>" value="yes" <?php echo should_show_comments(0,$mb->get_the_value()) ? 'checked="checked"' : ''?>/></td>
      </tr>
    </table>
  </p>  
</div>
