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
?>
<style type="text/css">

#page {
    width: 960px;
    margin: auto auto;
}

#code {
    width: 960px;
    height: 300px;
}

</style>
<script type="text/javascript" src="<?php echo $prefix ?>/js/edit_area/edit_area_full.js"></script>
<script type="text/javascript">
    editAreaLoader.init({
        id: "code"
        ,start_highlight: true
        ,font_size: "12"
        ,font_family: "verdana, monospace"
        ,allow_resize: "y"
        ,allow_toggle: false
        ,language: "en"
        ,syntax: "css"
        ,toolbar: "save, |, charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
        ,save_callback: "save_style"
        ,plugins: "charmap"
        ,charmap_default: "arrows"

    });
</script>
<div id="page">    
  <textarea id="code"></textarea>
</div>
