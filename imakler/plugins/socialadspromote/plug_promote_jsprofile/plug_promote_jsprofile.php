<?php
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );
$lang = JFactory::getLanguage();
$lang->load('plg_socialadspromote_promote_jsprofile', JPATH_ADMINISTRATOR);
class plgsocialadspromoteplug_promote_jsprofile extends JPlugin
{
	//function promotlist starts
	function onPromoteList($uid)
	{
			jimport( 'joomla.filesystem.file' );
			$db 	= JFactory::getDBO();

			if($uid)
			{
				$user = JFactory::getUser($uid);
			}
			else
			{
				$user = JFactory::getUser();
			}

			$name = JFile::getName(__FILE__);
			$name = JFile::stripExt($name);

			$jschk = $this->_chkextension();
			if(!empty($jschk)){
					$query = "SELECT CONCAT_WS('|', '".$name."', u.id) as value, u.name AS text FROM #__users AS u
										LEFT JOIN #__community_users AS c ON u.id = c.userid
										WHERE u.id =". $user->id ;

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

			$jschk = $this->_chkextension();
			if(!empty($jschk)){
				$query = "SELECT cf.id FROM #__community_fields as cf
								LEFT JOIN #__community_fields_values AS cfv ON cfv.field_id=cf.id
									WHERE cf.name like '%About me%' AND cfv.user_id=".$id;
				$db->setQuery($query);
				$fieldid = $db->loadresult();

				$query = "SELECT u.name AS title, cu.avatar AS image, cfv.value AS bodytext
									FROM #__users AS u
									LEFT JOIN #__community_users AS cu ON u.id=cu.userid
									LEFT JOIN #__community_fields_values AS cfv ON cu.userid=cfv.user_id
									LEFT JOIN #__community_fields AS cf ON cfv.field_id=cf.id
									WHERE cu.userid =".$id;
				if($fieldid)
					$query .= " AND cfv.field_id=".$fieldid;

				$db->setQuery($query);
				$previewdata = $db->loadObjectlist();
				if(!$fieldid)
					$previewdata[0]->bodytext = '';
				// Include Jomsocial core
				$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
				include_once($jspath.DS.'libraries'.DS.'core.php');
				$previewdata[0]->url = JUri::root().substr(CRoute::_('index.php?option=com_community&view=profile&userid='.$id),strlen(JUri::base(true))+1);
				if($previewdata[0]->image == '')
					$previewdata[0]->image = 'components/com_community/assets/user-Male.png';

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
		$extpath = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_community';
		if(JFolder::exists($extpath) )
			return 1;
		else
			return 0;
	}

}//class ends
