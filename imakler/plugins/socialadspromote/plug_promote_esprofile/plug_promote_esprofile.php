<?php
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'activity.integration.stream' );
jimport( 'activity.socialintegration.profiledata' );
$lang = JFactory::getLanguage();
$lang->load('plg_socialadspromote_promote_esprofile', JPATH_ADMINISTRATOR);
class plgsocialadspromoteplug_promote_esprofile extends JPlugin
{
	//function promotlist starts
	function onPromoteList($uid)
	{

		if($uid)
		{
			$user = JFactory::getUser($uid);
		}
		else
		{
			$user = JFactory::getUser();
		}

			jimport( 'joomla.filesystem.file' );
			$db 	= JFactory::getDBO();
			$name = JFile::getName(__FILE__);
			$name = JFile::stripExt($name);
			$eschk = $this->_chkextension();
			if(!empty($eschk)){
				$query = "SELECT CONCAT_WS('|', '".$name."', u.id) as value, u.name AS text FROM #__users AS u
					LEFT JOIN #__social_users AS s ON u.id = s.user_id
					WHERE u.id =". $user->id;
					$db->setQuery($query);
					$itemlist = $db->loadObjectlist();
					return $itemlist;
			}


	}//function promotlist ends

	//function promot data starts
	function onPromoteData($id)		//this function returns the data in array format
	{
			$db = JFactory::getDBO();

			$Itemid = JRequest::getInt('Itemid');

			$eschk = $this->_chkextension();
			if(!empty($eschk)){
				/*$query = "SELECT cf.id FROM #__community_fields as cf
						LEFT JOIN #__community_fields_values AS cfv ON cfv.field_id=cf.id
						WHERE cf.name like '%About me%' AND cfv.user_id=".$id;
				$db->setQuery($query);
				$fieldid = $db->loadresult();
				*/
				$query = "SELECT u.name AS title
						FROM #__users AS u
						WHERE u.id =".$id;
				$db->setQuery($query);
				$previewdata = $db->loadobjectlist();
				//if($fieldid)
					//$query .= " AND cfv.field_id=".$fieldid;
				require_once( JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php' );
				$user     = Foundry::user($id);
				//easysocial data for user

				$activitysocialintegrationprofiledata=new activitysocialintegrationprofiledata();
				$img_path=$activitysocialintegrationprofiledata->getUserAvatar('EasySocial',JFactory::getUser($id));
				$link=$activitysocialintegrationprofiledata->getUserProfileUrl('EasySocial',$id);

				$previewdata[0]->image=$img_path;
				$previewdata[0]->url=JUri::root().substr(JRoute::_($link,false),strlen(JUri::base(true))+1);
				$previewdata[0]->bodytext='';
				return $previewdata;
			}
			else
			{
					return '';
			}
	}
			//_chkextension function checks if the extension folder is present
	function _chkextension()
	{
		jimport('joomla.filesystem.file');
		$extpath = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_easysocial';
		if(JFolder::exists($extpath) )
			return 1;
		else
			return 0;
	}

}//class ends
