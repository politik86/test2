<?php
/**
 * @package Social Ads
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );
$lang = JFactory::getLanguage();
$lang->load('plg_socialadspromote_promote_sobi', JPATH_ADMINISTRATOR);
///home/deepak/html/socialadscb/administrator/language/en-GB/en-GB.en-GB.plg_socialadspromote_promote_sobi.ini
//print_r($lang);
class plgsocialadspromoteplug_promote_sobi extends JPlugin
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
		$db = JFactory::getDBO();
		$name = JFile::getName(__FILE__);
		$name = JFile::stripExt($name);
		//$name = $this->params->get('plugin_name');


		$sobichk = $this->_sobichk();
		if(!empty($sobichk)){
				$query ="SELECT CONCAT_WS('|', '".$name."', s.itemid) as value, s.title as text FROM #__sobi2_item AS s
								 LEFT JOIN #__users AS u ON  s.updating_user = u.id
								 WHERE u.id=".$user->id."
								 ORDER BY itemid";
				$db->setQuery($query);
				$itemlist = $db->loadObjectlist();
				if(empty($itemlist))
				{
				  	$list[0]->value=$name.'|'.'0';
				  	$list[0]->text=JText::_("NO_SOBILIST");

				  	return $list;
				}
				else{
				return $itemlist;
				}
		}

	}//function promotlist ends

		//function promot data starts
		function onPromoteData($id)		//this function returns the data in array format
		{
			$db	= JFactory::getDBO();

			$Itemid = JRequest::getInt('Itemid');

			$sobichk = $this->_sobichk();
			if(!empty($sobichk)){
					$query= "SELECT s.title AS title, CONCAT_WS('/', 'images/com_sobi2/clients', s.image) AS image, s.metadesc AS bodytext
										FROM  #__sobi2_item AS s
										LEFT JOIN #__users AS u ON s.updating_user = u.id
									 	WHERE itemid =".$id."
									 	AND approved=1
									 	AND published=1";
					$db->setQuery($query);
					$previewdata = $db->loadObjectlist();
					$previewdata[0]->url = JUri::root().substr(JRoute::_('index.php?option=com_sobi2&sobi2Task=sobi2Details&sobi2Id='.$id.'&Itemid='.$Itemid),strlen(JUri::base(true))+1);
					$previewdata[0]->url = JUri::base().'index.php?option=com_sobi2&sobi2Task=sobi2Details&sobi2Id='.$id.'&Itemid='.$Itemid;
					/*$url = explode('://', JUri::base()); //.'index.php?option=com_sobi2&sobi2Task=sobi2Details&sobi2Id='.$id.'&Itemid='.$Itemid;
					$previewdata->url1 = $url[0];
					$previewdata->url2 = $url[1].'index.php?option=com_sobi2&sobi2Task=sobi2Details&sobi2Id='.$id.'&Itemid='.$Itemid;*/

					return $previewdata;
			}
			else
			{
					return '';
			}
		}//function ends


		function _sobichk()
		{
			$sobipath = JPATH_ROOT.DS.'components'.DS.'com_sobi2';
			if( JFolder::exists($sobipath) )
				return 1;
			else
				return '';
		}

}//class ends
