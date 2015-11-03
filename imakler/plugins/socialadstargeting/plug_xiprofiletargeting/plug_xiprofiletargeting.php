<?php
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

$lang =  JFactory::getLanguage();
$lang->load('plg_socialadstargeting_xiprofiletargeting', JPATH_ADMINISTRATOR);

class plgsocialadstargetingplug_xiprofiletargeting extends JPlugin
{
	function plgsocialadstargetingplug_xiprofiletargeting( $subject, $config)
	{
		parent::__construct( $subject, $config);
		if($this->params===false)
		{
				$this->_plugin = JPluginHelper::getPlugin( 'socialadstargeting', 'plug_xiprofiletargeting' );
        $this->params = json_decode( $jPlugin->params);
		}

	}

	function onFrontendTargetingDisplay($plgfields, $tableColumns)
	{
		//Check required column exist in table
		if (in_array('xiprofiletargeting_xipt', $tableColumns))
		{
			$list=array();
			$pluginlist= '';
			$list[0]=$this->_getList(); // all options
			$list[1]= $pluginlist;//preselected values
			$list[1]= explode(',',$plgfields->xiprofiletargeting_xipt);//preselected values
			$ht=array();
			if($list[0])
			{
				$ht[] = $this->_getLayout($this->_name, $list);//print_r($ht);
			}
			else
			{
				$ht[] = NULL;
			}
			return $ht;
		}
	}

	function onFrontendTargetingSave($pluginlistfield, $tableColumns)
	{
		//Check required column exist in table
		if (in_array('xiprofiletargeting_xipt', $tableColumns))
		{
			$param="";
			$prvparam="";

			if(isset($pluginlistfield))
			{
				foreach($pluginlistfield as $fields)
				{
					if(isset($fields['xiprofile,select']))
					 $param.=$fields['xiprofile,select'].",";
				}
			}

			$param=substr_replace($param ,"",-1);

			if($param != "")
			{
				$paramsvalue = $param;
			}
			else
			{
				$paramsvalue ="";
			}
			$row->xiprofiletargeting_xipt=$paramsvalue;
			return $row;
		}
	}

//OnAfterGetAds function of append one more AND in the SocialADs main query
	function OnAfterGetAds($paramlist)
	{
		//onlyif the entry for particular targeting plugin if present in #__ad_fields
		if (array_key_exists('xiprofiletargeting_xipt', $paramlist))
		{
			$sub_query=array();
			$check = $this->_chkextension();
			if(!($check)){
				return;
			}
			$user = JFactory::getUser();
			$userid=$user->id;
			$query="SELECT profiletype from #__xipt_users WHERE userid=$userid";
			$db= Jfactory::getDBO();
			$db->setQuery($query);
			$userlist= $db->loadObjectList();
			$query_str = array();
			if($userlist){
				foreach ($userlist as $userval)
				{
					$query_str[] = "b.xiprofiletargeting_xipt Like '%".$userval->profiletype."%'";
				}
			}
			$query_str[] = "b.xiprofiletargeting_xipt =''";
			$query_str = (count($query_str) ? ' '.implode(" OR ", $query_str) : '');

			$sub_query[] = "(". $query_str.")";
			return $sub_query;
		}
	}

	function OnAfterGetEstimate($plg_targetfiels)
	{
		if(!$plg_targetfiels)
		return array();
		$userlist=array();
		foreach($plg_targetfiels as $key=>$value)
		{
			if($key=='xiprofile')
			{
				$query="SELECT userid from #__xipt_users AS xu LEFT JOIN  #__xipt_profiletypes AS xp  ON xu.profiletype=xp.id  WHERE xu.profiletype IN('$value') AND xp.published=1 GROUP BY xu.userid";//die;
				$db= Jfactory::getDBO();
				$db->setQuery($query);
				$userlist= $db->loadColumn();
			}
		}
		//print_r($userlist);die;
		return $userlist;

	}
	function _getselected($pluginlistfield)
	{
		$pluginlist ="";
		if(isset($pluginlistfield))
		{
			foreach($pluginlistfield as $key=>$varfileds)
			{

				if($key == "xiprofiletargeting_xipt")
				{
						$pluginlist = explode(',', $varfileds);

				}
			}
		}
		return $pluginlist;
	}
	//_getList function give profile types
	function _getList()
	{
		$check = $this->_chkextension();
		if(!($check)){
			return;
		}
			$list="";
			$query="SELECT id,name FROM #__xipt_profiletypes  ";
			$db= Jfactory::getDBO();
			$db->setQuery($query);
			$list= $db->loadObjectList();
			return $list;

	}
	//_chkextension function checks if the extension folder is present
	function _chkextension()
	{
		jimport('joomla.filesystem.file');
		$extpath = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_xipt';
		if(JFolder::exists($extpath) )
			return 1;
		else
			return 0;

	}
	function _getLayout($layout, $vars = false, $plugin = '', $group = 'socialadstargeting')
	{
		$plugin =  $this->_name;
		ob_start();
        $layout = $this->_getLayoutPath( $plugin, $group, $layout );
        include($layout);
        $html = ob_get_contents();
        ob_end_clean();
				return $html;
	}

	function _getLayoutPath($plugin, $group, $layout = 'default')
	{
	  	$app = JFactory::getApplication();
		if(JVERSION >= '1.6.0')
		{
			$defaultPath = JPATH_SITE.DS.'plugins'.DS.$group.DS.$plugin.DS.$plugin.DS.$layout.'.php';
			$templatePath = JPATH_SITE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.'plugins'.DS.$group.DS.$plugin.DS.$plugin.DS.$layout.'.php';
		}
		else
		{
		 	$defaultPath = JPATH_SITE.DS.'plugins'.DS.$group.DS.$plugin.DS.$layout.'.php';
			$templatePath = JPATH_SITE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.'plugins'.DS.$group.DS.$plugin.DS.$layout.'.php';
	  	}

	  jimport('joomla.filesystem.file');
	  if (JFile::exists( $templatePath )){
		  return $templatePath;
	  }
	  else{
		  return $defaultPath;
	  }
	}
}
?>
