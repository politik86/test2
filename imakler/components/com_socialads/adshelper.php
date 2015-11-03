<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.html.parameter' );
jimport('joomla.filesystem.file');

class adRetriever
{
	static $staticvar = array();

	static $ad_entry_number = 1;
	static $_resultads = array();
	var $_my = null;
	var $_fromemail = null;
	var $_geodebug = 0; // will show debug for geo
	var $_contextdebug = 0; // will show debug for geo
	var $_contextmainquery ='';

	/*function __construct(user object,Module parameters, from mail) */
	function __construct($userid=0,$extra=0)
	{
		//$this->_my = ($user == 0) ? (JFactory::getUser()) : (JFactory::getUser($user));
		if($userid==0)
			$this->_my = JFactory::getUser();
		else if($userid== -1)
			$this->_my->id = 0;
		else
			$this->_my = JFactory::getUser($userid);
		$this->_fromemail =  $extra;
	}

	function join_camp()
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$join ='';
		if($socialads_config['select_campaign']==1)
		{
			$join = " INNER JOIN #__ad_campaign as c ON c.camp_id = a.camp_id  ";
		}
		return $join;
	}

	function query_common($params,$function_name,$adRetriever)
	{
		//@TODO:- flag for(ignoe ad,no ads function call)(
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
//		$adRetriever = new adRetriever();
		$zone = $adRetriever->getParam($params,'zone');

		$date = date('Y-m-d');
		$no_ads_ids= $adRetriever->get_shownAds($zone);
		$no_ads=" a.ad_id NOT IN (".$no_ads_ids.") ";
		$comon_query[] = " a.ad_zone =". $zone;
		if($socialads_config['select_campaign']==1 && $function_name!="alt" && $function_name!= "affiliate")
		{
			$comon_query[] = " c.camp_published = 1";
		}
		else
		{
			if($function_name=="adids" || $function_name=="guest" || $function_name=="contextual")
			{
				$comon_query[] = " (a.ad_credits_balance>0  OR a.ad_noexpiry =1 OR (a.ad_payment_type=2 AND (a.ad_enddate <>'0000-00-00' AND a.ad_startdate <= CURDATE() AND a.ad_enddate > CURDATE() ) ) )";
			}
		}
		$comon_query[] = " a.ad_published = 1";
		$comon_query[] = " a.ad_approved = 1";
		$comon_query[] = " a.ad_id NOT IN (SELECT adid FROM #__ad_ignore WHERE userid =  ".$this->_my->id.")";

		if($no_ads_ids)
		$comon_query[] = $no_ads;

		// for showing user itz own ad
		if($this->_fromemail == 1){
			$owner_ad = $adRetriever->getParam($params,'owner_ad');
		}else{
			$owner_ad = $socialads_config['own_ad'];
		}
		if($owner_ad==0)
		{
			if($function_name!="alt")
				$comon_query[] = "  a.ad_creator <>  ".$this->_my->id;
		}
		return $comon_query;
	}

	function get_shownAds($zone_id)
	{
		$str='';
		if (count(self::$_resultads[$zone_id])) { // exclude the already shown ads
			$str= array();
			foreach (self::$_resultads[$zone_id] as $val)
			{
				$str[]= $val->ad_id;
			}
			$str = implode(',',$str);
		}
		return $str;
	}

	function CheckifAdsavailable($ad_id,$module_id,$zone_id)
	{
		$session = JFactory::getSession();
		$resultads	=	$session->get('SA_resultads');
		$adRetriever = new adRetriever();

		$ret	=	new stdClass;
		shuffle($resultads[$zone_id]);	//for getting random ads in ad rotation

		$is_ad	= 0;
		foreach($resultads[$zone_id] as $key=>$ad_obj)
		{
			if(empty($ad_obj->seen))
			{
				$is_ad	=	1;
				$ret	= $ad_obj;
				$resultads[$zone_id][$key]->seen	= $module_id;
				if(empty($ad_obj->impression_done))
				{
					$status_adcharge = $adRetriever->getad_status($ad_obj->ad_id);
					//reduce the credits
					if($status_adcharge['status_ads'] == 1)
					{
						$adRetriever->reduceCredits($ad_obj->ad_id,0,$status_adcharge['ad_charge'],$module_id);
						$resultads[$zone_id][$key]->impression_done	=	1;
					}
					else
						unset($resultads[$zone_id][$key]);
				}
				break;
			}
		}
		if($is_ad == 1)
		{
			foreach($resultads[$zone_id] as $key=>$ad_obj)
			{
				if($ad_obj->ad_id == $ad_id)
				{
					$resultads[$zone_id][$key]->seen = '';
				}
			}
			$session->set('SA_resultads', $resultads);
			if($ret->ad_id != '')
				return $ret;
		}
		return false;
	}

	function getAdsforZone($params,$module_id)
	{
		$adRetriever = new adRetriever();
		$ads	=	$adRetriever->getnumberofAds($params,$module_id,$adRetriever);
		return $ads;

	}
	function getnumberofAds($params,$module_id,$adRetriever)
	{
		$session = JFactory::getSession();

//		$adRetriever = new adRetriever();
		$number = $adRetriever->getParam($params,'num_ads');
		$zone_id=  $adRetriever->getParam($params,'zone');
		$adRetriever->getMatchAds($params,$adRetriever);
		$i=1;
		$ret_ads	=	array();
		$temp=	array();
		//$adRetriever->checkIfAdspresent();


		$temp =  self::$_resultads;
		$session->set('SA_resultads',$temp);
		$resultads_session_ads = array();
		$resultads_session_ads = $session->get('SA_resultads',array());
		foreach($resultads_session_ads[$zone_id] as $key=>$ad)
		{
			if (!empty($ad))
			{
				//self::$ad_array[$ad_id]= array();
				if(empty($ad->seen))
				{
					$statue_adcharge = $adRetriever->getad_status($ad->ad_id);

					//reduce the credits
					$resultads_session_ads[$zone_id][$key]->impression_done	= 0;

					if($statue_adcharge['status_ads'] == 1)
					{
						$resultads_session_ads[$zone_id][$key]->seen = $module_id;
						$adRetriever->reduceCredits($ad->ad_id,0,$statue_adcharge['ad_charge'],$module_id);
						$resultads_session_ads[$zone_id][$key]->impression_done	=	1;
					$i++;
					$ret_ads[]=$ad;
					}
					else
						unset($resultads_session_ads[$zone_id][$key]);

					if($i > $number)
						break;
				}
				else
					continue;
			}
		}
if($adRetriever->_geodebug == '1')
{
	echo '<br><br><b>Total static ads in getnumberofAds b4 seess </b>';
	print_r($resultads_session_ads);
}
		$session->set('SA_resultads', $resultads_session_ads);
if($adRetriever->_geodebug == '1')
{
	echo '</pre>';
}
		return $ret_ads;
	}


	function getMatchAds($params,$adRetriever)
	{
		$debug = $adRetriever->getParam($params,'debug');

		if($debug==1)
		{
			$adRetriever->_geodebug = 1;
			$adRetriever->_contextdebug=1;
		}

		$ads =$adRetriever->fillslots($params,$adRetriever);
	}

	function checkIfAdspresent()
	{
		foreach (self::$_resultads[1] as $key=>$ad)
		{
			// Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName(array('ad_id')));
			$query->from($db->quoteName('#__ad_data'));
			$query->where($db->quoteName('ad_id') . ' = '. $db->quote($ad->ad_id));

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadresult();

			if (empty($results))
			{
				unset(self::$_resultads[1][$key]);
			}
		}
	}

	function getParam($params,$paramindex)
	{
		if($this->_fromemail == 0){
			return $params->get($paramindex,1);
		}
		else{
			return $params[$paramindex];
		}
	}

	function fillslots($params,$adRetriever)
	{
		//$adRetriever = new adRetriever();
		$resultads=$adRetriever->getpriorityAds($params,$adRetriever);
		if($adRetriever->_geodebug == '1')
		{
			echo '<br><br><b>Total static ads variable </b><pre>';
			print_r(self::$_resultads);
		}

		return $resultads; // do not change the return type since it is used by jma_socialads plugin
	}

	function getpriorityAds($params,$adRetriever)
	{
		$zone_id=  $adRetriever->getParam($params,'zone');
		$remain = $adRetriever->getParam($params,'num_ads');

		if(empty(self::$_resultads[$zone_id]))
			self::$_resultads[$zone_id] = array();

		$ad_rotation = $adRetriever->getParam($params,'ad_rotation');

		if($ad_rotation == 1)
			$remain *= 6; //@TODO logic to increase total number of ads for that module.

		if($adRetriever->_geodebug == '1')
		{
			echo '<br>Start debug => ';
		}

		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		if(!($socialads_config['priority_random']))
		{
			$func_list = array('Context','Social','Geo');
			shuffle($func_list);
		}
		else
		{
			$i=0;
			foreach($socialads_config['priority'] as $key=>$value) {
				if($value==0)
					$valuestr='Social';
				else if($value==1)
					$valuestr='Geo';
				else if($value==2)
					$valuestr='Context';
				$func_list[$i]=$valuestr;
				$i++;
			}
		}

		$func_list[]='Guest';
		$func_list[]='Affiliate';
		$func_list[]='Alt';

		$session = JFactory::getSession();

		foreach($func_list as $func)
		{
			if($remain != 0 || $remain == '' )
			{
				$ads = array();
				$func_name = 'get'.$func.'Ads';

				if($adRetriever->_geodebug == '1')
				{
					echo '<br><br><b>'.$func.' Ads::</b> ';
				}

				$data_func_name = 'get'.$func.'Data';
				$func_data=array();
				$ads = $session_ads1 = $session_ads = array();

				if(!($func == 'Guest' || $func == 'Affiliate' || $func == 'Alt' ))
				{
					if($func != 'Context' )
					{
						$data = $session->get($func.'Data',array());
						if(!empty($data))
						{
							$func_data = $data;
							if($adRetriever->_geodebug == '1')
							{
								echo '<br><br>user data from session for '.$func;
							}
						}
						else
						{
							$func_data=$adRetriever->$data_func_name($params,$adRetriever);
							$session->set($func.'Data',$func_data);
						}
					}
					else
						$func_data=$adRetriever->$data_func_name($params,$adRetriever);

					if ($socialads_config['enable_caching'] == 1 && isset($_COOKIE[$func.'_Ads1']))
					{
						$cooke_ads = json_decode($_COOKIE[$func.'_Ads1'],true);
					}
					else
					{
						$cooke_ads[$zone_id] = array();
					}

					// REMOVE AD FROM COOKIE ID AD NOT PRESENT
					if (isset($cooke_ads[$zone_id]) && !empty($cooke_ads[$zone_id]))
					{
						foreach ($cooke_ads[$zone_id] as $key => $cookieAd)
						{
							// Get a db connection.
							$db = JFactory::getDbo();

							// Create a new query object.
							$query = $db->getQuery(true);

							// Select all records from the user profile table where key begins with "custom.".
							// Order it by the ordering field.
							$query->select($db->quoteName(array('ad_id')));
							$query->from($db->quoteName('#__ad_data'));
							$query->where($db->quoteName('ad_id') . ' = '. $db->quote($cookieAd['ad_id']));

							// Reset the query using our newly populated query object.
							$db->setQuery($query);

							// Load the results as a list of stdClass objects (see later for more options on retrieving data).
							$results = $db->loadresult();

							if (empty($results))
							{
								unset($cooke_ads[$zone_id][$key]);
							}
						}
					}

					if ($socialads_config['enable_caching'] == 1)
					{
						setcookie($func.'_Ads1', '', -time());
						setcookie($func.'_Ads1', json_encode($cooke_ads), time() + $socialads_config['cache_time']);
					}

					if(!empty($func_data) )
					{
						if($func != 'Context' )
						{
							if(empty($cooke_ads[$zone_id]))
							{
								$ads=$adRetriever->$func_name($func_data,$params,$adRetriever);
							}
						}
						else
							$ads=$adRetriever->$func_name($func_data,$params,$adRetriever);
					}
				}
				else
				{
					if($socialads_config['enable_caching'] == 1 && isset($_COOKIE[$func.'_Ads1']))
					{
						$cooke_ads = json_decode($_COOKIE[$func.'_Ads1'],true);
					}
					else
					{
						$cooke_ads[$zone_id] = array();
					}

					// REMOVE AD FROM COOKIE ID AD NOT PRESENT
					if (isset($cooke_ads[$zone_id]) && !empty($cooke_ads[$zone_id]))
					{
						foreach ($cooke_ads[$zone_id] as $key => $cookieAd)
						{
							// Get a db connection.
							$db = JFactory::getDbo();

							// Create a new query object.
							$query = $db->getQuery(true);

							// Select all records from the user profile table where key begins with "custom.".
							// Order it by the ordering field.
							$query->select($db->quoteName(array('ad_id')));
							$query->from($db->quoteName('#__ad_data'));
							$query->where($db->quoteName('ad_id') . ' = '. $db->quote($cookieAd['ad_id']));

							// Reset the query using our newly populated query object.
							$db->setQuery($query);

							// Load the results as a list of stdClass objects (see later for more options on retrieving data).
							$results = $db->loadresult();

							if (empty($results))
							{
								unset($cooke_ads[$zone_id][$key]);
							}
						}
					}

					if ($socialads_config['enable_caching'] == 1)
					{
						setcookie($func.'_Ads1', '', -time());
						setcookie($func.'_Ads1', json_encode($cooke_ads), time() + $socialads_config['cache_time']);
					}

					if(empty($cooke_ads[$zone_id]))
					{
						$ads = $adRetriever->$func_name($params,$adRetriever);
					}
				}

				if($func != 'Context' )
				{
					if(empty($cooke_ads[$zone_id]))
					{
						if($adRetriever->_geodebug == '1')
						{
							echo '<br><br>ads from func for '.$func;
							print_r($ads);
						}

						$oldCookieAds = new stdclass();

						if ($socialads_config['enable_caching'] == 1 && isset($_COOKIE[$func.'_Ads1']))
						{
							$oldCookieAds = json_decode($_COOKIE[$func.'_Ads1']);
						}

						$oldCookieAds->$zone_id =  $ads;

						if ($socialads_config['enable_caching'] == 1)
						{
							setcookie($func.'_Ads1', json_encode($oldCookieAds), time()+3600);

							$_COOKIE[$func.'_Ads1'] =  json_encode($oldCookieAds);
						}
					}


					if ($socialads_config['enable_caching'] == 1)
					{
						$session_ads_all = json_decode($_COOKIE[$func.'_Ads1'],true);
					}
					else
					{
						$session_ads_all = json_decode((json_encode($oldCookieAds)));
					}

					if(!empty($session_ads_all))
					{
						$session_ads = json_decode((json_encode($session_ads_all)));
					}
					else
					{
						$session_ads = array();
					}

					if($adRetriever->_geodebug == '1')
					{
						echo '<br><br>ads from session for '.$func;
						print_r($session_ads);
					}
				}
				else{
					$session_ads = new stdclass;
					$session_ads->$zone_id  = $ads;
				}

					//print_r($session_ads); die;
					if(!empty($session_ads))
					{
						foreach ($session_ads as $zone_id => $value)
						{
							$adsArray = new stdclass();
							$adsArray->$zone_id = $value;

							$remain_num = $adRetriever->pushinSlot($value,$remain,$params,$adRetriever, $zone_id);

							if ($remain_num != -999)
							{
								$remain = $remain_num;
							}
						}
					}

			}
		}
///		print_r(self::$_resultads);die('ffff');
		return self::$_resultads;
	}

	//callback function for array_udiff
	function compare_ids($a, $b)
	{
		return ($a->ad_id - $b->ad_id);
	}

	function pushinSlot($ads,$no_of_ads='',$params,$adRetriever, $zone_id)
	{
		//$zone_id =  $adRetriever->getParam($params,'zone');

		if(empty(self::$_resultads[$zone_id]))
		{
			self::$_resultads[$zone_id] = array();
		}

		// push the ads in the ads slots according to the num of ads limit & if the slot is full return
		if(count(self::$_resultads[$zone_id]))
		{
			$ad_ids = array_udiff($ads, self::$_resultads[$zone_id], array('self','compare_ids'));

			if(!($ad_ids))	//if there are no ads to show
			{
				return -999;
			}
		}
		else
		{
			$ad_ids = $ads;
		}

		//randomise the ads at every step
		if($adRetriever->getParam($params,'no_rand') == 1)
		{
			shuffle($ad_ids);
		}

		$ad_ids = array_slice($ad_ids, 0, $no_of_ads);

		if($adRetriever->_geodebug == '1')
		{
			echo '<br><b> Pushed Ads: </b>';
			print_r($ad_ids);
		}

		foreach ($ad_ids as $ad1)
		{
			self::$_resultads[$zone_id][] = $ad1;  // $ad_ids[0];
		}

		if(count($ad_ids) < $no_of_ads)
		{
			return ($no_of_ads - count($ad_ids));
		}

		return 0;
	}

	function getContextData($params,$adRetriever)
	{
		$link_id='';
		$db = JFactory::getDBO();
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		if(!($socialads_config['context_target']) )
			return array();

		$input=JFactory::getApplication()->input;
		$query1=$query2=$querylink='';

		$context_target_param=$socialads_config['context_target_param'];
		if(!empty($context_target_param) and !empty($socialads_config['context_target_keywordsearch']))
		{
			$Context_searchData = $adRetriever->getContext_searchData();
			$searchkeywords=strtolower($Context_searchData['search']);
		}
		if(!empty($searchkeywords))
		$search_terms = explode(',',$searchkeywords);

		if(!empty($socialads_config['context_target_smartsearch']) and empty($searchkeywords))
		{
			$vv=JUri::getInstance()->toString();
			$vv=str_replace(JUri::base().'','',$vv);
			$vv=str_replace(JUri::base().'','',$vv);
			$id=$input->get('id',0,'INT');
			$option=$input->get('option','','STRING');
			$view=$input->get('view','','STRING');
			$Itemid=$input->get('Itemid',0,'INT');
			$query1='index.php?'.'option='.$option.'&view='.$view.'&id='.$id;
			$where1[]=" url LIKE '$query1%'";
			$where1[]=" route LIKE '$query1%'";

			$cond2=$cond21='';
			if(!empty($where1))
			{
				$cond1=implode(' OR ',$where1);
				$cond1=" WHERE ".$cond1;
			}
			$doc = JFactory::getDocument();
			if(JVERSION >= '2.5.0')
			{
				$querylink ="SELECT  distinct(link_id) FROM #__finder_links  ".$cond1;
				$db->setQuery($querylink);
				$link_id = $db->loadResult();

				if(!empty($link_id) )
				{
					$linkcondition=" link_id IN('".$link_id."')";
					$query ="SELECT term,weight FROM #__ad_contextual_terms WHERE".$linkcondition." AND term<>'' ORDER BY weight DESC  LIMIT 100 ";
					$db->setQuery($query);
					$terms = $db->loadObjectList();

					if(!empty($terms))
					{
						foreach ($terms as $term)
						{
							if(!empty($term->term))
							{
								$term->term=trim($term->term);
								if($term->term)
								$pagekeywords[]=$search_terms[] = trim(strtolower($term->term));
							}
							// TODO: Find an alternative for htmlspecialchars
						}
					}
				}
			}
		}
		if(empty($searchkeywords))
		{
			$metakeywords='';
			$doc = JFactory::getDocument();
			$metakeywords =$doc->getMetaData('keywords');
			if($metakeywords)
			{
				$metaarr=explode(',',$metakeywords);
				foreach($metaarr as $metadt)
				{
					$search_terms_meta[] =trim(strtolower($metadt));
					$search_terms[]=trim(strtolower($metadt));
				}
			}
		}

		if(empty($socialads_config['context_target_metasearch']) and empty($searchkeywords))
		{
			if(!empty($search_terms_meta))
			$search_terms=array_diff($search_terms,$search_terms_meta);
		}

		if($adRetriever->_contextdebug=='1')
		{
			echo '<br><br>Contextual Debug Info:: ';
			if(!empty($searchkeywords))
				echo "<br><br>searchkeywords==".$searchkeywords;
			else
			{
				echo "<br><br>link1====".$query1;
				echo "<br><br>link_ids in #__finder_links====".$link_id;

				if(!empty($metakeywords))
					echo "<br><br>metakeywords==".$metakeywords;
				else
					echo "<br><br>metakeywords not found";
				if(!empty($pagekeywords))
					echo "<br><br>pagekeywords==".implode(',',$pagekeywords);
				else
					echo "<br><br>pagekeywords not found";

				if(!empty($search_terms))
					echo "<br><br>finalkeywords==".implode(',',$search_terms);
				else
					echo "<br><br>finalkeywords not found";
			}
		}
		if(!empty($search_terms))
			return $search_terms;
		else
			return array();
	}

	function getContext_searchData()
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$context_target_param=$socialads_config['context_target_param'];
		$input=JFactory::getApplication()->input;
		if(!empty($context_target_param) and !empty($socialads_config['context_target_keywordsearch']))
		{
			$final_query=array();
			$context_target_param=trim($context_target_param);
			$comp_parmas_arrs=explode("\n",$context_target_param);
			$i=0;
			foreach($comp_parmas_arrs as $comp_parmas)
			{
				$comp_parmas_inarr=explode("|",$comp_parmas);
				$querystring=$comp_parmas_inarr[0];
				$querystring_params=explode("&",$querystring);

				foreach($querystring_params as $querystringf)
				{
					$querystring_values=explode("=",$querystringf);
					$final_query_key=$querystring_values[0];
					$final_query_value=$querystring_values[1];
					$final_query[$i][$final_query_key]=$final_query_value;
				}
				$final_query[$i]['search']=$comp_parmas_inarr[1];
				$i++;
				$searchword=$comp_parmas_inarr[1];
			}
		}

		$option = $input->get('option','','STRING');
		if(!empty($option))
			$matchdata['option']=$option;
		$view = $input->get('view','','STRING');
		if(!empty($view))
			$matchdata['view']=$view;
		$layout = $input->get('layout');
		if(!empty($layout))
			$matchdata['layout']=$layout;
		$controller = $input->get('controller','','STRING');
		if(!empty($controller))
			$matchdata['controller']=$controller;
		$task = $input->get('task','','STRING');
		if(!empty($task))
			$matchdata['task']=$task;
		$flag=0;
		$finaldata['search']='';
		$finaldata['flag']=0;

		foreach($final_query as $queries)
		{
			$search=trim($queries['search']);
			unset($queries['search']);
			$result = array_diff($matchdata, $queries);
			if(!$result)
			{
				$finaldata['search']=$input->get($search,'','STRING');
				$finaldata['flag']=1;
				break;
			}

			if($finaldata['flag']==1)
				break;
		}
		return $finaldata;
	}

	function getContextAds($data,$params,$adRetriever){
		$search_terms = $data;
		$db = JFactory::getDBO();
		$search_terms=array_unique($search_terms);
		$where = array();

		foreach ($search_terms as $fuz_value)
		{
			if(!empty($fuz_value))
			{
				$fuz_value=trim($fuz_value);
				$fuz_value=$db->escape($fuz_value);
				$search_values[] = "".htmlspecialchars($fuz_value)."";
			}
			// TODO: Find an alternative for htmlspecialchars
		}
		$function_name="contextual"; //for common query function userd as flag
		$camp_join = $this->join_camp();
		$common_where = $this->query_common($params,$function_name,$adRetriever);

		$common_where = implode(' AND ',$common_where);
		if(empty($search_values))
			return;
		$search_valuestr = implode(' ', $search_values);
		$search_valuestr = strtolower($search_valuestr);
		$where[] = "MATCH (keywords) AGAINST ( '".$search_valuestr."' IN BOOLEAN MODE ) ";
		$where = (count($where) ? ' WHERE '.implode("\n AND ", $where) : '');
		$debug = "";
		if($this->_contextdebug == '1')
			$debug = " g.*, ";
		$result_ads = array();

		$query ="SELECT  distinct(g.ad_id),MATCH (keywords) AGAINST ( '".$search_valuestr."' IN BOOLEAN MODE ) as relevance
		FROM #__ad_contextual_target as g , #__ad_data as a
		$camp_join
		$where
		AND g.ad_id = a.ad_id
		AND keywords<>''
		";
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$query .="
		AND $common_where";
		"
		HAVING relevance>.2
		ORDER BY relevance DESC
		";
		$this->_contextquery=$query;
		$db->setQuery($query);
		$result_ads = $db->loadObjectList();
		if($adRetriever->_contextdebug=='1')
		{
			echo "<br><br>";
			if(!empty($result_ads))
				print_r($result_ads);
		}
		return $result_ads;
	}

	function getSocialData($params,$adRetriever){
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$UserData = array();
//echo 'outt userr';
		if($this->_my->id) {
//echo 'in userr';
		//get the user data according to the targetted fields
			$UserData = $adRetriever->getUserData($socialads_config['integration'], $adRetriever);
		}
		return $UserData;
	}

	function getSocialAds1($params,$adRetriever){
		$social_ads = array();
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		if($this->_my->id){
			//$adRetriever = new adRetriever();
			$social_ads = $adRetriever->getAdids($params,$adRetriever);
		}
		return  $social_ads;
	}
	//retruns all the possible matches of the ads
	function getSocialAds($data,$params,$adRetriever){
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		require(JPATH_SITE.DS."components".DS."com_socialads".DS."defines.php");
		$db = JFactory::getDBO();
		$fuzzy_fields = array();
		$getUserData = '';
		$getUserData = $data;
		if(!empty($getUserData) ) {
			$socialadshelper = new socialadshelper();
			$paramlist = $socialadshelper->getTableColumns('ad_fields');
			if(empty($paramlist))
			{
				return array();
			}
			//sort 'the exact & fuzzy part
			foreach($getUserData as $values)
			{
				if (in_array($values->mapping_fieldname, $paramlist))
				{
					if($values->mapping_match==0)	// Gather all the fuzzy fields
					{
						/*$fuzzy_fields[] = $values->mapping_fieldname;
						$fuzzy_data[] = $values->value;*/
						if(strlen($values->value) > 4)
							$where[] = "( MATCH ({$values->mapping_fieldname}) AGAINST (".$db->quote($values->value)."  IN BOOLEAN MODE) OR b.{$values->mapping_fieldname} = '')";
						else
							$where[]	=	" ( b.{$values->mapping_fieldname} = '$values->value'  OR  b.{$values->mapping_fieldname} = '' ) ";
					}
					else
					{

						switch ($values->mapping_fieldtype) { // switch to add where conditions for field types
							case 'singleselect':
							case 'gender':
							case 'boolean':
							case 'multiselect':
								$where[] = "(b.{$values->mapping_fieldname} LIKE ".$db->Quote("%|{$values->value}|%")." OR b.{$values->mapping_fieldname} = '')";
							break;
							case 'textbox':
								$where[] = "(b.{$values->mapping_fieldname} LIKE ".$db->Quote("%|{$values->value}|%")." OR b.{$values->mapping_fieldname} = '')";
							break;
							case 'date':
								$where[] = "(b.{$values->mapping_fieldname} = " . $db->Quote($values->value)." OR b.{$values->mapping_fieldname} = '')";
							break;
							case 'daterange':
							case 'numericrange':
								$where[] = "(b.{$values->mapping_fieldname}_low <= {$db->Quote($values->value)} AND b.{$values->mapping_fieldname}_high >= 	{$db->Quote($values->value)})";
							break;
						}
					}
				}
			}

			$where[] = " b.adfield_ad_id = a.ad_id";

			/*if(count($fuzzy_fields)) //if there is any fuzzy targeted field
			{
				$field_names = implode(',', $fuzzy_fields);

				$valueswithqoutesinarray = array();
				foreach ($fuzzy_data as $fuz_value)
				{
					$fuz_value = addslashes($fuz_value);
					$fuzzy_values[] = "'".htmlspecialchars($fuz_value)."'"; // TODO: Find an alternative for htmlspecialchars
				}
				$fuzzy_values = implode(' ', $fuzzy_values);
				$query_fuz = "MATCH ($field_names) AGAINST ( \"$fuzzy_values\" IN BOOLEAN MODE )";
			}*/

			/*if ($query_fuz) {
				//$query_fuz .= ' AS relevance ';
				$extra ="HAVING relevance >.2 ORDER BY relevance ";
			} else {
				$query_fuz = " a.ad_id as relevance ";
				$extra = "ORDER BY a.ad_id ";
			}*/

			$extra = "ORDER BY a.ad_id ";
			if($limit)
				$extra .=" LIMIT $limit";

			$camp_join = $this->join_camp(); //camp_join if camp enabled in backend
			// Begin composing the query
			$query = "SELECT a.ad_id  ";
			/*if ($query_fuz) {
				$query .= ', ' . $query_fuz . "\n";
			}*/
			$query .= " FROM ".(($getUserData)?" #__ad_fields as b ,": "" )." #__ad_data as a $camp_join  \n";

			$function_name="adids";
			$common_where = $this->query_common($params,$function_name,$adRetriever); //common query
			$common_where = implode(' AND ',$common_where);
			$where[] = (!$this->_my->id)? " a.ad_guest = 1" : " a.ad_guest <> 1";

			//Start Added by Sheetal
			if($this->_my->id && ($getUserData)){

				//added by aniket --to call only those plugin who has the entry in ad_fields table
				//add this query in separate function so that it can aslo be used while creating ad

				JPluginHelper::importPlugin('socialadstargeting');
				$dispatcher = JDispatcher::getInstance();
				$results = $dispatcher->trigger( 'OnAfterGetAds',array($paramlist)); //Call the plugin and get the result
				foreach($results as $value)
				{
					foreach($value as $val)
					{
						$where[] = " $val";
					}
				}
			}

			//End Added by Sheetal

			$where = (count($where) ? ' WHERE '.implode("\n AND ", $where) : ''); //commpon where imploded...
			$where =$where." AND ".$common_where;
			$query .= "\n " . $where . "\n " . $extra;

			$db->setQuery($query);
			$result = $db->loadObjectList();
			//print_r($result); die('final');
			$ads=$result;
		}//chk for $getUserData exists
		if(empty($result))
 		{
			return array();
		}
		return $ads;
	}//function getAdids ends

	function getUserData($int_typ, $adRetriever)		// get the user data according to targeted fields
	{
	 $socialadshelper = new socialadshelper();

		if($int_typ==0){
			$cbchk = $socialadshelper->cbchk();
			if(!empty($cbchk))
			{
				$ud = $adRetriever->getCBData();
				return $ud;
			}//chk empty
		}
		elseif($int_typ==1){

			$jschk = $socialadshelper->jschk();
			if(!empty($jschk))
			{

				$ud = $adRetriever->getJSData();
				return $ud;
			}//chk empty
		}
		elseif($int_typ==3)// if set to Easy social
		{
			$eschk = $socialadshelper->eschk();
			if(!empty($eschk))
			{

				$ud = $adRetriever->getESData();
				return $ud;
			}//chk empty
		}
		elseif($int_typ==2){	// check if intregration is set to None...
			return;
		}
	}

	function getCBData()
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__ad_fields_mapping WHERE mapping_fieldtype <> 'targeting_plugin' ORDER BY mapping_id";
		$db->setQuery($query);
		$mapdata = $db->loadObjectlist();
		//dont go inside if mapdata is empty
		if(!empty($mapdata)){
			$i=0;
			foreach($mapdata as $map)
			{
				$col_nam[]=$map->mapping_fieldname;
			}
			$col_nam= implode(',',$col_nam);

			$query = "SELECT ".$col_nam."
						FROM #__comprofiler
						WHERE user_id =  ".$this->_my->id;
			$db->setQuery($query);
			$col_value=$db->loadObjectlist();

			$result = array();
			foreach($mapdata as $key=>$map)
			{
				// get the field values of the above mapping field names
				$str=$map->mapping_fieldname;
				if(!empty($col_value[0]->$str)){
					$result[$i] = new stdClass;
					$result[$i]->value = $col_value[0]->$str;
					$result[$i]->mapping_fieldtype = $map->mapping_fieldtype;
					$result[$i]->mapping_fieldname = $map->mapping_fieldname;
					$result[$i]->mapping_match = $map->mapping_match;
					$i++;
				}
			}
			return $result;
		}//mapdata empty condition
	}

	function getJSData()
	{
		$db = JFactory::getDBO();
		$query	= "SELECT cfv.value,afm.mapping_fieldtype,afm.mapping_fieldname,afm.mapping_match
					FROM #__community_fields_values as cfv
					JOIN #__ad_fields_mapping as afm ON afm.mapping_fieldid = cfv.field_id
					LEFT JOIN #__community_fields as cfc ON cfc.id = afm.mapping_fieldid
					WHERE cfv.user_id =  ".$this->_my->id."
					ORDER BY cfv.field_id";
		$db->setQuery($query);
		return	$values = $db->loadObjectList();
	}

	function getESData()
	{

		require_once( JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php' );

		$db = JFactory::getDBO();
		$uid= JFactory::getUser()->id;
		$query	= "SELECT sf.unique_key,afm.mapping_fieldtype,afm.mapping_fieldname,afm.mapping_match
					FROM #__ad_fields_mapping as afm
					LEFT JOIN #__social_fields as sf ON sf.id = afm.mapping_fieldid";


		$db->setQuery($query);
		$values = $db->loadObjectList();
		$result = array();
		$i=0;
		foreach($values as $val)
		{
			//remove this when replied by stackideas people.
				/*if($val->mapping_fieldname=='address')
				$val->value='flat no 6 saket app pune maharashtra';
				else
				{*/
					$ES_value = Foundry::user()->getFieldValue( $val->unique_key );
					if( !empty($ES_value->value) )
					{
						$result[$i] = new stdClass;
						//special condition for gender fields.
						if($val->unique_key == 'GENDER')
						{
							$result[$i]->value = $ES_value->value->title;
						}
						else
						{
							$result[$i]->value = 	$ES_value->value;
						}
						//if returned value is in array format then get that into a||b format
						if(is_array($result[$i]->value))
						{
							$result[$i]->value=implode('||',$val->value);
						}

						$result[$i]->mapping_fieldtype = $val->mapping_fieldtype;
						$result[$i]->mapping_fieldname = $val->mapping_fieldname;
						$result[$i]->mapping_match = $val->mapping_match;
						$i++;
					}
				//}
		}
		return	$result;
	}


	function getGeoData($params,$adRetriever){
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		if ($this->_fromemail== 1)
			return;
		if( !($socialads_config['geo_target']) )
			return array();
		//$adRetriever = new adRetriever();
		$ip = $adRetriever->getUserIP();
		//$ip = '219.64.91.134'; //local ip hard coded
		if($this->_geodebug == '1')
			echo '<br>IP:: '.$ip;
		if(!$ip){ 	//"89.242.88.250") ; // local ip 121.247.141.120 or 219.64.91.134
			return array();
		}
		return $adRetriever->getUserLoca($ip);
	}

	//get the ip of the client
	function getUserIP()
	{
		if ( isset( $_SERVER ) ) {
			if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"]) ) {
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
				if ( $ip != '' && strtolower($ip) != 'unknown' ) {
					$addresses = explode( ',', $ip);
					return $addresses[ count($addresses) - 1 ];
				}
			}
			if (isset($_SERVER["HTTP_CLIENT_IP"]) && $_SERVER["HTTP_CLIENT_IP"] != '' )
				return $_SERVER["HTTP_CLIENT_IP"];
			return $_SERVER["REMOTE_ADDR"];
		}
		if ( $ip = getenv( 'HTTP_X_FORWARDED_FOR' )) {
			if ( strtolower($ip) != 'unknown' ) {
				$addresses = explode( ',', $ip);
				return $addresses[ count($addresses) - 1 ];
			}
		}
		if ($ip = getenv('HTTP_CLIENT_IP')) {
			return $ip;
		}
		return getenv('REMOTE_ADDR');
	}

	//get the location of the client from IP
	function getUserLoca($ip){
		require_once(JPATH_SITE.DS."components".DS."com_socialads".DS."geo".DS."geoipcity.inc");
		require(JPATH_SITE.DS."components".DS."com_socialads".DS."geo".DS."geoipregionvars.php");
		$dbfile = JPATH_SITE.DS."components".DS."com_socialads".DS."geo".DS."GeoLiteCity.dat";
		$formatted_data = array();
		$formatted_data['country']=$formatted_data['region']=$formatted_data['city']='';

		if(!JFile::exists($dbfile))
			return $formatted_data;
		$gi = geoip_open($dbfile,GEOIP_STANDARD);
		$data = geoip_record_by_addr($gi, $ip) ; // local ip 121.247.141.120
		if($data || isset($data) ){
			if(isset($data->country_name))
				$formatted_data['country'] = $data->country_name;
			if(isset($data->region))
				$formatted_data['region'] =$GEOIP_REGION_NAME[$data->country_code][$data->region];
			if(isset($data->city))
				$formatted_data['city'] = $data->city;
		}
		if($this->_geodebug == '1')
		{
			echo '<br>GEO location:: ';
			echo 'formatted====';print_r($formatted_data);
		}
		geoip_close($gi);
		return $formatted_data;
	}

	function getGeoAds($data,$params,$adRetriever){
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		if ($this->_fromemail== 1)
			return;
		if( !($socialads_config['geo_target']) )
			return array();
		$userloca = $data;
		if(!$userloca){
			return array();
		}
		$where = array();
		foreach($userloca as $key=>$value){
			$where[] = "(g.$key LIKE \"%|{$value}|%\" OR g.{$key} = '')";
		}
		$where = (count($where) ? ' WHERE '.implode("\n AND ", $where) : '');
		$debug = "";
		if($adRetriever->_geodebug == '1')
			$debug = " , g.* ";
		$result_ads = array();
		$function_name = "geo";
		$camp_join = $this->join_camp();
		$common_where = $this->query_common($params,$function_name,$adRetriever);
		$common_where = implode(' AND ',$common_where);

		$db = JFactory::getDBO();
		$query ="SELECT  distinct(g.ad_id) $debug
		FROM #__ad_geo_target as g , #__ad_data as a
		$camp_join
		$where
		AND g.ad_id = a.ad_id
		AND $common_where
		 ";
		$db->setQuery($query);
		$result_ads = $db->loadObjectList();
		if($adRetriever->_geodebug == '1'){
			echo '<br>GEO Ads:: ';
			print_r($result_ads);
		}
		if($result_ads)
			return $result_ads;
		else
			return array();
	}

	//returns all the Affiliate ads
	function getAffiliateAds($params,$adRetriever)
	{
		$db = JFactory::getDBO();
		$function_name="affiliate";
		$common_where = $this->query_common($params,$function_name,$adRetriever);
		$common_where = implode(' AND ',$common_where);
		$query = "SELECT a.ad_id
					FROM #__ad_data as a
					WHERE a.ad_affiliate = 1
					AND $common_where
					ORDER by a.ad_created_date ";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}
	//returns all the alternate ads
	function getAltAds($params,$adRetriever)
	{
		$alt_ad =	$adRetriever->getParam($params,'alt_ad');
		if( !($this->_my->id) && $alt_ad == '0' ) {
			return array();
		}
		$db = JFactory::getDBO();
		$function_name="alt";
		$common_where = $this->query_common($params,$function_name,$adRetriever);
		$common_where = implode(' AND ',$common_where);
		$query = "SELECT a.ad_id
					FROM #__ad_data as a
					WHERE a.ad_alternative = 1
					AND  $common_where
					ORDER by a.ad_created_date ";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}

	//returns all the guest ads
	function getGuestAds($params,$adRetriever)
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$db = JFactory::getDBO();
		$camp_join = $this->join_camp();
		$function_name = "guest";
		$common_where = $this->query_common($params,$function_name,$adRetriever);
		$common_where = implode(' AND ',$common_where);
		//$adRetriever = new adRetriever();
		$query = "SELECT a.ad_id
					FROM #__ad_data as a
					$camp_join
					WHERE a.ad_guest = 1
					AND $common_where
					";
			if( $socialads_config['geo_target'])
				$query .= "	AND a.ad_id NOT IN (SELECT ad_id
						 	FROM #__ad_geo_target
							)";
					"ORDER by a.ad_created_date ";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}

	// function reduces the credits
	function reduceCredits($adid,$caltype,$ad_charge,$widget="")	//caltype= 0 imprs;caltype =1 clks;
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$user=JFactory::getUser();
		$userid=$user->id;
			/*load language file for plugin frontend*/
		$lang =  JFactory::getLanguage();
		$lang->load('com_socialads', JPATH_SITE);
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();

		$sql="SELECT ad_payment_type,ad_alternative,ad_affiliate,ad_noexpiry,ad_creator
				FROM #__ad_data
				WHERE ad_id='".$adid."'";
		$db->setQuery($sql);
		list($result,$alter,$affiliate,$unltd,$creator) = $db->loadRow();

    	$sql="SELECT count(*)
				FROM #__ad_stats
				WHERE ad_id='".$adid."' AND ip_address = '".$_SERVER["REMOTE_ADDR"]."' AND display_type = ".$caltype." AND time > NOW() - INTERVAL ";
		//include the time interval for clicks if cal is for the clicks ad...
		if($caltype==1)
			$sql.=$socialads_config['timeclicks']." SECOND";
		else
			$sql.=$socialads_config['timeimpressions']." SECOND";

		$db->setQuery($sql);
		$ipresult = $db->loadResult();
		$adRetriever = new adRetriever();
		if($caltype==0 && $result==1)
		{
			$ad_charge=0.00;
		}
		if($socialads_config['select_campaign']==1)
		{
			if($ipresult<1)
			{
				if($creator != $userid)
				{
					if( $result==0 && $alter==0 && $affiliate==0 && $unltd==0 && $caltype==0)						// reduce credits for impressions
					{
						$adRetriever->spentupdate($adid,$caltype,$ad_charge);
					}
					elseif( $result==1 && $alter==0 && $affiliate==0 && $unltd==0 && $caltype==1)	// reduce credits for clicks & it is called from the redirector file
					{
						$adRetriever->spentupdate($adid,$caltype,$ad_charge);
					}
					if($alter==1 || $unltd==1 || $affiliate==1)
					{
						$adRetriever->putStats($adid,$caltype,$ad_charge,$widget);
//						$adRetriever->incrementStats($adid,$caltype); //for Task #31607 increment ad stats in independent column against the ad
					}
					else
					{
						$adRetriever->putStats($adid,$caltype,$ad_charge,$widget);
//						$adRetriever->incrementStats($adid,$caltype);  //for Task #31607 increment ad stats in independent column against the ad
					}
				}
			}

				// mail for low balance
/*			$query = "SELECT camp_id,ad_creator FROM #__ad_data WHERE ad_id=$adid";
			$db->setQuery($query);
			$campinfo = $db->loadobjectlist();
			//print_r($campinfo);
			$ad_creator = $campinfo['0']->ad_creator;
			//print_r($campinfo['0']->ad_creator); die();
*/
			$query = "SELECT SUM(earn) FROM `#__ad_camp_transc` WHERE user_id=".$creator;
			$db->setQuery($query);
			$total_amt = $db->loadresult();

			$query = "SELECT balance FROM `#__ad_camp_transc` where time = (select MAX(time) from #__ad_camp_transc where user_id =".$creator.")";
			$db->setQuery($query);
			$remaining_amt = $db->loadresult();

			if($alter==0 && $affiliate==0 && $unltd==0 && ( ($caltype==0 && $result==0)||($caltype==1 && $result==1) ))
			{
				if($socialads_config['balance'] )
				{
					$low_val = $total_amt*($socialads_config['balance']/100 );
					if((ceil($low_val)) == $remaining_amt)
					{
						$adRetriever->mailLowBal($adid,$socialads_config['select_campaign']);
					}
					if($remaining_amt <= 0)
					{
//						foreach($campinfo as $key)
						{
							$query = "UPDATE #__ad_campaign SET camp_published = 0 WHERE user_id=".$creator; //as amount is zero camp should be unpublished
							$db->setQuery($query);
							$db->execute();
						}
						$adRetriever->mailExpir($adid,$socialads_config['select_campaign']); //send a ad expiry mail
					}
				}
			}
		}
		else{
			if($ipresult<1){
				$query="SELECT ad.ad_credits_balance, api.ad_credits_qty
						FROM #__ad_data as ad LEFT JOIN #__ad_payment_info as api ON ad.ad_id = api.ad_id
						WHERE ad.ad_id='".$adid."' AND api.status='C' ORDER BY api.mdate DESC LIMIT 1 ";
				$db->setQuery($query);
				$credits_data= $db->loadObjectList();							//get the balance credits and credits brought
				if($creator != $userid)
				{
					if( $result==0 && $alter==0 && $affiliate==0 && $unltd==0 && $caltype==0)						// reduce credits for impressions
					{
						$adRetriever->subCredits($adid);
					}
					elseif( $result==1 && $alter==0 && $affiliate==0 && $unltd==0 && $caltype==1)	// reduce credits for clicks & it is called from the redirector file
					{
						$adRetriever->subCredits($adid);
					}
					if($alter==0 && $affiliate==0 && $unltd==0 && ( ($caltype==0 && $result==0)||($caltype==1 && $result==1) )){
						if(($socialads_config['balance']) && ($credits_data[0]->ad_credits_qty) ){
							$low_val = $credits_data[0]->ad_credits_qty*($socialads_config['balance']/100 );
							if((ceil($low_val)) == ($credits_data[0]->ad_credits_balance-1))
							{
								$adRetriever->mailLowBal($adid,$socialads_config['select_campaign']);							//send a Low Balance mail
							}
							if(($credits_data[0]->ad_credits_balance-1) == 0)
							{
								$adRetriever->mailExpir($adid,$socialads_config['select_campaign']);							//send a ad expiry mail
							}
						}
					}
					$adRetriever->putStats($adid,$caltype,$ad_charge,$widget);							//update the stats table for the ad
//					$adRetriever->incrementStats($adid,$caltype);  //for Task #31607 increment ad stats in independent column against the ad
				}
			}//closing of the main ipresult
		}	//else
	}

	function spentupdate($adid,$caltype,$ad_charge)
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$db=JFactory::getDBO();
		$query = "SELECT a.camp_id,a.ad_zone,s.per_imp,a.ad_creator, s.per_click FROM `#__ad_data` as a INNER JOIN #__ad_zone as s ON s.id = a.ad_zone WHERE ad_id = $adid";
		$db->setQuery($query);
		$camp_zone = $db->loadobjectlist();
		foreach($camp_zone as $key)
		{
			$date1 = microtime(true);
			$key->c_date=$date1;
			$date2 = date('Y-m-d');
			$key->only_date=$date2;
			$query = "SELECT id FROM #__ad_camp_transc WHERE DATE(FROM_UNIXTIME(time)) = '".$key->only_date."' AND type_id =".$key->camp_id." AND type = 'C'";
			$db->setQuery($query);
			$check = $db->loadresult();

			$query = "SELECT balance FROM #__ad_camp_transc WHERE time = (SELECT MAX(time)  FROM #__ad_camp_transc WHERE user_id=".$key->ad_creator.")";
			$db->setQuery($query);
			$bal = $db->loadresult();
			if($check)
			{
				$query = "UPDATE #__ad_camp_transc SET time ='".$key->c_date."', spent = spent +". $ad_charge. ",balance = ".$bal." - ".$ad_charge." where id=".$check;
				$db->setQuery($query);
				$db->execute();
			}
			else
			{
				$query = "INSERT INTO #__ad_camp_transc VALUES ('','".$key->c_date."',".$key->ad_creator.",". $ad_charge.",'',".$bal." - ".$ad_charge.", 'C' ,".$key->camp_id.",'DAILY_CLICK_IMP')";
				$db->setQuery($query);
				$db->execute();
			}
		}
		return;
	}

	function subCredits($adid)					//reduce credits
	{
		$db=JFactory::getDBO();
		$sql="UPDATE #__ad_data SET ad_credits_balance = ad_credits_balance-1 WHERE ad_id='".$adid."' AND ad_credits_balance>0";
		$db->setQuery($sql);
		$db->execute();
		return;
	}

	function putStats($adid,$type,$ad_charge,$widget="")					//update the stats table for the ad
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$insertstat = new stdClass;
		$insertstat->id = '';
		$insertstat->ad_id = $adid;
		$insertstat->user_id = $user->id;
		$insertstat->display_type = $type;
		$insertstat->ip_address = $_SERVER["REMOTE_ADDR"];
		$insertstat->spent = $ad_charge;
		if(!empty($_SERVER['HTTP_REFERER'])){
			$parse = parse_url($_SERVER['HTTP_REFERER']);
			if($parse['host'] == $_SERVER['HTTP_HOST'] && $type==1){
				$insertstat->referer = $widget;

			}else{
				if($widget!="")
					$insertstat->referer = $parse['host']."|".$widget;
				else
					$insertstat->referer = $parse['host'];
			}
		}
		if(!$db->insertObject( '#__ad_stats', $insertstat, 'id' ))
		{
			echo $db->stderr();
				return false;
		}
	}

	/*increment stats in the ad_data table for the ad
	 * adid = id of the Ad
	 * type= 0 imprs;type =1 clks;
	 */
	function incrementStats($adid,$type,$qty=1)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Fields to update.
		if($type === 1){
			$fields = array(
				$db->quoteName('ad_clicks') . ' = ' .$db->quoteName('ad_clicks').' + '.$qty
			);
		}
		else{
			$fields = array(
				$db->quoteName('ad_impressions') . ' = ' .$db->quoteName('ad_impressions').' + '.$qty
			);
		}

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('ad_id') . ' = '.(int)$adid
		);

		$query->update($db->quoteName('#__ad_data'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
		return;
	}

	function mailLowBal($adid,$mode)				//send a Low Balance mail
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		if($mode==0){
			$subject = JText::_('SUB_LOWBAL');
			$body	= JText::_('BALANCERL');
		}
		else{
			$subject	= JText::_('COM_SOCIALADS_LOW_WALBAL_SUBJ');
			$body	= JText::_('COM_SOCIALADS_LOW_WALBAL_BODY');
		}


		$db->setQuery("SELECT a.ad_creator, a.ad_title, a.ad_url2, u.name, u.email
				FROM #__ad_data AS a, #__users AS u
				WHERE a.ad_id=".$adid." AND a.ad_creator=u.id");
		$result	= $db->loadObject();

		$body	= str_replace('[SEND_TO_NAME]', $result->name, $body);
		if($mode==0){
			$ad_title=($result->ad_title!= '') ? JText::_("PERIDIC_STATS_ADTIT").' <b>"'.$result->ad_title.'"</b>' : JText::_("PERIDIC_STATS_ADID").' : <b>'.$adid.'</b>';
			$body	= str_replace('[ADTITLE]', $ad_title, $body);
		}
		$sitename=$mainframe->getCfg('sitename');
		$body	= str_replace('[SITENAME]',$sitename, $body);
		$body	= str_replace('[SITE]', JUri::base(), $body);

		$from = $mainframe->getCfg('mailfrom');
		$fromname = $mainframe->getCfg('fromname');
		$recipient[] = $result->email;
		$body = nl2br($body);

		$mode = 1;
		$cc = null;
		$bcc = null;
		$bcc = null;
		$attachment = null;
		$replyto = null;
		$replytoname = null;

		JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
	}

	function mailExpir($adid,$mode)				//send a ad expiry mail
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$socialadshelper=new socialadshelper();
		if($mode==0){
		$itemid = $socialadshelper->getSocialadsItemid('buildad');
		}
		$db = JFactory::getDBO();
		$sql="UPDATE #__ad_data
				SET ad_published = 0
				WHERE ad_id='".$adid."'
				AND ad_alternative <> 1
				AND ad_noexpiry <> 1";
		$db->setQuery($sql);
		$db->execute();

		if($mode==0){
			$body	= JText::_('EXPIRED');
			$subject =  JText::_('SUB_EXPR');
		}
		else{
			$subject	= JText::_('COM_SOCIALADS_WALEXPRI_SUBJ');
			$body	= JText::_('COM_SOCIALADS_WALEXPRI_BODY');
		}

		$query = "SELECT a.ad_creator, a.ad_title, a.ad_url2, u.name, u.email
				FROM #__ad_data AS a, #__users AS u
				WHERE a.ad_id=".$adid."
				AND a.ad_creator=u.id";
		$db->setQuery($query);
		$result	= $db->loadObject();

		$body	= str_replace('[SEND_TO_NAME]', $result->name, $body);
		if($mode==0){
		$ad_title=($result->ad_title!= '') ? JText::_("PERIDIC_STATS_ADTIT").' <b>"'.$result->ad_title.'"</b>' : JText::_("PERIDIC_STATS_ADID").' : <b>'.$adid.'</b>';
		$body	= str_replace('[ADTITLE]', $ad_title, $body);
		}
		$sitename=$mainframe->getCfg('sitename');
		$body	= str_replace('[SITENAME]',$sitename, $body);
		$body	= str_replace('[SITE]', JUri::base(), $body);
		if($mode==0){
		$edit_ad_link  = JRoute::_(JUri::base()."index.php?option=com_socialads&view=buildad&adid=".$adid."&Itemid=".$itemid);
		$body	= str_replace('[EDITLINK]',$edit_ad_link, $body);
		}
		$from = $mainframe->getCfg('mailfrom');
		$fromname = $mainframe->getCfg('fromname');

		$recipient[] = $result->email;

		$body = nl2br($body);
		$mode = 1;
		$cc = null;
		$bcc = null;
		$bcc = null;
		$attachment = null;
		$replyto = null;
		$replytoname = null;

		JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
	}

	function getAdDetails($ad)				//get details of the ad
	{
		$addata = array();

		if (!empty($ad) && isset($ad->ad_id) && !empty($ad->ad_id))
		{
			$db = JFactory::getDBO();
			$query = "SELECT ad.ad_id,ad.ad_title,ad.ad_image,ad.ad_body,ad.layout,az.id as zone_name,az.ad_type
						FROM #__ad_data as ad LEFT JOIN #__ad_zone as az ON ad.ad_zone = az.id
						WHERE ad_id =".$ad->ad_id;
			if($this->_fromemail==1){	/*jugad for not showing flash/video Ads in mails @TODO remove when add HTML5 support*/
				$query .= " AND (ad.ad_image NOT LIKE '%.flv' AND ad.ad_image NOT LIKE '%.swf' AND ad.ad_image NOT LIKE '%.mp4' )";
			}
			$db->setQuery($query);
			$addata = $db->loadObject();
		}

		return $addata;
	}

	function getAdHTML($addata,$adseen = 0,$adrotation = 0,$widget='')
	{
		jimport( 'joomla.application.module.helper');
		require(JPATH_SITE.DS."components".DS."com_socialads".DS."defines.php");
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$document = JFactory::getDocument();
		$mod_sfx = "";

//		$adRetriever = new adRetriever();
//		$addata = $adRetriever->getAdDetails($ad_id);
		$module = JModuleHelper::getModule( 'socialads');
		if ( $mainframe->isSite() && $adseen == 1 && $addata!=0) //if (($mainframe->isSite()) && !empty($addata))
		{
			if(JVERSION >= '1.6.0'){
				$moduleParams = json_decode($module->params);
				$mod_sfx = $moduleParams->moduleclass_sfx;
			}
			else{
				$moduleParams = new JParameter($module->params);
				$mod_sfx = $moduleParams->get('moduleclass_sfx');
			}
		}
		$zone_data = '';
		if($adseen == 1){
			if($addata==0)		//this is when the ad is not saved ie on the Ad Review page
			{
				$buildadsession = JFactory::getSession();
				$adses = $buildadsession->get('ad_data');
				$addata = new stdClass;
				$pluginlist = $buildadsession->get('addatapluginlist');
				///Extra code for zone pricing
				$addata->layout = $buildadsession->get('layout');
				$addata->ad_image =  str_replace(JUri::base(),'',$buildadsession->get('upimg'));

				if (!($buildadsession->get('adzone')))
					$adzone = 1;
				else
					$adzone = $buildadsession->get('adzone');
				$addata->ad_title = $adses[2]['ad_title'];
				$addata->ad_body = $adses[3]['ad_body'];
				$query="SELECT zone_type,max_title,max_des,img_width,img_height FROM #__ad_zone WHERE id =".$adzone;
				$db->setQuery($query);
				$zone_data = $db->loadObjectList();

			}
			else{					//ad preview for the lightbox, showad , adsummary view
				$query = "SELECT * FROM #__ad_data WHERE ad_id =".$addata;
				$db->setQuery($query);
				$addata = $db->loadObject();
			}
			$addata->link = '#';
		}
		if($zone_data == '' )
		{
			$query = "SELECT az.id AS zone_id,az.zone_type,az.max_title,az.max_des,az.img_width,az.img_height FROM #__ad_data as ad LEFT JOIN #__ad_zone as az ON ad.ad_zone=az.id WHERE ad_id =".$addata->ad_id;
			$db->setQuery($query);
			$zone_data = $db->loadObjectList();
			$adzone=$zone_data[0]->zone_id;//added in 2.7.5 stable
		}
		$tit = $addata->ad_title;
		$addata->ad_title = mb_substr($tit, 0 , $zone_data[0]->max_title,'UTF-8' );
		if($addata->layout !='layout6'  ){
			$bod = $addata->ad_body;
			$addata->ad_body = mb_substr($bod, 0 , $zone_data[0]->max_des ,'UTF-8');
		}
		$addata->ignore = "";
		$upload_area = '';

		$left_class=' row-fluid ';

		if($zone_data[0]->zone_type == 1 && $adseen == 0)
			$left_class = " pull-left ";		//For the orientation of the Ad ie Horizontal or Vertical

		//$style	=	'style="'.$float_style.'"';

		$saad_entry_number	=	0;
		if($adrotation	== 1)
		{
			$saad_entry_number	=	self::$ad_entry_number;
			++self::$ad_entry_number;
		}
		@$html= '<div class = " ad_prev_main'.$mod_sfx. $left_class . ' " preview_for= "'.$addata->ad_id.'" ad_entry_number="'.$saad_entry_number.'" >';
		if ($adseen == 0)
		{
			if($this->_my->id == 1){
				//$widget='';
				if(!empty($_SERVER['HTTP_REFERER'])){
				$parse = parse_url($_SERVER['HTTP_REFERER']);
				if($widget!="")
					$widget = '&amp;widget='.$parse['host'].'|'.$widget;
				}
			}else{
				$widget = '&amp;widget='.$widget;
			}
			$addata->link = JUri::root().substr(JRoute::_("index.php?option=com_socialads&amp;task=adredirector&amp;adid=".$addata->ad_id."&amp;caltype=1".$widget),strlen(JUri::base(true))+1);
			if($socialads_config['ignore'] !=0 && $this->_my->id != 0 && $this->_fromemail == 0)		//show ignore button
			{
				if($this->_my->id != 1)
					$addata->ignore = "ignore_ads(this,".$addata->ad_id.",".$socialads_config['feedback'].");" ;
				//$html .= '<img class="ad_ignore_button" src="'.JUri::Root().'components/com_socialads/images/fbcross.gif" onClick="ignore_ads(this,'.$addata->ad_id.','.$socialads_config["feedback"].');" />';
			}
		}

		$plugin = 'plug_'.$addata->layout;
		$document->addStyleSheet(JUri::root().'components/com_socialads/css/helper.css');
		$document->addScript(JUri::root().'components/com_socialads/js/helper.js');
		$adRetriever = new adRetriever();
		//START changed by manoj 2.7.5b2
		//no passing zone id all time changed in 2.7.5 stable
		if(!$adseen){
			$adHtmlTyped= $adRetriever->getAdHTMLByMedia($upload_area,$addata->ad_image,$addata->ad_body,$addata->link,$addata->layout,$track=1,$adzone,$addata->ad_id);
		}
		else{
			if(!$addata){
				$adHtmlTyped= $adRetriever->getAdHTMLByMedia($upload_area,$addata->ad_image,$addata->ad_body,$addata->link,$addata->layout,$track=0,$adzone);
			}else{
				$adHtmlTyped= $adRetriever->getAdHTMLByMedia($upload_area,$addata->ad_image,$addata->ad_body,$addata->link,$addata->layout,$track=0,$adzone);
			}
		}
		//END changed by manoj 2.7.5b2

		if(JVERSION >= '1.6.0')
			$layout = JPATH_SITE.DS.'plugins'.DS.'socialadslayout'.DS.$plugin.DS.$plugin.DS.'layout.php';
		else
			$layout = JPATH_SITE.DS.'plugins'.DS.'socialadslayout'.DS.$plugin.DS.'layout.php';

		if(JFile::exists($layout))
		{
			if(JVERSION >= '1.6.0')
			$document->addStyleSheet(JUri::root().'plugins/socialadslayout/'.$plugin.'/'.$plugin.'/layout.css','text/css','',array("id"=>$addata->layout.'css'));
			else
			$document->addStyleSheet(JUri::root().'plugins/socialadslayout/'.$plugin.'/layout.css','text/css','',array("id"=>$addata->layout.'css'));

			ob_start();
				include($layout);
				$html .= ob_get_contents();
			ob_end_clean();
		}
		else{
			/*Ad title starts here...*/
			$html .= '<!--div for preview ad-title-->
					<div class="ad_prev_first">';
			if($adseen == 0){
				$html.='<a class="ad_prev_anchor" href="'.$addata->link.'" target="_blank">'.$addata->ad_title .'</a>';
			}
			else{
				$html.= $addata->ad_title;
			}
			$html.= '</div>';
			/*Ad title ends here*/

			/*Ad image starts here...*/
			if($addata->ad_image != ''){							//check it image exists
				$html.='<!--div for preview ad-image-->
						<div class="ad_prev_second">';
				if($adseen == 0){
					$html.='<a href="'.$addata->link.' " target="_blank">';
				}
				$html.= '<img class="ad_prev_img"  src="'.JUri::Root().$addata->ad_image.'" border="0" />';
				if($adseen == 0){
					$html.='</a>';
				}
				$html.= '</div>';
			}
			/*Ad image ends here*/

			/*Ad description starts here...*/
			$html .= '<!--div for preview ad-descrip-->
					<div class="ad_prev_third">'.$addata->ad_body.'</div>';
			/*Ad description ends here*/
		}

		$html .= '</div>';
		return $html;
	}

	function getEstimatedReach($target_field,$plg_targetfiels)
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$adRetriever = new adRetriever();
		if($socialads_config['integration']==1)
			$reach=$adRetriever->getEstimatedReach_JS($target_field,$plg_targetfiels);
		if($socialads_config['integration']==0)
			$reach=$adRetriever->getEstimatedReach_CB($target_field,$plg_targetfiels);
		return $reach;
	}

	function getEstimatedReach_JS($target_field,$plg_targetfiels)
	{
		$db = JFactory::getDBO();
		$exact_field=array();
		$query_fuz = '';
		$fuzzy_fields='';
		$fuzzy_data=array();
		$fuzzy_values=array();
		$fuz_value='';
		$mapping_fieldids='';
		$exact_values=array();
		$condition='';
		$est_reach='';
		foreach($target_field as $key => $currentvalue)
		{
			$original_key=$key;
			$pos=-1;
			$range_flag= 0;
			if(strpos($key, "_low")>0)
			{
				$range_flag=1;
				$pos			= strpos($key, "_low");
				$key			=	substr($original_key,0,$pos);
			}
			if(strpos($key, "_high")>0)
			{
				$range_flag=2;
				$pos			= strpos($key, "_high");
				$key			=	substr($original_key,0,$pos);
			}

			$query_field = "SELECT 	mapping_match,mapping_fieldtype,mapping_fieldid,mapping_fieldname FROM #__ad_fields_mapping
						WHERE mapping_fieldname='".$key."'";
			$db->setQuery($query_field);
			$mapping_values = $db->loadObject();
			$mapping_fieldids[]=$mapping_values->mapping_fieldid;

			if($mapping_values->mapping_match==0)
			{
				$fuzzy_fields[] = $original_key;
				$fuzzy_data[] = $currentvalue;
			}
			else
			{
				// switch to add where conditions for field types
				switch ($mapping_values->mapping_fieldtype) {
					case 'singleselect':
						$exact_field[] = "+".$currentvalue."";
					break;
					case 'multiselect':
							$currentvalue_str_multi="'".$currentvalue."'";
							$currentvalue_arr_multi=explode("','",$currentvalue_str_multi);
							foreach($currentvalue_arr_multi as $currentvalue_arr_key => $currentvalue_arr_val)
							{
								$currentvalue_arr_val=str_replace("'","",$currentvalue_arr_val);
								$exact_field[] = $currentvalue_arr_val;
							}
					break;
					case 'textbox':
						$exact_field[] ="".$currentvalue."";
					break;
					case 'date':
						$where[] = "(value = " . $db->Quote($currentvalue).")";
					break;
					case 'daterange':
					case 'numericrange':
					{
						if($range_flag==1)
						$where[] = "(value >= {$db->Quote($currentvalue)})";
						if($range_flag==2)
						$where[] = "(value<= {$db->Quote($currentvalue)})";

					}
					break;
				}
			}
		}

		//if there is any fuzzy targeted field
		if(count($fuzzy_fields)!=0 and count($exact_field)==0)
		{
			foreach ($fuzzy_data as $fuz_value)
			{
				$fuzzy_values[] = $fuz_value;
			}
			$fuzzy_values = implode(" ", $fuzzy_values);
			if($fuzzy_values)
			$where[] = " MATCH (value) AGAINST ( '".$fuzzy_values."' IN BOOLEAN MODE ) ";
		}
		else if(count($fuzzy_fields)==0 and count($exact_field)!=0)
		{
			foreach ($exact_field as $exact_value)
			{
				$exact_values[] =$exact_value;
			}
			$exact_values = implode(" ", $exact_values);
			if($exact_values)
				$where[] = " MATCH (value) AGAINST ( '".$exact_values."' IN BOOLEAN MODE ) ";
		}
		else if(count($fuzzy_fields)!=0 and count($exact_field)!=0)
		{
			foreach ($fuzzy_data as $fuz_value)
			{
				$fuzzy_values[] = $fuz_value;
			}
			$fuzzy_values = implode(" ", $fuzzy_values);

			///Exact Fields
			foreach ($exact_field as $exact_value)
			{
				$exact_values[] = $exact_value;
			}
			$exact_values = implode(" ", $exact_values);
			if($exact_values or $fuzzy_values)
				$where[] = " MATCH (value) AGAINST ( '".$exact_values." ".$fuzzy_values."' IN BOOLEAN MODE ) ";
		}
		$plgugindata=0;
		$plugindata_mapadata=0;
			//print_r($plg_targetfiels);die("IN plugin");//die;
		JPluginHelper::importPlugin('socialadstargeting');
		$dispatcher = JDispatcher::getInstance();
		$plg_results = $dispatcher->trigger( 'OnAfterGetEstimate',array($plg_targetfiels));//Call the plugin and get the result
		$userlist_str='';
		$userlist_arr=array();
		foreach($plg_results as $plgvalue)
		{
			if($plgvalue)
			{
				$userlist_arr[]=implode("','",$plgvalue);
				$plgugindata=1;
			}
		}
		$userlist_str=implode("','",$userlist_arr);

		if(!$exact_values and !$fuzzy_values and !$plgugindata)
		{
			$where=array();			//This is made 	if No selection Of AQ=ny Fields
			$query = "SELECT  COUNT(distinct(`userid`)) as reach FROM #__community_users";
		}
		else if((!$exact_values and !$fuzzy_values) and $plgugindata)
		{
			$query = "SELECT  COUNT(distinct(`userid`)) as reach FROM #__community_users";
			if($userlist_str)
			$where[]=" userid IN('".$userlist_str."')";
		}
		else
		{
			$query = "SELECT  count(distinct(`user_id`)) as raech";
			$query .= " FROM #__community_fields_values "."\n";
			if($mapping_fieldids)
			{
				$mapping_fieldidlist= implode(",", $mapping_fieldids);
				if($where)
					$condition = "AND (field_id IN($mapping_fieldidlist))";
				else
					$condition = "WHERE (field_id IN($mapping_fieldidlist))";
			}
			$plugindata_mapadata=1;
		}

		$where	= (count($where) ? ' WHERE '.implode("\n AND ", $where) : '');
		$query .= "\n " . $where .$condition. "\n";

		if($plugindata_mapadata)
		{
			$final_userlist=array();
			$query=str_replace("count(distinct(`user_id`)) as raech","distinct(`user_id`) as user_id",$query);
			$db->setQuery($query);
			$users_list_mapdata= $db->loadColumn();
			$query = "SELECT  distinct(`userid`) as reach FROM #__community_users
						WHERE  userid IN('".$userlist_str."')";

			$db->setQuery($query);
			$users_list_plgdata=$db->loadColumn();

			if($users_list_plgdata and $users_list_mapdata)
			{
				$final_userlist=(array_intersect($users_list_mapdata,$users_list_plgdata));
				$est_reach=count($final_userlist);
			}
			else if($users_list_plgdata)
				$est_reach=count($users_list_plgdata);
			else if($users_list_mapdata)
				$est_reach=count($users_list_mapdata);
		}
		else
		{
			$db->setQuery($query);
			$est_reach = $db->loadResult();
		}

		if($est_reach)
			return $est_reach;
		else
			return 0;
	}

	function getEstimatedReach_CB($target_field,$plg_targetfiels)
	{
		$db = JFactory::getDBO();
		$query_cb 	= "SHOW COLUMNS FROM #__comprofiler ";
		$db->setQuery($query_cb);
		$cb_fields 	= $db->loadObjectlist();
		foreach($cb_fields as $key => $currentvalue_cb)
		{
			$cbfields_arr[]	= $currentvalue_cb->Field;
		}

		foreach($target_field as $key => $currentvalue)
		{
			$key = str_replace('field_', '' , $key);
			$original_key=$key;
			if($original_key=='mobile')
			$original_key='phone';
			if(in_array($original_key,$cbfields_arr))
			{
				$pos=-1;
				$range_flag= 0;
				if(strpos($key, "_low")>0)
				{
					$range_flag=1;
					$pos			= strpos($key, "_low");
					$key			=	substr($original_key,0,$pos);
				}
				if(strpos($key, "_high")>0)
				{
					$range_flag=2;
					$pos			= strpos($key, "_high");
					$key			=	substr($original_key,0,$pos);
				}

				$query_field = "SELECT 	mapping_match,mapping_fieldtype,mapping_fieldid,mapping_fieldname FROM #__ad_fields_mapping
							WHERE mapping_fieldname='".$key."'";
				$db->setQuery($query_field);
				$mapping_values = $db->loadObject();
				$mapping_fieldids[]=$mapping_values->mapping_fieldid;

				if($mapping_values->mapping_match==0)
				{
					//$where1[]=" $original_key LIKE '%".$currentvalue."%'";
					$fuzzy_fields[] = $key;
					$fuzzy_data[] = $currentvalue;
				}
				else
				{
					// switch to add where conditions for field types
					switch ($mapping_values->mapping_fieldtype)
					{
						case 'singleselect':
						case 'multiselect':
							if($mapping_values->mapping_fieldtype=='multiselect')
							{
								$where[]=" $original_key IN('$currentvalue')";
							}
							else
								$where[]=" $original_key LIKE '".$currentvalue."'";
						break;

						case 'textbox':
							$where[]=" $original_key LIKE '".$currentvalue."'";
						break;

						case 'date':
							$where[] = "($original_key = " . $db->Quote($currentvalue).")";
						break;

						case 'daterange':
						case 'numericrange':
							if($range_flag==1)
								$where[] = "($original_key >= {$db->Quote($currentvalue)})";
							if($range_flag==2)
								$where[] = "($original_key<= {$db->Quote($currentvalue)})";
						break;
					}//switch
				}//else
			}//if
		}//foreach

		//if there is any fuzzy targeted field
		if(count($fuzzy_fields))
		{
			$field_names = implode(',', $fuzzy_fields);
			$valueswithqoutesinarray = array();

			foreach ($fuzzy_data as $fuz_value)
			{
				$fuzzy_values[] = "'".htmlspecialchars($fuz_value)."'"; // TODO: Find an alternative for htmlspecialchars
			}
			$fuzzy_values = implode(' ', $fuzzy_values);
			$where[] = "MATCH ($field_names) AGAINST ( $fuzzy_values IN BOOLEAN MODE ) ";
		}

		$query = "SELECT distinct(count(`user_id`)) as reach";
		$query .= " FROM #__comprofiler "."\n";
		$condition='';
		$where = (count($where) ? ' WHERE '.implode("\n AND ", $where) : '');
		$query .= "\n " . $where;
		$db->setQuery($query);
		$est_reach = $db->loadObject();
		if($est_reach->reach)
			return $est_reach->reach;
		else
			return 0;
	}

	//added by manoj in 2.7.5b2
	function getAdHTMLByMedia($upload_area,$ad_image,$ad_body,$ad_link,$ad_layout,$track=0,$adzone,$ad_id='')
	{
		$adHtmlTyped='';
		if($ad_layout=='layout2' || $ad_layout=='layout4' || $ad_layout=='layout6' ) //layout2 or layout4 are for text ads & layout6 is for affiliate ads
		{
			$ad_type='text';
			$adHtmlTyped.=$ad_body;
		}
		else
		{
			require_once(JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'helpers'.DS.'media.php');
			require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
			$media=new sa_mediaHelper();
			$fpath=JUri::root().$ad_image;
			$fextension=JFile::getExt($fpath);
			$ad_type=$media->get_ad_type($fextension);
			$socialadshelper = new socialadshelper();
			/*
			//@TODO use image/zone dimensions here?
			$media_d=$media->check_media_resizing_needed($zone_d,$fpath);
			$opti_d=$media->get_new_dimensions($zone_d->img_width, $zone_d->img_height, 'auto');
			*/
			switch($ad_type)
			{
				case "image":
					//@TODO use resized image dimensions here
					$adHtmlTyped='<img class="'.$ad_layout.'_ad_prev_img" alt="" src="'.JUri::root().$ad_image.'" style="border:0px;" />';
				break;

				case "flash":
					if($track)//if disaplying ad in module
					{
						$zone_d=$media->get_adzone_media_dimensions($adzone);
						//include flowplayer javascript
						$socialadshelper->loadScriptOnce(JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.9.min.js');
						$adHtmlTyped='';

						//@TODO use image?zone dimensions here?
						//$ht_wd='width:'.$media_d['width_img'].'px;height:'.$media_d['height_img'].'px';
						//$ht_wd='width:'.$opti_d['new_calculated_width'].'px;height:'.$opti_d['new_calculated_height'].'px';

						$ht_wd='width:'.$zone_d->img_width.'px;height:'.$zone_d->img_height.'px';


						//create uniquq tag for each ad for video ads
						$adHtmlTyped.='<div
							href="'.JUri::root().$ad_image.'"
							style="display:block;'.$ht_wd.'"
							id="vid_player_'.$ad_id.'">
							</div>';

						//flow player js, configured as required
						$adHtmlTyped.='
						<script>
							flowplayer("vid_player_'.$ad_id.'",
							{
								src:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.10.swf",
								wmode:"opaque"
							},
							{
								/*
								canvas: {
									backgroundColor:"#000000",
									width:'.$zone_d->img_width.',
									height:'.$zone_d->img_height.'
								},
								*/

								clip : {
									scaling: "scale",
									autoPlay: true
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
									controls: null,

									content: {
										url:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer.content-3.2.8.swf",
										width:'.$zone_d->img_width.',
										height:'.$zone_d->img_height.',
										backgroundColor: "#112233",
										opacity: 0.0,
										onClick: function() {
										window.open("'.$ad_link.'","_blank");/*opens in new tab*/
										}
									}
								}
							});
						</script>';
					}
					else//backend ad preview / build ad change layout /manage ads all previews / edit ad
					{
						//FLOWPLAYER
						$zone_d=$media->get_adzone_media_dimensions($adzone);
						$adHtmlTyped='';
						$adHtmlTyped.='
						<div class="vid_ad_preview"
						href="'.JUri::root().$ad_image.'"
						style="background:url('.JUri::root().'/components/com_socialads/images/black.png);width:'.$zone_d->img_width.'px;height:'.$zone_d->img_height.'px;
						">
						</div>
						';
						//this is needed for ad preview from backend
						$adHtmlTyped.='<script type="text/javascript">
						flowplayer("div.vid_ad_preview",
						{
							src:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.10.swf",
							wmode:"opaque"
						},
						{
							canvas: {
								backgroundColor:"#000000",
								width:'.$zone_d->img_width.',
								height:'.$zone_d->img_height.'
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
					}
				break;

				case "video":
					if($track)//if disaplying ad in module
					{
						$zone_d=$media->get_adzone_media_dimensions($adzone);

						//include flowplayer javascript
						$socialadshelper->loadScriptOnce(JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.9.min.js');
						$adHtmlTyped='';

						/*
						//@TODO use image?zone dimensions here?
						$ht_wd='width:'.$media_d['width_img'].'px;height:'.$media_d['height_img'].'px';
						$ht_wd='width:'.$opti_d['new_calculated_width'].'px;height:'.$opti_d['new_calculated_height'].'px';
						*/
						$ht_wd='width:'.$zone_d->img_width.'px;height:'.$zone_d->img_height.'px';

						//calculate top margin for play button icon
						$top_margin=($zone_d->img_height/2)-48;

						//calculate overlay div height
						$div_height=$zone_d->img_height-25;

						//create uniquq tag for each ad for video ads
						$adHtmlTyped.='<div
							href="'.JUri::root().$ad_image.'"
							style="display:block;'.$ht_wd.'"
							id="vid_player_'.$ad_id.'">
							</div>';

						//flow player js, configured as required
						$adHtmlTyped.='
						<script>
							flowplayer("vid_player_'.$ad_id.'",
							{
								src:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.10.swf",
								wmode:"opaque"
							},
							{
								canvas: {
									backgroundColor:"#000000",
									width:'.$zone_d->img_width.',
									height:'.$zone_d->img_height.'
								},

								//default settings for the play button
								play: {
									opacity: 0.0,
								 	label: null,
								 	replayLabel: null,
								 	fadeSpeed: 500,
								 	rotateSpeed: 50
								},
							';
							if($socialads_config["allow_vid_ads_autoplay"]==0){

								//added by aniket for stop auto play
							$adHtmlTyped.='	clip: {
									// these two settings will make the first frame visible
									autoPlay: false,
									autoBuffering: true,
									duration: 0,
									// locate a good looking first frame with the start parameter
									start: 62,
									},
							';
							}
							$adHtmlTyped.='	plugins:{
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
									},

									content: {
										url:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer.content-3.2.8.swf",
										width:'.$zone_d->img_width.',
										height:'.$div_height.',
										backgroundColor: "#112233",
										opacity: 0.0,
										onClick: function() {
										window.open("'.$ad_link.'","_blank");/*opens in new tab*/
										}
									}
								}
							});
						</script>';
					}
					else//backend ad preview / build ad change layout /manage ads all previews / edit ad
					{
						//FLOWPLAYER
						$zone_d=$media->get_adzone_media_dimensions($adzone);
						$adHtmlTyped='';
						$adHtmlTyped.='
						<div class="vid_ad_preview"
						href="'.JUri::root().$ad_image.'"
						style="background:url('.JUri::root().'/components/com_socialads/images/black.png);width:'.$zone_d->img_width.'px;height:'.$zone_d->img_height.'px;
						">
						</div>
						';

						//this is needed for ad preview from backend
						$adHtmlTyped.='<script type="text/javascript">
						flowplayer("div.vid_ad_preview",
						{
							src:"'.JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.10.swf",
							wmode:"opaque"
						},
						{
							canvas: {
								backgroundColor:"#000000",
								width:'.$zone_d->img_width.',
								height:'.$zone_d->img_height.'
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
					}
				break;
			}//end switch case
		}//end else (media ads)
		$adHtmlTypedwithad_type	= "<div class='adtype' adtype='".$ad_type."'>".$adHtmlTyped."</div>";
		return $adHtmlTypedwithad_type;
	}

	//function for sending value for receiveing bid
	function sendbidvalue($adid){
		//$url = "http://172.132.45.200/~aniket/Joomla/index.php?option=com_socialads&task=getbidvalue"; //api url
		$url = JUri::root()."/components/com_socialads/AdServices.php?cmd=Choosebid"; //api url/aniket/html/Joomla/components/com_socialads/AdServices.php
		$adRetriever = new adRetriever();
		$bid_details = $adRetriever->getbidarray($adid);
		$content['json'] = json_encode($bid_details);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		$json_response = curl_exec($curl);
		if(!$json_response){
			die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
		}
		curl_close($curl);
		$response = json_decode($json_response, true);
		return $response;
	}

	// biddinng aray..to be send
	function getbidarray($adid)
	{
		$user=JFactory::getUser();
		$userid=$user->id;
		$db = JFactory::getDBO();
		$bid_array = array();
		//ad info
		$query = "select ad_id,ad_payment_type,bid_value,ad_creator FROM #__ad_data WHERE ad_id = ".$adid;
		$db->setQuery($query);
		$ad_data = $db->loadObject();

		if($ad_data->ad_payment_type==0)
		{
			$ad_data->ad_payment_type = "impressions";
		}
		else
		{
			$ad_data->ad_payment_type = "clicks";
		}
		$ad_creator = $ad_data->ad_creator;
		$query = "select id,cdate,ad_original_amt FROM #__ad_payment_info WHERE payee_id = ".$ad_creator;
		$db->setQuery($query);
		$order_history = $db->loadObjectlist();
		$bid_array['orderhistory']=$order_history;
		//total count of histoy
		$total_orders = count($order_history);
		$bid_array['totalorders']=$total_orders;
		//user info
		$query = "select id,email FROM #__users WHERE id = ".$ad_creator;
		$db->setQuery($query);
		$userdata = $db->loadObject();
		$bid_array['userdata']=$userdata;
		$bid_array['ad_data']=$ad_data;
		return $bid_array;
	}

	//check remaining amount ...if > than charge required for ad show ..return true.
	function check_balance($adid,$bid_price)
	{
		$spent=0;
		$db = JFactory::getDBO();
		$date1 = date('Y-m-d');
		$query = "SELECT a.ad_creator,a.camp_id,c.daily_budget FROM #__ad_data as a INNER JOIN #__ad_campaign as c ON c.camp_id = a.camp_id WHERE ad_id = ".$adid;
		$db->setQuery($query);
		$info = $db->loadobject();
		$ad_creator = $info->ad_creator;
		$camp_id = $info->camp_id;
		$daily_budget = $info->daily_budget;
		$query = "SELECT balance FROM `#__ad_camp_transc` where time = (select MAX(time) from #__ad_camp_transc where user_id =".$ad_creator.")";
		$db->setQuery($query);
		$remaining_amt = $db->loadresult();
		$query = "SELECT spent FROM `#__ad_camp_transc` where DATE(FROM_UNIXTIME(time)) ='".$date1."'  AND type_id = ". $camp_id. " AND type='C'";
		$db->setQuery($query);
		$spent = $db->loadresult();

		$status = 0;
		if(((($bid_price) - $remaining_amt) <= 0 ) && ((($spent+$bid_price) - $daily_budget)  <= 0 ))
		{
			$status = 1;
		}
		return $status;
	}

	// get charges for add to show
	function getad_charges($adid,$caltype)
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		if($socialads_config['zone_pricing']==1)
		{
			$db=JFactory::getDBO();
			$query = "SELECT a.camp_id,a.ad_zone,s.per_imp,a.ad_creator, s.per_click FROM `#__ad_data` as a INNER JOIN #__ad_zone as s ON s.id = a.ad_zone WHERE ad_id = $adid";
			$db->setQuery($query);
			$camp_zone = $db->loadobjectlist();
			foreach($camp_zone as $key)
			{
				if($key->ad_zone)
				{
					if($caltype==0)
					{
						$modify_price = "  $key->per_imp ";
					}
					else
					{
						$modify_price = " $key->per_click  ";
					}
				}
			}
		}
		else
		{
			if($caltype==0)
				{
					$modify_price =  $socialads_config['impr_price'];
				}
				else
				{
					$modify_price =   $socialads_config['clicks_price'];
				}
		}
		return $modify_price;
	}

	//get status for an ad which satisfy all condition
	function getad_status($adid)
	{
		if ($adid)
		{
			require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
			$adRetriever = new adRetriever();
			$db=JFactory::getDBO();
			/* not needed... code done for Moses
			$query = "SELECT bid_value FROM #__ad_data WHERE ad_id =".$adid;
			$db->setQuery($query);
			$bid = $db->loadresult();
			*/
			$query = "SELECT ad_noexpiry,ad_alternative,ad_affiliate FROM #__ad_data WHERE ad_id =".$adid;
			$db->setQuery($query);
			$ad_alt_exp = $db->loadObject();
			if($ad_alt_exp->ad_noexpiry == 1  || $ad_alt_exp->ad_alternative == 1 || $ad_alt_exp->ad_affiliate == 1)
			{
				$statue_adcharge = array();
				$statue_adcharge['ad_charge']=0.00;
				$statue_adcharge['status_ads']=1;
				return $statue_adcharge;
			}
			$query = "SELECT ad_payment_type FROM #__ad_data WHERE ad_id =".$adid;
			$db->setQuery($query);
			$caltype = $db->loadresult();

			/* not needed... code done for Moses
			if(!empty($bid))
			{
				$bid_value = $adRetriever->sendbidvalue($adid);
				$ad_charge = $bid_value['price'];
			}
			else*/
			{
				$ad_charge = $adRetriever->getad_charges($adid,$caltype);
			}
			if($socialads_config['select_campaign']==1 && $ad_alt_exp->ad_noexpiry == 0  && $ad_alt_exp->ad_alternative == 0 && $ad_alt_exp->ad_affiliate == 0)
			{
				$status_ads = $adRetriever->check_balance($adid,$ad_charge);
			}
			else
			{
				$status_ads= 1 ;
			}
			$statue_adcharge = array();
			$statue_adcharge['ad_charge']=$ad_charge;
			$statue_adcharge['status_ads']=$status_ads;
			return $statue_adcharge;
		}
		return false;
	}

	//JLIKE integration
	function DisplayjlikeButton($ad_url,$id,$title)
	{
		$jlikeparams=array();
		$jlikeparams['url']=$ad_url;
		$jlikeparams['id']=$id;
		$jlikeparams['title']=$title;
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$grt_response=$dispatcher->trigger('onAfterSaAdDispay',array('com_socialads.viewad',$jlikeparams));
		if(!empty($grt_response['0']))
			return $grt_response['0'];
		else
			return '';
	}
}
