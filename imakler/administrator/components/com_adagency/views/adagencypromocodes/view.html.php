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

class adagencyAdminViewadagencyPromocodes extends JViewLegacy {

	function display ($tpl =  null ) {
		
		JToolBarHelper::title(JText::_('Promotions Manager'), 'generic.png');
		JToolBarHelper::addNew();
		JToolBarHelper::editList();	
		
		JToolBarHelper::divider();

		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();

		JToolBarHelper::divider();

		JToolBarHelper::deleteList();

		$promos = $this->get('Items');
		
		$this->promos = $promos;
		$pagination = $this->get('Pagination');
		$this->assignRef('pagination', $pagination);
		
		parent::display($tpl);

	}

	function editForm($tpl = null) {

		$db = JFactory::getDBO();
		$promo = $this->get('promo');
		$isNew = ($promo->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('Promo').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::spacer();
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::spacer();
			JToolBarHelper::apply();
			JToolBarHelper::divider();

			JToolBarHelper::cancel ('cancel', 'Close');
		}
			
		$this->assign("promo", $promo);

		$db =  JFactory::getDBO();
        $sql = "SELECT * FROM #__ad_agency_settings LIMIT 1";
        $db->setQuery( $sql );
        $configs = $db->loadObject();
		$this->assign("configs", $configs);
			
		$lists = null;

		$this->assign("lists", $lists);
		parent::display($tpl);

	}
	
	function publishAndExpiryHelper(&$img, &$alt, &$times, &$status, $timestart, $timeend, $published, $limit = 0, $used = 0) {
	
		$now = time();
		$nullDate = 0;

		if ( $now <= $timestart && $published == "1" ) {
	                $img = "tick.png";
        	        $alt = JText::_('HELPERPUBLISHED');
		} else if ($limit > 0 && $used >= $limit) {
				$img = "publish_r.png";
				$alt = JText::_('HELPERUSEAGEEXPIRED');
		} else if ( ( $now <= $timeend || $timeend == $nullDate ) && $published == "1" ) {
				$img = "tick.png";
				$alt = JText::_('HELPERPUBLISHED');
		} else if ( $now > $timeend && $published == "1" && $timeend != $nullDate) {
				$img = "publish_r.png";
				$alt = JText::_('HELPEREXPIRED');
		} elseif ( $published == "0" ) {
				$img = "publish_x.png";
				$alt = JText::_('HELPERUNPUBLICHED');
		}
		$times = '';

		if (isset( $timestart)) {
			if ( $timestart == $nullDate) {
					$times .= "<tr><td>".(JText::_("HELPERALWAWSPUB"))."</td></tr>";
				} else {
					$times .= "<tr><td>".(JText::_("HELPERSTARTAT"))." ".date("Y-m-d H:i:s", $timestart)."</td></tr>";
				}
		}
		
		if ( isset( $timeend ) ) {
			if ( $timeend == $nullDate) {
				$times .= "<tr><td>".(JText::_("HELPERNEVEREXP"))."</td></tr>";
			} else {
				$times .= "<tr><td>".(JText::_("HELPEXPAT"))." ".date("Y-m-d H:i:s", $timeend)."</td></tr>";
			}
		}

		$status = '';
		if (!isset ($promo->codelimit)) {
			@$promo->codelimit = 0;
		}
		if (!isset ($promo->used)) {
			$promo->used = 0;
		}

		$remain = $promo->codelimit - $promo->used;
		if (($timeend > $now || $timeend == $nullDate )&& ($limit == 0 || $used < $limit) && $published == "1") {
			$status = JText::_("HELPERACTIVE");
		} else if ($published == "0") {
			$status = "<span style='color:red'>".(JText::_("HELPERUNPUBLISHED"))." </span>";
		} else if ($limit >0  && $used  >= $limit) {
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE"))." (".(JText::_("Amount")).")</span>";
		} else if ($timeend != $nullDate && $timeend < $now && ($remain < 1 && $promo->codelimit > 0)) {
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE"))." (".(JText::_("Date"))." ,".(JText::_("Amount")).")</span>";
		} else if ($timeend < $now && $timeend != $nullDate){
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE"))." (".(JText::_("Date")).")</span>";
		} else {
			$status = "<span style='color:red'>".(JText::_("HELPERPROMOERROR"))."</span>";
		}
		return $status;
	}

}

?>