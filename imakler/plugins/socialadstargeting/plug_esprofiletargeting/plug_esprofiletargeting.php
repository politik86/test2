<?php
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

$lang =  JFactory::getLanguage();
$lang->load('plg_socialadstargeting_esprofiletargeting', JPATH_ADMINISTRATOR);

class plgsocialadstargetingplug_esprofiletargeting extends JPlugin
{
	function plgsocialadstargetingplug_esprofiletargeting( $subject, $config)
	{
		parent::__construct( $subject, $config);
		if($this->params===false)
		{
				$this->_plugin = JPluginHelper::getPlugin( 'socialadstargeting', 'plug_esprofiletargeting' );
				$this->params = json_decode( $jPlugin->params);
		}

	}

	function onFrontendTargetingDisplay($plgfields, $tableColumns)
	{
		//Check required column exist in table
		if (in_array('esprofiletargeting_espt', $tableColumns))
		{
			$list=array();
			$pluginlist= '';
			$list[0]=$this->_getList(); // all options
			$list[1]= explode(',',$plgfields->esprofiletargeting_espt);//preselected values
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
		if (in_array('esprofiletargeting_espt', $tableColumns))
		{
			$param="";
			$prvparam="";

			if(isset($pluginlistfield))
			{
				foreach($pluginlistfield as $fields)
				{
					if(isset($fields['esprofile,select']))
					 $param.=$fields['esprofile,select'].",";
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
			$row->esprofiletargeting_espt=$paramsvalue;
			return $row;
		}
	}

	//OnAfterGetAds function of append one more AND in the SocialADs main query
	function OnAfterGetAds($paramlist)
	{
		//onlyif the entry for particular targeting plugin if present in #__ad_fields
		if (in_array('esprofiletargeting_espt', $paramlist))
		{
			$sub_query=array();
			$check = $this->_chkextension();
			if(!($check)){
				return;
			}
			$user = JFactory::getUser();
			$userid=$user->id;
			$query="SELECT profile_id FROM #__social_profiles_maps WHERE user_id =$userid";
			$db= Jfactory::getDBO();
			$db->setQuery($query);
			$userlist= $db->loadObjectList();
			$query_str = array();
			if($userlist){
				foreach ($userlist as $userval)
				{
					$query_str[] = "b.esprofiletargeting_espt Like '%".$userval->profile_id."%'";
				}
			}
			$query_str[] = "b.esprofiletargeting_espt =''";
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
			if($key=='esprofile')
			{
				$query="SELECT user_id from #__social_profiles_maps AS spm LEFT JOIN #__social_profiles AS sp ON sp.id=spm.profile_id WHERE spm.profile_id IN('$value') AND sp.state=1  GROUP BY spm.user_id";//die;
				$db= Jfactory::getDBO();
				$db->setQuery($query);
				$userlist= $db->loadColumn();
			}
		}
		return $userlist;

	}


	function _getselected($pluginlistfield)
	{
		$pluginlist ="";
		if(isset($pluginlistfield))
		{

			foreach($pluginlistfield as $key=>$varfileds)
			{
				if($key == "esprofiletargeting_espt")
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
		$query="SELECT id,title FROM #__social_profiles  ORDER BY title";
		$db= Jfactory::getDBO();
		$db->setQuery($query);
		$list= $db->loadObjectList();
		return $list;

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
