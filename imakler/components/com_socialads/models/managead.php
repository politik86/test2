<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
jimport('joomla.filesystem.file');
jimport( 'joomla.application.component.model' );
require_once(JPATH_SITE.DS.'components'.DS.'com_socialads'. DS . 'helper.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'helpers'.DS.'media.php');//2.7.5b4 manoj
/*
 * ManageAd Model
 * @package    socialads
 * @subpackage Models
 */
class socialadsModelManageAd extends JModelLegacy
{
   var $_data;
	 var $_total = null;
	 var $_pagination = null;

	//for getting my ads list
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$option = $input->get('option');
		$filter_order     = $mainframe->getUserStateFromRequest(  $option.'filter_order', 'filter_order', 'ad.ad_id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $input->get('limitstart',0,'INT');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getAds($where='')
	{

		$input=JFactory::getApplication()->input;
		global $mainframe, $option,$approved;
		$mainframe = JFactory::getApplication();

		$option = $input->get('option','','STRING');

		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');

		$user=JFactory::getUser();
		$ad_id=$input->get('adid',0,'INT');




		if($where)
		{
			$whr = "  AND ad.camp_id=$where";
			}


		$query="SELECT ad.*,az.id,ap.subscription_id,az.zone_name,ap.status,c.campaign FROM #__ad_data as ad LEFT JOIN #__ad_zone as az ON ad.ad_zone=az.id
						LEFT JOIN  #__ad_payment_info AS ap ON ap.ad_id = ad.ad_id
						LEFT JOIN #__ad_campaign as c ON c.camp_id = ad.camp_id
				 		WHERE ad.ad_creator=".$user->id."$whr";
		//------------For Filter Based on Zone
				$search_zone = $mainframe->getUserStateFromRequest( $option.'search_zone', 'search_zone', '', 'string' );
				$search_zone = JString::strtolower( $search_zone);

		if($search_zone!=0){
			  $query.= '  AND ad.ad_zone='.$search_zone;
			}
		//-----------End For Filter Based on Zone

		//------------For Filter Based on campaign
		$search_camp = $mainframe->getUserStateFromRequest( $option.'search_camp', 'search_camp', '', 'string' );

		$search_camp = JString::strtolower( $search_camp);


		$whr='';
		if($search_camp!=0){
				$query.= '  AND ad.camp_id='.$search_camp;
			}

		//-----------End For Filter Based on campaign*/

		$query.= ' GROUP BY ad.ad_id ';

		$orderby = '';
		$filter_order     = $this->getState('filter_order');
		$filter_order_Dir = $this->getState('filter_order_Dir');


		/* Error handling is never a bad thing*/
		if(!empty($filter_order) && !empty($filter_order_Dir) ){
			if($filter_order != 'az.per_imp' && $filter_order != 'az.per_click')
				$query.= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}
		else
			$query.= ' ORDER BY ad.ad_id';


		if($limit != 0)
		{
			$query .= " LIMIT ".$limitstart.",";
			$query .= $limit;
		}




		$this->_db->setQuery($query);
		$myads= $this->_db->loadObjectList();

		$impcnt=array();
		$clickcnt=array();
		foreach($myads as $ads)
		{
			$ads->ad_impressions=$this->getAdstatTotal($ads->ad_id,0);//$this->getImpCount($ads->ad_id);
			$ads->ad_clicks=$this->getAdstatTotal($ads->ad_id,1);//$this->getClickCount($ads->ad_id);
		}
		if(!empty($filter_order) && !empty($filter_order_Dir) ){
			if($filter_order == 'az.per_imp' || $filter_order == 'az.per_click'){
				if($filter_order=='az.per_imp')
				{
					$myads=$this->multi_d_sort($myads,'ad_impressions',$filter_order_Dir);
				}
				if($filter_order=='az.per_click')
				{
					$myads=$this->multi_d_sort($myads,'ad_clicks',$filter_order_Dir);
				}
			}
		}

		return $myads;
	}//function getAds ends
	function multi_d_sort($array,$column,$order)
    {
		if(isset($array) && count($array))
		{
			foreach($array as $key=>$row)
			{
				//$orderby[$key]=$row['campaign']->$column;
				$orderby[$key]=$row->$column;
			}
			if($order=='asc')
			{
				array_multisort($orderby,SORT_ASC,$array);
			}
			else
			{
				array_multisort($orderby,SORT_DESC,$array);
			}
		}
        return $array;
    }

	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

			return $this->_total;
	}

	function getPagination()
	{
	    // Lets load the content if it doesn't already exist
	    if (empty($this->_pagination))
		  {
		   	jimport('joomla.html.pagination');
		    $this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		  }

		    	return $this->_pagination;
	 }

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$user=JFactory::getUser();
		$query="SELECT * FROM #__ad_data WHERE ad_creator=".$user->id;

		return $query;
	}
	// functions for pagination ends here

    //impression count
	function getImpCount($id)
	{
		$query = "SELECT COUNT(*) FROM #__ad_stats WHERE ad_id=".$id." AND display_type=0";
		$this->_db->setQuery($query);
		$cnt= $this->_db->loadResult();

		$query = "SELECT SUM(a.impression) ";
		$query .= "FROM #__ad_archive_stats as a
		WHERE a.ad_id=".$id;
		$this->_db->setQuery($query);
		$archive = $this->_db->loadresult();
		return $archive + $cnt;

/* //for Task #31607 increment ad stats in independent column against the ad
		$query = "SELECT ad_impressions FROM #__ad_data WHERE ad_id=".$id;
		$this->_db->setQuery($query);
		$cnt= $this->_db->loadResult();
		return $cnt;
*/
	}
	//click count
	function getClickCount($id)
	{
		$query = "SELECT COUNT(*) FROM #__ad_stats WHERE ad_id=".$id." AND display_type=1";
		$this->_db->setQuery($query);
		$cnt= $this->_db->loadResult();

		$query = "SELECT SUM(a.click) ";
		$query .= "FROM #__ad_archive_stats as a
		WHERE a.ad_id=".$id;
		$this->_db->setQuery($query);
		$archive = $this->_db->loadresult();
		return $archive + $cnt;
/* //for Task #31607 increment ad stats in independent column against the ad
		$query = "SELECT ad_clicks FROM #__ad_data WHERE ad_id=".$id;
		$this->_db->setQuery($query);
		$cnt= $this->_db->loadResult();
		return $cnt;
*/
	}

	/*count number of clicks == 1, impressions == 0*/
	function getAdstatTotal($ad_id, $ad_type)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('COUNT(s.`display_type`) ')
		->from('`#__ad_stats` AS s')
		->where( ' s.`ad_id` ='.(int)$ad_id)
		->where( ' s.`display_type` ='.(int)$ad_type);
		$db->setQuery($query);
		$adtype = $db->loadresult();

		$query = $db->getQuery(true);
		if($ad_type === 1)
			$query->select('SUM(  a.`click` ) ');
		else
			$query->select('SUM(  a.`impression` ) ');
		$query->from('`#__ad_archive_stats` AS a')
		->where( 'a.`ad_id` ='.(int)$ad_id);
		$db->setQuery($query);
		$archive = $db->loadresult();

		return ($archive + $adtype);
	}

	//add by vaibhav
	function getPlgfields()
	{
		$input=JFactory::getApplication()->input;
		 $db=JFactory::getDBO();
		 $ad_id=$input->get('adid',0,'INT');
			$query = "SHOW TABLES LIKE '#__ad_fields';";
			$db->setQuery($query);
			$fields = $db->loadResult();
			if(!$fields)
			{
					 return array();
			}
		 $query="SELECT * FROM #__ad_fields WHERE adfield_ad_id=$ad_id";
		 $db->setQuery($query);

		 $paramlist=array();
		 $paramlist= $db->loadObject();
		 return $paramlist;//die;
	}
	//get all the fields of JS
	function getFields($ad_id)
	{
		include(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		 $input=JFactory::getApplication()->input;
		 $db=JFactory::getDBO();
		 $user=JFactory::getUser();
		 //$ad_id=$input->get('adid',0,'INT');

		$query="SELECT * FROM #__ad_fields_mapping";
		$db->setQuery($query);
		$fields= $db->loadObjectList();
		//return $fields;
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
	  foreach($options as $optn)
	  {
			foreach ($optn as $k=>$v){
	   	$opt = new stdClass;
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


		$query = "SELECT * FROM #__ad_fields_mapping";
		$db->setQuery($query);
		$allfields= $db->loadObjectList();

		return $allfields;
	 }//end of options are empty condition
	}

	function _getJSOptions($fields)
	{
		$db   = JFactory::getDBO();
		$socialadshelper=new socialadshelper();
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
						//die('asd');
						$model     = Foundry::model( 'Fields' );
						$options_value = $model->getOptions($fields[$i]->mapping_fieldid);
						$options_value=$options_value['items'];
					}
					*/
					$options=implode("\n",$options_value);
					//print_r($options);
					$filed_array->options=$options;
					$field_option[] = $filed_array;

					$mapping_options[] = $field_option;
				}
			}
		}
		//print_r($mapping_options); die('dssdfg');
		if(!empty($mapping_options))
		return $mapping_options;

	}

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
	//fetching all inserted details from DB for geo targeting
	function getData_geo($ad_id)
	{
		$db=JFactory::getDBO();
		$user=JFactory::getUser();
		$input=JFactory::getApplication()->input;
		//$ad_id=$input->get('adid',0,'INT');
		$query = "SELECT a.* FROM #__ad_geo_target AS a WHERE a.ad_id='$ad_id'";
		$db->setQuery($query);
		$addata= $db->loadAssocList();
		if(!empty($addata[0]))
		return $addata[0];
		else
		return $addata;

	}

	//fetching all inserted details from DB for geo targeting
	function getData_context_target($ad_id)
	{
		$db=JFactory::getDBO();
		$user=JFactory::getUser();
		$input=JFactory::getApplication()->input;
		//$ad_id=$input->get('adid',0,'INT');
		$query = "SELECT a.* FROM #__ad_contextual_target AS a WHERE a.ad_id='$ad_id'";
		$db->setQuery($query);
		$addata= $db->loadAssocList();
		if(!empty($addata[0]))
		return $addata[0];
		else
		return $addata;

	}
	//fetching all inserted details from DB
	function getData($ad_id)
	{
		$db=JFactory::getDBO();
		$user=JFactory::getUser();
		$input=JFactory::getApplication()->input;

		//$ad_id=$input->get('adid',0,'INT');
		$query = "SELECT a.* FROM #__ad_data AS a WHERE a.ad_id='$ad_id'";
		$db->setQuery($query);
		$addata= $db->loadObject();
		$count= 0;
		$socialadshelper=new socialadshelper();
		$adfields = $socialadshelper->chkadfields();
		if($adfields != ''){//chk empty

			$query = "SELECT COUNT(*) FROM #__ad_fields AS f WHERE f.adfield_ad_id=".$ad_id;
			$db->setQuery($query);
			$count= $db->loadResult();


			if($addata->ad_alternative==0 && $count>0)
			{
				$query = "SELECT a.*, f.* FROM #__ad_data AS a, #__ad_fields AS f WHERE a.ad_id='$ad_id' AND f.adfield_ad_id='$ad_id'";
				$db->setQuery($query);
				$addata= $db->loadObject();

			}
		}//chk empty
		$addata_result[0] = $count;
		$addata_result[1] = $addata;

		return $addata_result;

	}
	function getzone($ad_id)
	{
		$db=JFactory::getDBO();
		$input=JFactory::getApplication()->input;
		//$ad_id = $input->get('adid',0,'INT');
		$query="SELECT az.id,az.zone_name, az.published,az.zone_type,az.ad_type,az.max_title,az.max_des,az.img_width,az.img_height,az.per_click,az.per_imp,az.per_day,az.layout
		FROM #__ad_data as ad LEFT JOIN #__ad_zone as az ON ad.ad_zone = az.id
		WHERE ad.ad_id =".$ad_id;
		$db->setQuery($query);
		$zone = $db->loadObjectList();
		//$layouts = explode ('|',$zone->layout);
		return $zone;
	}

	function getadcheck()
	{
		$user = JFactory::getUser();
		$input=JFactory::getApplication()->input;
		$ad_id = $input->get('adid',0,'INT');
		$query = "SELECT ad_id FROM #__ad_data WHERE ad_id = $ad_id AND ad_creator = $user->id";
		$this->_db->setQuery($query);
		$id = $this->_db->loadResult();

		return $id;
	}

	function dataSanitize($data)
	{
		if(isset($data['addata']))
		{
			for($i = 0 ; $i<sizeOf($data['addata']) ; $i++)
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

		if(isset($data['context_target_data']))
		{
			$data['context_target_data_keywordtargeting']=$data['context_target_data']['keywordtargeting'];
		}
		return $data;
	}

	//function update for manageads
	function store()
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		//for getting adfield_id from DB
		$db=JFactory::getDBO();
		$user=JFactory::getUser();
		$data 	= JRequest::get( 'post' );
		$data = $this->dataSanitize($data);
		$user = JFactory::getUser();

		$build = new stdClass;
		$ad_id = $input->get('adid',0,'INT');
		$build->ad_id = $ad_id;
		$imgname = str_replace(JUri::base(),'',$data['upimg']);
		foreach($data['addata'] as $addata)
		{
			foreach($addata as $k=>$ad)
			{
		     $build->$k = $ad;
			}
		}
		//code for guest
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
		 ){
		 			if(($data['context_target']=="on" && !($data['context_target_data_keywordtargeting'])) || ($data['context_target']!="on"))
					$build->ad_guest = 1;
					else
			$build->ad_guest = 0;

		}
		else
			$build->ad_guest = 0;

	//code for guest
		$build->ad_image = $imgname;
		$build->layout = $data['layout'] ;
		//update fields
	  if (!$this->_db->updateObject( '#__ad_data', $build, 'ad_id' ))
		{
	   	echo $this->_db->stderr();
			return false;
		}

		//For saving demographic details
		/*start of geo*/
		$fielddata = new stdClass;
		$fielddata->ad_id = $build->ad_id;
		if( $socialads_config['geo_target']  && !empty($data['geo_target'])){
			$geo_adfields = $data['geo'];
			if($geoflag)
			{
				$first_key = array_keys($data['geo']);
				$type = str_replace("by","",$data['geo_type']);

				foreach($data['geo'] as $key => $value)
				{
					if($first_key[0] == $key){
						$fielddata->$key = $value;
					}
					else if($data['geo_type'] == "everywhere")
						$fielddata->$key = "";
					else if($type == $key)
						$fielddata->$key = $value;
				}
			$query="SELECT  a.id FROM #__ad_geo_target AS a, #__ad_data AS b WHERE a.ad_id=".$ad_id." AND a.ad_id=b.ad_id AND  b.ad_creator=".$user->id."";
			$db->setQuery($query);
			$adfieldid= $db->loadResult();
			$query="DELETE FROM #__ad_geo_target WHERE ad_id=".$ad_id;
			$this->_db->setQuery($query);
			if (!$db->execute()) {
						echo $this->_db->stderr();

			}
					$fielddata->id='';
					if(!$this->_db->insertObject( '#__ad_geo_target', $fielddata, 'id' ))
						{
							echo $this->_db->stderr();
						//return false;
						}

			}



		}
		else
		{

			$query="DELETE FROM #__ad_geo_target WHERE ad_id=".$ad_id;
			$this->_db->setQuery($query);
			if (!$db->execute()) {
										echo $this->_db->stderr();

			}
		}

		/*end of geo*/



		/*start of context*/
		$fielddata = new stdClass;
			$fielddata->id='';
			$fielddata->ad_id = $build->ad_id;
			$fielddata->keywords=strtolower(trim($context_adfields));
			$query="SELECT  id FROM #__ad_contextual_target AS a, #__ad_data AS b WHERE a.ad_id=".$ad_id." AND a.ad_id=b.ad_id ";
			$db->setQuery($query);
			$adfieldid= $db->loadResult();
		if($socialads_config['context_target']){

		if($data['context_target']=="on")
		{

			$context_adfields =trim($data['context_target_data_keywordtargeting']);

			if($context_adfields)
			{

			$fielddata->keywords=strtolower(trim($context_adfields));
				if($adfieldid)
				{
					$fielddata->id=$adfieldid;


					if (!$this->_db->updateObject( '#__ad_contextual_target',$fielddata,'id' ))
					{

					}
				}
				else
				{
					if(!$this->_db->insertObject( '#__ad_contextual_target',$fielddata,'id' ))
					{
						echo $this->_db->stderr();
						return false;
					}

				}
			}
		}
		else if($adfieldid)
		{

			 $fielddata->id=$adfieldid;
			 $query = 'DELETE FROM #__ad_contextual_target'
                . ' WHERE id= '. $fielddata->id;
			$db->setQuery($query);
			if (!$db->execute()) {
			}
		}
		}
		else if($adfieldid)
		{
							//echo "HERE4";
			 $fielddata->id=$adfieldid;
			 $query = 'DELETE FROM #__ad_contextual_target'
                . ' WHERE id= '. $fielddata->id;
			$db->setQuery($query);
			if (!$db->execute()) {
			}
		}





/*end of context*/


		$fielddata = new stdClass;


		$ad_id=$input->get('adid',0,'INT');

		$socialadshelper=new socialadshelper();
		$adfields = $socialadshelper->chkadfields();
		if(!empty($adfields))
		{
			$query="SELECT distinct adfield_id
			FROM #__ad_fields AS a, #__ad_data AS b
			WHERE a.adfield_ad_id=".$ad_id." AND b.ad_creator=".$user->id."";
			$db->setQuery($query);
			$adfieldid= $db->loadResult();

			$fieldsas = $db->getTableColumns("#__ad_fields");
			if(($data['social_target']=="on") && !empty($fieldsas)){//chk empty

			$query = "SELECT COUNT(*) FROM #__ad_fields AS f WHERE f.adfield_ad_id=".$build->ad_id;
			$db->setQuery($query);
			$count= $db->loadResult();

			$fielddata->adfield_id=$adfieldid;
			$fielddata->adfield_ad_id = $build->ad_id;

			$date_low = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,1910));
			$date_high = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,2030));
			$grad_low=0;
			$grad_high=2030;
			foreach($data['mapdata'] as $mapdata)
			{
				foreach($mapdata as $m=>$map)
				{
					if($m)
					{
						if(strstr($m,','))
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
			$results = $dispatcher->trigger('onFrontendTargetingSave',array($data['plgdata']));

	//		$res = new stdClass();

			for($j=0; $j<count($results); $j++)
			{
				if($results[$j] !="")
				{
					foreach($results[$j] as $key => $value)
					{
						$fielddata->$key = $value;
					}
				}
			}

			if($count>0)
			{
			  //if multiselect box is empty then field name should be post blank value
			  $db=JFactory::getDBO();
				$query = "SELECT mapping_fieldname FROM #__ad_fields_mapping WHERE mapping_fieldtype='multiselect'";
				$db->setQuery($query);
				$mulfields = $db->loadResultarray();

			  foreach($fielddata as $k=>$v)
			  {
				$fields[] = $k;
			  }

			  for($i=0; $i<count($mulfields); $i++){
					 if(!(in_array($mulfields[$i], $fields)))
					  $fielddata->$mulfields[$i] = '';
			  }

				//update fields
				if (!$this->_db->updateObject( '#__ad_fields', $fielddata, 'adfield_id' ))
				{
					 echo $this->_db->stderr();
					 return false;
				}
			}
			else
			{
				$fielddata->adfield_id = null;
				//insert fields
				if (!$this->_db->insertObject( '#__ad_fields', $fielddata, 'adfield_id' ))
				{
					echo $this->_db->stderr();
					return false;
				}
			}
		}
		else if((($data['social_target']!="on") && $adfieldid)){

							//echo "HERE4";
			 $fielddata->id=$adfieldid;
			 $query = 'DELETE FROM #__ad_fields'
                . ' WHERE adfield_id= '. $adfieldid;
			$db->setQuery($query);
			if (!$db->execute()) {
			}

		}
		}//chk empty
		if( $socialads_config['approval'] == 1){
			$build = new stdClass;
			$build->ad_id = $ad_id;
			$build->ad_approved  = '0' ;
			//update fields
			if (!$this->_db->updateObject( '#__ad_data', $build, 'ad_id' ))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
		  //mail function after upadting an Ad
		$query = "SELECT a.ad_title,  u.username FROM #__ad_data as a, #__users as u
						WHERE a.ad_creator=u.id
						AND a.ad_id=".$ad_id;
		$this->_db->setQuery($query);
		$result	= $this->_db->loadObject();
		//print_r($result);

		$body = JText::_('UPDATE_MAIL_BODY');
		$body	= str_replace('{username}', $result->username, $body);
		$ad_title=($result->ad_title!= '') ? JText::_("PERIDIC_STATS_ADTIT").' <b>"'.$result->ad_title.'"</b>' : JText::_("PERIDIC_STATS_ADID").' : <b>'.$ad_id.'</b>';
		$body	= str_replace('{title}', $ad_title, $body);
		$body = str_replace('{link}', JUri::base().'administrator/index.php?option=com_socialads&view=approveads', $body);

		$from = $mainframe->getCfg('mailfrom');
		$fromname = $mainframe->getCfg('fromname');
		$recipient = $mainframe->getCfg('mailfrom');
		$user = JFactory::getUser();
		$subject = JText::_('MAIL_UPDATE_SUB');
		$body = nl2br($body);
		$mode = 1;
		$cc = null;
		$bcc = null;
		$bcc = null;
		$attachment = null;
		$replyto = null;
		$replytoname = null;

		JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
		return true;
	} // end function

	function altUpdate()
	{
		$data 	= JRequest::get( 'post' );
//print_r($data);
		$data = $this->dataSanitize($data);
		//for storing ad details
				$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
		$build = new stdClass;
		$ad_id = $input->get('adid',0,'INT');
		$build->ad_id = $ad_id;
if(isset($data['hadtype'])){
	$build->ad_title = $data['addata']['0']['ad_title'];
	$rawhtml 	= $input->get( 'addata', '', 'post', 'ARRAY',JREQUEST_ALLOWRAW);
//	print_r($rawhtml);
	$build->ad_body = stripslashes($rawhtml['1']['ad_body']);
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
if(!	isset($data['hadtype'])){
		$imgname = str_replace(JUri::base(),'',$data['upimg']);

		$build->ad_image = $imgname;
		$build->layout = $data['layout'] ;
}
//print_r($build);die('hhhii');
		//update fields
		if (!$this->_db->updateObject( '#__ad_data', $build, 'ad_id' ))
		{
		  echo $this->_db->stderr();
		  return false;
		}
		return true;
	}


	//////////////////
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
				$fullPath = JUri::base().'images'.DS.'socialads'.DS;
				$relPath = 'images'.DS.'socialads'.DS;
				$colorR = 255;
				$colorG = 255;
				$colorB = 255;

				$file_name_without_extension=$media->get_media_file_name_without_extension($file_name);

				$upload_image = $media->uploadImage($file_field,$max_zone_width, $max_zone_height, $fullPath, $relPath, $colorR, $colorG, $colorB,$new_media_width,$new_media_height,$blank_height,$top_offset,$media_extension,$file_name_without_extension);

			}
		}

		if($error_flag)
		{
			echo '<img src="'.JUri::base().'components/com_socialads/images/error.gif" width="16" height="16px" border="0" style="margin-bottom: -3px;" /> Error(s) Found: ';
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
			echo '<img src="'.JUri::base().'components/com_socialads/images/error.gif" width="16" height="16px" border="0" style="margin-bottom: -3px;" /> Error(s) Found: ';
			foreach($errorList as $value)
			{
				echo $value.', ';
			}
			jexit();
		}
	}
	//////////////////

	/* function to check guest ad*/
	function getCheckGuestad()
	{
		$db=JFactory::getDBO();
		$input=JFactory::getApplication()->input;
		$ad_id = $input->get('adid',0,'INT');
		$query = "SELECT ad_guest FROM #__ad_data WHERE ad_id=".$ad_id;
		$db->setQuery($query);
		$guestad = $db->loadResult();

		return $guestad;
	}

 function getIgnorecount($ad_id)
  {
  			$db = JFactory::getDBO();
  			$query = "SELECT COUNT(adid) FROM #__ad_ignore
  								WHERE adid=".$ad_id;
  			$db->setQuery($query);
				$ignorecount = $db->loadresult();

				return $ignorecount;
  }

	function adzonelist()
  {

	$db = JFactory::getDBO();
	/*$query = "SELECT id,zone_name FROM #__ad_zone as z, #__ad_data as d
					 WHERE z.id=d.ad_zone";*/
	$query = "SELECT id,zone_name FROM #__ad_zone WHERE published=1";
	$db->setQuery($query);
	$zone_list = $db->loadObjectList();
	return $zone_list;

  }

/*function for line chart*/
	function statsforbar($where='')
	{

		$whr = '';

		if($where)
		{
			$whr = "  AND ad.camp_id=$where";
		}


		$user=JFactory::getUser();
		$year1=" ,YEAR(st.time) as year ";
		$socialads_from_date=date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 30 days'));
		$socialads_end_date=date('Y-m-d');


		$query = " SELECT COUNT(st.id) as value,DAY(st.time) as day,MONTH(st.time) as month ".$year1."
					FROM #__ad_stats as st LEFT JOIN #__ad_data as ad ON ad.ad_id=st.ad_id
					WHERE  ad.ad_creator=".$user->id."$whr AND  st.display_type = 0  AND DATE(st.time) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."') "."
					GROUP BY DATE(st.time) ORDER BY DATE(st.time)";






		$this->_db->setQuery($query);
		$statistics[0] = $this->_db->loadObjectList(); //die;
/*query for archive*/
		$query = " SELECT ar.impression as value,DAY(ar.date) as day,MONTH(ar.date) as month,YEAR(ar.date) as year
					FROM #__ad_archive_stats as ar LEFT JOIN #__ad_data as ad ON ad.ad_id=ar.ad_id
					WHERE ar.impression<>0 AND ad.ad_creator=".$user->id."$whr AND DATE(ar.date) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."')
					GROUP BY DATE(ar.date) ORDER BY DATE(ar.date)";
		$this->_db->setQuery($query);
		$acrh_imp_statistics = $this->_db->loadObjectList();
		if( !empty($statistics[0]) && (isset($acrh_imp_statistics[0]->value) && $acrh_imp_statistics[0]->value) ){
		$statistics[0] =  array_merge($statistics[0], $acrh_imp_statistics);
		}
		elseif( empty($statistics[0])  && (isset($acrh_imp_statistics[0]->value) && $acrh_imp_statistics[0]->value) ){
			$statistics[0] = $acrh_clk_statistics;
		}
//print_r($acrh_imp_statistics); die;
/*eoc for archive*/

		$query = " SELECT COUNT(st.id) as value,DAY(st.time) as day,MONTH(st.time) as month ".$year1."
					FROM #__ad_stats as st LEFT JOIN #__ad_data as ad ON ad.ad_id=st.ad_id
					WHERE  ad.ad_creator=".$user->id."$whr AND  st.display_type = 1 AND DATE(st.time) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."') "."
					GROUP BY DATE(st.time) ORDER BY DATE(st.time)";
		$this->_db->setQuery($query);

		$statistics[1] = $this->_db->loadObjectList();
/*query for archive*/
		$query = " SELECT ar.click as value,DAY(ar.date) as day,MONTH(ar.date) as month,YEAR(ar.date) as year
					FROM #__ad_archive_stats as ar LEFT JOIN #__ad_data as ad ON ad.ad_id=ar.ad_id
					WHERE ar.click<>0 AND ad.ad_creator=".$user->id."$whr AND DATE(ar.date) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."')
					GROUP BY DATE(ar.date) ORDER BY DATE(ar.date)";
		$this->_db->setQuery($query);
		$acrh_clk_statistics = $this->_db->loadObjectList();
		if( !empty($statistics[1]) && (isset($acrh_imp_statistics[0]->value) && $acrh_imp_statistics[0]->value) ){
			$statistics[1] =  array_merge($statistics[1], $acrh_clk_statistics);
		}
		elseif( empty($statistics[1]) && (isset($acrh_imp_statistics[0]->value) && $acrh_imp_statistics[0]->value) ){
			$statistics[1] = $acrh_clk_statistics;
		}
/*eoc for archive*/
/*			//commented in 2.7.5 beta3
		//Used to calculate From Date
		$query = " SELECT date(st.time) as fromdate FROM #__ad_stats as st LEFT JOIN #__ad_data as ad ON ad.ad_id=st.ad_id  WHERE  ad.ad_creator=".$user->id."   GROUP BY DATE(st.time) ORDER BY DATE(st.time) limit 1";
		$this->_db->setQuery($query);
		$statistics[]= $this->_db->loadObjectList();
*/
		return $statistics;
	}//function statsforbar ends here



	function getcampaign_dd(){

			$user=JFactory::getUser();
			$userid=$user->id;
			$query = "SELECT camp_id,campaign FROM #__ad_campaign WHERE user_id=$userid";
			$this->_db->setQuery($query);

			return $this->_db->loadobjectList();

		}


}// class
