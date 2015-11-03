<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
/*layout for affiliate ads only (ie. only decrip)

*/
$ht='';
if( $addata->ignore !='')
$ht .= '<span class="ad_ignore_button_span" ><img title="'.JText::_('CLK_ING').'" class="ad_ignore_button layout6_ad_ignore_button" src="'.JUri::Root().'components/com_socialads/images/fbcross.gif" alt="" onClick="'.$addata->ignore.'" /></span>';

$ht.='<div class="ad_prev_wrap layout6_ad_prev_wrap">';

/*Ad description starts here...*/
	$ht .= '<!--div for preview ad-descrip-->
		<div class="preview-bodytext layout6_ad_prev_third">';
			$ht .=''. $adHtmlTyped;
	$ht .='</div>';
/*Ad description ends here*/
$ht .='</div>';
echo $ht;
?>
