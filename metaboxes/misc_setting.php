<?php
global $meta_defaults;

function pdefault( $a, $v ) {
  if( $a === null || !isset($a) ) $a = $v;
  return $a;
}

$keys = array(
  'show_comments' => array('Theme should show comments','checkbox'),
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
      <td><label><?=$info[0]?></label></td>
      <td><input type="<?=$info[1]?>" name="misc_opts[<?=$k?>]"
		 value="<?=($info[1]=='checkbox'?'yes':$nopts[$k])?>"
		 <?=($info[1]=='checkbox'&&$nopts[$k]?'checked="checked"':'')?>
		 />
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <input type="submit" name="submit"/>
</form>
