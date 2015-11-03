<?php
/**
 * @version		1.5 jgive $
 * @package		jgive
 * @copyright	Copyright © 2013 - All rights reserved.
 * @license		GNU/GPL
 * @author		TechJoomla
 * @author mail	extensions@techjoomla.com
 * @website		http://techjoomla.com
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
// Component Helper
jimport('joomla.application.component.helper');

class createAdHelper
{

	function getUserCampaign($userid)
	{
		$db=JFactory::getDBO();
		$query = "SELECT camp_id,campaign,daily_budget FROM #__ad_campaign WHERE user_id=$userid";
		$db->setQuery($query);
		$camp_value = $db->loadobjectList();
		return $camp_value;
	}

	// VM:
	function getLatestPendigOrder($ad_id,$userid)
	{
		$db=JFactory::getDBO();
		$query='SELECT p.`id` FROM `#__ad_payment_info` as p
				WHERE p.ad_id='.$ad_id.' AND p.`payee_id`='.$userid . ' AND p.`status`=\'p\' ORDER BY `id` DESC' ;
		$db->setQuery($query);
		return $db->loadResult();
	}
	/**
	 * @param desingAdd object provide ad data information
	 * @return
	 * */
	function  sendForApproval($desingAdd)
	{
		$return['sa_sentApproveMail'] = '';
		if(empty($desingAdd))
		{
			return $return;
		}
		$db=JFactory::getDBO();

		// 1. check whether any order  is confirmed
		$query='SELECT p.`id` FROM `#__ad_payment_info` as p
				WHERE p.ad_id='.$desingAdd->ad_id.' AND p.`status`=\'C\' ORDER BY `id` DESC' ;
		$db->setQuery($query);
		$ConfirmOrders = $db->loadResult();

		if(empty($ConfirmOrders))
		{
			// no order is confirm then allow to edit ad
			return $return;
		}

		// get old ad details
		$query='SELECT a.`ad_id`,a.`ad_image`,a.`ad_title`,a.`ad_body`,a.`ad_url2` FROM `#__ad_data` as a
				WHERE a.ad_id='.$desingAdd->ad_id . '  AND a.ad_approved=1';
		$db->setQuery($query);
		$oldAd = $db->loadObject();

		// ANY ONE IS CHANGED
		if(!empty($oldAd) &&
			($oldAd->ad_image != $desingAdd->ad_image
			|| $oldAd->ad_title!= $desingAdd->ad_title
			||  $oldAd->ad_body != $desingAdd->ad_body
			||  $oldAd->ad_url2 != $desingAdd->ad_url2
			)
		)
		{
			$createAdHelper = new createAdHelper;
			$createAdHelper->adminAdApprovalEmail($desingAdd->ad_id);
			$return['ad_approved'] = 0;
			$return['sa_sentApproveMail']=1;
			return $return;
		}
		return $return;
	}

	function adminAdApprovalEmail($ad_id)
	{
		$db=JFactory::getDBO();
		$query='SELECT a.`ad_id`,a.`ad_image`,a.`ad_title`,a.`ad_body`,a.`ad_url2` FROM `#__ad_data` as a
				WHERE a.ad_id='.$ad_id ;//. '  AND a.ad_approved=1';
		$db->setQuery($query);
		$oldAd = $db->loadObject();

			jimport('joomla.utilities.utility');
			$user = JFactory::getUser();
			global $mainframe;
			$mainframe = JFactory::getApplication();
			$sitelink	= JUri::root();
			$socialadshelper = new socialadshelper;
			$manageAdLink = "<a href='".$sitelink."administrator".DS."index.php?option=com_socialads&view=approveads' targe='_blank'>".JText::_( "COM_SOCIALADS_EMAIL_THIS_LINK" )."</a>";

			// GET config details
			$frommail  	= $mainframe->getCfg('mailfrom');
			$fromname = $mainframe->getCfg('fromname');;
			$adUserName = $user->username	;
			$adTitle = $oldAd->ad_title;
			$siteName = $mainframe->getCfg('sitename');
			$today		= date('Y-m-d H:i:s');
			//DEFINE SEARCH
			$find 				= array('[SEND_TO_NAME]','[ADVERTISER_NAME]','[SITENAME]','[SITELINK]','[ADTITLE]','[CONTENT]', '[TIMESTAMP]');


			// SEND ADMIN MAIL
			$recipient = $frommail;
			$subject = JText::_( "COM_SOCIALADS_APPRVE_MAIL_TO_ADMIN_SUBJECT" );
			$adminEmailBody = JText::sprintf( "COM_SOCIALADS_EMAIL_HELLO") . JText::sprintf('COM_SOCIALADS_APPRVE_MAIL_TO_ADMIN_CONTENT',$manageAdLink) .  JText::sprintf( "COM_SOCIALADS_EMAIL_SITENAME_TEAM" ) ;

			// NOW REPLACE TAG
			$replace 			= array($fromname,$adUserName,$siteName,$sitelink,$adTitle,$content,$today);
			$adminEmailBody	    = str_replace($find, $replace, $adminEmailBody);
			$status  = $socialadshelper->sendmail($recipient,$subject,$adminEmailBody,$bcc_string='',$singlemail=0,$attachmentPath="");

			// SEND TO ADVERTISER MAIL
			 $advertiserEmail = $user->email;
			$subject = JText::_( "COM_SOCIALADS_APPRVE_MAIL_TO_ADVERTISER_SUBJECT" );
			$advertiserEmailBody = JText::sprintf( "COM_SOCIALADS_EMAIL_HELLO" ) . JText::sprintf('COM_SOCIALADS_APPRVE_MAIL_TO_ADVERTISR_CONTENT') .  JText::sprintf( "COM_SOCIALADS_EMAIL_SITENAME_TEAM" ) ;

			// NOW REPLACE TAG
			$replace 			= array($adUserName,$adUserName,$siteName,$sitelink,$adTitle,$content,$today);
			$advertiserEmailBody= str_replace($find, $replace, $advertiserEmailBody);

			$status  = $socialadshelper->sendmail($advertiserEmail,$subject,$advertiserEmailBody,$bcc_string='',$singlemail=0,$attachmentPath="");
	}



}
