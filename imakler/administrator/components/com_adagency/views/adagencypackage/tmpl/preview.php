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


if (JComponentHelper::getParams('com_templates')->get('template_positions_display') == 0) {
  echo JText::_('ADAG_ENABLE_POS');
  echo "<p><img src='" . JURI::root() . "/components/com_adagency/images/enable.gif' /></p>";
  die();
}

$default_template = $this->default_template;
$the_zones = $this->the_zones;
$html = implode('', file(str_replace('administrator/', '', JURI::base().'index.php?tp=1&template='.$default_template)));

$new_css = '<style type="text/css">
    <!--
.mod-preview-info { padding: 2px 4px 2px 4px; border: 2px solid black; position: absolute; background-color: yellow; color: red;opacity: .80; filter: alpha(opacity=80); -moz-opactiy: .80; text-transform:uppercase; font-weight:bold; }.mod-preview-wrapper { background-color:#eee;  border: 1px dotted black; color:#700; opacity: 1; filter: alpha(opacity=50); -moz-opactiy: .50;}
    -->
  </style>
';

$html = str_replace('</head>', $new_css.'</head>', $html);
	$string_notice = 'Notice: ';
	$extract_notice = strpos($html, $string_notice);
	$html_tmp = substr($html, 0, $extract_notice );
	$html = substr($html, ($extract_notice + strlen($string_notice)), strlen($html) );
	
	$string_notice_2 = '<';
	$extract_notice_2 = strpos($html, $string_notice_2);
	$html_tmp = $html_tmp. substr($html, $extract_notice_2, strlen($html) );
	$html = $html_tmp;	
	
	
	$position = strpos($html, '<div class="mod-preview-info">');

	while($position)
		{
		
			$html_tmp = substr($html, 0, $position);
			$html = substr($html, ($position + strlen('<div class="mod-preview-info">')), strlen($html) );
			$the_position_name_end = strpos($html, '[');
			$the_position_name = substr($html, 0, $the_position_name_end);

			$html_tmp = $html_tmp.'<div class="mod-preview-info" was_checked="1">'.$the_position_name.'</div>';
			$the_div_end = strpos($html, '</div>');
			$html = substr($html, ($the_div_end + strlen('</div>')), strlen($html) );
			$html_tmp = $html_tmp.$html;

			$html = $html_tmp;
			$position = strpos($html, '<div class="mod-preview-info">');	

		}	
	
	echo $html;
?>
