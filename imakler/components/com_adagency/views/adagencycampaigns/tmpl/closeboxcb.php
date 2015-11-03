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
    $document->addScript( JURI::base() . "components/com_adagency/includes/js/jquery.adagency.js" );
    $document->addScriptDeclaration("
        ADAG(function() {
            current_camp = window.parent.ADAG('.camp" . $this->vars->cid . "');
            //console.log(current_camp);
            window.setTimeout(function() {
                window.parent.ADAG.data(current_camp[0], 'totalbanners', '" . $this->vars->totalads . "');
                window.parent.ADAG('#close_cb').click();
            }, 1300);
        });
    ");

    echo "<div style='font-weight: bold; font-size: 22px;margin: 190px 70px;'>"
    . JText::_('ADAG_REM_ADS_CMP') . "</div>";
?>
