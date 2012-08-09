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
<?php
global $meta_defaults;

wp_enqueue_script('farbtastic');
wp_enqueue_style('farbtastic');

function pdefault( $a, $v ) {
  if( $a === null || !isset($a) ) $a = $v;
  return $a;
}

$keys = array(

  'general_sect' => array('<h2>'.__("Random Things").'</h2>','none'),    
  'show_comments' => array(__('Theme should show comments'),'checkbox'),
  'placeholder_img' => array(__('Placeholder Image'),'text'),
  'ppps' => array(__('Posts Per Page for %s'),array(
    'type'=>'selector',
    'search_ppp' => array(__('Search'),'number'),
    'archive_ppp'=> array(__('Category'),'number')
  )),  

  'header_sect' => array('<h2>'.__("Header").'</h2>','none'),
  'show_weather_bar' => array('Show a weather bar','checkbox'),
  'marquee_info_bar' => array('Apply marquee effect on the info bar','checkbox'),
  'global_msg' => array('Global header annoncement','text'),
  'infobar_bg' => array('Info bar background','colour'),

  'body_sect' => array('<h2>Body</h2>','none'),
  
  'newstile_sect' => array('<h3>News Tile</h3>','none'),
  'tiles_rollup_days' => array('Number of days to round off when evaluating views','number'),  
  'tiles_img_count' => array('Number of posts to show images for on the news tile','number'),  
  'tiles_lines_per_post' => array('Number of lines to show per post','number'),
  'tiles_font_size' => array('Font size of text in the news tile','number'),
  'tiles_bgcolours' => array('%s background',array(
    'type'=>'selector',
    'tiles_bg_colour'=>array('Tiles','colour'),
    'tiles_title_bg'=>array('Title','colour'),
    'tiles_title_hover_bg'=>array('Title hover','colour')
  )),
  'tiles_fgcolours' => array('%s foreground',array(
    'type'=>'selector',
    'tiles_fg_colour'=>array('Tiles','colour'),
    'tiles_heading_fg'=>array('Heading','colour'),
    'tiles_heading_hover_fg'=>array('Heading hover','colour')
  )),

  'sidebar_sect' => array('<h3>Sidebar</h3>','none'),
  'widget_bgcolours' => array('Widget %s background',array(
    'type'=>'selector',
    'widgettitle_bg' => array('title','colour'),
    'widget_bg' => array('body','colour')
  )),
  'widget_fgcolours' => array('Widget %s foreground',array(
    'type'=>'selector',
    'widgettitle_fg' => array('title','colour'),
    'widget_fg' => array('body','colour')
  )),  
  
  'footer_sect' => array('<h2>Footer</h2>','none'),
  'footer_colours' => array('Footer %s',array(
    'type'=>'selector',
    'footer_fg' => array('Foreground','colour'),
    'footer_bg' => array('Background','colour')    
  )),
  'footer_msg' => array('Footer message','text'),
  'soc_sect' => array('%s Link',array(
    'type' => 'selector',
    'fb_link' => array('Facebook','text'),
    'twitter_link' => array('Twitter','text'),
    'ln_link' => array('LinkedIn','text'),
    'youtube_link' => array('Youtube','text')    
  )),

  'style_sect' => array('<h2>Global stuff</h2>','none'),  
  'colours' => array('%s Colour', array(
    'type'=>'selector',
    'bg_colour'=>array('Page Background','colour'),
    'menu_colour'=>array('Menu','colour'),
    'menu_font_colour'=>array('Menu Font','colour')
  )),

  'reset_sect' => array('<h2 style="color:red">Danger Zone</h2>','none'),
  'resets' => array('Reset', array(
    'type'=>'row',
    'btn_reset_all'=>array('All','button'),
    'btn_reset_misc'=>array('This page','button')
  ))
);

$field_input_type = array(
    'text' => 'text',
    'checkbox' => 'checkbox',
    'radio' => 'radio',
    'colour' => 'text',
    'button' => 'Submit',
    'number' => 'number'
);


$opts = get_option("misc_opts");
$opts = is_array($opts) ? $opts : array();
$nopts = array();
foreach( $keys as $k=>$t ) {
  if( is_array($t[1]) ) {
    foreach( $t[1] as $kk=>$tt )
      if( $kk != 'type' )
	$nopts[$kk] = pdefault( $opts[$kk], $meta_defaults['misc_opts'][$kk] );
  } else {
    $nopts[$k] = pdefault( $opts[$k], $meta_defaults['misc_opts'][$k] );
  }
}

?>
<style type="text/css">
    div.farbtastic {
	position: fixed;
	display: none;
	top: 28px;
	left: 146px;
    }
    tr td:first {
	width: 30%;
    }
    td {
	width: 20%;
    }
    .text-box input {
	width: 100%;
    }
  
    div.inline-row {
      float: left;
    }

    #submit-btn {
      display: block;
      width: 50px;
      margin: auto auto;
      margin-top: 30px;
    }
</style>
<script type="text/javascript">
    jQuery(function($){
	/* Handles selector events for showing/hiding elements in row */
	$('.option-switch').change(function(e) {
	    var i = e.target.selectedIndex;
	    var s =  $('>td div.input-box',$(this).parents("tr"));
	    $(s).css('display','none');
	    $($(s).get(i)).removeAttr('style');
	}).trigger( 'change' );
	/* Creates farbtastic colour wheels */
	$('.colour-box input').each(function(){
	    $(this).after( $("<div>").farbtastic(this) );
	}).focus(function() {
	    $('+div>.farbtastic',this).css('display','block');
	}).blur(function() {
	    $('+div>.farbtastic',this).removeAttr('style');
	});
    });
</script>
<form action="options.php" method="POST">
  <?php settings_fields("misc_opts") ?>
  <table>
    <?php foreach( $keys as $k=>$info ) : ?>
    <tr>
      <?php  
         if( is_array($info[1]) ){
	   $label = $info[0];
	   $fields = $info[1];
	   $nhtml = '';	   
	   $t = $fields['type'];
	   unset($fields['type']);
	    
	   if( $t == 'selector' ) {
	      $nhtml .= '<select class="option-switch">';
	      foreach( $fields as $j=>$ele ) {
		$nhtml .= '<option>'.$ele[0].'</option>';
	      }
	      $nhtml .= '</select>';
	   }
	 } else {
	   $label = $info[0];
	   $nhtml = '';
	   $fields = $info[1] == 'none' ? array() : array( $k => $info );
	 }
      ?> 
      <td><label><?php printf( $label, $nhtml ); ?></label></td>
      <td>
	<?php foreach( $fields as $key=>$field ) : ?>
	<div class="<?php echo $field[1] ?>-box input-box <?php echo isset($t)&&$t=='row'?'inline-row':''?>">
	  <input
	     type="<?php echo $field_input_type[$field[1]]?>" name="misc_opts[<?php echo $key?>]"
	     value="<?php switch($field[1]) :
		    case 'checkbox': echo 'yes'; break;
		    case 'button': echo $field[0]; break;
		    default: echo $nopts[$key]; break; endswitch;?>"
	     <?php echo ($field[1]=='checkbox'&&$nopts[$key]?'checked="checked"':'')?>
          />
	</div>
	<?php endforeach; ?>
      <td>
    </tr>
    <?php endforeach; ?>
  </table>
  <input type="submit" name="submit" id="submit-btn" />
</form>
