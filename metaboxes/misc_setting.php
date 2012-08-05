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
global $meta_defaults;

wp_enqueue_script('farbtastic');
wp_enqueue_style('farbtastic');

function pdefault( $a, $v ) {
  if( $a === null || !isset($a) ) $a = $v;
  return $a;
}

$keys = array(

  'general_sect' => array('Random Things',array('type'=>'sep')),    
  'show_comments' => array('Theme should show comments','checkbox'),

  'header_sect' => array('Header',array('type'=>'sep')),
  'show_weather_bar' => array('Show a weather bar','checkbox'),
  'marquee_info_bar' => array('Apply marquee effect on the info bar','checkbox'),
  'global_msg' => array('Global header annoncement','text'),

  'footer_sect' => array('Footer',array('type'=>'sep')),
  'footer_msg' => array('Footer message','text'),

  'style_sect' => array('Styles stuff',array('type'=>'sep')),  
  'colours' => array('%s Colour', array(
    'type'=>'selector',
    'bg_colour'=>array('Background','colour'),
    'menu_colour'=>array('Menu','colour'),
    'menu_font_colour'=>array('Menu Font','colour')
  )),

  'reset_sect' => array('Danger Zone',array('type'=>'sep')),
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
    td {
	width: 200px;
    }
    td.input-box {
	width: 350px;
    }
    .text-box input {
	width: 100%;
    }
</style>
<script type="text/javascript">
    jQuery(function($){
	/* Handles selector events for showing/hiding elements in row */
	$('.option-switch').change(function(e) {
	    var i = e.target.selectedIndex;
	    var s =  $('>td.input-box',$(this).parents("tr"));
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
	   } elseif ( $t == 'sep' ) {
	     $label = sprintf('<h2>%s',$label);
	     $fields = array();
	   }
	    
	 } else {
	   $label = $info[0];
	   $nhtml = '';
	    $fields = array( $k => $info );
	 }
      ?> 
      <td><label><?php printf( $label, $nhtml ); ?></label></td>
      <?php foreach( $fields as $key=>$field ) : ?>
      <td class="<?php echo $field[1] ?>-box input-box">
	<input
	   type="<?php echo $field_input_type[$field[1]]?>" name="misc_opts[<?php echo $key?>]"
	   value="<?php switch($field[1]) :
		  case 'checkbox': echo 'yes'; break;
		  case 'button': echo $field[0]; break;
		  default: echo $nopts[$key]; break; endswitch;?>"
	   <?php echo ($field[1]=='checkbox'&&$nopts[$key]?'checked="checked"':'')?>
         />
      </td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
  </table>
  <input type="submit" name="submit"/>
</form>
