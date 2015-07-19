<?php
/*
	Plugin Name: Dominant Colour
	Description: Add an attachment meta option to provide the hex of the most dominant colour of an image.
	Version: 1.0
	Author: Liam Gladdy
	Author URI: https://gladdy.uk
*/

require('vendor/autoload.php');
use ColorThief\ColorThief;

add_action('edit_attachment', 'update_attachment_color_dominance', 10, 1);
add_action('add_attachment', 'update_attachment_color_dominance', 10, 1);

function update_attachment_color_dominance($attachment_id) {
	$image = wp_get_attachment_image_src($attachment_id);
	
	if (!$image) return;
	
	$dominantColour = ColorThief::getColor($image[0]);
	$hex = rgb2hex($dominantColour);
	
	update_post_meta($attachment_id, 'dominant_colour_hex', $hex);
	update_post_meta($attachment_id, 'dominant_colour_rgb', $dominantColour);	
	
	$palette = ColorThief::getPalette($image[0], 8);
	update_post_meta($attachment_id, 'colour_palette_rgb', $palette);	
	
	$hex_palette = array();
	foreach($palette as $rgb) {
		$hex_palette[] = rgb2hex($rgb);
	}
	update_post_meta($attachment_id, 'colour_palette_hex', $hex_palette);	
}

function rgb2hex($rgb) {
   $hex = "#";
   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

   return $hex; // returns the hex value including the number sign (#)
}

function get_colour_data($attachment_id, $thing_to_get) {
	$data = get_post_meta($attachment_id, $thing_to_get);
	if (!$data) {
		update_attachment_color_dominance($attachment_id);
		return get_post_meta($attachment_id, $thing_to_get);
	} else {
		return $data;
	}
}