<?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
define('_JEXEC', 1);
defined('_JEXEC') or die( 'Restricted access' );

function smart_spam($code){
	@putenv('GDFONTPATH=' . realpath('.'));
	$font = 'FONT.TTF'; 
	
	// antispam image height
	$height = 40;
	
	// antispam image width
	$width = 110;
	
	
	$font_size = $height * 0.6;
	$image = @imagecreate($width, $height);
	$background_color = @imagecolorallocate($image, 255, 255, 255);
	$noise_color = @imagecolorallocate($image, 20, 40, 100);
	
	/* add image noise */
	for($i=0; $i < ($width * $height) / 4; $i++) {
	  @imageellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
	}
	/* render text */
	$text_color = @imagecolorallocate($image, 20, 40, 100);
	@imagettftext($image, $font_size, 0, 7, 29, $text_color, $font , $code) or die('Cannot render TTF text.');
	
	//output image to the browser *//*
	header('Content-Type: image/png');
	@imagepng($image) or die('imagepng error!');
	@imagedestroy($image); 
	exit();
}

smart_spam($_GET['code']);
?>