<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class socialadsControllerDashboard extends socialadsController
{
	function __construct() 	{
		
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add' , 'edit' );
	}

	function save() {
		
		$model = $this->getModel('dashboard');
		$post	= JRequest::get('post');
		
		$model->setState( 'request', $post );
		if ($model->store()==1) {
			$msg = JText::_( 'FIELD_SAVING_MSG' );
		} elseif($model->store()==3) {
			$msg = JText::_( 'REFUND_SAVING_MSG' );
		} else {
			$msg = JText::_( 'FIELD_ERROR_SAVING_MSG' );
		}
		
		$link = 'index.php?option=com_socialads&view=adorders';
		$this->setRedirect($link, $msg);
	}
	
	function cancel() {
		
		$msg = JText::_( 'FIELD_CANCEL_MSG' );
		$this->setRedirect( 'index.php?option=com_socialads', $msg );
	}
	
function SetsessionForGraph()
	{
		$periodicorderscount='';
	 	$fromDate =  $_GET['fromDate'];
	 	$toDate =  $_GET['toDate'];
		$periodicorderscount=0;
		
		$session = JFactory::getSession();
		$session->set('socialads_from_date', $fromDate);
		$session->set('socialads_end_date', $toDate);
		
		$model = $this->getModel('dashboard');		
		$statsforpie=$model->statsforpie();
		$ignorecnt=$model->getignoreCount();
		$periodicorderscount=$model->getperiodicorderscount();
		$session->set('statsforpie', $statsforpie);
		$session->set('ignorecnt', $ignorecnt);
		$session->set('periodicorderscount', $periodicorderscount);
		
		header('Content-type: application/json');
	  	echo (json_encode(array("statsforpie"=>$statsforpie,
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
		$periodicorderscount=$session->get('periodicorderscount','0.00');
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
		$emptylinechart=0;
		$barchart='';
		$fromDate= $session->get('socialads_from_date', '');
		$toDate=$session->get('socialads_end_date', '');
	 
		$dateMonthYearArr = array();
		$fromDateSTR = strtotime($fromDate);
		$toDateSTR = strtotime($toDate);
		$pending_orders=$confirmed_orders=$refund_orders=0;
		
			if(empty($statsforpie[0]) && empty($statsforpie[1]) && empty($statsforpie[2]))
			{
				$barchart=JText::_('NO_STATS');
				$emptylinechart=1;
			}
			else
			{
			  	if(!empty($statsforpie[0]))
				{
					
						$pending_orders= $statsforpie[0];
				}
			  	
				if(!empty($statsforpie[1]))
				{
					
						$confirmed_orders = $statsforpie[1];
				}	

				if(!empty($statsforpie[1]))
				{
					
						$refund_orders = $statsforpie[2];												
				}	
			  	 				  
			}
			//$barchart='<img src="http://chart.apis.google.com/chart?cht=lc&chtt=+'.$titlebar.'|'.JText::_('NUMICHITSMON').'  	+&chco=0000ff,ff0000&chs=900x310&chbh=a,25&chm='.$chm_str.'&chd=t:'.$imprs.'|'.$clicks.'&chxt=x,y&chxr=0,0,200&chds=0,'.$max_invite.',0,'.$cmax_invite.'&chxl=1:|'.$yscale.'|0:|'. $daystring.'|" />';
			header('Content-type: application/json');
		  	echo (json_encode(array("pending_orders"=>$pending_orders,
		  						"confirmed_orders"=>$confirmed_orders,
		  						"refund_orders"=>$refund_orders,
		  						"periodicorderscount"=>$periodicorderscount,
		  						"emptylinechart"=>$emptylinechart
		  						)));
		  	
		  	jexit();
	}
}
