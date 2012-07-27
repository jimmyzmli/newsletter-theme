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

function print_tile_html( $tile, &$p ) {
  static $func_desc = array( "cat"=>"Cateogory Viewer", "widget"=>"Single Widget" );
  static $catInfo = null;
  if( $catInfo == null ) {
    /* Organize Category info for ID lookup */
    $catInfo = get_categories( array('hide_empty' => 0) );
    $t = array();
    foreach( $catInfo as $i=>$cat )
      $t[$cat->cat_ID] = $cat;
    $catInfo = $t;
  }
  print "1/".$tile["size"];
  $func = $tile["func"]; $id = $tile["id"];
  if( !isset($func_desc[$func]) ) {
    return false;
  }
  print "<div class='title' >".$func_desc[$func]."</div>";
  if( $func == "cat" && isset($catInfo[intval($id)]) ) {
    $info = $catInfo[$id];
    printf( "<div>%s</div>", $info->name );
  } else {
    return false;
  }
  return true;
}

$prefix = get_template_directory_uri();

$opts = get_option("layout_opts");
$opts = is_array($opts) ? $opts : array();
$layout = is_array( $opts['layout'] ) ? $opts['layout'] : get_empty_layout();
$hlayout = is_array( $opts['hidden_layout'] ) ? $opts['hidden_layout'] : get_empty_layout();

$args = array( "box_class"=>"box", "tab_count" => 1, "tile_class"=>"tile", "cat_class"=>"cat", "tile_cb"=>"print_tile_html" );


/* Get the not recorded categories also */
$empty = get_empty_cat_ids($layout);
$empty = get_empty_cat_ids($hlayout, $empty);
if( $hlayout["type"] == "root" && is_array($hlayout["children"]) )
  foreach( $empty as $i=>$catID ) {
    $tile = array( "type"=>"tile", "func"=>"cat", "id"=>$catID, "size"=>"2");
    array_push( $hlayout["children"], $tile);
  }

?>

<link href="<?="$prefix/layout_setting.css"?>" rel="stylesheet"/>
<link href="<?="$prefix/js/resizable.css"?>" rel="stylesheet"/>

<script type="text/javascript" src="<?="$prefix/js/jquery.min.js"?>"></script>
<script type="text/javascript" src="<?="$prefix/js/jquery-ui.min.js"?>"></script>
<script type="text/javascript" src="<?="$prefix/js/json2.js"?>"></script>
<script type="text/javascript" src="<?="$prefix/layout_setting.js"?>"></script>

<section id="body" class="clearfix">
  <section id="layout-panel" class="clearfix">
    <h2 class="title"><?=__("Layout",$theme)?></h2>
    <section id="box-main" class="clearfix box-section boxroot">
      <?php print_layout_data( $layout, HTML, $args ); ?>
    </section>
    <section id="box-board" class="boxroot clearfix">
      <section class="box">
	<input type="hidden" name="type" value="box"/>
      </section>
      <section class="quart-box box tile">
	<input type="hidden" name="type" value="tile"/>
	<input type="hidden" name="size" value="4"/>
	1/4
      </section>
      <section class="half-box box tile">
	<input type="hidden" name="type" value="tile"/>
	<input type="hidden" name="size" value="2"/>
	1/2
      </section>
      <section class="full-box box tile">
	<input type="hidden" name="type" value="tile"/>
	<input type="hidden" name="size" value="1"/>
	1/1
      </section>
    </section>
    <section id="trash-bin"><?=__("Trash",$theme)?></section>
    <section id="box-hidden" class="clearfix box-section boxroot">
      <h2 class="title"><?=__("Categories",$theme)?></h2>
      <?php print_layout_data( $hlayout, HTML, $args ) ?>
    </section>
  </section>

  <form id="form-ctl" action="options.php" method="POST">
    <?php settings_fields("layout_opts") ?>
    <input type="hidden" name="layout_opts[layout]" id="layout_field" value=""/>
    <input type="hidden" name="layout_opts[hidden_layout]" id="hidden_layout_field" value=""/>
    <input type="submit" name="submit" id="save-btn" value="Save Changes"/>
  </form>
</section>
