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

class adagencyAdminViewadagencyAdvertisers extends JViewLegacy {

    function display ($tpl =  null ) {
        JToolBarHelper::title(JText::_('AD_ADV_MANAGER'), 'generic.png');
        JToolBarHelper::custom( 'wizard', 'new', 'new', 'New', false, false );
        JToolBarHelper::publishList('approve_task', JText::_("AD_APPROVE"));
		JToolBarHelper::unpublishList('decline_task', JText::_("AD_DECLINE"));
		JToolBarHelper::editList();			
		
        JToolBarHelper::deleteList(JText::_('AGENCYCONFIRMDEL'));
        $advertisers = $this->get('listAdvertisers');
        $this->assignRef('advertisers', $advertisers);
        $pagination = $this->get( 'Pagination' );
        $this->assignRef('pagination', $pagination);

        $db = JFactory::getDBO();
        $query = "SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
        $db->setQuery($query);
        $params = $db->loadResult();
        $params = @unserialize($params);
        if(isset($params['timeformat'])){
            $params = $params['timeformat'];
        } else { $params = "-1"; }

        //echo $params;die();
        $this->assignRef('params', $params);

        parent::display($tpl);
    }

    function wizard($tpl = null) {
        JToolBarHelper::title(JText::_('ADAG_ADV_WIZ'), 'generic.png');
        JToolBarHelper::custom('next','forward.png','forward_f2.png','Next',false);
        JToolBarHelper::cancel('cancel','Cancel');
        parent::display($tpl);
    }

    function temp($tpl = null) {
        JToolBarHelper::title(JText::_('ADAG_ADV_WIZ'), 'generic.png');
        JToolBarHelper::custom('existing','forward.png','forward_f2.png','Next',false);
        JToolBarHelper::cancel('cancel','Cancel');
        parent::display($tpl);
    }

    function existing($tpl = null) {
    	$helper = new adagencyAdminHelper();
        JToolBarHelper::title(JText::_('ADAG_ADV_WIZ'), 'generic.png');
        JToolBarHelper::custom('storeExistent','save','save','Save',false);
        JToolBarHelper::cancel('cancel','Cancel');
        require_once( JPATH_COMPONENT.DS.'helpers'.DS.'sajax.php' );
        global $mainframe;
        $data = JRequest::get('data');
        
        $usr = $this->get('UserToAdv');
		
        if(!isset($usr->id)) {
            $msg = JText::_('ADVSAVEFAILED_EXST');
            if(isset($data['tmpl'])&&($data['tmpl'] == 'component')) { $tmpl = '&tmpl=component'; } else { $tmpl = NULL;  }
            $mainframe->redirect('index.php?option=com_adagency&controller=adagencyAdvertisers&task=temp'.$tmpl, $msg, 'notice');
        } else {
            $_SESSION['temp_user'] = NULL; //unset($_SESSION['temp_user']);
        }

        $configs = $this->_models['adagencyconfig']->getConfigs();

        if(isset($configs->show)){
            $show=explode(";",$configs->show);
        } else {$show = NULL;}
        if(isset($configs->mandatory)){
            $mandatory=explode(";",$configs->mandatory);
        } else {$mandatory = NULL;}
        if(count($show)>=2){ unset($show[count($show)-1]);}
        if(count($mandatory)>=2){ unset($mandatory[count($mandatory)-1]);}

        $configs->show = $show;
        $configs->mandatory = $mandatory;

        $country_option = $helper->get_country_options(NULL, false, $configs);
        $lists['country_option'] = $country_option;

        $shipcountry_option = $helper->get_country_options(NULL, true, $configs);
        $lists['shipcountry_options'] = $shipcountry_option;
        $lists['customerlocation'] = $helper->get_store_province(NULL);
        $lists['customershippinglocation'] = $helper->get_store_province(NULL, true, $configs);

        $sts_select = new StdClass;
        $sts_select->status = JText::_("ADAG_SEL_STS");
        $sts_select->value = '';
        $sts_approve = new StdClass;
        $sts_approve->status = JText::_("VIEWADVERTISERAPPROVED");
        $sts_approve->value = "Y";
        $sts_decline = new StdClass;
        $sts_decline->status = JText::_("ADAG_DECLINED");
        $sts_decline->value = "N";
        $sts_pending = new StdClass;
        $sts_pending->status = JText::_("ADAG_PENDING");
        $sts_pending->value = 'P';
        $statuses[] = $sts_select;$statuses[] = $sts_approve; $statuses[] = $sts_decline;$statuses[] = $sts_pending;

        $lists['approved'] = JHTML::_('select.genericlist', $statuses, 'approved', 'class="inputbox" size="1"', 'value', 'status', 'Y');

        if(!isset($_SESSION['adv_ext'])) {
            $_SESSION['adv_ext'] = NULL;
        }

        $this->assignRef('datas', $_SESSION['adv_ext']);
        $this->assignRef('lists', $lists);
        $this->assignRef('configs', $configs);
        $this->assignRef('usr', $usr);

        parent::display($tpl);
    }

    function editForm($tpl = null) {
    	$helper = new adagencyAdminHelper();
        jimport("joomla.database.table.user");
        require_once( JPATH_COMPONENT.DS.'helpers'.DS.'sajax.php' );
        $db = JFactory::getDBO();
        $advertiser = $this->get('advertiser');
        $isNew = ($advertiser->aid < 1);
        $text = $isNew?JText::_('New'):JText::_('Edit');
        JToolBarHelper::title(JText::_('AD_EDIT_ADV').":<small>[".$text."]</small>");
        JToolBarHelper::save();
        if ($isNew) {
            JToolBarHelper::cancel();
        } else {
            JToolBarHelper::cancel ('cancel', 'Close');
        }
        $user = new JUser();
        if (!$isNew) $user->load($advertiser->user_id);
        $this->assign("user", $user);
        $this->assign("advertiser", $advertiser);
        if(isset($_SESSION['ad_country'])) $advertiser->country = $_SESSION['ad_country'];
        $configs = $this->_models['adagencyconfig']->getConfigs();

        if(isset($configs->show)){
            $show=explode(";",$configs->show);
        } else {$show = NULL;}
        if(isset($configs->mandatory)){
            $mandatory=explode(";",$configs->mandatory);
        } else {$mandatory = NULL;}
        if(count($show)>=2){ unset($show[count($show)-1]);}
        if(count($mandatory)>=2){ unset($mandatory[count($mandatory)-1]);}

        $configs->show = $show;
        $configs->mandatory = $mandatory;

        $country_option = $helper->get_country_options($advertiser, false, $configs);
        $lists['country_option'] = $country_option;
        $profile = new StdClass();
        $profile->country = $advertiser->country;
        $profile->state = $advertiser->state;
        if(isset($_SESSION['ad_state']) && $_SESSION['ad_state']!='') $advertiser->state = $_SESSION['ad_state'];
        $shipcountry_option = $helper->get_country_options($advertiser, true, $configs);
        $lists['shipcountry_options'] = $shipcountry_option;
        $lists['customerlocation'] = $helper->get_store_province($advertiser);
        $profile = new StdClass();
        $profile->country = $advertiser->shipcountry;
        $profile->state = $advertiser->state;
        $lists['customershippinglocation'] = $helper->get_store_province($profile, true, $configs);
        //check for plugins
        $sqls = "SELECT count(*) FROM #__ad_agency_plugins";
        $db->setQuery($sqls);
        $exists = $db->loadResult();
        if (intval($exists) >0 ) {
            $content = $this->_models['adagencyplugin']->getPluginOptions($advertiser->paywith);
            $lists['paywith'] = $content;
        } else {
            $lists['paywith'] = '<select name="payment_type"></select>';
            $lists['paywith'] .= "&nbsp;&nbsp;&nbsp;Please upload payment plugins";
        }
        //end check

        // Create status list
        if($isNew) { $status_selected = 'Y'; } else { $status_selected = $advertiser->approved; }
        $sts_select = new StdClass;
        $sts_select->status = JText::_("ADAG_SEL_STS");
        $sts_select->value = '';
        $sts_approve = new StdClass;
        $sts_approve->status = JText::_("VIEWADVERTISERAPPROVED");
        $sts_approve->value = "Y";
        $sts_decline = new StdClass;
        $sts_decline->status = JText::_("ADAG_DECLINED");
        $sts_decline->value = "N";
        $sts_pending = new StdClass;
        $sts_pending->status = JText::_("ADAG_PENDING");
        $sts_pending->value = 'P';
        $statuses[] = $sts_select;$statuses[] = $sts_approve; $statuses[] = $sts_decline;$statuses[] = $sts_pending;

        $lists['approved'] = JHTML::_('select.genericlist', $statuses,'approved','class="inputbox" size="1"','value','status',$status_selected);

     //   if ($user->block=='1') $isenabled='0'; else $isenabled='1';

        //$lists['enabled'] = JHTML::_('select.booleanlist',  'enabled', '', $isenabled );
        $this->assign("lists", $lists);
        $this->assign("configs", $configs);
        parent::display($tpl);
    }

    function approve( &$row, $i, $prefix='' )
    {
        //$imgP = "components/com_adagency/images/pending.gif";
		$icon_class = "fa fa-clock-o";
        if($row->approved=='Y') {
           // $img = 'templates/bluestork/images/admin/'.$imgY;
            $task = "pending";
            $alt = JText::_('Approve');
            $action = JText::_('ADAG_CHTPEN');
			$icon_class = "fa fa-check";
        } elseif ($row->approved=='N') {
           // $img = 'templates/bluestork/images/admin/'.$imgX;
            $task = "approve";
            $alt = JText::_('Unapprove');
            $action = JText::_('Approve item');
			$icon_class = "fa fa-ban";
        } elseif ($row->approved=='P') {
           // $img = $imgP;
            $task = "unapprove";
            $alt = JText::_("ADAG_PENDING");
            $action = "Unnapprove Item";
			$icon_class = "fa fa-clock-o";
        } else {return false;}

        //$href = '<img src="'. $img .'" border="0" alt="'. $alt .'" />';
		$href = '<i class="'.$icon_class.'"></i>';
        return $href;
    }

    function block( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
    {
        $img 	= ($row->block==0) ? 'templates/bluestork/images/admin/'.$imgY : 'templates/bluestork/images/admin/'.$imgX;
        $task 	= ($row->block==0) ? 'block' : 'unblock';
        $alt 	= ($row->block==0) ? JText::_( 'Block' ) : JText::_( 'Unblock' );
        $action = ($row->block==0) ? JText::_( 'Unblock Item' ) : JText::_( 'Block item' );
        $href = '
        <a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
        <img src="'. $img .'" border="0" alt="'. $alt .'" /></a>'
        ;
        return $href;
    }

    function addadv ($tpl =  null ) {
        JToolBarHelper::title(JText::_('Advertisers Manager'), 'generic.png');
        JToolBarHelper::addNewX();
        JToolBarHelper::editListX();
        JToolBarHelper::deleteList(JText::_('AGENCYCONFIRMDEL'));
        $db1 = JFactory::getDBO();
        $db1->setQuery("SELECT aid FROM #__ad_agency_advertis ORDER BY aid DESC LIMIT 1 ");
        $newest_adv=$db1->loadResult();
        $this->assignRef('newest_adv', $newest_adv);
        $advertisers = $this->get('listAdvertisers');
        $this->assignRef('advertisers', $advertisers);
        $pagination =  $this->get( 'Pagination' );
        $this->assignRef('pagination', $pagination);
        parent::display($tpl);
    }

}
?>
