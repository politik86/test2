<?php
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );
$lang =  JFactory::getLanguage();
$lang->load('plg_socialadspromote_promote_cb', JPATH_ADMINISTRATOR);
class plgsocialadspromoteplug_promote_cb extends JPlugin
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

		$cbchk = $this->_chkextension();
		if(!empty($cbchk)){
				$query = "SELECT CONCAT_WS('|', '".$name."', u.id) as value, u.name AS text FROM #__users AS u
									LEFT JOIN #__comprofiler AS c ON u.id = c.user_id
									WHERE u.id = $user->id ";

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
			$desc = $this->params->get('cb_field');

			$cbchk = $this->_chkextension();
			if(!empty($cbchk)){
					$query = "SELECT u.name AS title, CONCAT_WS('/' ,'images/comprofiler',c.avatar) AS image, c.$desc AS bodytext
										FROM 	#__comprofiler AS c
										LEFT JOIN #__users AS u ON u.id=c.user_id
										WHERE c.user_id = $id";

					$db->setQuery($query);
					$previewdata = $db->loadObjectlist();
					$previewdata[0]->url = JUri::root().substr(JRoute::_('index.php?option=com_comprofiler&task=userprofile&user='.$id.'&Itemid='.$Itemid),strlen(JUri::base(true))+1);
					/*if($previewdata[0]->bodytext == '')
					{
						$previewdata[0]->bodytext = '';
					}*/
					if($previewdata[0]->image == 'images/comprofiler/'){
					$previewdata[0]->image = 'components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png';
					}


					return $previewdata;
		}
		else
				{
						return '';
				}
	}//function ends

			//_chkextension function checks if the extension folder is present
	function _chkextension()
	{
		jimport('joomla.filesystem.file');
		$extpath = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_comprofiler';
		if(JFolder::exists($extpath) )
			return 1;
		else
			return 0;
	}

}//class ends
