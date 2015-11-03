<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport( 'joomla.application.component.model' );
jimport( 'joomla.application.application' );
require_once(JPATH_SITE. DS."components".DS."com_socialads". DS . 'helper.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'helpers'.DS.'media.php');//2.7.5b1 manoj
/*
 * buildad Model
 * @package    socialads
 * @subpackage Models
 */

class socialadsModelBuildad extends JModelLegacy
{

	/* Store the Ad Details */
	function store()
	{
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		$user = JFactory::getUser();

		if($user->id)
		{
				$data = JRequest::get( 'post' );

				$data = $this->dataSanitize($data);

				$buildadsession = JFactory::getSession();
				$ad_image = $buildadsession->get('ad_image');
				$img =$data['upimg'] ;

				// for storing ad details in session variables for ad_data
				$buildadsession = JFactory::getSession();
				$buildadsession->set('user_id', $user->id);
				$buildadsession->set('ad_image', $img);

				$buildadsession->set('upimgcopy', $data['upimgcopy']);
				$buildadsession->set('upimg', $data['upimg']);
				$buildadsession->set('ad_data',$data['addata']);
				$buildadsession->set('guestbutton',$data['guestbutton']);
				$buildadsession->set('addatapluginlist', $data['addatapluginlist']);

				// for storing ad details in session variables for ad_fields
				$buildadsession->set('user_id', $user->id);
				//added for geo targeting

				$buildadsession->set('geo_type',$data['geo_type']);
				$buildadsession->set('geo_fields',$data['geo']);
				$buildadsession->set('geo_target',$data['geo_target']);
				$buildadsession->set('social_target',$data['social_target']);

				//added for context targeting
				$buildadsession->set('context_target',$data['context_target']);
				if($data['context_target']=="on")
				{

				$buildadsession->set('context_target','on');
				$buildadsession->set('context_target_data_keywordtargeting', $data['context_target_data_keywordtargeting']);
				}
				else
				{
					$buildadsession->clear('context_target');
					$buildadsession->clear('context_target_data_keywordtargeting');
				}
				//End added for context targeting


				$buildadsession->set('ad_fields',$data['mapdata']);
			if(isset($data['plgdata']))
			$buildadsession->set('plg_fields',$data['plgdata']);
				//session for Pricing
				$buildadsession->set('ad_chargeoption',$data['chargeoption']);
				$buildadsession->set('datefrom', $data['datefrom']);

				$buildadsession->set('ad_totaldays', $data['ad_totaldays']);
				$buildadsession->set('ad_totaldisplay',$data['totaldisplay']);
				$buildadsession->set('sa_recuring',$data['sa_recuring']);

				$buildadsession->set('totalamount',$data['totalamount']);
				$buildadsession->set('ad_gateway',$data['gateway']);
				$buildadsession->set('ad_currency',$data['h_currency']);
				$buildadsession->set('user_points',$data['jpoints']);
				$buildadsession->set('ad_rate',$data['h_rate']);
				////////Extra code for zone type
				$buildadsession->set('adtype',$data['adtype']);
				$adzone = '';
				if($data['ad_zone_id']!= '')
					$adzone = $data['ad_zone_id'];
				else
					$adzone = $data['adzone'];
				$buildadsession->set('adzone',$adzone);
				$buildadsession->set('layout',$data['layout']);



				//ANIKET
		if(empty($data['camp_name']))
		{
			$buildadsession->set('camp',$data['camp']);

		}
		else
		{
			$buildadsession->set('camp',$data['camp_name']);
			$buildadsession->set('value',$data['camp_amount']);

		}
		$buildadsession->set('pricing_opt',$data['pricing_opt']);
		$buildadsession->set('bid_value',$data['bid_value']);
		//echo "<pre>";
		///////////
		return true;
		}
		else
		{
			return false;
		}
	} // end function

	// Save alternaive ads
	function storeAltAd()
	{
	  $db   = JFactory::getDBO();
		$data 	= JRequest::get( 'post' );
		$data = $this->dataSanitize($data);
$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$user = JFactory::getUser();

		/*if($data['addatapluginlist'] != '')
		{
	 		  $img = str_replace(JUri::base(),'',$data['upimgcopy']);
		}
		else
		{
			$img = str_replace(JUri::base(),'',$data['upimg']);
		}*/

		$img = str_replace(JUri::base(),'',$data['ad_image']);
		if($img == ''){
		$img = str_replace(JUri::base(),'',$data['upimg']);
		}

		$build = new stdClass;
		$build->ad_id = '';
		$build->ad_creator = $user->id;
//code for affiliate ads
if($data['adtype']== 'affiliate'){
	$build->ad_title = $data['addata']['2']['ad_title'];
	$rawhtml 	= $input->get( 'addata', '', 'post', 'ARRAY',JREQUEST_ALLOWRAW);
	$build->ad_body = stripslashes($rawhtml['3']['ad_body']);
}
else{
		foreach($data['addata'] as $addata)
		{
			foreach($addata as $k=>$ad)
			{
		     	$build->$k = $ad;
			}
		}
}
		$build->ad_image = $img;
		$build->ad_startdate = '';
		$build->ad_enddate = '';
		$build->ad_noexpiry = '';

		$build->ad_created_date = date('Y-m-d H:i:s');
		$build->ad_published = 1;
		$build->ad_approved = 1;

		if(isset($data['altadbutton']))
		{
			$build->ad_alternative = 1;
		}

		//Extra Code For Zone
		$adzone = '';
		if($data['ad_zone_id']!= '')
			$adzone = $data['ad_zone_id'];
		else
			$adzone = $data['adzone'];
		$build->ad_zone 	 =	$adzone;
		$build->layout 		 =	$data['layout'];
		//Extra Code For Zone

//code for affiliate ads
if($data['adtype']== 'affiliate'){
	$build->ad_affiliate = 1;
	$build->layout = "layout6";
}
//
		//insert fields
	  if (!$this->_db->insertObject( '#__ad_data', $build, 'ad_id' ))
		{
	   		echo $this->_db->stderr();
	   		return false;
		}

		$ad_id = $db->insertid();

		return $ad_id;

	} // end function


	//get all the fields of JS/CB
	function getFields()
	{
		include(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$query = "SELECT * FROM #__ad_fields_mapping";
		$db->setQuery($query);
		$fields= $db->loadObjectList();


		$type = $socialads_config['integration'];

		if ($type == 0)
		{
			 $options = $this->_getCBOptions($fields);//calling CB functions for options
		}
		elseif($type == 1)
		{
			 $options = $this->_getJSOptions($fields);//calling JS functions for options
		}
		elseif($type == 3)
		{
			$options = $this->_getESOptions($fields);//calling ES functions for options
		}

	  //dont go inside if options are empty
		if(!empty($options)){
			foreach ($options as $optn)
			{
				foreach ($optn as $k=>$v){
						$i=0;
						$id1 = $optn[$k]->id;
						$id2 = $optn[$k++]->id;
					if($id1 == $id2)
					{
						foreach($optn as $o)
						{
								$arr[] = $o->options;
						}

						$finalopt = implode("\n", $arr);
						$arr = array();
						$opt = new stdClass;
						$opt->mapping_fieldid = $optn[0]->id;
						$opt->mapping_options = $finalopt;
						$db->updateObject('#__ad_fields_mapping', $opt, 'mapping_fieldid');
					}
					else
					{
						$opt->mapping_fieldid = $optn[0]->id;
						$opt->mapping_options = $optn[0]->options;
						$db->updateObject('#__ad_fields_mapping', $opt, 'mapping_fieldid');
					}
				}//2nd foreach
			}//1st foreach

	 }	// end of options are empty condition
	 	$query = "SELECT * FROM #__ad_fields_mapping";
		$db->setQuery($query);
		$allfields= $db->loadObjectList();

		return $allfields;
	}// function getFields end

	function _getJSOptions($fields)
	{
		$db   = JFactory::getDBO();

		$socialadshelper = new socialadshelper();
		$jschk = $socialadshelper->jschk();



	  if(!empty($jschk)){
		for($i=0; $i<count($fields); $i++)
		{
			$query = "SELECT id as id, options as options FROM #__community_fields WHERE id=".$fields[$i]->mapping_fieldid;
			$db->setQuery($query);
			$mapping_options[] = $db->loadobjectlist();
		}
		}//if
	  if(!empty($mapping_options))
		return $mapping_options;

	}


	function _getESOptions($fields)
	{
		$db   = JFactory::getDBO();

		$socialadshelper = new socialadshelper();
		$eschk = $socialadshelper->eschk();

		if(!empty($eschk)){
			for($i=0; $i<count($fields); $i++)
			{
				if($fields[$i]->mapping_fieldtype !='textbox')
				{
					$field_option=array();

					require_once( JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php' );

					$field = Foundry::table( 'Field' );
					$field->load( $fields[$i]->mapping_fieldid );

					$filed_array=new stdclass();

					$filed_array->id=$fields[$i]->mapping_fieldid;
					$options_value=Foundry::fields()->getOptions($field);

					/*
					if(empty($options_value))
					{
						$model     = Foundry::model( 'Fields' );
						$options_value = $model->getOptions($fields[$i]->mapping_fieldid);
						$options_value=$options_value['items'];
					}
					*/
					$options=implode("\n",$options_value);

					$filed_array->options=$options;
					$field_option[] = $filed_array;

					$mapping_options[] = $field_option;
				}

			}

		}
		if(!empty($mapping_options))
		return $mapping_options;

	}


	//Extra code for zone to Check if only one entry of zones while instlalling components
	function getdefaultzone()
	{
		$db   = JFactory::getDBO();
		$query = "SELECT id,ad_type FROM #__ad_zone WHERE published=1";
		$db->setQuery($query);
		$count = $db->loadobjectlist();
		if($count)
		{
		$publish_mod=$this->getZoneamodule();
		$results = array_unique($publish_mod);
		$text_img_flag=$img_flag=$text_flag=$affiliate_flag=0;





		foreach($results as $publish_asign_zones)
		{
		if($text_img_flag==1 and $img_flag==1 and $text_flag==1 and $affiliate_flag=1)
		break;
		foreach($count as $zoneids)
		{

				if($publish_asign_zones==$zoneids->id)
				{
					$query1 = "SELECT ad_type FROM #__ad_zone WHERE id=".$publish_asign_zones." AND published=1 group by ad_type";
					$db->setQuery($query1);
//					$ad_type = $db->loadResult();
/*jugad code*/
$rawresult = str_replace('||',',',$db->loadResult());
$rawresult = str_replace('|','',$rawresult);
					$ad_type1 = explode(",",$rawresult);
/*jugad code*/
					$ad_type = $ad_type1[0];
					if($ad_type)
					{
						if($ad_type=='text_img')
						{
							if($text_img_flag==0)
							$text_img_flag=1;

						}
						if($ad_type=='img')
						{
							if($img_flag==0)
							$img_flag=1;

						}
						if($ad_type=='text')
						{
							if($text_flag==0)
							$text_flag=1;

						}
					}
				/* ADDED for affiliate ads to show only when zone is present for it   */
					$ad_type_affiliate = $ad_type1[1];
					{
						if($ad_type_affiliate=='affiliate')
						{
							if($affiliate_flag==0)
								$affiliate_flag=1;
							//$published_zone_type[]='affiliate';
						}
					}

				}

			}
		}


	}

	if($text_img_flag)
	$published_zone_type[]='text_img';
	if($img_flag)
	$published_zone_type[]='img';
	if($text_flag)
	$published_zone_type[]='text';
	if($affiliate_flag)
	$published_zone_type[]='affiliate';
	return $published_zone_type;

}

	function getZoneamodule(){
		$db=JFactory::getDBO();
		$query="SELECT params FROM #__modules WHERE published = 1 AND module LIKE '%mod_socialads%'";
		$db->setQuery($query);
		$params=$db->loadObjectList();
		$module = array();

		foreach($params as $params)
		{
			$params1 = str_replace('"','',$params->params);
			if(JVERSION >= '1.6.0')
				$single= explode(",", $params1);
			else
				$single= explode("\n", $params1);
			foreach ($single as $single)
			{
				if(JVERSION >= '1.6.0')
					$name= explode(":", $single);
				else
					$name = explode("=", $single);

				if($name[0] == 'zone')
					$module[] = $name[1];
			}
		}
		return $module;
	}
	//Extra code for zone to Check  End Check if only one entry of zones while instlalling components

	function _getCBOptions($fields)
	{
		$db   = JFactory::getDBO();
		$cbchk = socialadshelper::cbchk();
		if(!empty($cbchk)){
		for($i=0; $i<count($fields); $i++)
		{
			$query = "SELECT fieldid as id, fieldtitle as options FROM #__comprofiler_field_values WHERE fieldid=".$fields[$i]->mapping_fieldid;
			$db->setQuery($query);
			$mapping_options[] = $db->loadobjectlist();
		}
		}//if
		if(!empty($mapping_options))
		return $mapping_options;
	}


	function dataSanitize($data)
	{
		if(isset($data['addata']))
		{
			for($i=0; $i < sizeOf($data['addata']); $i++)
			{
				foreach($data['addata'][$i] as $k=>$ad)
				{
					$data['addata'][$i][$k] = strip_tags($ad);
				}
			}
		}
		if(isset($data['mapdata']))
		{
			for($i = 0 ; $i<sizeOf($data['mapdata']) ; $i++)
			{
				foreach($data['mapdata'][$i] as $k=>$ad)
				{
					$data['mapdata'][$i][$k] = strip_tags($ad);
				}
			}
		}
if(isset($data['plgdata']))
		{
			for($i = 0 ; $i<sizeOf($data['plgdata']) ; $i++)
			{
				foreach($data['plgdata'][$i] as $k=>$ad)
				{
					$data['plgdata'][$i][$k] = strip_tags($ad);
				}
			}
		}


		if(isset($data['context_target_data']))
		{
			$data['context_target_data_keywordtargeting']=$data['context_target_data']['keywordtargeting'];
		}

				return $data;
	}

	function storeUnlimiteAd()
	{

		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$data 	= JRequest::get( 'post' );
		$data = $this->dataSanitize($data);
		$user = JFactory::getUser();

		$img = str_replace(JUri::base(),'',$data['upimgcopy']);
		if($img == ''){
		$img = str_replace(JUri::base(),'',$data['upimg']);
		}

		$build = new stdClass;
		$build->ad_id = '';
		$build->ad_creator = $user->id;

		foreach($data['addata'] as $addata)
		{
			foreach($addata as $k=>$ad)
			{
		     	$build->$k = $ad;
			}
		}

		//ANIKET
								$buildadsession = JFactory::getSession();

								if(empty($data['camp_name']))
								{
									$buildadsession->set('camp',$data['camp']);

								}
								else
								{
									$buildadsession->set('camp',$data['camp_name']);
									$buildadsession->set('value',$data['camp_amount']);

								}
								$buildadsession->set('pricing_opt',$data['pricing_opt']);

								if( $socialads_config['select_campaign']==1)
								{
									$db= JFactory::getDBO();

									$query = "SELECT campaign FROM #__ad_campaign WHERE campaign ='".$buildadsession->get('camp')."'";
									$db->setQuery($query);
									$campaign = $db->loadResult();

									if(empty($campaign))
									{
										$query = "INSERT INTO #__ad_campaign (campaign,user_id,daily_budget,camp_published) VALUES ('".$buildadsession->get('camp')."',".$user->id.",".$buildadsession->get('value').",1)";
										$db->setQuery($query);
										$db->execute();
									}

									$query = "SELECT camp_id FROM #__ad_campaign WHERE campaign ='".$buildadsession->get('camp')."'";
									$db->setQuery($query);
									$camp_id = $db->loadResult();
									$build->camp_id	 =	$camp_id;
									//$build->ad_payment_type	 =	$buildadsession->get('pricing_opt');
								}


		$build->ad_image = $img;
		$build->ad_created_date = date('Y-m-d H:i:s');
		$build->ad_published = 1;
		$build->ad_approved = 1;
		$build->ad_noexpiry = 1;



		if(isset($data['guestbutton'])){
		$buildadsession->set('guestbutton',$data['guestbutton']);
		$guest = $buildadsession->get('guestbutton');
		}

		$geoflag = 0;
		if(isset($data['geo']) || !empty($data['geo']) ){
			foreach( $data['geo'] as $geo){
				if(!empty($geo)){
					$geoflag=1;
					break;
				}
			}
		}

		if(isset($data['geo_target']) && !$geoflag && !(isset($data['social_target']) )
		|| !(isset($data['geo_target'])) && !(isset($data['social_target']) )
		  )
		{
			if(($data['context_target']!="on") || ($data['context_target']=="on" &&!($data['context_target_data_keywordtargeting'])))
			$build->ad_guest = 1;
		}
		//Extra Code For Zone
		$adzone = '';
		if($data['ad_zone_id']!= '')
			$adzone = $data['ad_zone_id'];
		else
			$adzone = $data['adzone'];
		$build->ad_zone 	 =	$adzone;
		$build->layout 		 =	$data['layout'];
		//Extra Code For Zone

		//insert fields
	  if (!$this->_db->insertObject( '#__ad_data', $build, 'ad_id' ))
		{
	   		echo $this->_db->stderr();
	   		return false;
		}
/*start of geo*/
if( $socialads_config['geo_target'] && isset($data['geo_target']) ){
		if($geoflag)
		{
			$first_key = array_keys($data['geo']);
			$type = str_replace("by","",$data['geo_type']);
			$fielddata = new stdClass;
			$fielddata->ad_id = $build->ad_id;
			foreach($data['geo'] as $key => $value)
			{
					if($first_key[0] == $key){
						$fielddata->$key = $value;
					}
					else if($type == $key)
						$fielddata->$key = $value;
					else if($data['geo_type'] == "everywhere")
						break;
			}
			if (!$this->_db->insertObject( '#__ad_geo_target', $fielddata, 'id' ))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
}
/*end of geo*/


		$var = new socialadshelper();
	  $adfields = $var->chkadfields();

	  if(!empty($adfields) && isset($data['social_target'])){
		//For saving demographic details
		$fielddata = new stdClass;
		$fielddata->adfield_id = '';
		$fielddata->adfield_ad_id = $this->_db->insertid();

		$date_low = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,1910));
		$date_high = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,2030));
		$grad_low=0;
    $grad_high=2030;
		foreach($data['mapdata'] as $mapdata)
		{
			foreach($mapdata as $m=>$map)
			{
				if($m)
				{	if(strstr($m,','))
					{
						$selcheck = explode(',',$m);
						$var = isset($fielddata->$selcheck[0]);
					}
					else{
						$var = isset($fielddata->$m);
					}
					if(!$var)
					{
						if(strstr($m,'|'))
						{
							$rangecheck = explode('|',$m);
							if($rangecheck[2]==0)
							{
								if($map)
								{
									if($rangecheck[1]=='daterange')
										$date_low = $map;
									elseif($rangecheck[1]=='numericrange')
										$grad_low = $map;
								}
								if($rangecheck[1]=='daterange')
									$fielddata->$rangecheck[0] = $date_low;  //1900
								elseif($rangecheck[1]=='numericrange')
									$fielddata->$rangecheck[0] = $grad_low;  //0

							}
							elseif($rangecheck[2]==1)
							{
								if($map)
								{
									if($rangecheck[1]=='daterange')
										$date_high = $map;
									elseif($rangecheck[1]=='numericrange')
										$grad_high = $map;
								}
								if($rangecheck[1]=='daterange')
									$fielddata->$rangecheck[0] = $date_high;  //2030
								elseif($rangecheck[1]=='numericrange')
									$fielddata->$rangecheck[0] = $grad_high;  //2030
							}
						}
						elseif(strstr($m,','))
						{
							$selcheck = explode(',',$m);
							if($selcheck[1]=='select')
							{
								if($map)
								{	$fielddata->$selcheck[0] = '|'.$map.'|';}
								else
								{	$fielddata->$selcheck[0] = $map;}
							}
						}
						else
						{
							$fielddata->$m = $map;
						}
					}
					else
					{
						if(strstr($m,','))
						{
							$selcheck = explode(',',$m);
							if($selcheck[1]=='select')
							{
								$fielddata->$selcheck[0] .= '|'.$map.'|';
							}
						}else{
							$fielddata->$m .= '|'.$map.'|';
						}
						//$fielddata->$m .= ','.$map;
					}
				}
			}
		}

		JPluginHelper::importPlugin('socialadstargeting');
		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger('onFrontendTargetingSave',array($profile));
		for($i=0; $i<count($results); $i++)
		{
			if($results[$i] !="")
			{
				foreach($results[$i] as $key => $value)
				{
					$fielddata->$key = $value;
				}
			}
		}

		//insert fields
		if (!$this->_db->insertObject( '#__ad_fields', $fielddata, 'adfield_id' ))
		{
	   		 echo $this->_db->stderr();
	   		 return false;
		}
	}//chk empty ad_fields
		$ad_id = $build->ad_id;


		 /*start of context*/
if($socialads_config['context_target']  && $data['context_target']=="on"){
	  $context_adfields = $data['context_target_data_keywordtargeting'];
		if($context_adfields)
		{
			$fielddata = new stdClass;
			$fielddata->id='';
			$fielddata->ad_id = $build->ad_id;
			$fielddata->keywords=trim(strtolower($context_adfields));
			/*$keyarr=explode(',',$fielddata->keywords);
			foreach($keyarr as $keyword)
			{
						if(strlen($keyword)<=3)
						{
							$no_of_chars=3-strlen($keyword);
							for($i=0;$i<$no_of_chars;$i++)
							{
							$keyword=$keyword.'8';
							}

						}
						$newkeyarr[]=$keyword;

			}

			$keystr=implode(',',$newkeyarr);

			$fielddata->keywords=$keystr;*/

			if (!$this->_db->insertObject( '#__ad_contextual_target',$fielddata,'id' ))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
}

/*end of context*/

		return $build->ad_id;

	}

	//changed image upload code in 2.7.5b1 manoj
	//image upload
	function imageupload()
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$data = JRequest::get( 'post' );

		//create object of media helper class
		$media=new sa_mediaHelper();

		//get uploaded media details
		$file_field = strip_tags($_REQUEST['filename']);
		$file_name  = $_FILES[$file_field]['name'];//orginal file name
		$file_name  = strtolower($_FILES[$file_field]['name']);//convert name to lowercase
		$file_name  = preg_replace('/\s/', '_', $file_name);//replace "spaces" with "_" in filename
		$file_type  = $_FILES[$file_field]['type'];
		$file_tmp_name=$_FILES[$file_field]['tmp_name'];
		$file_size    = $_FILES[$file_field]['size'];
		$file_error   = $_FILES[$file_field]['error'];

		//set error flag, if any error occurs set this to 1
		$error_flag=0;

		//check for max media size allowed for upload
		$max_size_exceed=$media->check_max_size($file_size);
		if($max_size_exceed){
			$errorList[] = JText::_('FILE_BIG')." ".$socialads_config['image_size']."KB<br>";
			$error_flag=1;
		}

		if(!$error_flag)
		{
			//detect file type
			//& detect media group type image/video/flash
			$media_type_group=$media->check_media_type_group($file_type);

			if(!$media_type_group['allowed']){
				$errorList[]= JText::_('FILE_ALLOW');
				$error_flag=1;
			}

			if(!$error_flag)
			{
				$media_extension=$media->get_media_extension($file_name);

				//determine if resizing is needed for images
				//get max height and width for selected zone
				$adzone='';
				if($data['ad_zone_id']!= '')
					$adzone=$data['ad_zone_id'];
				else
					$adzone=$data['adzone'];

				$adzone_media_dimnesions=$media->get_adzone_media_dimensions($adzone);

				//@TODO get video frame height n width

				$max_zone_width  = $adzone_media_dimnesions->img_width;
				$max_zone_height = $adzone_media_dimnesions->img_height;

				//if($media_type_group['media_type_group']!="video" )// skip resizing for video
				if($media_type_group['media_type_group']=="image" )
				{
					//get uploaded image dimensions
					$media_size_info=$media->check_media_resizing_needed($adzone_media_dimnesions,$file_tmp_name);
					$resizing=0;
					if($media_size_info['resize']){
						$resizing=1;
					}

					switch ($resizing)
					{
						case 0:
								$new_media_width=$media_size_info['width_img'];
								$new_media_height=$media_size_info['height_img'];
								$top_offset=0;//@TODO not sure abt this
								$blank_height=$new_media_height;//@TODO not sure abt this
							break;
						case 1:
								$new_dimensions=$media->get_new_dimensions($max_zone_width, $max_zone_height,'auto');
								$new_media_width=$new_dimensions['new_calculated_width'];
								$new_media_height=$new_dimensions['new_calculated_height'];
								$top_offset=$new_dimensions['top_offset'];
								$blank_height=$new_dimensions['blank_height'];
							break;
					}
				}
				else //as we skipped resizing for video , we will use zone dimensions
				{
					$new_media_width=$adzone_media_dimnesions->img_width;
					$new_media_height=$adzone_media_dimnesions->img_height;
					$top_offset=0;//@TODO not sure abt this
					$blank_height=$new_media_height;
				}
				$fullPath = JUri::root().'images/socialads/';
				$relPath = 'images/socialads/';
				$colorR = 255;
				$colorG = 255;
				$colorB = 255;

				$file_name_without_extension=$media->get_media_file_name_without_extension($file_name);

				$upload_image = $media->uploadImage($file_field,$max_zone_width, $max_zone_height, $fullPath, $relPath, $colorR, $colorG, $colorB,$new_media_width,$new_media_height,$blank_height,$top_offset,$media_extension,$file_name_without_extension);

			}
		}

		if($error_flag)
		{
			echo '<img src="'.JUri::root().'components/com_socialads/images/error.gif" width="16" height="16px" border="0" style="margin-bottom: -3px;" /> Error(s) Found: ';
			foreach($errorList as $value)
			{
					echo $value.', ';
			}
			jexit();
		}

		if(isset($data['upimgcopy']))
		{
			$img = str_replace(JUri::base(),'',$data['upimgcopy']);
			jimport( 'joomla.filesystem.file' );

			/*if(JFile::exists(JPATH_ROOT.DS.$img))
				JFile::delete(JPATH_ROOT.DS.$img);*/
		}

		if(is_array($upload_image))
		{
			foreach($upload_image as $key => $value)
			{
				if($value == "-ERROR-")
				{
					unset($upload_image[$key]);
				}
			}
			$document = array_values($upload_image);
			for ($x=0; $x<sizeof($document); $x++)
			{
				$errorList[] = $document[$x];
			}
			$imgUploaded = false;
		}
		else
		{
			$imgUploaded = true;
		}

		if($imgUploaded)
		{
			switch($media->media_type_group)
			{
				case "flash":
					//FLOWPLAYER METHOD
					echo '<div><input type="hidden" name="upimg" value="'.$upload_image.'"></div>';
					echo '<script src="'.JUri::root().'components/com_socialads/js/flowplayer-3.2.9.min.js" type="text/javascript"></script>';
					echo '<div class="vid_ad_preview"
					href="'.$upload_image.'"
					style="background:url('.JUri::root().'/components/com_socialads/images/black.png);width:'.$new_media_width.'px;height:'.$new_media_height.'px;
					">
					</div>';
					//configure flowplayer	//disable all controls //hide play button
					echo '
					<script type="text/javascript">
						flowplayer("div.vid_ad_preview",
						{
							src:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.10.swf",
							wmode:"opaque"
						},
						{
							canvas: {
								backgroundColor:"#000000",
								width:'.$new_media_width.',
								height:'.$new_media_height.'
							},

							//default settings for the play button
							play: {
								opacity: 0.0,
							 	label: null,
							 	replayLabel: null,
							 	fadeSpeed: 500,
							 	rotateSpeed: 50
							},

							plugins:{
								controls: null
							}
						});
					</script>';

					jexit();

				break;

				case "video":
					//FLOWPLAYER METHOD
					echo '<div><input type="hidden" name="upimg" value="'.$upload_image.'"></div>';
					echo '<script src="'.JUri::root().'components/com_socialads/js/flowplayer-3.2.9.min.js" type="text/javascript"></script>';
					echo '<div class="vid_ad_preview"
					href="'.$upload_image.'"
					style="background:url('.JUri::root().'/components/com_socialads/images/black.png);width:'.$new_media_width.'px;height:'.$new_media_height.'px;
					">
					</div>';
					//configure flowplayer	//disable all controls //hide play button
					echo '
					<script type="text/javascript">
						flowplayer("div.vid_ad_preview",
						{
							src:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.10.swf",
							wmode:"opaque"
						},
						{
							canvas: {
								backgroundColor:"#000000",
								width:'.$new_media_width.',
								height:'.$new_media_height.'
							},

							//default settings for the play button
							play: {
								opacity: 0.0,
							 	label: null,
							 	replayLabel: null,
							 	fadeSpeed: 500,
							 	rotateSpeed: 50
							},

							plugins:{

								controls: {
									url:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer.controls-3.2.10.swf",
									height:25,
									timeColor: "#980118",
									all: false,
									play: true,
									scrubber: true,
									volume: true,
									time: false,
									mute: true,
									progressColor: "#FF0000",
									bufferColor: "#669900",
									volumeColor: "#FF0000"
								}

							}
						});
					</script>';

					jexit();
				break;
			}
					if($max_zone_width == $media_size_info['width_img'] && $max_zone_height == $media_size_info['height_img']){
									   echo '<img src="'.$upload_image.'" border="0" />';
									   }
							   else if($max_zone_width != $media_size_info['width_img'] || $max_zone_height != $media_size_info['height_img']){
									 //  $msg  = JText::sprintf('UPLOAD_NEWMSG', $media_size_info['width_img'],$media_size_info['height_img'],$max_zone_width,$max_zone_height);
									   $msg = JText::_('IMAGE_RESIZE_1').$max_zone_width." x ".$max_zone_height.JText::_('IMAGE_RESIZE_2').$media_size_info['width_img']." x ".$media_size_info['height_img'].JText::_('IMAGE_RESIZE_3');
									   echo '<img src="'.$upload_image.'" border="0" />';
									   echo '<script>alert("'.$msg.'")</script>';
							   }


			echo '<div><input type="hidden" name="upimg" value="'.$upload_image.'"></div>';
			jexit();
		}
		else
		{
			echo '<img src="'.JUri::root().'components/com_socialads/images/error.gif" width="16" height="16px" border="0" style="margin-bottom: -3px;" /> Error(s) Found: ';
			foreach($errorList as $value)
			{
				echo $value.', ';
			}
			jexit();
		}
	}


	//This is function to hide the ad type and zone div when there is only one adtype and only one zone
	function checkdefaultzone($adtype='')
	{
		$db   = JFactory::getDBO();
		$query = "SELECT id,ad_type FROM #__ad_zone WHERE published=1 "." AND ad_type LIKE '%|".$adtype[0]."|%'";
		$db->setQuery($query);
		$count = $db->loadobjectlist();
		if($count)
		{
		$publish_mod=$this->getZoneamodule();
		$results = array_unique($publish_mod);
		$flag=0;

		foreach($results as $publish_asign_zones)
		{
		//if($text_img_flag==1 and $img_flag==1 and $text_flag==1)
		//break;
		foreach($count as $zoneids)
		{
				if($publish_asign_zones==$zoneids->id)
				{
					$query1 = "SELECT ad_type FROM #__ad_zone WHERE id=".$publish_asign_zones." AND published=1 group by ad_type";
					$db->setQuery($query1);
					$ad_type = $db->loadResult();
					if($ad_type)
					{
						if( strstr($ad_type,'|text_img|') )
						{

							$flag++;

						}
						if( strstr($ad_type,'|img|') )
						{

							$flag++;

						}
						if( strstr($ad_type,'|text|') )
						{

							$flag++;

						}
					}

				}

			}
		}

	}
	if($flag==1)
	return 0;
	else if($flag>1)
	return 1;

}


	function getcampaign(){

			$user=JFactory::getUser();
		$userid=$user->id;
			$query = "SELECT * FROM #__ad_campaign WHERE user_id=$userid";
			$this->_db->setQuery($query);

			return $this->_db->loadObjectList();

		}


		function getcampname($cid){

			$query = "SELECT campaign FROM #__ad_campaign WHERE camp_id=$cid";
			$this->_db->setQuery($query);
			return $this->_db->loadresult();

			}


	// 	This function to save step1 -> design ad data
	function saveDesignAd($post, $adminApproval)
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		// vm : start
		$input=JFactory::getApplication()->input;
		$preSentApproveMailStatus =$input->get('sa_sentApproveMail',0);
		// to avoid repetative mail while editing confirm ads
		$return['sa_sentApproveMail'] = 0;
		// vm : end
		$session=JFactory::getSession();

		$app = JFactory::getApplication();
		//.. do Back End Stuff
		if( $app->isAdmin() )
		{
			$ad_creator_id = $post->get('ad_creator_id');
			$user = JFactory::getUser($ad_creator_id);
			$userid = $user->id;
		}
		else// .. do Front End stuff
		{
			$user = JFactory::getUser();
			$userid = $user->id;
		}

		if(!$userid)
		{
			$userid = 0;
			return false;
		}

		$desingAdd = new stdClass;
		$desingAdd->ad_id = '';

		$ad_id = $session->get('ad_id');

		if($ad_id)
		{
			$desingAdd->ad_id = $ad_id;
		}

		$desingAdd->ad_creator = $userid;
		$desingAdd->ad_image =str_replace(JUri::root(),'',$post->get('upimg','','STRING'));
		$desingAdd->ad_noexpiry = '';

		//$desingAdd->ad_payment_type='';

		$desingAdd->ad_enddate = '';
		$desingAdd->ad_created_date = date('Y-m-d H:i:s');
		$desingAdd->ad_modified_date = date('Y-m-d H:i:s');

		// Get ad data which in form of array in layout

		if ($post->get('ad_zone_id','','INT'))
		{
			$desingAdd->ad_zone = $post->get('ad_zone_id','','INT');
		}
		else
		{
			$desingAdd->adzone = $post->get('adzone','','INT');
		}

		$desingAdd->layout = $post->get('layout','','STRING');

		$addData = $post->get('addata','','Array');

		//code for affiliate ads
		$addType=$post->get('adtype','','STRING');

		if ( $addType == 'affiliate')
		{

			//@params $table_name, $where_field_name= where column name , $where_field_value = column value

			if($ad_id)
			{
				//Delete __ad_contextual_target data
				$this->deleteData('ad_contextual_target','ad_id',$ad_id);

				//Delete __ad_geo_target data
				$this->deleteData('ad_geo_target','ad_id',$ad_id);

				//Delete __ad_fields data
				$this->deleteData('ad_fields','adfield_ad_id',$ad_id);

				//Delete __ad_geo_target data
				$db=JFactory::getDBO();
				$query=" DELETE FROM `#__ad_payment_info`
						 WHERE ad_id = " . $ad_id . " AND  status ='P' ";

				$db->setQuery($query);
				$db->execute();
			}

			$desingAdd->ad_affiliate = 1;
			$desingAdd->layout = "layout6";
			$desingAdd->ad_title = $addData[2]['ad_title'];
			$input=JFactory::getApplication()->input;
			$rawhtml 	= $input->get( 'addata', '', 'post', 'Array',JREQUEST_ALLOWRAW);
			$desingAdd->ad_body = stripslashes($rawhtml['3']['ad_body']);
		}
		else
		{
			$desingAdd->ad_url1 = $addData[0]['ad_url1'];
			$desingAdd->ad_url2 = $addData[1]['ad_url2'];
			$desingAdd->ad_title = $addData[2]['ad_title'];
			$desingAdd->ad_body = $addData[3]['ad_body'];
		}


		$geo_target = $post->get('geo_targett','','INT');
		$social_target = $post->get('social_targett','','INT');
		$context_target = $post->get('context_targett','','INT');

		//IF any one targeting set then ad is not a guest ad
		if($geo_target || $social_target || $context_target)
		{
			$desingAdd->ad_guest = 0;
		}
		else
		{
			$desingAdd->ad_guest = 1;
		}

		$desingAdd->ad_published = 1;

		if(!$ad_id)	// do not update publish state on ad edit
		{
			//Ad not will not be published by default published for campaign
			if ($socialads_config['select_campaign'] == 1)
			{
				$desingAdd->ad_published = 0;
			}
		}

		$desingAdd->ad_noexpiry=$post->get('unlimited_ad','','INT');

		//code for guest
		if(!empty($adminApproval))
		{
			$desingAdd->ad_approved = 1;
		}
		else
		{
			if($socialads_config['approval']==0)
			{
				$desingAdd->ad_approved = 1;
			}
			else
				$desingAdd->ad_approved = 0;
		}

		$altadbutton=$post->get('altadbutton','','STRING');

		if($altadbutton=='on')
		{
			$desingAdd->ad_alternative = 1;
			$desingAdd->ad_approved = 1;
			$desingAdd->ad_published = 1;
			$desingAdd->ad_guest = 0;
			$desingAdd->camp_id = 0;

			//IF aleternate ad then delete other data if exist
			if($ad_id)
			{
				$this->deleteDataAlternateAd($ad_id);
			}
		}
		else
		{
			$desingAdd->ad_alternative = 0;
		}

		if($desingAdd->ad_id )
		{
			//Admin Approval Needed for Ad edits ?  // $adminApproval ==1 means admin creating the ad so dont send approve mail
			if (!$app->isAdmin()  && empty($adminApproval) && $socialads_config['approval'] == 1 && $preSentApproveMailStatus==0 )
			{
				// while updating (confirm ) ad sent ad approval email
				$createAdHelper = new createAdHelper;
				$result = $createAdHelper->sendForApproval($desingAdd);
				$return['sa_sentApproveMail'] = $result['sa_sentApproveMail'];
				if(isset($result['ad_approved']))
					$desingAdd->ad_approved = $result['ad_approved'];
			}
			//insert fields
			if (!$this->_db->updateObject( '#__ad_data', $desingAdd, 'ad_id' ))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
		else
		{
			//insert fields
			if (!$this->_db->insertObject( '#__ad_data', $desingAdd, 'ad_id' ))
			{
				echo $this->_db->stderr();
				return false;
			}
		}

		if(empty($ad_id))
		{
			$ad_id=$this->_db->insertid();
			$session->set('ad_id',$ad_id);
		}
		return $return ;
	}

	//IF admin has selected alternate ad then delete other data
	function deleteDataAlternateAd($ad_id)
	{
		$db = JFactory::getDBO();

		$query = "DELETE FROM #__ad_geo_target WHERE ad_id=".$ad_id;
		$db->setQuery( $query );

		if (!$db->execute())
		{
			echo $db->stderr();
			return false;
		}

		$query = "DELETE FROM #__ad_contextual_target WHERE ad_id=".$ad_id;
		$db->setQuery( $query );

		if (!$db->execute())
		{
			echo $db->stderr();
		}

		//Delete __ad_fields data
		$this->deleteData('ad_fields','adfield_ad_id',$ad_id);

		$query=" DELETE FROM `#__ad_payment_info`
				 WHERE ad_id = " . $ad_id . " AND  status ='P' ";
		$db->setQuery($query);

		if (!$db->execute())
		{
			echo $db->stderr();
		}

	}

	function saveTargetingData($post)
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		$session=JFactory::getSession();

		$app = JFactory::getApplication();
		//.. do Back End Stuff
		if( $app->isAdmin() )
		{
			$ad_creator_id = $post->get('ad_creator_id');
			$user = JFactory::getUser($ad_creator_id);
			$userid = $user->id;
		}
		else// .. do Front End stuff
		{
			$user = JFactory::getUser();
			$userid = $user->id;
		}

		if(!$userid)
		{
			$userid = 0;
			return false;
		}

		$adData = new stdClass;
		$adData->ad_id = '';

		$ad_id = $session->get('ad_id');
		$tagetId = '';

		//Get primary key of table
		if($ad_id)
		{
			$adData->ad_id = $ad_id;

			//Get order id
			// @params $value,$field_name,$tableName
			$targetAdId = $this->getIdFromAnyFieldValue($adData->ad_id, 'ad_id', '#__ad_geo_target');
			if($targetAdId)
			{
				$tagetId = $targetAdId;
			}
		}
		else
		{
			$app=JFactory::getApplication();
			$app->enqueueMessage('Session Expire','error');
			return false;
		}

		//Added for geo targeting
		$geo_type=$post->get('geo_type','','STRING');
		$geo_fields=$post->get('geo','','Array');
		$geo_target=$post->get('geo_targett','','INT');
		$social_target=$post->get('social_targett','','INT');
		$context_target=$post->get('context_targett','','INT');

		//Set  geoflag
		$geoflag = 0;
		if(isset($geo_fields) || !empty($geo_fields) ){
			foreach( $geo_fields as $geo){
				if(!empty($geo)){
					$geoflag=1;
					break;
				}
			}
		}

		$context_target_data=$post->get('context_target_data','','Array');
		$context_target_data_keywordtargeting=$context_target_data['keywordtargeting'];

		//check to decide if ad is non targeted ie guest ad

		/*if( $geo_target=="1" && !$geoflag && !($social_target=="1") || !($geo_target=="1") && !($social_target=="1") )
		{

			if(($context_target!="1")
			|| ($context_target=="1" &&!($context_target_data_keywordtargeting)))
			{
				$adData->ad_guest = 1;
			}
			else
			{
				$adData->ad_guest = 0;
			}
		}*/

		//IF any one targeting set then ad is not a guest ad
		if($geo_target || $social_target || $context_target)
		{
			$adData->ad_guest = 0;
		}
		else
		{
			$adData->ad_guest = 1;
		}


		//Start of geo
		if($socialads_config['geo_target']  && $geo_target=="1" )
		{
			$geo_adfields = $geo_fields; //form field name="geo[country]" name="geo[region]" name="geo[city]"
			if($geoflag)
			{

				$first_key = array_keys($geo_fields);
				$type = str_replace("by","",$geo_type); //name="geo_type"  everywhere || city || region
				$fielddata = new stdClass;
				$fielddata->id='';

				//Get tagert table id
				if($tagetId)
				{
					$fielddata->id = $tagetId;
				}
				$fielddata->ad_id = $ad_id;

				foreach($geo_fields as $key => $value)
				{
					if($first_key[0] == $key){ // for country
						$fielddata->$key = $value;
					}
					else if($type == $key) // for region & city
						$fielddata->$key = $value;
					else if($geo_type == "everywhere")
						break;
				}

				if($fielddata->id)
				{
					if(!$this->_db->updateObject( '#__ad_geo_target', $fielddata, 'id' ))
					{
						echo $this->_db->stderr();
						return false;
					}
				}
				else if(!$this->_db->insertObject( '#__ad_geo_target', $fielddata, 'id' ))
				{
					echo $this->_db->stderr();
					return false;
				}
			}
		}
		else
		{
			$query = "DELETE FROM #__ad_geo_target WHERE ad_id=".$ad_id;
			$this->_db->setQuery( $query );

			if (!$this->_db->execute())
			{
				echo $this->_db->stderr();
				return false;
			}
		}
		/*end of geo*/

		 /*Start of context*/

		//Get primary key of table
		if(!empty($ad_id))
		{
			//Get order id
			// @params $value,$field_name,$tableName
			$id = $this->getIdFromAnyFieldValue($ad_id, 'ad_id', '#__ad_contextual_target');
			if($id)
			{
				$context_targetId = $id;
			}
		}
		else
		{
			$app=JFactory::getApplication();
			$app->enqueueMessage('Session Expire','error');
			return false;
		}


		if($socialads_config['context_target']  && $context_target=="1")
		{
			if($context_target_data_keywordtargeting)
			{
				$context_target = new stdClass;
				$context_target->id='';
				$context_target->ad_id = $ad_id;

				if($context_targetId)
				{
					$context_target->id=$context_targetId;
				}

				$context_target->keywords=trim(strtolower($context_target_data_keywordtargeting));

				if($context_target->id)
				{
					if (!$this->_db->updateObject( '#__ad_contextual_target',$context_target,'id' ))
					{
						echo $this->_db->stderr();
						return false;
					}
				}
				else if (!$this->_db->insertObject( '#__ad_contextual_target',$context_target,'id' ))
				{
					echo $this->_db->stderr();
					return false;
				}

			}
		}
		else if($context_targetId)
		{
			$query = "DELETE FROM #__ad_contextual_target WHERE id=".$context_targetId;
			$this->_db->setQuery( $query );

			if (!$this->_db->execute())
			{
				echo $this->_db->stderr();
			}
		}



		//START Save --Social Targeting data--

		//$buildadsession->set('ad_fields',$data['mapdata']);
		//$session_adfields = $buildadsession->get('ad_fields');
		//$profile=$buildadsession->get('plg_fields');

		$ad_fields=$post->get('mapdata','','Array');
		$profile=$post->get('plgdata','','Array');

		//Get social target ID
		// @params $value,$field_name,$tableName
		if($ad_id)
		{
			$db=JFactory::getDBO();

			if($social_target=="1")
			{

				$db=JFactory::getDBO();

				$app= JFactory::getApplication();
				$dbprefix = $app->getCfg('dbprefix');

				$tbexist_query = "SHOW TABLES LIKE '".$dbprefix."ad_fields'";
				$db->setQuery($tbexist_query);
				$isTableExist = $db->loadResult();

				if($isTableExist)
				{
					$query="SELECT adfield_id FROM  #__ad_fields
					WHERE adfield_ad_id =  ". $ad_id;
					$db->setQuery($query);
					$adfield_id = $db->loadResult();
				}
			}

		}

	 	if((!empty($ad_fields) || !empty($profile) )   && $social_target=="1")
	 	{
			//For saving demographic details
			$fielddata = new stdClass;
			$fielddata->adfield_id = '';
			if($adfield_id)
			{
				$fielddata->adfield_id = $adfield_id;
			}

			$fielddata->adfield_ad_id = $ad_id;

			$date_low = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,1910));
			$date_high = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,2030));
			$grad_low=0;
			$grad_high=2030;
			if(!empty($ad_fields))
			{
				foreach($ad_fields as $mapdata)
				{
					foreach($mapdata as $m=>$map)
					{
						if($m)
						{	if(strstr($m,','))
							{
								$selcheck = explode(',',$m);
								$var = isset($fielddata->$selcheck[0]);
							}
							else{
								$var = isset($fielddata->$m);
							}
							if(!$var)
							{
								if(strstr($m,'|'))
								{
									$rangecheck = explode('|',$m);
									if($rangecheck[2]==0)
									{
										if($map)
										{
											if($rangecheck[1]=='daterange')
												$date_low = $map;
											elseif($rangecheck[1]=='numericrange')
												$grad_low = $map;
										}
										if($rangecheck[1]=='daterange')
											$fielddata->$rangecheck[0] = $date_low;  //1900
										elseif($rangecheck[1]=='numericrange')
											$fielddata->$rangecheck[0] = $grad_low;  //0

									}
									elseif($rangecheck[2]==1)
									{
										if($map)
										{
											if($rangecheck[1]=='daterange')
												$date_high = $map;
											elseif($rangecheck[1]=='numericrange')
												$grad_high = $map;
										}
										if($rangecheck[1]=='daterange')
											$fielddata->$rangecheck[0] = $date_high;  //2030
										elseif($rangecheck[1]=='numericrange')
											$fielddata->$rangecheck[0] = $grad_high;  //2030
									}
								}
								elseif(strstr($m,','))
								{
									$selcheck = explode(',',$m);
									if($selcheck[1]=='select')
									{
										if($map)
										{	$fielddata->$selcheck[0] = '|'.$map.'|';}
										else
										{	$fielddata->$selcheck[0] = $map;}
									}
								}
								else
								{
									$fielddata->$m = $map;
								}
							}
							else
							{
								if(strstr($m,','))
								{
									$selcheck = explode(',',$m);
									if($selcheck[1]=='select')
									{
										$fielddata->$selcheck[0] .= '|'.$map.'|';
									}
								}else{
									$fielddata->$m .= '|'.$map.'|';
								}
								//$fielddata->$m .= ','.$map;
							}
						}
					}
				}
			}

			$socialadshelper = new socialadshelper();
			$tableColumns = $socialadshelper->getTableColumns('ad_fields');

			JPluginHelper::importPlugin('socialadstargeting');
			$dispatcher = JDispatcher::getInstance();
			$results = $dispatcher->trigger('onFrontendTargetingSave',array($profile,$tableColumns));

			for($i=0; $i<count($results); $i++)
			{
				if($results[$i] !="")
				{
					foreach($results[$i] as $key => $value)
					{
						$fielddata->$key = $value;
					}
				}
			}
			//insert fields

			$db=JFactory::getDBO();

			$app= JFactory::getApplication();
			$dbprefix = $app->getCfg('dbprefix');

			$tbexist_query = "SHOW TABLES LIKE '".$dbprefix."ad_fields'";
			$db->setQuery($tbexist_query);
			$isTableExist = $db->loadResult();

			if($isTableExist)
			{
				if($fielddata->adfield_id)
				{
					if (!$this->_db->updateObject( '#__ad_fields', $fielddata, 'adfield_id' ))
					{
						 echo $this->_db->stderr();
						 return false;
					}
				}
				else
				{
					if (!$this->_db->insertObject( '#__ad_fields', $fielddata, 'adfield_id' ))
					{
						 echo $this->_db->stderr();
						 return false;
					}
				}
			}

		}
		else if($adfield_id)
		{
			$this->deleteData('ad_fields','adfield_id',$adfield_id);
		}

		//Update ad data table

		if (!$this->_db->updateObject( '#__ad_data', $adData, 'ad_id' ))
		{
			 echo $this->_db->stderr();
			 return false;
		}

		//empty condition checkin ends

		//END Save --Social Targeting data--

		return true;
	}


	// 	This function to save step3 -> pricing data
	function savePricingData($post)
	{

		$response = array();
		$db = JFactory::getDBO();
		$session = JFactory::getSession();

		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		$app = JFactory::getApplication();
		//.. do Back End Stuff
		if( $app->isAdmin() )
		{
			$ad_creator_id = $post->get('ad_creator_id');
			$user = JFactory::getUser($ad_creator_id);
			$userid = $user->id;
		}
		else// .. do Front End stuff
		{
			$user = JFactory::getUser();
			$userid = $user->id;
		}

		if(!$userid)
		{
			$userid = 0;
			return false;
		}

		$ad_id = $session->get('ad_id');

		$ad_data = new stdClass;
		$ad_data->ad_id = $ad_id;
		$ad_data->ad_startdate=$post->get('datefrom','','STRING');
		$ad_data->ad_payment_type=$post->get('chargeoption');
		$ad_data->ad_noexpiry=$post->get('unlimited_ad','','INT');

		if($ad_data->ad_noexpiry==1)
		{
			$ad_data->ad_approved = 1;
		}

		$camp_id = '';

		if( $socialads_config['select_campaign']==1)
		{
			$db= JFactory::getDBO();

			$camp_id = $post->get('camp','0','STRING');
			$camp_name = $post->get('camp_name','','STRING');
			$camp_amount = $post->get('camp_amount','0','FLOAT');

			if(!$camp_id && !empty($camp_name) && !empty($camp_amount))
			{
				$db=JFactory::getDBO();

				$obj=new stdclass;
				$obj->camp_id='';

				$obj->user_id=$userid;
				$obj->campaign= $post->get('camp_name','','STRING');
				$obj->daily_budget= $post->get('camp_amount','0','FLOAT');
				$obj->camp_published= 1;

				if($obj->camp_id)
				{
					if(!$db->updateObject('#__ad_campaign', $obj, 'camp_id'))
					{
						echo $db->stderr();
						return false;
					}
				}
				else
				{
					if(!$db->insertObject('#__ad_campaign', $obj, 'camp_id'))
					{
						echo $db->stderr();
						return false;
					}
					$response['camp_id'] = $camp_id = $db->insertid();
				}
			}

			$ad_data->camp_id =	$camp_id;
			$ad_data->ad_payment_type = $post->get('pricing_opt','','STRING');

			if($socialads_config['bidding']==1)
			{
				$ad_data->bid_value	 =	$post->get('bid_value','','STRING');
			}

		}

		if(!$db->updateObject('#__ad_data', $ad_data, 'ad_id'))
		{
			echo $db->stderr();
			return false;
		}

		//If campaign is selected then there is no need to place order So need to skip oreder code
		//If unlimited ad then there is no need to save payment info so return from here itself
		if($ad_data->ad_noexpiry==1)
		{
			//Delete Price data if already exist
			$db=JFactory::getDBO();

			$query=" DELETE FROM `#__ad_payment_info`
					 WHERE ad_id = " . $ad_id . " AND  status ='P' ";
			$db->setQuery($query);
			$db->execute();
			return true;
		}

		//NO campaign option is not selected then only place order
		if( $socialads_config['select_campaign']==0)
		{
			$paymentdata = new stdClass;
			$paymentdata->id = '';

			//Get ad id
			if($ad_id)
			{
				$paymentdata->ad_id = $ad_id;
				//Get order id
				// @params $value,$field_name,$tableName
				//$orderid = $this->getIdFromAnyFieldValue($paymentdata->ad_id, 'ad_id', '#__ad_payment_info');

				$db=JFactory::getDBO();
				$query=" SELECT id FROM `#__ad_payment_info`
						 WHERE ad_id = " . $paymentdata->ad_id . " AND  status ='P' ";
				$db->setQuery($query);

				$orderid=$db->loadResult();
				if($orderid)
				{
					$paymentdata->id = $orderid;
				}
			}
			else
			{
				$app=JFactory::getApplication();
				$app->enqueueMessage('Session Expire','error');
				return false;
			}

			$paymentdata->cdate =  date('Y-m-d H:i:s');
			$paymentdata->processor = '';

			$ad_chargeoption=$post->get('chargeoption','','INT');
			if($ad_chargeoption >= 2)
			{
				$credits =$post->get('totaldays','','INT');
			}
			else
			{
				$credits = $post->get('totaldisplay','','INT');
			}

			$paymentdata->ad_credits_qty = $credits;

			//Need
			$paymentdata->ad_amount = $post->get('totalamount','','FLOAT');

			$paymentdata->ad_original_amt =$post->get('totalamount','','FLOAT');

			$paymentdata->status = 'P';

			//Need
			$paymentdata->ad_coupon = '';

			$paymentdata->payee_id = $userid;

			$paymentdata->ip_address = $_SERVER["REMOTE_ADDR"];


			jimport('joomla.application.component.model');
			JLoader::import( 'showad', JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'models' );

			//@ VM CHECK FOR COUPON
			$coupon = $post->get('sa_cop','','RAW');
			//JLoader::import('controller', JPATH_SITE.DS.'components'.DS.'com_socialads');
			$socialadsModelShowad = new socialadsModelShowad;
			$adcop = $socialadsModelShowad->getcoupon($coupon);

			if(!empty($adcop))
			{
				if($adcop[0]->val_type == 1) 		//discount rate
				{
					$val = ($adcop[0]->value/100) * $paymentdata->ad_original_amt;
				}
				else
					$val = $adcop[0]->value;

				if(!empty($val))
				{
					$paymentdata->ad_coupon = $coupon;
				}
			}
			else
			{
				$val = 0;
			}

			$discountedPrice = $paymentdata->ad_original_amt - $val;
			if ($discountedPrice <= 0)
			{
				$discountedPrice = 0;
			}
			//<!-- TAX CALCULATION-->

			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('adstax');//@TODO:need to check plugim type..
			$taxresults=$dispatcher->trigger('addTax',array($discountedPrice));//Call the plugin and get the result

			$appliedTax = 0;

			if( !empty($taxresults) )
			{
				foreach($taxresults as $tax)
				{
						if( !empty($tax) )
						{
								$appliedTax += $tax[1];
						}
				}
			}

			$amountAfterTax = $discountedPrice + $appliedTax;
			$paymentdata->ad_amount = $amountAfterTax;

			// if order amount is 0 due to coupon
			if($paymentdata->ad_amount == 0  && !empty($paymentdata->ad_coupon))
			{
				//$paymentdata->status = 'C';
			}
			//@ VM CHECK FOR COUPON

			if(!$paymentdata->id)
			{
				if(!$db->insertObject('#__ad_payment_info', $paymentdata, 'id'))
				{
					echo $db->stderr();
					return false;
				}
			}
			else
			{
				if(!$db->updateObject('#__ad_payment_info', $paymentdata, 'id'))
				{
					echo $db->stderr();
					return false;
				}
			}
		}
		return $response;
	}

	//Function to Get id from any field name & value
	//@params value=>column value, $field_name =>column name, $tableName=>table name
	function getIdFromAnyFieldValue($value,$field_name,$tableName)
	{
		$db=JFactory::getDBO();

		$query="SELECT id FROM `" . $tableName . "`
		 WHERE  `" . $field_name . "` =  ". $value;

		$db->setQuery($query);

		$id=$db->loadResult();

		return $id;

	}

	/** */
	function getpricingData($ad_id)
	{
		$db=JFactory::getDBO();
		$query="
			SELECT p.ad_credits_qty, d.ad_payment_type, d.ad_startdate, p.ad_original_amt
			FROM `#__ad_data` AS d
			LEFT JOIN `#__ad_payment_info` AS p ON p.ad_id=d.ad_id
			WHERE d.ad_id = ".$ad_id."
			AND p.status = 'P' " ;

		$db->setQuery($query);
		$result=$db->loadObject();
		return $result;
	}
		// added by VM
			// To Fetch country list from Db
	function getCountry()
	{
		$db = JFactory::getDBO();
		$query="SELECT `country` FROM `#__ad_geo_country`";
		$db->setQuery($query);
		$rows = $db->loadColumn();
		return $rows;
	}
	function getuserState($country)
	{
		$db = JFactory::getDBO();
		$query="SELECT r.region FROM `#__ad_geo_region` AS r LEFT JOIN `#__ad_geo_country` as c
		ON r.country_code=c.country_code where c.country=\"".$country."\"";
		$db->setQuery($query);
		$rows = $db->loadColumn();
		return $rows;
	}

	function billingaddr($uid,$data)
	{
		//$data = $data1->get('bill',array(),'ARRAY');
		$row = new stdClass;
		$row->user_id=$uid;
		$row->user_email=$data['email1'];
		$row->firstname=$data['fnam'];
		$row->firstname=$data['fnam'];

		if(isset($data['mnam']))
		{
			$row->middlename=$data['mnam'];
		}
		$row->lastname=$data['lnam'];

		if(!empty($data['vat_num']))
		{
			$row->vat_number=$data['vat_num'];
		}
		$row->country_code=$data['country'];
		$row->address=$data['addr'];

		// in smoe country city,state,zip code is not present - eg HONG CONG
		$row->city = (!empty($data['city']))?$data['city']:'';
		$row->state_code = (!empty($data['state']))?$data['state']:'';//$data['state'];
		$row->zipcode = (!empty($data['zip']))?$data['zip']:'';//$data['zip'];
		$row->phone=$data['phon'];
		$row->approved='1';


		$query = "Select id FROM #__ad_users WHERE user_id=".$uid .' ORDER BY `id` DESC';
		$this->_db->setQuery($query);
		$bill = $this->_db->loadResult();
	 	if($bill)
	 	{
	 		$row->id=$bill;
			if(!$this->_db->updateObject('#__ad_users', $row, 'id'))
			{
				echo $this->_db->stderr();
				return 0;
			}
		}
		else
		{
			if(!$this->_db->insertObject('#__ad_users', $row, 'id'))
			{
				echo $this->_db->stderr();
				return 0;
			}
		}
		return 1;
	}

	function getbillDetails($userId)
	{
		$db = JFactory::getDBO();
		$query = "Select * FROM #__ad_users WHERE user_id=".$userId .' ORDER BY `id` DESC';
		$db->setQuery($query);
		return $billDetails = $db->loadObject();
	}

	// added by VM END

	//Function to delete record
	//@params $table_name, $where_field_name= where column name , $where_field_value = column value
	function deleteData($table_name,$where_field_name,$where_field_value)
	{
		$db=JFactory::getDBO();

		$app= JFactory::getApplication();
		$dbprefix = $app->getCfg('dbprefix');

		$tbexist_query = "SHOW TABLES LIKE '".$dbprefix.$table_name."'";
		$db->setQuery($tbexist_query);
		$isTableExist = $db->loadResult();

		$paramlist=array();

		if($isTableExist)
		{
			$query="DELETE FROM #__".$table_name. "
					 WHERE ".$where_field_name ." = ". $where_field_value;

			$db->setQuery($query);
			$db->execute();
		}
	}

	// Amol
	//Targetting radio value => Yes/No
	function getRadioValues($ad_id)
	{

		$radioValue=array();
		//@params value=>column value, $field_name =>column name, $tableName=>table name
		$radioValue['geo_target']=$this->getIdFromAnyFieldValue($ad_id,'ad_id','#__ad_geo_target');
		$radioValue['cont_target']=$this->getIdFromAnyFieldValue($ad_id,'ad_id','#__ad_contextual_target');

		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$integration = $socialads_config['integration'];
		$radioValue['social_target']='';
		if($integration != 2)
		{
			$db=JFactory::getDBO();
			$query="SELECT adfield_id FROM  #__ad_fields
			 WHERE adfield_ad_id =  ". $ad_id;
			$db->setQuery($query);

			$radioValue['social_target'] = $db->loadResult();
		}
		return $radioValue;
	}

	//Amol
	function getAdPreviewData($ad_id)
	{
		$db=JFactory::getDBO();

		$query = "SELECT ad.camp_id, ad.ad_payment_type, camp.campaign
			FROM #__ad_data as ad
			LEFT JOIN #__ad_campaign as camp ON ad.camp_id = camp.camp_id
			WHERE ad.ad_id = ".$ad_id;
		$db->setQuery($query);

		$result=$db->loadObject();
		return $result;
	}

	//activateAd
	function activateAd()
	{
		$session=JFactory::getSession();
		$db=JFactory::getDBO();
		$ad_id = $session->get('ad_id');

		$obj = new stdclass();
		$obj->ad_id = $ad_id;
		$obj->ad_published =1;

		if(!$db->updateObject('#__ad_data', $obj, 'ad_id'))
		{
			echo $this->_db->stderr();
			return 0;
		}

		// vm: send admin approve mail for new ad
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		if($socialads_config['approval']==1)
		{
			$createAdHelper = new createAdHelper();
			$createAdHelper->adminAdApprovalEmail($ad_id);
		}
		// vm:end
		return true;
	}

	//draftAd
	function draftAd()
	{
		$session=JFactory::getSession();
		$db=JFactory::getDBO();
		$ad_id = $session->get('ad_id');

		$obj = new stdclass();
		$obj->ad_id = $ad_id;
		$obj->ad_published =0;

		if(!$db->updateObject('#__ad_data', $obj, 'ad_id'))
		{
			echo $this->_db->stderr();
			return 0;
		}
		return true;
	}

	function allowWholeAdEdit($ad_id)
	{

		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		if($socialads_config['select_campaign']==1)
		{
			return 1;
		}

		$db=JFactory::getDBO();
		$query="SELECT a.ad_id as confirmpayment FROM
				#__ad_data as a WHERE a.ad_id=$ad_id
				AND (
				a.ad_credits_balance>0  OR a.ad_noexpiry =1 OR a.ad_alternative = 1
					OR (a.ad_payment_type=2 AND (a.ad_enddate <>'0000-00-00' AND a.ad_startdate <= CURDATE() AND a.ad_enddate > CURDATE() ) ) )
				 ";
		$db->setQuery($query);
		$result=$db->loadResult();

		//Ad Payment confirmation
		if(!$result)
		{
			return 1;
		}
		return 0;
	}

	function checkItIsuserAd($ad_id)
	{
		$db=JFactory::getDBO();

		$query="SELECT a.ad_creator
			FROM #__ad_data	as a
			WHERE a.ad_id=$ad_id ";

		$db->setQuery($query);
		$result=$db->loadResult();

		$userid=JFactory::getUser()->id;

		if($userid!=$result)
		{
			$app = JFactory::getApplication();
			$app->enqueuemessage('Wrong Ad ID','error');
			return false;
		}
		return true;

	}

	//Get selected user data
	function getPromoterPlugin($uid)
	{
		JPluginHelper::importPlugin( 'socialadspromote' );
		$dispatcher = JDispatcher::getInstance();
		$results	= $dispatcher->trigger('onPromoteList',array($uid));

		foreach($results as $result)
		{
			if(!empty($result))
			{
				$plug_name = $result[0]->value;
				$plug_name = explode('|', $plug_name);

				$plugin = JPluginHelper::getPlugin( 'socialadspromote',$plug_name[0]);
				$pluginParams = json_decode( $plugin->params );
				$opt[] = JHtml::_('select.option','<OPTGROUP>', $pluginParams->plugin_name);

				foreach($result as $res)
				{
					$opt[] = JHtml::_('select.option', $res->value, $res->text);
				}

				$opt[] = JHtml::_('select.option','</OPTGROUP>');
			}
		}

		$sel[0]->value = '';
		$sel[0]->text = JText::_('SELECT_PLG');
		$opt = array_merge($sel, $opt);

		$htmlSelect = JHtml::_('select.genericlist',  $opt, 'addatapluginlist', 'class="promotplglist chzn-done" onchange="promotplglistOnchange()" ', 'value', 'text', '');

		return $htmlSelect;
	}
}
// end of class

