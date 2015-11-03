<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
/*layout for text ads only (ie. title & decrip) */
$ht='';
if( $addata->ignore !='')
$ht .= '<span class="ad_ignore_button_span" style="display:none;"><img title="'.JText::_('CLK_ING').'" class="ad_ignore_button layout4_ad_ignore_button" src="'.JUri::Root().'components/com_socialads/images/fbcross.gif" alt="" onClick="'.$addata->ignore.'" /></span>';

$ht.='<div class="ad_prev_wrap layout4_ad_prev_wrap well well-small">';

$ht.= '<div>';
/*Ad title starts here...*/
	$ht .= '<!--div for preview ad-title-->
		<div class="layout4_ad_prev_first">';
		$ht.='<a class="preview-title preview-title-lnk layout4_ad_prev_anchor" href="'.$addata->link.'" target="_blank">';
		$ht .= ''.$addata->ad_title;
		$ht.='</a>';
	$ht.= '</div>';
/*Ad title ends here*/

/*Ad description starts here...*/
	$ht .= '<!--div for preview ad-descrip-->
		<div class="preview-bodytext layout4_ad_prev_third">';
			$ht .=''. $adHtmlTyped;
			//changed in 2.7.5 beta 2
			//$ht.=$adHtmlTyped;
	$ht .='</div>';
/*Ad description ends here*/
$ht.= '</div><div style="clear:both;"></div>';
$ht.= '</div>';
echo $ht;
?>
