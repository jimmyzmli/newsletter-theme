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
<script type="text/javascript">
  jQuery(function($) {
      function changeDetect() {
	  $("input[class|='mediafield']").each( function() {
	      if( this.oldvalue != this.value ) {
		  $(this).parent().children().eq($(this).index()-1).attr('src', this.value.length < 6 ? "http://placehold.it/1x1" : this.value);
	      }
	      this.oldvalue = this.value;
	  });
      }
      setInterval( changeDetect, 1000 );
  });
</script>
<div class="my_meta_control metabox">
  <?php $mb->the_field('featured_thumb_img'); ?>  
  <?php $ma->setGroupName('nn')->setInsertButtonLabel('Insert'); ?>
  <p>
    <div>Thumbnail Image</div><br/>
    <img src="<?php echo $mb->get_the_value()?>" style="width:70px;height:70px;"/>
    <?php echo $ma->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
    <?php echo $ma->getButton(); ?>
  </p>

  <?php $mb->the_field('slideshow_img'); ?>
  <?php $ma->setGroupName('nn2')->setInsertButtonLabel('Insert This')->setTab('gallery'); ?>
  <p>
    <div>Slideshow Image</div><br/>
    <img src="<?php echo $mb->get_the_value()?>" style="width:225px;height:130px;"/>
    <?php echo  $ma->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
    <?php echo $ma->getButton(array('label' => 'Add Image From Library')); ?>
  </p>
</div>
