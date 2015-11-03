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
defined( '_JEXEC' ) or die( 'Restricted access' );

$get = $this->get;
$link = $this->link;
$link = JURI::base().$this->link;
JRequest::setVar("tmpl", "component");

// echo $link;die();
if (function_exists('curl_init')) {
	// initialize a new curl resource
	$ch = curl_init();

	// set the url to fetch
	curl_setopt($ch, CURLOPT_URL, $link);

	// don't give me the headers just the content
	curl_setopt($ch, CURLOPT_HEADER, 0);

	// return the value instead of printing the response to browser
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// use a user agent to mimic a browser
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');

	$content = file_get_contents($link); //curl_exec($ch);
	
	// remember to always close the session and free all resources
	curl_close($ch);

	$html = $content;

	if(isset($get['cid'])&&($get['cid'] != NULL)){
	$new_css = '<style type="text/css">
		<!--
		#ijoomlazone'.$get['cid'].' {
			background: yellow; border: 4px dotted black;
			overflow:hidden;
		}
		-->
	  </style>
	';
	} else {
	$new_css = '<style type="text/css">
		<!--
		.mod_ijoomlazone {
			background: yellow; border: 4px dotted black;
		}
		-->
	  </style>
	';
	}

	$html = str_replace('</head>', $new_css.'</head>', $html);

	echo $html;
} else {
	echo "<div style='text-align:center;font-size:20px;font-weight:bold; margin-top: 25%;'>
		Please enable CURL extension in your PHP server configuration to view this page properly!<br />
	</div>";
}
?>
