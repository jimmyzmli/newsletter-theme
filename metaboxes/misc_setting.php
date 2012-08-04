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

function pdefault( $a, $v ) {
  if( $a === null || !isset($a) ) $a = $v;
  return $a;
}

$keys = array(
  'show_comments' => array('Theme should show comments','checkbox'),
  'show_weather_bar' => array('Show a weather bar','checkbox'),  
  'global_msg' => array('Global header annoncement','text')
);
$opts = get_option("misc_opts");
$opts = is_array($opts) ? $opts : array();
$nopts = array();
foreach( $keys as $k=>$t ) {
  $nopts[$k] = pdefault( $opts[$k], $meta_defaults['misc_opts'][$k] );
}
extract( $nopts, EXTR_PREFIX_ALL, 'p' );

?>
<form action="options.php" method="POST">
  <?php settings_fields("misc_opts") ?>
  <table>
    <?php foreach( $keys as $k=>$info ) : ?>
    <tr>
      <td><label><?php echo $info[0]?></label></td>
      <td><input type="<?php echo $info[1]?>" name="misc_opts[<?php echo $k?>]"
		 value="<?php echo ($info[1]=='checkbox'?'yes':$nopts[$k])?>"
		 <?php echo ($info[1]=='checkbox'&&$nopts[$k]?'checked="checked"':'')?>
		 />
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <input type="submit" name="submit"/>
</form>
