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

jimport ("joomla.application.component.view");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminViewadagencyupgrade extends JViewLegacy {

	function display ($tpl =  null ) {	
		$configs = &$this->get('Configs');	
		$zones = &$this->get('Zones');
		
		$this->assign('configs',$configs);
		$this->assign('zones',$zones);
		parent::display($tpl);
	}

	function packs($tpl = null){
		$configs = &$this->get('Configs');	
		$packs = &$this->get('Packs');
		$zones = &$this->get('Zones');
		
		$lists['zones'] = NULL;
		foreach($zones as $zone){
			$lists['zones'].= "<input type='checkbox' name='replaceable' value='".$zone->zoneid."' />&nbsp;".$zone->z_title."<br />";
		}
		
		$this->assign('lists',$lists);
		$this->assign('configs',$configs);
		$this->assign('packs',$packs);
		$this->assign('zones',$zones);
		parent::display($tpl);
	}
	
	function camps($tpl = null){
		global $mainframe;
		$camps = &$this->get('Camps');
		
		if(count($camps) == 0) { 
			$_SESSION['limit_upgrade_cmp_1'] = NULL;
			$_SESSION['limit_upgrade_cmp_2'] = NULL;
			$mainframe->redirect('index.php?option=com_adagency&final_up=1'); 
		} else {	
			$this->assign('camps',$camps);
			parent::display($tpl);
		}
	}

	function final_up($tpl = null){
		parent::display($tpl);
	}

}

?>