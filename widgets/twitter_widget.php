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

class TwitterWidget extends WP_Widget {
  private static $INFO = array(
    'classname'=>'TwitterWidget',
    'description'=>'Displays twitts from a list of users, ordered by date'
  );

  function __construct() {
    parent::__construct( self::$INFO['classname'], 'Twitter Widget', self::$INFO );
  }

  function form( $c ) {
    if( strlen($this->errmsg) > 0 ) printf('<p style="color:red">%s</p>',$this->errmsg);
    
    widget_form( $this, $c, array(
      'heading'=>array('type'=>'text','default'=>'Recent Tweets','label'=>'Heading'),
      'show'=>array('type'=>'number','default'=>3,'label'=>'Number of posts to show'),
      'list'=>array('type'=>'text','default'=>array('user-name','list-name'),'label'=>'Twitter list (<a href="http://support.twitter.com/articles/76460-how-to-use-twitter-lists" target="_blank">Help</a>)'),
      'colour'=>array('type'=>'colour','default'=>'#000000','label'=>'Background Colour')
    ));
  }

  function update( $new_c , $old_c ) {
    $c = $old_c;
    $this->errmsg = "";
    $list = array_filter( explode(':',$new_c['list']), create_function('$a','return strlen($a)>0;') );

    if( strlen($new_c['colour']) > 2 )
      $c['colour'] = $new_c['colour'];
    if( strlen($new_c['heading']) > 0 )
      $c['heading'] = $new_c['heading'];
    if( count($list) == 2 )
      $c['list'] = $list;
    else
      $this->errmsg .= "Format of Twitter list must be user-name:list-name.";
    if( is_numeric($new_c['show']) && $new_c['show'] >= 0 )
      $c['show'] = $new_c['show'];
    else
      $this->errmsg .= "Unreasonable number of posts to show.";
    return $c;
  }
  
  function widget( $args, $c ) {
    extract( $args, EXTR_OVERWRITE );
    extract( $c, EXTR_PREFIX_ALL, 'p' );
    $ch = $p_show*80;
    $ch = $ch > 300 ? 300 : $ch;
    echo <<<HTML
<section class="updates">      
      <script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
      <script>
      new TWTR.Widget({
	version: 2,
	    type: 'list',
	    rpp: $p_show,
	    interval: 30000,
	    title: '',
	    subject: '$p_heading',
	    width: 'auto',
	    height: $ch,
	    theme: {
	  shell: {
	    background: '$p_colour',
		color: '#ffffff'
		},
	      tweets: {
	    background: '#ffffff',
		color: '#444444',
		links: '#b740c2'
		}
	  },
	    features: {
	  scrollbar: true,
	      loop: false,
	      live: true,
	      behavior: 'all'
	      }
	}).render().setList('$p_list[0]', '$p_list[1]').start();
    </script>
    <noscript><a href="http://api.twitter.com/1/lists/statuses.atom?slug=$p_list[1]&owner_screen_name=$p_list[0]" target="_blank">Twitter Link</a></noscript>
</section> 
HTML;
  }
}


?>
