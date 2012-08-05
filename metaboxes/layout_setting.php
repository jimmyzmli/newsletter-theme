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
<?php

$prefix = get_template_directory_uri();

/* Get stored option */
$opts = get_option("layout_opts");
/* First time? Then set a stub */
if( $opts === false )
  add_option("layout_opts", array());
$opts = is_array($opts) ? $opts : array();

$layout = is_array( $opts['layout'] ) ? $opts['layout'] : array();
$hlayout = is_array( $opts['hidden_layout'] ) ? $opts['hidden_layout'] : array();

$cats = get_cats($layout);
$hcats = get_cats($hlayout);
$hcats = array_merge($hcats,cat_inverse($hcats,$cats));


wp_enqueue_style( "theme-layout-settings", "$prefix/metaboxes/layout_setting.css" );
wp_enqueue_style( "smoothness-ui", "$prefix/css/resizable.css" );

wp_enqueue_script( "jquery", "$prefix/js/jquery.min.js" );
wp_enqueue_script( "jquery-ui", "$prefix/js/jquery-ui.min.js" );
wp_enqueue_script( "json2", "$prefix/js/json2.js" );
wp_enqueue_script( "theme-utils", "$prefix/utils.js" );
wp_enqueue_script( "theme-layout-settings", "$prefix/metaboxes/layout_setting.js" );

?>

<section id="body" class="clearfix">
  <section id="layout-panel" class="clearfix">
    <h2 class="title"><?php echo __("Layout",$theme)?></h2>
    <section id="tile-main" class="clearfix tile-section">
      <?php foreach( $cats as $i=>$c ) : ?>
      <section class="tile-cat clearfix">
	<h2 class="cat-title"><?php echo $c->name?></h2>
	<input type="hidden" name="cat_ID" value="<?php echo $c->cat_ID?>"/>

        <?php foreach( $c->tileInfo['tiles'] as $j=>$tile ) : ?>
        <section class="<?php echo get_size_name($tile['size'])?>-tile tile" style="height:<?php echo intval($tile['height'])*50?>px">
	  <input type="hidden" name="tile-size" value="<?php echo $tile['size']?>"/>
	  1/<?php echo $tile['size']?>
	</section>
	<?php endforeach; ?>

      </section>
      <?php endforeach; ?>
    </section>
    <section id="tile-board" class="clearfix">
      <section class="quart-tile tile">
	<input type="hidden" name="tile-size" value="4"/>
	1/4
      </section>
      <section class="half-tile tile">
	<input type="hidden" name="tile-size" value="2"/>
	1/2
      </section>
      <section class="full-tile tile">
	<input type="hidden" name="tile-size" value="1"/>
	1/1
      </section>
    </section>
    <section id="trash-bin"><?php echo __("Trash",$theme)?></section>
    <section id="cat-list" class="clearfix tile-section">
      <h2 class="title"><?php echo __("Categories",$theme)?></h2>
      <?php foreach( $hcats as $c ) : ?>
      <section class="tile-cat">

	<h2 class="cat-title"><?php echo $c->name?></h2>
	<input type="hidden" name="cat_ID" value="<?php echo $c->cat_ID?>"/>
	
        <?php foreach( $c->tileInfo['tiles'] as $j=>$tile ) : ?>
        <section class="<?php echo get_size_name($tile['size'])?>-tile tile">
	  <input type="hidden" name="tile-size" value="<?php echo $tile['size']?>"/>
	  1/<?php echo $tile['size']?>
	</section>
	<?php endforeach; ?>	

      </section>
      <?php endforeach; ?>
    </section>
  </section>

  <form id="form-ctl" action="options.php" method="POST">
    <?php settings_fields("layout_opts") ?>
    <input type="hidden" name="layout_opts[layout]" id="layout_field" value=""/>
    <input type="hidden" name="layout_opts[hidden_layout]" id="hidden_layout_field" value=""/>
    <input type="submit" name="submit" id="save-btn" value="Save Changes"/>
  </form>
</section>
