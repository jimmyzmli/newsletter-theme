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

$posts = get_posts( array('meta_key' => 'post_banner_image') );
$slidelist = get_option( 'slide_opts' );
$slidelist = is_array($slidelist) ? $slidelist : array();

foreach( $posts as $i=>$post )
    if( in_array( $post->ID, $slidelist ) ) unset($posts[$i]);

function get_post_banner_img( $id, $type = 'full') {
    $img_ID = get_post_meta( $id, 'post_banner_image', true );
    $src = wp_get_attachment_image_src( $img_ID, $type )[0];
    return is_string($src) ? $src : "";
}

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
  
</style>
<link rel="stylesheet" href="<?=$prefix?>/slideshow.css"/>
<script type="text/javascript" src="<?=$prefix?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=$prefix?>/js/jquery-slides.min.js"></script>    
<script type="text/javascript" src="<?=$prefix?>/js/json2.js"></script>
<script type="text/javascript" src="<?=$prefix?>/utils.js"></script> 
<script type="text/javascript">
    jQuery( function($) {
	var g = window;

	$(".post-list li").click( function() {
	    $(this).toggleClass("selected-post");
	});
	$("#btn-list input:first").click( function() {
	    $(".post-list:last").append( $(".post-list:first .selected-post")
					 .detach().toggleClass("selected-post") );
	    g.updateSlide();
	});
	$("#btn-list input:last").click( function() {
	    $(".post-list:first").append( $(".post-list:last .selected-post")
					  .detach().toggleClass("selected-post") );
	    g.updateSlide();
	});

	$("#submit-btn").parent()[0].onsubmit = function() {
	    var list = [];
	    $(".post-list:last li").each(function() {
		var post_ID = $(this).layoutAttr("post_ID");
		if( typeof(post_ID) != "undefined" ) list.push( +post_ID );
	    });
	    $("#slidelist-field").val( JSON.stringify( list ) );
	    //console.log( JSON.stringify( list ) );
	    return true;
	};
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
    });
</script>
<?php
function print_post() {
  global $post;
  $catID = wp_get_post_categories( get_the_ID() )[0];
  $catID = is_numeric($catID) && $catID >= 0 ? $catID : 0;
  $catName = get_category( $catID )->name;
?>
  <li>
     <input type="hidden" name="img" value="<?=get_post_banner_img(get_the_ID(),'slideshow')?>"/>    
     <input type="hidden" name="post_ID" value="<?=get_the_ID()?>"/>
     <input type="hidden" name="title" value="<?=get_the_title()?>"/>
     <input type="hidden" name="cat" value="<?=$catName?>"/>
     <input type="hidden" name="excerpt" value="<?=get_the_excerpt()?>"/>
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
