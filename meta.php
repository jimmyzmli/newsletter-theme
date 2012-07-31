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

define( 'METAPREF', 'nws' );

/* Meta value accessors */
function get_post_meta_img( $id, $type = 'full') {
  $src = get_post_meta( $id, METAPREF."_$type"."_img", true );
  return is_string($src) ? $src : "";
}

function get_cat_meta_img( $id, $type = 'full' ) {
  $img = get_tax_meta( $id, METAPREF."_$type"."_img" );
  $src = $img["src"];
  return is_string($src) ? $src : "";  
}

/* Load WP plugin dependencies */
/* Please read the terms and credits for each respective plugin. I hold no claims over them. */
require_once("plugins/wpalchemy/MetaBox.php");
require_once("plugins/wpalchemy/MediaAccess.php");
require_once("plugins/tax-meta-class/Tax-meta-class.php");


add_action( 'admin_init', 'theme_settings_init' );
add_action( 'admin_menu', 'theme_settings_add_pages' );
add_action( 'admin_init', 'theme_custom_metabox_init' );

function theme_custom_metabox_init() {
  /* Add themes and helper scripts */
  if( is_admin() ) {
    wp_enqueue_style( 'wpalchemy-metabox', get_template_directory_uri().'/metaboxes/meta.css' );
    wp_enqueue_script( 'tax-meta-class-helper', get_template_directory_uri().'/metaboxes/meta.js' );
  }
}

/* Meta boxes for post */
$ma = new WPAlchemy_MediaAccess();
$post_metabox = new WPAlchemy_MetaBox(
  array(
    'id'=>'post_img_meta_box',
    'title'=>'Thumbnail Image',
    'template'=> get_template_directory().'/metaboxes/post-img-meta.php',
    'types'=>array('post','page'),
    'context'=>'normal',
    'mode'=>WPALCHEMY_MODE_EXTRACT,
    'prefix'=>METAPREF."_"
  )
);



/* Meta boxes for the category/taxonomy editor */
$cat_meta = new Tax_Meta_Class(
  array(
    'id' => 'cat_img_meta_box',
    'title' => 'Demo Meta Box',
    'pages' => array('category'),
    'context' => 'normal',
    'fields' => array(),
    'local_images' => true,
    'use_with_theme' => get_template_directory_uri()."/plugins/tax-meta-class"
  )
);
$cat_meta->addImage( METAPREF.'_featured_thumb_img',array('name'=> 'Category Thumbnail') );
$cat_meta->Finish();

/* Custom admin menu pages */
function theme_settings_init() {
  register_setting( 'layout_opts', 'layout_opts', 'validate_layout_opts' );
  register_setting( 'slide_opts', 'slide_opts', 'validate_slide_opts' );
}

function theme_settings_add_pages() {
  add_theme_page(
    __("Layout"),
    __("Layout"),
    "edit_theme_options",
    "theme_layout_options",
    create_function('','require_once("metaboxes/layout_setting.php");')
  );
  add_theme_page(
    __("Slideshow"),
    __("Slideshow"),
    "edit_theme_options",
    "theme_slideshow_options",
    create_function('','require_once("metaboxes/slideshow_setting.php");')
  );
}

/* Validates layout editor data */
function validate_layout_opts($opts) {
  $old = get_option('layout_opts');
  $layout = json_decode($opts['layout'],true);
  $hlayout = json_decode($opts['hidden_layout'],true);
  $opts = &$old;
  
  if( $layout !== null ) $opts['layout'] = $layout;
  if( $hlayout !== null ) $opts['hidden_layout'] = $hlayout;
  
  return $opts;
}

/* Validates slide editor data */
function validate_slide_opts( $opts ) {
  $old = get_option('slide_opts');
  $list = json_decode($opts,true);
  /* var_dump($list);exit(0); */
  return is_array($list) ? $list : $old;
}

?>
