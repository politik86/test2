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

class adagencyAdminViewadagencygeo extends JViewLegacy {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('AD_GEOCHANS'), 'generic.png');
		JToolBarHelper::addNew('edit','New');
		JToolBarHelper::editList();			
		JToolBarHelper::deleteList(JText::_('ADAG_GEO_CNF_DEL'),'delete');
		$db = JFactory::getDBO();
		
		$public = JRequest::getVar('public');
		if(isset($public)){
			$_SESSION['filter_public']= $public;
			$search_public = $public;
		}
		elseif(isset($_SESSION['filter_public'])) {
			$search_public = $_SESSION['filter_public'];
		}
		$this->assignRef('p_filter', $search_public);
		
		$query = "SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$db->setQuery($query);
		$params = $db->loadResult();
		$params = @unserialize($params);
		if(isset($params['timeformat'])){
			$params = $params['timeformat'];
		} else { $params = "-1"; }
		
		$this->assignRef('params', $params);
		
		$chans = $this->_models['adagencygeo']->getChannels();
		//echo "<pre>";var_dump($chans);die();echo "</pre><hr />";
		
		$pagination =  $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);
		foreach($chans as $element) {
			$who =  $this->_models['adagencygeo']->getUIDByAid($element->advertiser_id);
			$what = $this->_models['adagencygeo']->getBannerById($element->banner_id);
			if(!isset($what->title)) { @$what->title = NULL;}
			if(isset($what->media_type)) {
				$blink = NULL;
				if($what->media_type == 'Advanced') {
					$blink = "index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid[]=".intval($element->banner_id);
				} elseif ($what->media_type == 'TextLink'){
					$blink = "index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=".intval($element->banner_id);
				} else {
					$blink = "index.php?option=com_adagency&controller=adagency".$what->media_type."&task=edit&cid[]=".intval($element->banner_id);
				}
				$element->blink = $blink;
			} else { $element->blink = NULL;}
			$element->uid = $who[0];
			$element->user = $who[1];
			$element->bname = $what->title;
			//echo "<pre>";var_dump($what);die();
		}
		$ul =  $this->get('UL');
		$this->assign("ul", $ul);
		//echo "<pre>";var_dump($users);die();
		$this->assign("chans", $chans);
		parent::display($tpl);
	}
	
	function settings ($tpl = null) {
		JToolBarHelper::title(JText::_('ADAG_GEOSET'), 'generic.png');
		JToolBarHelper::apply('applysettings', 'Apply');
		JToolBarHelper::save('savesettings', 'Save');
		JToolBarHelper::cancel('cancel', 'Cancel');
	
		$this->_model = $this->getModel("adagencygeo");
		$configs = $this->_model->getConf();		
		$this->assign("configs",$configs);
		$data = unserialize($this->_model->getSettings());
		//echo "<pre>";var_dump($configs);die();
		$this->assign("data",$data);
		parent::display($tpl);	
	}
	
	function editForm ($tpl = null)	{
		JToolBarHelper::apply('applychannel', 'Apply');
		JToolBarHelper::save('savechannel', 'Save');
		JToolBarHelper::cancel('cancelGoToChannels', 'Cancel');
		
		$this->_model = $this->getModel("adagencygeo");
		$currentChannel = $this->_model->getChannel();
		//echo "<pre>";var_dump($currentChannel);die();

		$configs = $this->_model->getConf();		
		$this->assign("configs",$configs);
		
		if(isset($currentChannel)) {
			JToolBarHelper::title(JText::_('ADAG_GEOEDIT'), 'generic.png');
		} else {
			JToolBarHelper::title(JText::_('ADAG_GEONEW'), 'generic.png');
		}
		$this->assign("currentChannel",$currentChannel);
		
		parent::display($tpl);
	}
}

?>