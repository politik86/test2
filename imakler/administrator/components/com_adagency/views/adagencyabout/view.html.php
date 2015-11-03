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

class adagencyAdminViewadagencyabout extends JViewLegacy {

	function display ($tpl =  null ) {
		
		//JToolBarHelper::title(JText::_('AD_ABOUTTITLE'), 'generic.png');

		JToolBarHelper::Cancel();
		
		$component = array();
		$modulezone = array();
		$modulemenu = array();
		$component['installed'] = $modulezone['installed'] = $modulemenu['installed'] = 0;
		$component['name'] = 'iJoomla Ad Agency';
		$component['file'] = JPATH_SITE. DIRECTORY_SEPARATOR ."administrator". DIRECTORY_SEPARATOR ."components". DIRECTORY_SEPARATOR ."com_adagency". DIRECTORY_SEPARATOR ."adagency.xml"; 
		
		$modulezone['name'] = 'Ad Agency Zone';
		$modulezone['file'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_zone". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_zone.xml";
		$modulezone['php'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_zone". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_zone.php";
		
		$modulemenu['name'] = 'Ad Agency Menu';
		$modulemenu['file'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_menu". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_menu.xml";
		$modulemenu['php'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_menu". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_menu.php";
		
		$modulecpanel['name'] = 'Ad Agency cPanel';
		$modulecpanel['file'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_cpanel". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_cpanel.xml";
		$modulecpanel['php'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_cpanel". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_cpanel.php";
		
		$modulegeo['name'] = 'Ad Agency Geo';
		$modulegeo['file'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_geo". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_geo.xml";
		$modulegeo['php'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_geo". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_geo.php";
		
		$moduleremote['name'] = 'Ad Agency Remote';
		$moduleremote['file'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_remote". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_remote.xml";
		$moduleremote['php'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_remote". DIRECTORY_SEPARATOR ."mod_ijoomla_adagency_remote.php";
		
		if ( file_exists($component['file']) ){
			$component['installed'] = 1;
			$data = implode ("", file ( $component['file'] ) );
	        $pos1 = strpos ($data,"<version>");
	        $pos2 = strpos ($data,"</version>");
	        $component['version'] = 'version '.substr ($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
		}
		
		if ( file_exists($modulezone['file']) && file_exists($modulezone['php']) ){
			$modulezone['installed'] = 1;
			$data = implode ("", file ( $modulezone['file'] ) );
	        $pos1 = strpos ($data,"<version>");
	        $pos2 = strpos ($data,"</version>");
	        $modulezone['version'] = 'version '.substr ($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
		}
		
		if ( file_exists($modulemenu['file']) && file_exists($modulemenu['php']) ){
			$modulemenu['installed'] = 1;
			$data = implode ("", file ( $modulemenu['file'] ) );
	        $pos1 = strpos ($data,"<version>");
	        $pos2 = strpos ($data,"</version>");
	        $modulemenu['version'] = 'version '.substr ($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
		}
		
		if ( file_exists($modulecpanel['file']) && file_exists($modulecpanel['php']) ){
			$modulecpanel['installed'] = 1;
			$data = implode ("", file ( $modulecpanel['file'] ) );
	        $pos1 = strpos ($data,"<version>");
	        $pos2 = strpos ($data,"</version>");
	        $modulecpanel['version'] = 'version '.substr ($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
		}
		
		if ( file_exists($modulegeo['file']) && file_exists($modulegeo['php']) ){
			$modulegeo['installed'] = 1;
			$data = implode ("", file ( $modulegeo['file'] ) );
	        $pos1 = strpos ($data,"<version>");
	        $pos2 = strpos ($data,"</version>");
	        $modulegeo['version'] = 'version '.substr ($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
		}
		
		if ( file_exists($moduleremote['file']) && file_exists($moduleremote['php']) ){
			$moduleremote['installed'] = 1;
			$data = implode ("", file ( $moduleremote['file'] ) );
	        $pos1 = strpos ($data,"<version>");
	        $pos2 = strpos ($data,"</version>");
	        $moduleremote['version'] = 'version '.substr ($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
		}
		
		$this->assignRef('component', $component);
		$this->assignRef('modulezone', $modulezone);	
		$this->assignRef('modulemenu', $modulemenu);
		$this->assignRef('modulecpanel', $modulecpanel);
		$this->assignRef('modulegeo', $modulegeo);
		$this->assignRef('moduleremote', $moduleremote);
		
		parent::display($tpl);

	}
	
	function vimeo($tpl = null) {
        $id = JRequest::getVar('id', '0');
        $this->assignRef('id', $id);
        parent::display($tpl);
    }

}

?>