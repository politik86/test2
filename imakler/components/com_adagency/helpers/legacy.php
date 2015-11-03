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

// Allow Legacy for J!1.5, J!1.6, J!1.7, J!2.5 < 2.5.6
if(version_compare(JVERSION, '2.5.6', 'lt')){
	jimport('joomla.application.component.controller');
	jimport('joomla.application.component.view');
	jimport('joomla.aplication.component.model');
	class JControllerLegacy extends JController{};
	class JViewLegacy extends JView{};
	class JModelLegacy extends JModel{};
}
?>