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

    $document = JFactory::getDocument();
    $document->addStyleSheet( JURI::base() . "components/com_adagency/includes/css/changecb.css" );
    $document->addScript( JURI::base() . "components/com_adagency/includes/js/jquery.adagency.js" );
    $camp = $this->camp;
    $banners = $this->banners;
    //echo "<pre>";var_dump($banners);die();
?>
<div id="adagency_container">
<div id="changecb_container">
<div id="ijadagencycampaigns2" class="ada-campaigns-heading">
    <h2 class="ada-campaigns-title"><?php echo JText::sprintf('ADAG_CMP_SEL_ADS2', $camp->name); ?></h2>
</div>

<form class="uk-form" id="goform" action="index.php?option=com_adagency&controller=adagencyCampaigns&task=savechangecb" method="post">

<table class="uk-table">
<thead>
	<tr>
	    <td><?php echo ucwords(JText::_('AD_NEW_CAMP_BAN_NAME')); ?></td>
   		<td><?php echo JText::_('ADAG_REMOVE'); ?></td>
	</tr>
</thead>

<tbody>

<?php
    $k = 0;
    if ( is_array($banners) )
    foreach ($banners as $banner) {
?>
    <tr class="row<?php echo $k; ?>">
        <td>
            <?php echo $banner->title; ?>
        </td>
        <td>
            <input type="checkbox" name="todel[]" value="<?php echo $banner->id; ?>" />
        </td>
	</tr>
	<tr>
		<td></td>
	</tr>
<?php
        $k = 1 - k;
    }
?>

</tbody>
</table>

<input type="hidden" name="campaignid" value="<?php echo $camp->id; ?>" />
<input type="hidden" name="component" value="com_adagency" />
<input type="hidden" name="controller" value="adagencyCampaigns" />
<input type="hidden" name="task" value="savechangecb" />

<input type="submit" name="submit" class="uk-button uk-button-success"> value="<?php echo JText::_('AD_SAVE'); ?>" />

</form>
</div>
</div>
