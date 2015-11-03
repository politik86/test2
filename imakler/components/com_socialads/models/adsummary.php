<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.model' );
require_once JPATH_COMPONENT.DS.'helper.php';


/* adsummery Model */
class socialadsModeladsummary extends JModelLegacy
{

	//function which calls helper function which shows pricing
	function getAds()
	{		
		require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');
		$input=JFactory::getApplication()->input;
		$adid=$input->get('adid',0,'INT');	
		$adRetriever = new adRetriever();	
		$preview =  $adRetriever->getAdHTML($adid, 1);	
		return $preview;
	}
	
	function getadtype()
	{
		$user = JFactory::getUser();
		$input=JFactory::getApplication()->input;
		$ad_id = $input->get('adid',0,'INT');
		$query = "SELECT ad_noexpiry ,ad_alternative, ad_enddate, ad_startdate, ad_credits,camp_id FROM #__ad_data WHERE ad_id = $ad_id AND ad_creator = $user->id";
		$this->_db->setQuery($query);
		$id = $this->_db->loadObjectList();
	   
	   return $id ;
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
		
	function getchargeoption()
	{
		$input=JFactory::getApplication()->input;
		$ad_id = $input->get('adid',0,'INT');
		$query = "SELECT ad_payment_type FROM #__ad_data WHERE ad_id = $ad_id ";
		$this->_db->setQuery($query);
		$id = $this->_db->loadResult();
		return $id;
	}		
	
	/*function for line chart*/
	function statsforbar() 
	{
		$db=JFactory::getDBO();
		$input=JFactory::getApplication()->input;
		$where='';	
		$session = JFactory::getSession();
		$ad_id=0;
		$statistics = array();
		$socialads_from_date=$session->get('socialads_from_date');
		$socialads_end_date=$session->get('socialads_end_date');
		if($socialads_from_date)
		{
			$ad_id=$session->get('socialads_adid');
		}	
		else 
		{
			$ad_id=$input->get('adid',0,'INT');	 	
			$session->set('socialads_adid',$ad_id);			
			$socialads_from_date=date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 30 days'));
			$socialads_end_date=date('Y-m-d');
		}
		$where=" AND DATE(time) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."')";
		$arch_where=" AND DATE(date) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."')";

		$query = " SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month,YEAR(time) as year  
					FROM #__ad_stats 
					WHERE display_type = 0  AND ad_id = ".$ad_id.$where."  
					GROUP BY DATE(time) ORDER BY DATE(time)";
		$db->setQuery($query);
		$statistics[0] = $db->loadObjectList();
/* start query for archive*/
		$query = " SELECT impression as value,DAY(date) as day,MONTH(date) as month,YEAR(date) as year 
					FROM #__ad_archive_stats 
					WHERE impression<>0 AND ad_id = ".$ad_id.$arch_where." 
					GROUP BY DATE(date) ORDER BY DATE(date)";
		$db->setQuery($query);
		$acrh_imp_statistics = $db->loadObjectList();
		if( !empty($statistics[0]) && (isset($acrh_imp_statistics[0]->value) && $acrh_imp_statistics[0]->value) ){
		$statistics[0] =  array_merge($statistics[0], $acrh_imp_statistics);
		}
		elseif( empty($statistics[0])  && (isset($acrh_imp_statistics[0]->value) && $acrh_imp_statistics[0]->value) ){
			$statistics[0] = $acrh_clk_statistics;
		}
/*eoc for archive*/

		$query = "SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month,YEAR(time) as year 
					FROM #__ad_stats 
					WHERE display_type = 1 AND ad_id =".$ad_id.$where."  
					GROUP BY DATE(time)  ORDER BY DATE(time)";
		$db->setQuery($query);
			
		$statistics[1] = $db->loadObjectList();
/* start query for archive*/
		$query = " SELECT click as value,DAY(date) as day,MONTH(date) as month,YEAR(date) as year 
					FROM #__ad_archive_stats 
					WHERE click<>0 AND ad_id = ".$ad_id.$arch_where." 
					GROUP BY DATE(date) ORDER BY DATE(date)";
		$db->setQuery($query);
		$acrh_clk_statistics = $db->loadObjectList();
		if( !empty($statistics[1]) && (isset($acrh_imp_statistics[0]->value) && $acrh_imp_statistics[0]->value) ){
			$statistics[1] =  array_merge($statistics[1], $acrh_clk_statistics);
		}
		elseif( empty($statistics[1]) && (isset($acrh_imp_statistics[0]->value) && $acrh_imp_statistics[0]->value) ){
			$statistics[1] = $acrh_clk_statistics;
		}
/*eoc for archive*/			
		//print_r($statistics);die;
		return $statistics;
	}//function statsforbar ends here
	
	function getAdcreators()
	{
	$db=JFactory::getDBO();
	$query = "SELECT ad_creator FROM #__ad_data  WHERE ad_published=1 AND ad_approved=1 GROUP BY ad_creator";//.$groupby;
	//$query = "SELECT ad.ad_creator FROM #__ad_data AS ad LEFT JOIN #__ad_stats  AS st ON ad.ad_id=st.ad_id   WHERE ad.ad_published=1 AND ad.ad_approved=1 GROUP BY ad.ad_creator";//.$groupby;
	$db->setQuery($query);
	$adcreators= $db->loadColumn(); 
	//print_r($adcreators);
	return $adcreators;
	}
		
	function statsforpie_mail($user_id='')
	{
		
		$db=JFactory::getDBO();
		$statsforpie = array();
		$socialads_from_date=date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 7 days'));
		$socialads_end_date= date('Y-m-d');
		$where='';
		$groupby='';
		
		$query_data = "SELECT ad_id  FROM #__ad_data WHERE ad_creator = $user_id ";
		$this->_db->setQuery($query_data);
		$adids = $db->loadColumn();
		$total_no_ads=count($adids);
		$cnt=0;
		foreach($adids as $adid)
		{
			$where=" AND DATE(time) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."') AND ad_id=".$adid;
			$arch_where=" AND DATE(date) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."')";
			
			$groupby="  GROUP BY DATE(time)";
		
		
			$query = " SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month 
				FROM #__ad_stats 
				WHERE display_type = 0  ".$where;//.$groupby;
			$db->setQuery($query);
			$statsforpie[$cnt][0] = $db->loadObjectList(); //impression

/*query for archive */
			$query = " SELECT SUM(impression) as value,DAY(date) as day,MONTH(date) as month 
						FROM #__ad_archive_stats 
						WHERE  impression<>0 AND ad_id = ".$adid.$arch_where;
			$db->setQuery($query);
			$acrh_imp_statistics = $db->loadObjectList();
			if (isset($acrh_imp_statistics[0]->value) && $acrh_imp_statistics[0]->value){
				$statsforpie[$cnt][0][0]->value +=  $acrh_imp_statistics[0]->value;
			}
/*eoc for archive*/		
			
			$query = "SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month 
				FROM #__ad_stats 
				WHERE display_type = 1 ".$where;//.$groupby;
			$db->setQuery($query);
			$statsforpie[$cnt][1] = $db->loadObjectList(); //clicks
/*query for archive*/
			$query = " SELECT SUM(click) as value,DAY(date) as day,MONTH(date) as month,YEAR(date) as year 
						FROM #__ad_archive_stats 
						WHERE  click<>0 AND ad_id = ".$adid.$arch_where ;
			$db->setQuery($query);
			$acrh_clk_statistics = $db->loadObjectList();
			if(isset($acrh_clk_statistics[0]->value) && $acrh_clk_statistics[0]->value){
				$statsforpie[$cnt][1][0]->value +=  $acrh_clk_statistics[0]->value;
			}
/*eoc for archive*/	

			$statsforpie[$cnt][2] = $adid;
			
			$cnt++;
		}
		//print_r($statsforpie);die;
		return $statsforpie; 
	
	}
	/*returns data for line chart (not used in v2.6)*/
	function getmaillinechartdata($adid='')
	{
		if($adid=='')
		return;
		$db=JFactory::getDBO();
		$statsforpie = array();
		$socialads_from_date=date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 7 days'));
		$socialads_end_date= date('Y-m-d');
		$where=" AND DATE(time) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."') AND ad_id=".$adid;
		$groupby="  GROUP BY DATE(time)";
		
		
			$query = " SELECT count(ad_id) AS value,DATE(time) AS date FROM #__ad_stats WHERE display_type = 0  
				".$where.$groupby." ORDER BY DATE(time)";
			$db->setQuery($query);
			$statsforpie[] = $db->loadObjectList(); //impression
			
			$query = "SELECT count(ad_id) AS value,DATE(time) AS date FROM #__ad_stats WHERE display_type = 1 
				".$where.$groupby." ORDER BY DATE(time)";
			$db->setQuery($query);
			$statsforpie[] = $db->loadObjectList(); //clicks
			$statsforpie[] = $adid;
			//print_r($statsforpie);die;
			return $statsforpie;
		
			
		
		
	}
	/* function for pie chart*/	
	function statsforpie()
	{ 
	 	$db=JFactory::getDBO();
		$session = JFactory::getSession();
		$input=JFactory::getApplication()->input;
		$statsforpie = array();
		$socialads_from_date=$session->get('socialads_from_date');
		$socialads_end_date=$session->get('socialads_end_date');
		$where='';
		$groupby='';
		if($socialads_from_date)
		{
	 		// for graph 
	 		$ad_id=$session->get('socialads_adid');		
		}
		else 
		{			
			$ad_id=$input->get('adid',0,'INT');
			$backdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 30 days'));
			//$groupby="  GROUP BY YEAR(time), MONTH(time)";
			$socialads_from_date=date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 30 days'));
			$socialads_end_date=date('Y-m-d');
		}
		$where=" AND DATE(time) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."')";
		$arch_where=" AND DATE(date) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."')";

		$query = " SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month 
					FROM #__ad_stats 
					WHERE display_type = 0 AND ad_id =".$ad_id.$where.$groupby;
		$db->setQuery($query);
		$statsforpie[0] = $db->loadObjectList(); //impression
/* start query for archive*/
		$query = " SELECT SUM(impression) as value,DAY(date) as day,MONTH(date) as month 
					FROM #__ad_archive_stats 
					WHERE  impression<>0 AND ad_id = ".$ad_id.$arch_where;
		$db->setQuery($query);
		$acrh_imp_statistics = $db->loadObjectList();
		if (isset($acrh_imp_statistics[0]->value) && $acrh_imp_statistics[0]->value){
			$statsforpie[0][0]->value +=  $acrh_imp_statistics[0]->value;
		}
/*eoc for archive*/		
			
		$query = "SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month 
					FROM #__ad_stats 
					WHERE display_type = 1 AND ad_id =".$ad_id .$where.$groupby;
		$db->setQuery($query);
		$statsforpie[1] = $db->loadObjectList(); //clicks
/* start query for archive*/
		$query = " SELECT SUM(click) as value,DAY(date) as day,MONTH(date) as month,YEAR(date) as year 
					FROM #__ad_archive_stats 
					WHERE  click<>0 AND ad_id = ".$ad_id.$arch_where ;
		$db->setQuery($query);
		$acrh_clk_statistics = $db->loadObjectList();
		if(isset($acrh_clk_statistics[0]->value) && $acrh_clk_statistics[0]->value){
			$statsforpie[1][0]->value +=  $acrh_clk_statistics[0]->value;
		}
/*eoc for archive*/			
//print_r($statsforpie); 
	
		return $statsforpie; 
	 }
		
	//impression count
	function getImpCount($id)
	{
		$db=JFactory::getDBO();
		$session = JFactory::getSession();
		$socialads_from_date=$session->get('socialads_from_date');
		$socialads_end_date=$session->get('socialads_end_date');
		$where='';
		
		if($socialads_from_date)
		{
			
			$id=$session->get('socialads_adid');
			 // for graph 
			 $where=" AND DATE(time) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."')";
			
		}
		$query = "SELECT COUNT(*) FROM #__ad_stats WHERE ad_id=".$id.$where." AND display_type=0"; 
		$this->_db->setQuery($query);
		$cnt= $this->_db->loadResult();
		return $cnt;		
	}
	
	//click count
	function getClickCount($id)
	{
		$db=JFactory::getDBO();
		$session = JFactory::getSession();
		$socialads_from_date=$session->get('socialads_from_date');
		$socialads_end_date=$session->get('socialads_end_date');
		$where='';
		
		if($socialads_from_date)
		{
			
			$id=$session->get('socialads_adid');
			 // for graph 
			 $where=" AND DATE(time) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."')";
			
		}
		 $query = "SELECT COUNT(*) FROM #__ad_stats WHERE ad_id=".$id.$where." AND display_type=1"; 
		$this->_db->setQuery($query);
		$cnt= $this->_db->loadResult();
		return $cnt;			
	}
	
	//function for payment history
	function getAdpaymentinfo()
	{
	  $user=JFactory::getUser();
	  $input=JFactory::getApplication()->input;
	  $ad_id=$input->get('adid',0,'INT');
	  $query="SELECT DISTINCT  a.*, b.* FROM #__ad_data as a, #__ad_payment_info AS b  WHERE a.ad_id=$ad_id AND b.ad_id=$ad_id AND a.ad_creator=".$user->id;
	  $this->_db->setQuery($query);
	  $payinfo= $this->_db->loadObjectList();
	  $impcnt=array();
	  $clickcnt=array();
		foreach($payinfo as $info)
		{
			$info->ad_impressions=$this->getImpCount($info->ad_id);
			$info->ad_clicks=$this->getClickCount($info->ad_id);
		}
	   
		return $payinfo;
	}
	
	//function to check ststus of add-pending/apparoved
	function getadapproval()
	{
	 $user=JFactory::getUser();
	 $input=JFactory::getApplication()->input;
	 $ad_id=$input->get('adid',0,'INT');
	 $query="SELECT ad_approved FROM #__ad_data as a, #__ad_payment_info AS b  WHERE a.ad_id=$ad_id AND b.ad_id=$ad_id  AND a.ad_creator=".$user->id;
	 $this->_db->setQuery($query);
	 $adapp= $this->_db->loadresult();
	 
	 return $adapp;
	}
	
	function getzoneprice()
	{
		$input=JFactory::getApplication()->input;
			$adid=$input->get('adid',0,'INT');
			$query = "SELECT  z.per_click,z.per_imp,z.per_day FROM #__ad_zone as z LEFT JOIN #__ad_data AS d ON z.id=d.ad_zone WHERE  d.ad_id=".$adid;
			$this->_db->setQuery($query);
			$zoneprice = $this->_db->loadObjectList();
			//var_dump($zoneprice);die;			
			return $zoneprice; 
		
	}
	
	function getignoreCount()
	{
		$db=JFactory::getDBO();
		$session = JFactory::getSession();
		$input=JFactory::getApplication()->input;
		$socialads_from_date=$session->get('socialads_from_date');
		$socialads_end_date=$session->get('socialads_end_date');
		$where='';
		
		if($socialads_from_date)
		{
			
			$ad_id=$session->get('socialads_adid');
			 // for graph 
			 $where=" AND DATE(idate) BETWEEN DATE('".$socialads_from_date."') AND DATE('".$socialads_end_date."')";
			
		}
		else
		{
		$ad_id	= $input->get('adid',0,'INT');
		}
		$query 	= "SELECT COUNT(*) as ignorecount,DATE(idate) as idate FROM #__ad_ignore WHERE adid=".$ad_id.$where." GROUP bY DATE(idate) ORDER BY DATE(idate)";
		
		$this->_db->setQuery($query);
		$cnt= $this->_db->loadObjectList();
		return $cnt;			
	}
	function getuserdetails($userid,$details)
	{
	
		$query = 'SELECT '.$details
		. ' FROM #__users'
		. ' WHERE ' 
		. '  id='.$userid;
		$this->_db->setQuery($query);
		$id = $this->_db->loadObjectList();		
		return $id;
	
	}
}// model class  ends here
