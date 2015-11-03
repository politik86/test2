<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
/*layout for img ads only (ie. images only) */
$ht='';
if( $addata->ignore !='')
$ht .= '<span class="ad_ignore_button_span" style="display:none;"><img title="'. JText::_('CLK_ING').'" class="ad_ignore_button layout5_ad_ignore_button" src="'.JUri::Root().'components/com_socialads/images/fbcross.gif" alt="" onClick="'.$addata->ignore.'" /></span>';

$ht.='<div class="ad_prev_wrap layout5_ad_prev_wrap well well-small">';
/*Ad image starts here...*/
$ht.='<!--div for preview ad-image-->
	<div class="layout5_ad_prev_second">';
	$ht.='<a '.$upload_area.' href="'.$addata->link.' " target="_blank">';
	//$ht.= '<img class="layout5_ad_prev_img" alt="" src="'.JUri::Root().$addata->ad_image.'" border="0" />';
	//changed in 2.7.5 beta 2
	$ht.=$adHtmlTyped;
	$ht.='</a>';
	$ht.='</div>';
/*Ad image ends here*/
$ht.= '</div>';

echo $ht;
?>
