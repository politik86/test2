<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');

class socialadsModelSettings extends JModelLegacy
{

	function getAPIpluginData()
	{
		$condtion = array(0 => '\'payment\'');
		$condtionatype = join(',',$condtion);
		if(JVERSION >= '1.6.0')
		{
			$query = "SELECT extension_id as id,name,element,enabled as published FROM #__extensions WHERE folder in ($condtionatype) AND enabled=1 order by name";
		}
		else
		{
			$query = "SELECT id,name,element,published FROM #__plugins WHERE folder in ($condtionatype) AND published=1 order by name";
		}
		$this->_db->setQuery($query);
		return $this->_db->loadobjectList();
	}

	/**
	 * Refreshes the Joomla! update sites for this extension as needed
	 *
	 * @return  void
	 */
	public function refreshUpdateSite()
	{
//		$isPro = defined('ADMINTOOLS_PRO') ? ADMINTOOLS_PRO : 0;

		JLoader::import('joomla.application.component.helper');
		$params = JComponentHelper::getParams('com_socialads');

		if(version_compare(JVERSION, '3.0', 'ge'))
		{
			$dlid = $params->get('downloadid', '');
		}
		else
		{
			$dlid = $params->getValue('downloadid', '');
		}

		$extra_query = null;

		// If I have a valid Download ID I will need to use a non-blank extra_query in Joomla! 3.2+
		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			// Even if the user entered a Download ID in the Core version. Let's switch his update channel to Professional
//			$isPro = true;

			$extra_query = 'dlid=' . $dlid;
		}

		// Create the update site definition we want to store to the database
		$update_site = array(
			'name'		=> 'SocialAds updates',
			'type'		=> 'extension',
			'location'	=> 'http://www.hou-de-kharcha.com/tjnew/tj3.2/index.php?option=com_ars&view=update&task=stream&format=xml&id=1&dummy=extension.xml',
			'enabled'	=> 1,
			'last_check_timestamp'	=> 0,
			'extra_query'	=> $extra_query
		);

		if (version_compare(JVERSION, '3.0.0', 'lt'))
		{
			unset($update_site['extra_query']);
		}

		$db = $this->getDbo();

		// Get the extension ID to ourselves
		$query = $db->getQuery(true)
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type') . ' = ' . $db->q('component'))
			->where($db->qn('element') . ' = ' . $db->q('com_socialads'));
		$db->setQuery($query);

		$extension_id = $db->loadResult();

		if (empty($extension_id))
		{
			return;
		}

		// Get the update sites for our extension
		$query = $db->getQuery(true)
			->select($db->qn('update_site_id'))
			->from($db->qn('#__update_sites_extensions'))
			->where($db->qn('extension_id') . ' = ' . $db->q($extension_id));
		$db->setQuery($query);

		$updateSiteIDs = $db->loadColumn(0);

		if (!count($updateSiteIDs))
		{
			// No update sites defined. Create a new one.
			$newSite = (object)$update_site;
			$db->insertObject('#__update_sites', $newSite);

			$id = $db->insertid();

			$updateSiteExtension = (object)array(
				'update_site_id'	=> $id,
				'extension_id'		=> $extension_id,
			);
			$db->insertObject('#__update_sites_extensions', $updateSiteExtension);
		}
		else
		{
			// Loop through all update sites
			foreach ($updateSiteIDs as $id)
			{
				$query = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__update_sites'))
					->where($db->qn('update_site_id') . ' = ' . $db->q($id));
				$db->setQuery($query);
				$aSite = $db->loadObject();

				// Does the name and location match?
				if (($aSite->name == $update_site['name']) && ($aSite->location == $update_site['location']))
				{
					// Do we have the extra_query property (J 3.2+) and does it match?
					if (property_exists($aSite, 'extra_query'))
					{
						if ($aSite->extra_query == $update_site['extra_query'])
						{
							continue;
						}
					}
					else
					{
						// Joomla! 3.1 or earlier. Updates may or may not work.
						continue;
					}
				}

				$update_site['update_site_id'] = $id;
				$newSite = (object)$update_site;
				$db->updateObject('#__update_sites', $newSite, 'update_site_id', true);
			}
		}
	}

	function store()
	{
		global 	$mainframe;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		$file = JPATH_COMPONENT.DS."config".DS.'config.php';
		$configs = $input->get('config', '','ARRAY');
		if(is_writable($file))
		{

			if($configs)
			{
				$txt[] = '<?php';
				$txt[] = "\n";
				$txt[] = '$socialads_config = array(';
				foreach ($configs as $k => $v)
				{
						$var = $this->chk_array($v);
						$opts[] = "'{$k}' => ".$var ;
				}

				$txt[] = implode(",\n", $opts);
				$txt[] = ')';
				$txt[] = "\n";
				$txt[] = "?>";
				$text = implode("\n", $txt);

			$ret = JFile::write($file, $text);

			return $ret;
			}
			else
			return false;
		}
		else
		return false;
	}

	function chk_array($configdata) {

		if(is_array($configdata))
		{
			$str = 'array(';
			$str_arr=array();
			foreach ($configdata as $kk => $vv)
			{
				$str_arr[]= "'{$kk}' => " . $this->chk_array($vv) ;
			}
			$str.= implode(",", $str_arr);;
			$str .= ')';
			return $str;
		}
		else{
			return  "'".addslashes($configdata). "'";
		}

	}

	function installgeoTabledata($tablefiles=array())
	{

		if(!empty($tablefiles))
		{
			foreach($tablefiles as $file)
			{
				$result='';
				$result=$this->executeMysqlfile($file);
				if($result['success']!=1)
				{
					return $result;


				}

			}
			$result['success']=1;
			return $result;
		}



	}

	function executeMysqlfile($file)
	{
	$db =  JFactory::getDBO();
		if (!($buffer = file_get_contents($file))) {
			$result['success']=0;
			$result['ErrorMsg']=$file." Not Exist";
			return $result;
		}

		$queries = $this->splitQueries($buffer);
		foreach ($queries as $query)
		{
			// Trim any whitespace.
			$query = trim($query);

			// If the query isn't empty and is not a comment, execute it.
			if (!empty($query) && ($query{0} != '#')) {
				// Execute the query.
				$db->setQuery($query);
				$db->execute();

				// Check for errors.
				if ($db->getErrorNum()) {
					$result['success']=0;
					$result['ErrorMsg']=$db->getErrorMsg();;
					return $result;
				}
			}
		}

		$result['success']=1;
		return $result;
	}



	function splitQueries($sql)
	{
	$db =  JFactory::getDBO();
		// Initialise variables.
		$buffer		= array();
		$queries	= array();
		$in_string	= false;

		// Trim any whitespace.
		$sql = trim($sql);

		// Remove comment lines.
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);

		// Parse the schema file to break up queries.
		for ($i = 0; $i < strlen($sql) - 1; $i ++)
		{
			if ($sql[$i] == ";" && !$in_string) {
				$queries[] = substr($sql, 0, $i);
				$sql = substr($sql, $i +1);
				$i = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
				$in_string = false;
			}
			elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\")) {
				$in_string = $sql[$i];
			}
			if (isset ($buffer[1])) {
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];
		}

		// If the is anything left over, add it to the queries.
		if (!empty($sql)) {
			$queries[] = $sql;
		}

		return $queries;
	}

	function creategeotables()
	{
		$db = & JFactory::getDBO();
		$query = "
CREATE TABLE IF NOT EXISTS `#__ad_geo_country` (
 `country_id` int(11) NOT NULL,
  `country` varchar(64) default NULL,
  `country_3_code` char(3) default NULL,
  `country_code` char(2) default NULL,
  `country_jtext` varchar(255) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1" ;

		$db->setQuery($query);
		if( ! $db->execute() )
		{
		echo $img_ERROR.JText::_('Unable to create table').$BR;
		echo $db->getErrorMsg();
		return FALSE;
		}


		$query = "
	CREATE TABLE IF NOT EXISTS `#__ad_geo_city` (
  `city_id` int(11) NOT NULL auto_increment,
  `city` varchar(255) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `region_code` varchar(255) NOT NULL,
  PRIMARY KEY  (`city_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1" ;

		$db->setQuery($query);
		if( ! $db->execute() )
		{
		echo $img_ERROR.JText::_('Unable to create table').$BR;
		echo $db->getErrorMsg();
		return FALSE;
		}


		$query = "
		CREATE TABLE IF NOT EXISTS `#__ad_geo_region` (
  `region_id` tinyint(4) NOT NULL,
  `country_code` varchar(8) NOT NULL,
  `region_code` varchar(8)  NOT NULL,
  `region` varchar(64)  NOT NULL
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1" ;

		$db->setQuery($query);
		if( ! $db->execute() )
		{
		echo $img_ERROR.JText::_('Unable to create table').$BR;
		echo $db->getErrorMsg();
		return FALSE;
		}



	}
	function checkgeotables()
	{
		$db =  JFactory::getDBO();
		$config = JFactory::getConfig();
		$return=array();



			if(JVERSION>=3.0) {
			$dbname=$config->get( 'db' );
			$dbprefix=$config->get( 'dbprefix' );

			}
			else
			{
				$dbname=$config->getValue( 'config.db' );
				$dbprefix=$config->getvalue( 'config.dbprefix' );
			}



		$query = "SELECT table_name
				FROM information_schema.tables
				WHERE table_schema = '" . $dbname . "'
					AND table_name = '" .$dbprefix
					. "ad_geo_city'";
		$db->setQuery( $query );
		$citycheck = $db->loadResult();

		if(!($citycheck)){
		$return[]=JPATH_ADMINISTRATOR.'/components/com_socialads/sqlfiles/ad_geo_city.sql';

		}

		$query = "SELECT table_name
				FROM information_schema.tables
				WHERE table_schema = '" . $dbname . "'
					AND table_name = '" . $dbprefix
					. "ad_geo_country'";
		$db->setQuery( $query );
		$countrycheck = $db->loadResult();

		if(!($countrycheck)){
		$return[]=JPATH_ADMINISTRATOR.'/components/com_socialads/sqlfiles/ad_geo_country.sql';

		}

		$query = "SELECT table_name
				FROM information_schema.tables
				WHERE table_schema = '" . $dbname . "'
					AND table_name = '" . $dbprefix
					. "ad_geo_region'";
		$db->setQuery( $query );
		$regioncheck = $db->loadResult();

		if(!($regioncheck)){
		$return[]=JPATH_ADMINISTRATOR.'/components/com_socialads/sqlfiles/ad_geo_region.sql';

		}

		return $return;
	}

	function getgeodb(){
		jimport('joomla.filesystem.file');
		jimport( 'joomla.filesystem.archive' );
    $url  = 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz';
    $file_name = JPATH_SITE.DS."components".DS."com_socialads".DS."geo".DS."GeoLiteCity.dat.gz";//'/path/to/a-large-file.zip';

    $fp = fopen($file_name, 'w');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FILE, $fp);

    $data = curl_exec($ch);
 		// Check if any error occured
		if(curl_errno($ch))
		{
			 $curlmsg = 'Curl error: ' . curl_error($ch);
		}
		else
		{
			$curlmsg = '1';//'Operation completed of download';

		  curl_close($ch);
		  fclose($fp);

			$extract= 0;
			$extract= JArchive::extract( $file_name,JPATH_SITE.DS."components".DS."com_socialads".DS."geo");

			JFile::delete($file_name);
		}

    $z = array("downmsg"=>$curlmsg,
    "readmsg"=>$extract,
    );

 		$json=json_encode($z);
		return $json;

	}

	function populategeoDB(){

	//$creategeotables = $this->creategeotables();
	$geoDBinstall=0;
		$geotable_list = $this->checkgeotables();
		if(!empty($geotable_list))
		$geotablepresent=0;
		else
		$geotablepresent=1;

		if($geotablepresent==0)
		{
			$result=$this->installgeoTabledata($geotable_list);
			if($result['success']==1)
			{
			$geoDBinstall=1;
				$resultdata[]='<div class=" alert alert-success"	>'.JText::_('SAGEO_INSTL_COMPLETE').'</div>';

			}
			else
			{
				$geoDBinstall=0;
				$sqlfiles=JPATH_ADMINISTRATOR.'/components/com_socialads/sqlfiles';
				$resultdata[]='<div class=" alert alert-danger" >'.JText::_('SAGEO_INSTL_NOT_COMPLETE').'<br>'.$result['ErrorMsg'].'<br/>'.JText::sprintf('SAGEO_INSTL_INSTRUCTION', $sqlfiles).'</div>';
			}
		}
		else
		{
					$geoDBinstall=1;
				$resultdata[]='<div class=" alert alert-primary"	>'.JText::_('SAGEO_INSTL_EXIST').'</div>';

		}

		$finalresult=array('geoDBinstall'=>$geoDBinstall,'displaymsg'=>$resultdata);

		return $finalresult;

	}

	function populategemaxmindDB(){
		//create maxmind tables
		$createmaxmindtables_json = $this->getgeodb();
		$geoDBinstall=0;
		$maxmind_result=json_decode($createmaxmindtables_json,true);
		if($maxmind_result['downmsg']!=1)
		{
			$resultdata[]= '<span class="install_fail" >'.JText::_('SAGEO_CITY_NO_DOWN').$maxmind_result['downmsg'].'<br/>'.JText::_('SAGEO_CITY_ERROR_MSG').'<br/>'.JText::_('SAGEO_FILE').'</span>';

		}
		else if($maxmind_result['readmsg']!=1)
		{
			$resultdata[]='<span class="install_fail" >'.JText::_('SAGEO_CITY_NO_READ').JText::_('SAGEO_FILE').'<br/>'.'</span>';

		}
		else
		{
			$geoDBinstall=1;
			$resultdata[]='<span class="install_success"	>'.JText::_('SAGEO_CITY_DONE').'</span>';
		}
		//end create maxmind tables
				$finalresult=array('maxmindDBinstall'=>$geoDBinstall,'displaymsg'=>$resultdata);
		return $finalresult;
		}


	function migrateads_camp($migrate_status){


		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$input=JFactory::getApplication()->input;
		//require(JPATH_SITE.DS."components".DS."com_socialads".DS."models".DS."payment.php");
		//jloader
		$db = JFactory::getDBO();
		$query = " SELECT ad_creator FROM #__ad_data GROUP BY ad_creator";
		$db->setQuery($query);
		$ad_creator=$db->loadColumn();
		$json = 0;
		//$msg = JText::_('AUTO_GENERATED');

		foreach($ad_creator as $key)   // progess..............
		{

				$query = " SELECT * FROM #__ad_data WHERE ad_creator = ".$key." AND camp_id = 0";
				$db->setQuery($query);
				$ad_info = $db->loadobjectlist();
				//print_r($ad_info);

				//create new camp as old camp.....
				$query = "SELECT campaign,camp_id FROM #__ad_campaign WHERE campaign = 'Old Ads' AND user_id = ".$key;
				$db->setQuery($query);
				$ifexists_camp = $db->loadobjectlist();


			if($ad_info)
			{
				foreach($ad_info as $row)
				{
						// for each ad calculate USD

						if($row->ad_credits_balance)
						{

							if($migrate_status=='camp_hide')
							{
									$json = 1;
									return $json;
							}
						}
				}
			}

				if(empty($ifexists_camp))
				{
					$insertcamp = new stdClass;

					$insertcamp->camp_id = '';
					$insertcamp->user_id = $key;
					$insertcamp->campaign = "Old Ads";

					$insertcamp->daily_budget = $socialads_config['camp_currency_daily'];
					$insertcamp->camp_published = 1;
					if(!$db->insertObject( '#__ad_campaign', $insertcamp, 'camp_id' ))
					{
						echo $db->stderr();
							return false;
					}
					$last_id_camp = $db->insertid();
				}
				else
				{
						$last_id_camp = $ifexists_camp['0']->camp_id;

				}


			if($ad_info)
			{
				foreach($ad_info as $row)
				{
						// for each ad calculate USD

						if($row->ad_credits_balance)
						{
								// if balance then convert in USD
							if($socialads_config['zone_pricing']==1)
							{
								if($row->ad_zone) // zone pricing
								{

									$query = "SELECT per_imp,per_click FROM #__ad_zone WHERE id = ".$row->ad_zone;
									$db->setQuery($query);
									$zone = $db->loadobjectlist();

									if($row->ad_payment_type==1)//per click ad
									{

										$usd_pay = $row->ad_credits_balance * $zone['0']->per_click;
									}
									else
									{

										$usd_pay = $row->ad_credits_balance * $zone['0']->per_imp;
									}

								}
							}
								else   //std pricing
								{
									if($row->ad_payment_type==1)//per click ad
									{
										$usd_pay = $row->ad_credits_balance * $socialads_config['clicks_price'];
									}
									else
									{
										$usd_pay = $row->ad_credits_balance * $socialads_config['impr_price'];
									}
								}
								$comment_array=array();
								$comment_array[] = 'VIA_MIGRATTION';
								$comment_array[] = $row->ad_id;
								$comment = implode('|',$comment_array);
								sleep(1);
									$insertpay = new stdClass;
									$insertpay->id = '';
									$insertpay->ad_id = 0;
									$insertpay->cdate = date('Y-m-d H:i:s');	//todays date
									$insertpay->mdate = date('Y-m-d H:i:s');
									$insertpay->payee_id = $key;
									$insertpay->ad_amount = $usd_pay;
									$insertpay->status = "C";
									$insertpay->ip_address = $_SERVER["REMOTE_ADDR"];
									$insertpay->ad_original_amt = $usd_pay;
									$insertpay->comment= 'AUTO_GENERATED';
									if(!$db->insertObject( '#__ad_payment_info', $insertpay, 'id' ))
									{
										echo $db->stderr();
										return false;
									}
									$last_id_pay = $db->insertid();
									JLoader::import('payment', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');

									$socialadsModelpayment = new socialadsModelpayment();
									//$comment = 'VIA_MIGRATTION';
									$transac_id = $socialadsModelpayment->add_transc($usd_pay,$last_id_pay,$comment);    //entry for camp_transc table

									$query = "UPDATE #__ad_data SET camp_id =".$last_id_camp." WHERE ad_id =".$row->ad_id;
									$db->setQuery($query);
									$db->execute();
									$json=1;
						}  // if ends for credits avaiable
						elseif($row->ad_noexpiry==1){

							$query = "UPDATE #__ad_data SET camp_id =".$last_id_camp." WHERE ad_id =".$row->ad_id;
									$db->setQuery($query);
									$db->execute();
									$json=1;
							}
				}

			}


		}

		return $json;
	}

	//function to migrate camp ads to old pricing
	function migrateads_old($migrate_status){



					require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
					$input=JFactory::getApplication()->input;
					//require(JPATH_SITE.DS."components".DS."com_socialads".DS."models".DS."payment.php");
					//jloader
					//$msg = JText::_('AUTO_GENERATED');
					$db = JFactory::getDBO();
					$query = " SELECT ad_creator FROM #__ad_data GROUP BY ad_creator";
					$db->setQuery($query);
					$ad_creator=$db->loadColumn();
					$json = 0;
				//	print_r($ad_creator);
					foreach($ad_creator as $key)   // progess..............
					{

							$query = " SELECT * FROM #__ad_data WHERE ad_creator = ".$key." AND camp_id <> 0 AND ad_alternative=0 AND ad_noexpiry=0 AND ad_affiliate=0";
							$db->setQuery($query);
							$ad_info = $db->loadobjectlist();
							//print_r($ad_info);

							//get balance///--------
							$query = "SELECT id,balance FROM `#__ad_camp_transc` where time = (select MAX(time) from #__ad_camp_transc where user_id =".$key.")";
							$db->setQuery($query);
							$current_bal = $db->loadobjectlist();

						if($ad_info)
						{
								if(isset($current_bal[0]->balance))
								{
									if($current_bal[0]->balance)
									{
										if($migrate_status=='camp_hide')
										{
												$json = 1;
												return $json;
										}
									}
								}

						}
							//---

						//get a paymenet gateway//
							$query = "SELECT processor FROM `#__ad_payment_info` where payee_id=".$key;
							$db->setQuery($query);
							$payment_gateway = $db->loadresult();

						if($ad_info)
						{
							//get nos of ads//
							$count_ads = count($ad_info);

							//divide bal/nos of ads
							$each_ad_money=0.00;
							if(isset($current_bal[0]->balance))
							{
								$each_ad_money = $current_bal[0]->balance/$count_ads;
							}
							$each_ad_money_to_use = round($each_ad_money,2);
							//convert into credit as per click /imp and zone pricing
							foreach($ad_info as $row)
							{

									// for each ad calculate USD
								if(isset($current_bal[0]->balance))
								{
									if($current_bal[0]->balance)
									{


											// if balance then convert in USD
										if($socialads_config['zone_pricing']==1)
										{
											if($row->ad_zone) // zone pricing
											{

												$query = "SELECT per_imp,per_click FROM #__ad_zone WHERE id = ".$row->ad_zone;
												$db->setQuery($query);
												$zone = $db->loadobjectlist();

												if($row->ad_payment_type==1)//per click ad
												{

													$row->ad_credits = $each_ad_money_to_use / $zone['0']->per_click;
												}
												else
												{

													$row->ad_credits = $each_ad_money_to_use / $zone['0']->per_imp;
												}

											}
										}
											else   //std pricing
											{
												if($row->ad_payment_type==1)//per click ad
												{
													$row->ad_credits = $each_ad_money_to_use / $socialads_config['clicks_price'];
												}
												else
												{
													$row->ad_credits = $each_ad_money_to_use / $socialads_config['impr_price'];
												}
											}


											sleep(1);
												$insertpay = new stdClass;
												$insertpay->id = '';
												$insertpay->ad_id = $row->ad_id;
												$insertpay->cdate = $row->ad_created_date;//original date
												$insertpay->mdate = date('Y-m-d H:i:s');
												$insertpay->payee_id = $key;
												$insertpay->ad_amount = $each_ad_money_to_use;
												$insertpay->status = "C";
												$insertpay->ip_address = $_SERVER["REMOTE_ADDR"];
												$insertpay->ad_original_amt = $each_ad_money_to_use;
												$insertpay->processor = $payment_gateway;
												$insertpay->comment='AUTO_GENERATED';
												if(!$db->insertObject( '#__ad_payment_info', $insertpay, 'id' ))
												{
													echo $db->stderr();
													return false;
												}


												$query = "UPDATE #__ad_data SET camp_id =0,ad_credits=".$row->ad_credits.",ad_credits_balance=".$row->ad_credits." WHERE ad_id =".$row->ad_id;
												$db->setQuery($query);
												$db->execute();


												/*$query = "UPDATE #__ad_camp_transc SET balance=0 WHERE id=".$current_bal[0]->id;
												$db->setQuery($query);
												$db->execute();
												*/
												$comment_array=array();
												$comment_array[] = 'SPENT_DONE_FROM_MIGRATION';
												$comment_array[] = $row->ad_id;
												$comment = implode('|',$comment_array);
												$date1 = microtime(true);
												sleep(1);
												$camp_trans = new stdClass;
												$camp_trans->id = '';
												$camp_trans->time  =$date1;
												$camp_trans->user_id =$key;
												$camp_trans->spent =$each_ad_money_to_use;
												$camp_trans->earn = '';
												$camp_trans->balance = '';
												$camp_trans->type = 'O';
												$camp_trans->type_id = '';
												$camp_trans->comment = $comment;

												if(!$db->insertObject( '#__ad_camp_transc', $camp_trans, 'id' ))
												{
													echo $db->stderr();
													return false;
												}
												$json=1;
									}  // if ends for credits avaiable
								}
							}

						}

						$query = "UPDATE #__ad_data SET camp_id=0 WHERE ad_noexpiry=1 OR ad_affiliate=1 AND ad_creator=".$key;
						$db->setQuery($query);
						$db->execute();
					}
					return $json;
				}



}
?>
