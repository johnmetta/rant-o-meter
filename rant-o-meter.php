<?php
/*
Plugin Name: Rant-o-Meter
Plugin URI: http://github.com/johnmetta/rant-o-meter
Description: Display a Google-o-meter of the "rant" level of a post
Version: 0.1 BETA
Author: John Metta
Author URI: http://johnmetta.com
*/

/*
Rant-o-Meter (Wordpress Plugin)
Copyright (C) 2011 John Metta
Contact me at http://johnmetta.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

//tell wordpress to register the rant shortcode
add_shortcode("rant", "rant_handler");
add_action('admin_menu', 'rant_menu');

// [rant level="70" mood="upset"]
function rant_handler($atts) {
	extract(shortcode_atts(array(
		'level' => 5,
		'mood' => '',
		'align' => 'left',
		'bottom' => 'Oregon',
		'top' => 'New York',
		'height' => 110,
		'width' => 200,
		'startcolor' => '00cc00',
		'endcolor' => 'cc0000',
		'caption' => 1,
		'caption_text' => '<a href="http://github.com/johnmetta/rant-o-meter">Rant-o-Meter</a>' 
	), $atts ) );
  $caption_width = $width + 10 . "px";
  //run function that actually does the work of the plugin
  $meter = build_meter($level, $mood, $bottom, $top, $height, $startcolor, $endcolor);
  //send back text to replace shortcode in post
  $image = "";
  if ((int)$caption)
	{
	  $image .= "<div id='attachment_715' 
				class='wp-caption align$align' 
				style='width: $caption_width'>";
	}
  $image .= "<img title='$mood' src='$meter' alt='$mood' style='float:$align' />";
  if ((int)$caption)
	{
	  $image .= "<p class='wp-caption-text'>$caption_text</p></div>";
	}
  return $image;
}

function build_meter($level, $mood, $bottom, $top, $height, $startcolor, $endcolor) {
  //process plugin
  $output = "https://chart.googleapis.com/chart?cht=gom
	&chs=200x$height
	&chd=t:$level
	&chxt=x,y&chxl=0:|$mood|1:|$bottom|$top
	&chco=$startcolor,$endcolor"; // colors
  //send back text to calling function
  return $output;
}

?>