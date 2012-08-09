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

$prefix = get_template_directory_uri();

wp_enqueue_script('jquery');

?>
<!DOCTYPE html>
<html <?php language_attributes() ?>>
  <head>
    <meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>" charset="<?php bloginfo('charset') ?>"/>
    <?php wp_head(); ?>
    <style type="text/css">
      html,
      body {
	  background: black;
	  position: relative;
	  border: 0px;
	  padding: 0px;
	  margin: 0px;
	  height: 100%;
	  <?php if( is_user_logged_in() ) echo 'top: -28px;'; ?>
	  background: url('<?php echo $prefix ?>/images/galaxy.gif') repeat;	  
      }
      #canvas {
	  position: absolute;
	   top: 0px;
	   left: 0px;
	  width: 960px;
	  height: 600px;
      }
      .content {
	  display: block;
	  width: 960px;
	  height: 100%;
	  margin: 0px auto;
	  padding: 0px;
	  position: relative;
      }
      .error {
	  display: block;
	  color: white;
	  font-weight: bold;
	  margin: 0px;
	  padding: 0px;

	  width: 276px;
	  height: 152px;
	  position: absolute;
	   top: 50%;
	   left: 50%;
	   margin-top: -126px;
	   margin-left: -138px;
      }
      #msg {
	  font-size: 30px;
	  text-align: center;
      }
    </style>
    <script type="text/javascript">
       jQuery(function($) {
	   var ship = $("#ship-img")[0];
	   $(ship).load( function() {
	       var $canvas = $("#canvas"), $page = $("html");
	       var pw = $page.width(), ph = $page.height(), cw = $canvas.width(), ch = $canvas.height(), ctx = $canvas[0].getContext("2d");
	       var sw = 40*(cw/pw), sh = 20*(cw/pw), x = 170, y = -sh;	       
	       setInterval( function() {
		   ctx.clearRect( 0, 0, cw, ch );
		   ctx.drawImage( ship, x, y, sw, sh);
		   x -= 1; y += 1;
		   if( x <= sh ) { x = Math.floor(Math.random()*170+50); y = -sh; }
	       }, 50 );
	   });
       });
    </script>
  </head>
  <body>
    <img src="<?php echo $prefix?>/images/ship.png" id="ship-img" style="display:none"/>    
    <div class="content">
      <canvas id="canvas"></canvas>
      <div class="error">
         <img src="<?php echo $prefix ?>/images/404.png"/>
	 <h2 id="msg">Page not found</h2>
      </div>
    </div>
  </body>
  <footer>
  <?php wp_footer(); ?>
  </footer>
</html>
