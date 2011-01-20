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
add_shortcode("rant-o-meter", "rant_handler");
add_action('admin_menu', 'rant_menu');

function rant_restore_config($force=false) {
	if ($force) {
		echo "YESSS!!!!!!!";
		update_option('mettarant_level', 5);
		update_option('mettarant_mood', '');
		update_option('mettarant_align', 'left');
		update_option('mettarant_bottom', 'Hood River');
		update_option('mettarant_top','New York');
		update_option('mettarant_height', 110);
		update_option('mettarant_width', 200);
		update_option('mettarant_startcolor', '00cc00');
		update_option('mettarant_endcolor', 'cc0000');
		update_option('mettarant_show_caption', 1);
		update_option('mettarant_caption_text', 'Rant-o-Meter by JohnMetta');
	}
	if (!get_option('mettarant_show_caption')) {
		add_option('mettarant_level', 5, '', 'yes');
		add_option('mettarant_mood', '' , '', 'yes');
		add_option('mettarant_align', 'left' , '', 'yes');
		add_option('mettarant_bottom', 'Hood River' , '', 'yes');
		add_option('mettarant_top','New York' , '', 'yes');
		add_option('mettarant_height', 110 , '', 'yes');
		add_option('mettarant_width', 200, '', 'yes');
		add_option('mettarant_startcolor', '00cc00' , '', 'yes');
		add_option('mettarant_endcolor', 'cc0000' , '', 'yes');
		add_option('mettarant_show_caption', 1, '', 'yes');
		add_option('mettarant_caption_text', 'Rant-o-Meter by JohnMetta', '', 'yes');
	}
}


// [rant level="70" mood="upset"]
function rant_handler($atts) {
	rant_restore_config();
	extract(shortcode_atts(array(
		'level' => get_option('mettarant_level'),
		'mood' => get_option('mettarant_mood'),
		'align' => get_option('mettarant_align'),
		'bottom' => get_option('mettarant_bottom'),
		'top' => get_option('mettarant_top'),
		'height' => get_option('mettarant_height'),
		'width' => get_option('mettarant_width'),
		'startcolor' => get_option('mettarant_startcolor'),
		'endcolor' => get_option('mettarant_endcolor'),
		'show_caption' => get_option('mettarant_show_caption'),
		'caption_text' => get_option('mettarant_caption_text') 
	), $atts ) );
  $caption_width = $width + 10 . "px";
  //run function that actually does the work of the plugin
  $meter = build_meter($level, $mood, $bottom, $top, $height, $startcolor, $endcolor);
  //send back text to replace shortcode in post
  $image = "";
  if ((int)$show_caption)
	{
	  $image .= "<div id='attachment_715' 
				class='wp-caption align$align' 
				style='width: $caption_width'>";
	}
  $image .= "<img title='$mood' src='$meter' alt='$mood' style='float:$align' />";
  if ((int)$show_caption)
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

function rant_menu() {
	add_submenu_page('options-general.php', 'Rant-o-Meter Options', 'Rant-o-Meter', 'manage_options', 'rant-o-meter-options', 'rant_options_page');
}

function rant_options_page() {
	$restore = false;
	if ($_POST['restore_rant_defaults']){
	    $restore = true;
	}
	rant_restore_config($restore);
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
    $updated = false;

    if (isset($_POST['mettarant_show_caption']) && ! $restore)
    {
            $show_caption = $_POST['mettarant_show_caption'];
			update_option('mettarant_level', $_POST['mettarant_level']);
			update_option('mettarant_mood', $_POST['mettarant_mood']);
			update_option('mettarant_bottom', $_POST['mettarant_bottom']);
			update_option('mettarant_top', $_POST['mettarant_top']);
			update_option('mettarant_startcolor', $_POST['mettarant_startcolor']);
			update_option('mettarant_endcolor', $_POST['mettarant_endcolor']);
			update_option('mettarant_align', $_POST['mettarant_align']);
			update_option('mettarant_width', $_POST['mettarant_width']);
			update_option('mettarant_height', $_POST['mettarant_height']);
			update_option('mettarant_caption_text', $_POST['mettarant_caption_text']);
            update_option('mettarant_show_caption', $_POST['mettarant_show_caption']);

            $updated = true;
    }
    if ($updated)
    {
            ?>
            <div class="updated"><p><strong>Options saved.</strong></p></div>
            <?php
    }
?>
<div class="wrap">
  <h2>Rant-o-Meter Default Settings</h2>
  <p>Choose what defaults you would like Rant-o-Meter to use</p>

  <form name="form1" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
     <fieldset class="options">
        <table width="100%" cellspacing="2" cellpadding="5" class="editform">
        <tr valign="top">
			<th width="33%" scope="row">Level:</th>
			<td><input name="mettarant_level" type="text" width="10" value="<?php echo get_option('mettarant_level'); ?>" /></td>
			</tr><tr valign="top">
			<th width="33%" scope="row">Mood:</th>
			<td><input name="mettarant_mood" type="text" width="60" value="<?php echo get_option('mettarant_mood'); ?>" /></td>
		</tr><tr valign="top">
			<th width="33%" scope="row">Bottom of Scale:</th>
			<td><input name="mettarant_bottom" type="text" width="60" value="<?php echo get_option('mettarant_bottom'); ?>" /></td>
		</tr><tr valign="top">
			<th width="33%" scope="row">Top of Scale:</th>
			<td><input name="mettarant_top" type="text" width="60" value="<?php echo get_option('mettarant_top'); ?>" /></td>
		</tr><tr valign="top">
			<th width="33%" scope="row">Starting Color:</th>
			<td><input name="mettarant_startcolor" type="text" width="60" value="<?php echo get_option('mettarant_startcolor'); ?>" /></td>
		</tr><tr valign="top">
			<th width="33%" scope="row">Ending Color:</th>
			<td><input name="mettarant_endcolor" type="text" width="60" value="<?php echo get_option('mettarant_endcolor'); ?>" /></td>
		</tr><tr valign="top">
			<th width="33%" scope="row">Align ('right' or 'left'):</th>
			<td><input name="mettarant_align" type="text" width="60" value="<?php echo get_option('mettarant_align'); ?>" /></td>
		</tr><tr valign="top">
			<th width="33%" scope="row">Width:</th>
			<td><input name="mettarant_width" type="text" width="60" value="<?php echo get_option('mettarant_width'); ?>" /></td>
		</tr><tr valign="top">
			<th width="33%" scope="row">Height:</th>
			<td><input name="mettarant_height" type="text" width="60" value="<?php echo get_option('mettarant_height'); ?>" /></td>
		</tr><tr valign="top">
            <th width="33%" scope="row">Show Caption?</th>
            <td><input name="mettarant_show_caption" type="text" width="60" value="<?php echo get_option('mettarant_show_caption'); ?>"/>
			</tr><tr valign="top">
			<th width="33%" scope="row">Caption:</th>
			<td><input name="mettarant_caption_text" type="textbox" width="100" height="20" value="<?php echo get_option('mettarant_caption_text'); ?>" /></td>
		</tr><tr valign="top">
			<th width="33%" scope="row">Restore Defaults: </th>
			<td><input name="restore_rant_defaults" type=checkbox />
        </tr>
        </table>

     </fieldset>

     <p class="submit">
       <input type="submit" name="Submit" value="Update Options &raquo;" />
     </p>
  </form>

</div>
<?php 

}

?>