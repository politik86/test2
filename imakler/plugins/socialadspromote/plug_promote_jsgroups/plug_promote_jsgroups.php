<?php
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );
$lang = JFactory::getLanguage();
$lang->load('plg_socialadspromote_promote_jsgroups', JPATH_ADMINISTRATOR);

class plgsocialadspromoteplug_promote_jsgroups extends JPlugin
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
					$query = "SELECT CONCAT_WS('|', '".$name."', g.id) as value, g.name AS text FROM #__community_groups AS g
										LEFT JOIN #__users AS u ON g.ownerid = u.id
										WHERE u.id = $user->id ";

					$db->setQuery($query);
					$itemlist = $db->loadObjectlist();

					if(empty($itemlist))
				  {
				  	$list = array();
				  	//$list[0]->value=$name.'|'.'0';
				  	//$list[0]->text=JText::_("NO_GROUPS");

				  	return $list;
				  }
				  else
				  {
						return $itemlist;
					}
			}

	}//function promotlist ends

	//function promot data starts
	function onPromoteData($id)		//this function returns the data in array format
	{
			$db = JFactory::getDBO();

			$groupid = $id;
			$Itemid = JRequest::getInt('Itemid');

			$jschk = $this->_chkextension();
			if(!empty($jschk)){
					$query = "SELECT ownerid as id, name AS title, avatar AS image, description AS bodytext
										FROM #__community_groups
										WHERE id=".$groupid;

					$db->setQuery($query);
					$previewdata = $db->loadObjectlist();
					// Include Jomsocial core
					$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
					include_once($jspath.DS.'libraries'.DS.'core.php');
					$previewdata[0]->url = JUri::root().substr(CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid='.$groupid),strlen(JUri::base(true))+1);
					if($previewdata[0]->image == '')
					$previewdata[0]->image = 'components/com_community/assets/group.png';

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
