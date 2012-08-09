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
      <footer>
	<div class="social">
	  <div class="icons">
	    <?php
	       $prefix=get_template_directory_uri(); $n = 0;
	       foreach( array("fb"=>array(36,0),"twitter"=>array(108,0),"youtube"=>array(0,0),"ln"=>array(64,0)) as $name=>$p ) :
	    ?>
	       <?php if( ($link=get_meta_option('misc_opts',$name.'_link')) && strlen($link) > 5 ) : $n++;?>
               <a href="<?php echo $link?>" target="_blank" style="width:36px;height:36px;background:<?php echo "url('$prefix/images/social.png') -".$p[0]."px ".$p[1]."px"?>;"></a>
	       <?php endif; ?>
	    <?php endforeach; ?>
	  </div>
	  <?php if( $n>0 ) :?><div class="cross-line" style="clear:both"></div><?php endif;?>
	</div>
	<div class="text">
	  <?php echo get_meta_option("misc_opts","footer_msg") ?>
	</div>
      </footer>
      <?php wp_footer() ?>
   </body>
</html>
