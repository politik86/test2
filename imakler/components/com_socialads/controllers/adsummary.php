<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT . DS . 'helper.php');

class socialadsControlleradsummary extends JControllerLegacy
{
	/* redirect to payment gateways*/	
	function buy()	
	{   
		$input=JFactory::getApplication()->input;
		$ad_id = $input->get('adid',0,'INT');
		$model = $this->getModel('adsummary');	
		//$itemid = & $this->get( 'SocialadsItemid');	
		$itemid = socialadshelper::getSocialadsItemid('managead');
		$link = JRoute::_("index.php?option=com_socialads&view=payment&Itemid=$itemid&adid=$ad_id",false);
		$this->setRedirect($link, $msg);
	}
	
	/* redirect to edit ad page*/
	function editad()
	{
		$model = $this->getModel('adsummary');	
		//$itemid = & $this->get( 'SocialadsItemid');	
		$socialadshelper = new socialadshelper();
		$itemid = $socialadshelper->getSocialadsItemid('buildad');
	 	//$this->setRedirect("index.php?option=com_socialads&view=buildad&Itemid=$itemid&frm=editad");
		$link = JRoute::_("index.php?option=com_socialads&view=buildad&Itemid=$itemid&frm=editad",false);
		$this->setRedirect($link, $msg);
	}
	
	
function SetsessionForGraph()
	{
		$aa=json_encode($_GET);
	
	 	$fromDate =  $_GET['fromDate'];
	 	/*$socialads_from_date = strtotime($socialads_from_date)-(60*60*24);
	 	$socialads_from_date=date('Y-m-d',$socialads_from_date);*/
	 	$toDate =  $_GET['toDate'];
		/*$socialads_from_date = strtotime($socialads_from_date)+(60*60*24);*/
		$session = JFactory::getSession();
		$session->set('socialads_from_date', $fromDate);
		$session->set('socialads_end_date', $toDate);
		$model = $this->getModel('adsummary');	
		$statsforbar=$model->statsforbar();
		$statsforpie=$model->statsforpie();
		$ignorecnt=$model->getignoreCount();
		
		//We exit here since this is a AJAX call and we don't want the view to be echo to the client
		$session->set('statsforbar', $statsforbar);
		$session->set('statsforpie', $statsforpie);
		$session->set('ignorecnt', $ignorecnt);
		
	header('Content-type: application/json');
	  	echo (json_encode(array("statsforbar"=>$statsforbar,
	  							"statsforpie"=>$statsforpie,
	  							"ignorecnt"=>$ignorecnt
	  	
	  				)));
	  
		jexit();
	}
	
	function makechart()
	{
		$month_array_name = array(JText::_('SA_JAN'),JText::_('SA_FEB'),JText::_('SA_MAR'),JText::_('SA_APR'),JText::_('SA_MAY'),JText::_('SA_JUN'),JText::_('SA_JUL'),JText::_('SA_AUG'),JText::_('SA_SEP'),JText::_('SA_OCT'),JText::_('SA_NOV'),JText::_('SA_DEC')) ;
		$session = JFactory::getSession();
		$socialads_from_date='';
		$socialads_end_date='';
		$statsforbar='';
		$socialads_from_date= $session->get('socialads_from_date', '');
		$socialads_end_date=$session->get('socialads_end_date', '');
		$total_days = (strtotime($socialads_end_date) - strtotime($socialads_from_date)) / (60 * 60 * 24);	
		$total_days=$total_days+1;
		$statsforbar = $session->get('statsforbar','');
		$statsforpie = $session->get('statsforpie','');
		$ignorecnt = $session->get('ignorecnt', '');
		/*print_r($statsforbar);
		print_r($statsforpie);
		print_r($ignorecnt);*/
		$imprs=0;
		$clicks=0;
		$max_invite=100;
		$cmax_invite=100;
		$yscale="";
		$titlebar="";
		$daystring="";
		$finalstats_date=array();
		$finalstats_clicks=array();
		$finalstats_imprs=array();
		$day_str_final='';
		$finalstats_date1=array();	
		$finalstats_date2=array();
		$finalstats_str_date1=array();//for date string
		$finalstats_str_date2=array();//for date string
		$barchart='';
		$fromDate= $session->get('socialads_from_date', '');
		$toDate=$session->get('socialads_end_date', '');
	 
		$dateMonthYearArr = array();
		$fromDateSTR = strtotime($fromDate);
		$toDateSTR = strtotime($toDate);
		
		if(empty($statsforbar[0]) && empty($statsforbar[1]))
		{
		  	$barchart=JText::_('NO_STATS');
		}
	   else
	   	{
			if(!empty($statsforbar[1]))
			{	
			
			$cnt=0;
			for ($currentDateSTR = $fromDateSTR; $currentDateSTR <= $toDateSTR; $currentDateSTR += (60 * 60 * 24)) 
			{
				// use date() and $currentDateSTR to format the dates in between
				$currentDateStr = date("Y-m-d",$currentDateSTR);
				$day='';
				$month= '';
				$year='';
				//echo $currentDateStr;
				 $day= date("d",strtotime($currentDateStr));
				 $finalstats_date1[] = $day;
				 $month= date("m",strtotime($currentDateStr));
				 $year=date("Y",strtotime($currentDateStr));
				$finalstats_str_date1[] = $year."-".$month."-".$day; //for date string
				$finalstats_clicks[$cnt]= 0;
				//$sts = array_reverse($statsforbar[1]);
				foreach($statsforbar[1] as $cur_statsforbar)
				{	
				$cur_month=$cur_statsforbar->month;
				$cur_day=$cur_statsforbar->day;
				//print_r($cur_statsforbar);die;
				if(($cur_statsforbar->month)<10)
				$cur_month="0".$cur_month;
								 
				if(($cur_statsforbar->day)<10)
				$cur_day="0".$cur_day;
						if(($day==$cur_day) and ($month == $cur_month )and ($year == $cur_statsforbar->year))
						{
							$finalstats_clicks[$cnt]=0+$cur_statsforbar->value;	
					 	}
				}
				$cnt++;	
			}
			//print_r($finalstats_clicks);die;
				
			}//if ststsforbar is not empty ends
			else
			{	  	
				$barchart=JText::_('NO_CLICKS');
			}
  
	  //for impressions starts here
	     	if(!empty($statsforbar[0]))
	     	{
				
	     	$cnt=0;
			for ($currentDateSTR = $fromDateSTR; $currentDateSTR <= $toDateSTR; $currentDateSTR += (60 * 60 * 24)) 
			{
				// use date() and $currentDateSTR to format the dates in between
				$currentDateStr = date("Y-m-d",$currentDateSTR);
				$dateMonthYearArr[] = $currentDateStr;
				$day='';
				$month= '';
				$year='';
				//echo $currentDateStr;
				 $day= date("d",strtotime($currentDateStr));
				 $finalstats_date2[] = $day;
				 $month= date("m",strtotime($currentDateStr));
				 $year=date("Y",strtotime($currentDateStr));
				$finalstats_str_date2[] = $year."-".$month."-".$day; //for date string
				$finalstats_imprs[$cnt]= 0;
				//$sts = array_reverse($statsforbar[1]);
				foreach($statsforbar[0] as $cur_statsforbar)
				{	
				$cur_month=$cur_statsforbar->month;
				$cur_day=$cur_statsforbar->day;
				//print_r($cur_statsforbar);die;
				if(($cur_statsforbar->month)<10)
				$cur_month="0".$cur_month;
								 
				if(($cur_statsforbar->day)<10)
				$cur_day="0".$cur_day;
						if(($day==$cur_day) and ($month == $cur_month )and ($year == $cur_statsforbar->year))
						{
							$finalstats_imprs[$cnt]=0+$cur_statsforbar->value;
					 	}
				}
				$cnt++;	
			}
			
				
			}	
			else
			{
				 
					$imprs=0;	
				
				$barchart=JText::_('NO_IMPRS');
			}
			
			if(count($finalstats_date1)>count($finalstats_date2))
				$finalstats_date=$finalstats_str_date1; //for date string
			else 
				$finalstats_date=$finalstats_str_date2; //for date string
			//for date string
			$day_str=implode(",",$finalstats_date);
			$day_str_final=$day_str;
			
			//echo "\n-----------";
			if($finalstats_imprs)
			$imprs=implode(",",$finalstats_imprs);
			//echo "\n-----------";
			if($finalstats_clicks)
			$clicks=implode(",",$finalstats_clicks);
			
			$session->set('statsfor_line_day_str_final', $day_str_final);
			 $session->set('statsfor_line_imprs', $imprs);
			 $session->set('statsfor_line_clicks', $clicks);
			//echo "\n-----------";
			
			/*echo "<br>-------------imprs";
			print_r($imprs);
			echo "<br>-------------clicks";
			print_r($clicks);	
			echo "<br>-------------titlebar";
			print_r($titlebar);
			echo "<br>-------------max_invite";
			print_r($max_invite);
			echo "<br>-------------cmax_invite";
			print_r($cmax_invite);
			echo "<br>-------------yscale";
			print_r($yscale);
			echo "<br>-------------daystring";
			print_r($daystring);
			echo "<br>-------------";
			die;*/
			//print_r($daystring);
			
			/*$session =& JFactory::getSession();
			$session->set('statsfor_line_day_str_final', $day_str_final);
			$session->set('statsfor_line_imprs', $imprs);
			$session->set('statsfor_line_clicks', $clicks);*/
			// Firstly, format the provided dates.  
			  // This function works best with YYYY-MM-DD  
			  // but other date formats will work thanks  
			  // to strtotime().  

			
		}//loop end foe else if imprs and clicks are not present
		$backdate =$socialads_from_date;
		$kk=0;
		$chm=array();
		foreach($ignorecnt as $ignorecnt1)
		{
		//$datedifference=(strtotime($ignorecnt1->idate)-strtotime($backdate))/(60 * 60 * 24);
		$start_ts = strtotime($backdate);
		$end_ts = strtotime($ignorecnt1->idate);
		$diff = $end_ts - $start_ts;
		
		$ignorecnt_day_diff	=abs($diff / 86400);
		$ignorecnt=$ignorecnt1->ignorecount;
		
		$ignorecnt_day_diff=$ignorecnt_day_diff-1;
		
		$chm[]='A'.$ignorecnt.',FF9900,0,'.$ignorecnt_day_diff.',15';
		//chm=A2,FF9900,0,10,15
		$kk++;
		}
		$chm_str=implode('|',$chm);
		
		//////////////////
		
				$clicks_pie = 0;
			  	$imprs_pie = 0;
			  	$currentmonth='';
			  		//print_r($statsforbar);
			  	//print_r($statsforbar[1]);
			  	
			if(empty($statsforpie[0]) && empty($statsforbar[1]))
			{
				echo "";
			}
			else
			{
			  	if(!empty($statsforpie[1]))
				{
					for($z = 0 ; $z < count($statsforpie[1]); $z++)
					{
						$clicks_pie= $statsforpie[1][$z]->value;
						$currentmonth=$statsforpie[1][$z]->month;
					}		 
			  	}
			  	
			 // echo "clk=";echo $clicks;
				if(!empty($statsforpie[0]))
				{
					for($z = 0 ; $z < count($statsforpie[0]); $z++)
					{
						$imprs_pie = $statsforpie[0][$z]->value;
						$currentmonth=$statsforpie[0][$z]->month;
					}	
				}			  	    
			  	 if($currentmonth)
				$currentmonth = $month_array_name[$currentmonth-1];						  
			}
		
		$emptylinechart=0;
		
		if(!($imprs) and !($clicks))
		{
			$barchart=JText::_('NO_STATS');
			$emptylinechart=1;
		}

			header('Content-type: application/json');
		  	echo (json_encode(array("barchart"=>$barchart,
		  						"clicks_pie"=>$clicks_pie,
		  						"imprs_pie"=>$imprs_pie,
		  						"linechart_imprs"=>$finalstats_imprs,
		  						"linechart_clicks"=>$finalstats_clicks,
		  						"linechart_day_str"=>$day_str_final,
		  						"emptylinechart"=>$emptylinechart
		  						)));
		  	
		  	jexit();
	}
	

	
}// controller class ends here	

