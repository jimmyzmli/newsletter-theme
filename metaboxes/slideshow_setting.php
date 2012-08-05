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

$posts = get_posts( array('meta_key' => METAPREF.'_slideshow_img') );

$slidelist = get_option( 'slide_opts' );
/* Set stub if needed */
if( $slidelist === false )
  add_option( 'slide_opts', array() );
$slidelist = is_array($slidelist) ? $slidelist : array();

foreach( $posts as $i=>$post )
    if( in_array( $post->ID, $slidelist ) ) unset($posts[$i]);

?>
<style type="text/css">
  #control-board {
      position: relative;
      width: 964px;
  }
  .post-list {
      float: left;
      padding: 0px;
      margin: 0px;
      width: 460px;
      min-height: 500px;
  }

  #btn-list {
      float: left;
      width: 40px;
      height: 100%;
  }
  #btn-list input {
      display: block;
       width: 30px;
       height: 30px;
      margin: auto auto;
  }

  .post-list li {
      border: 1px dashed black;
      margin: 0px;
      height: 50px;
  }

  .selected-post {
      background: yellow;
  }

  #submit-btn {
      display: block;
      width: 100px;
      margin: auto auto;
      margin-top: 30px;
  }

  .slider {
      border: 0px solid red;
  }
</style>
<link rel="stylesheet" href="<?php echo $prefix?>/slideshow.css"/>
<script type="text/javascript" src="<?php echo $prefix?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $prefix?>/js/jquery-slides.min.js"></script>    
<script type="text/javascript" src="<?php echo $prefix?>/js/json2.js"></script>
<script type="text/javascript" src="<?php echo $prefix?>/utils.js"></script> 
<script type="text/javascript">
    jQuery( function($) {
	var g = window;

	g.updateSlider = function() {
	    var info = [];	
	    $(".post-list:last li").each( function(){
					      var p = {
						  post_ID : +$(this).layoutAttr("page_ID"),
						  img: $(this).layoutAttr("img"),
						  title: $(this).layoutAttr("title"),
						  desc: $(this).layoutAttr("excerpt"),
						  cat: $(this).layoutAttr("cat")
					      };
					      info.push( p );
					  } );
	    $(".slider").jslides(info,{ start: 1 });
	};

	$(".post-list li").click( function() {
	    $(this).toggleClass("selected-post");
	});
	$("#btn-list input:first").click( function() {
	    $(".post-list:last").append( $(".post-list:first .selected-post")
					 .detach().toggleClass("selected-post") );
	    g.updateSlider();
	});
	$("#btn-list input:last").click( function() {
	    $(".post-list:first").append( $(".post-list:last .selected-post")
					  .detach().toggleClass("selected-post") );
	    g.updateSlider();	    
	});

	$("#submit-btn").parent()[0].onsubmit = function() {
	    var list = [];
	    $(".post-list:last li").each(function() {
		var post_ID = $(this).layoutAttr("post_ID");
		if( typeof(post_ID) != "undefined" ) list.push( +post_ID );
	    });
	    $("#slidelist-field").val( JSON.stringify( list ) );
	    return true;
	};

	g.updateSlider();
    });
</script>
<?php
function print_post() {
  global $post;
  $catID = wp_get_post_categories( get_the_ID() );
  $catID = $catID[0];
  $catID = is_numeric($catID) && $catID >= 0 ? $catID : 0;
  $catName = get_category( $catID )->name;
?>
  <li>
     <input type="hidden" name="img" value="<?php echo get_post_meta_img(get_the_ID(),'slideshow')?>"/>    
     <input type="hidden" name="post_ID" value="<?php echo get_the_ID()?>"/>
     <input type="hidden" name="title" value="<?php echo get_the_title()?>"/>
     <input type="hidden" name="cat" value="<?php echo $catName?>"/>
     <input type="hidden" name="excerpt" value="<?php echo get_the_excerpt()?>"/>
     <a><?php the_title(); ?></a>
  </li>
<?php
}
?>
<div class="slider"></div>
<div id="control-board">
  <ul class="post-list" style="border:1px solid black">
    <h2 class="title">All Posts</h2>
    <?php global $post;  foreach( $posts as $i=>$post ) { setup_postdata( $post ); print_post(); } ?>
  </ul>
  <div id="btn-list">
    <input type="button" value=">>" />
    <input type="button" value="<<" />
  </div>
  <ul class="post-list" style="border:1px solid black">
    <h2 class="title">Slide Show Posts</h2>    
    <?php foreach( $slidelist as $i=>$post_ID ) { setup_postdata( ($post=get_post($post_ID)) ); print_post(); } ?>
  </ul>
  <div style="clear:both"></div>
  <form action="options.php" method="POST">
    <?php settings_fields("slide_opts"); ?>
    <input type="hidden" name="slide_opts" id="slidelist-field" value=""/>
    <input type="submit" name="submit" id="submit-btn"/>
  </form>
</div>
