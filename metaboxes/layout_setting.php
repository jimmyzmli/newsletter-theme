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

$opts = get_option("layout_opts");
$opts = is_array($opts) ? $opts : array();
$layout = is_array( $opts['layout'] ) ? $opts['layout'] : array();
$hlayout = is_array( $opts['hidden_layout'] ) ? $opts['hidden_layout'] : array();

$cats = get_cats($layout);
$hcats = get_cats($hlayout);
$hcats = array_merge($hcats,cat_inverse($hcats,$cats));
/*
print "<pre>";
var_dump($layout,$cats);
print "</pre>";
*/

?>

<link href="<?="$prefix/metaboxes/layout_setting.css"?>" rel="stylesheet"/>
<link href="<?="$prefix/css/resizable.css"?>" rel="stylesheet"/>

<script type="text/javascript" src="<?="$prefix/js/jquery.min.js"?>"></script>
<script type="text/javascript" src="<?="$prefix/js/jquery-ui.min.js"?>"></script>
<script type="text/javascript" src="<?="$prefix/js/json2.js"?>"></script>
<script type="text/javascript" src="<?="$prefix/utils.js"?>"></script>
<script type="text/javascript" src="<?="$prefix/metaboxes/layout_setting.js"?>"></script>

<section id="body" class="clearfix">
  <section id="layout-panel" class="clearfix">
    <h2 class="title"><?=__("Layout",$theme)?></h2>
    <section id="tile-main" class="clearfix tile-section">
      <?php foreach( $cats as $i=>$c ) : ?>
      <section class="tile-cat clearfix">
	<h2 class="cat-title"><?=$c->name?></h2>
	<input type="hidden" name="cat_ID" value="<?=$c->cat_ID?>"/>

        <?php foreach( $c->tileInfo['tiles'] as $j=>$tile ) : ?>
        <section class="<?=get_size_name($tile['size'])?>-tile tile" style="height:<?=intval($tile['height'])*50?>px">
	  <input type="hidden" name="tile-size" value="<?=$tile['size']?>"/>
	  1/<?=$tile['size']?>
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
    <section id="trash-bin"><?=__("Trash",$theme)?></section>
    <section id="cat-list" class="clearfix tile-section">
      <h2 class="title"><?=__("Categories",$theme)?></h2>
      <?php foreach( $hcats as $c ) : ?>
      <section class="tile-cat">

	<h2 class="cat-title"><?=$c->name?></h2>
	<input type="hidden" name="cat_ID" value="<?=$c->cat_ID?>"/>
	
        <?php foreach( $c->tileInfo['tiles'] as $j=>$tile ) : ?>
        <section class="<?=get_size_name($tile['size'])?>-tile tile">
	  <input type="hidden" name="tile-size" value="<?=$tile['size']?>"/>
	  1/<?=$tile['size']?>
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
