<?php global $ma; ?>
<div class="my_meta_control metabox">
  <?php $mb->the_field('show_comments'); ?>
  <p>
    <table>
      <tr>
	<td><label for="<?=$mb->get_the_name()?>" style="width:300px">Show Comment Section</label></td>
	<td><input type="checkbox" id="<?=$mb->get_the_name()?>" name="<?=$mb->get_the_name()?>" value="yes" <?=should_show_post(0,$mb->get_the_value()) ? 'checked="checked"' : ''?>/></td>
      </tr>
    </table>
  </p>  
</div>
