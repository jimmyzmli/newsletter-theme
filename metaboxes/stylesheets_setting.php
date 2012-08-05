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
global $allowed_custom_styles;
$prefix = get_template_directory_uri();

/* Load styles and scripts dependencies */
wp_enqueue_style( "smoothness-ui", "$prefix/css/resizable.css" );

wp_enqueue_script( "jquery", "$prefix/js/jquery.min.js" );
wp_enqueue_script( "jquery-ui", "$prefix/js/jquery-ui.min.js" );
wp_enqueue_script( "edit-area", "$prefix/js/edit_area/edit_area_full.js" );


$opts = get_option("style_opts");
/* Set stub */
if( !is_array($opts) ) {
    $styles = array();    
    add_option('style_opts', $opts );
}

$styles = array();
foreach( $allowed_custom_styles as $k=>$title )
  $styles[$k] = array( 'content'=> (isset($opts[$k]) ? $opts[$k] : ""), 'title'=> (isset($title) ? $title : "$title Page")." Style");

?>
<style type="text/css">
  
#page {
    width: 960px;
    margin: auto auto;
}

#tabs li {
    float: left;
}

#editor {
    width: 100%;
    height: 400px;
    min-height: 400px;
}

#submit-frm {
    display: block;
    width: 80px;
    margin: 20px auto;
}

</style>
<script type="text/javascript">
    window.stylesData = <?php echo json_encode( $styles ); ?>;
    jQuery(function($) {
	var g = window;
	
	g.commit = function( ) {
	    if( arguments.length % 2 != 0 ) return false;
	    var r = {}, res = false, wp_frm = "#submit-frm";
	    for(var i=0;i<arguments.length;i+=2) {
		var fid = arguments[i], content = arguments[i+1];
		r['style_opts['+fid+']'] = content;
	    }
	    $.ajax( $(wp_frm).attr('action'), {
		type: 'POST',
		async: false,
		data: $(wp_frm).serialize()+"&"+jQuery.param(r),
		success: function(data,stat,xhr) {
		    res = true;
		},
		error: function(xhr,stat,err) {
		    res = false;
		}		
	    });
	    return res;
	};

	g.save_style = function( id, content ) {
	    var f = editAreaLoader.getCurrentFile(id);
	    if( g.commit( f.id, content ) === true ) {
		editAreaLoader.setFileEditedMode( id, f.id, false );
	    }
	};

	g.editor_loaded = function( id ) {
	    $.each( g.stylesData, function(i,k) {
		editAreaLoader.openFile('editor',{
		    id: i,
		    title: k.title,
		    syntax: "css",
		    text: k.content
		});
	    });
	};

	g.editor_closefile = function() { return false; };

	editAreaLoader.init({
	    id: 'editor'
	    ,start_highlight: true
	    ,font_size: "12"
	    ,font_family: "verdana, monospace"
	    ,allow_resize: "y"
	    ,allow_toggle: false
	    ,language: "en"
	    ,syntax: "css"
	    ,toolbar: "save, |, charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
	    ,save_callback: "save_style"
	    ,EA_load_callback: "editor_loaded"
	    ,EA_file_close_callback: "editor_closefile"
	    ,plugins: "charmap"
	    ,charmap_default: "arrows"
	    ,is_multi_files: true
	});

	$("#submit-frm").submit( function() {
	    var files = editAreaLoader.getAllFiles("editor"), p = [];
	    $.each( files, function(i,f) {
		p.push(f.id); p.push( editAreaLoader.getFile("editor",f.id).text );
	    });
	    return g.commit.apply( g, p ) === true;
	});
	
    });
</script>    
<div id="page">
    <textarea id="editor"></textarea>    
    <form action="options.php" method="POST" id="submit-frm">
      <?php settings_fields( "style_opts" ); ?>
      <input type="submit" name="submit" value="Save All"/>
    </form>    
</div>
