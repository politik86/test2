<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class socialadsControllerApproveads extends socialadsController
{
	function __construct()
	{

		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function save()
	{

		$model = $this->getModel('approveads');
		$post	= JRequest::get('post');

		$model->setState( 'request', $post );
		if ($model->store()) {
			$msg = JText::_( 'FIELD_SAVING_MSG' );
		} else {
			$msg = JText::_( 'FIELD_ERROR_SAVING_MSG' );
		}

		$link = 'index.php?option=com_socialads&view=approveads';
		$this->setRedirect($link, $msg);
	}
	function updatezone()
	{
		$model = $this->getModel('approveads');
		$post	= JRequest::get('post');

		$model->setState( 'request', $post );
		if ($model->updatezone()) {
			$msg = JText::_( 'FIELD_SAVING_MSG' );
		} else {
			$msg = JText::_( 'FIELD_ERROR_SAVING_MSG' );
		}

		$link = 'index.php?option=com_socialads&view=approveads';
		$this->setRedirect($link, $msg);

	}
	//export ads stats into a csv file
	function ad_csvexport(){
		$db =& JFactory::getDBO();
		$query = "SELECT ad_id,ad_title,ad_alternative,ad_noexpiry,ad_payment_type,ad_creator,ad_zone FROM #__ad_data ";
		$db->setQuery($query);
		$results = $db->loadObjectList();//print_r($results); die('hhhi');
		$csvData = null;
        $csvData.= "Ad_Id,Ad_Title,Ad_Type,Owner,Zone_Name,Clicks,Impressions,CTR,Ignores";
        $csvData .= "\n";
        $filename = "SA_Ads_".date("Y-m-d_H-i",time());
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m") .".csv");
        header("Content-disposition: filename=".$filename.".csv");
        foreach($results as $result ){
        	//if($result->ad_id){
	       		$csvData .= '"'.$result->ad_id.'"'.','.'"'.trim(( $result->ad_title == '' ? JText::_('IMGAD') : $result->ad_title )).'"'.',';

        		if($result->ad_alternative== 1){
					$csvData .= '"'. JText::_('ALT_AD').'"'.',';
				}
				elseif($result->ad_noexpiry== 1){
							$csvData .= '"'. JText::_('UNLTD_AD').'"'.',';
				}
				else{
					if($result->ad_payment_type== 0){
							$csvData .= '"'. JText::_('IMPRS').'"'.',';
					}
					else if($result->ad_payment_type == 1){
							$csvData .= '"'. JText::_('CLICKS').'"'.',';
					}
					else{
							$csvData .= '"'. JText::_('PERDATE').'"'.',';
					}
				}
				$csvData .= '"'.JFactory::getUser($result->ad_creator)->username.'"'.',';
				$model = $this->getModel('approveads');
				$zone_name=$model->adzonename($result->ad_id,$result->ad_zone);
				if($zone_name)
				{
					$csvData .= '"'.$zone_name.'"'.',';
				}
				$clicks = $model->getAdtype($result->ad_id, 1);
				$impr = $model->getAdtype($result->ad_id, 0);
				if($impr!=0)
				{
					$ctr=($clicks)/($impr);
					 $ctr = number_format ($ctr, 2);
				}
				else
					 $ctr = number_format ($clicks, 2);
				$csvData .= '"'.$clicks.'"'.','.'"'.$impr.'"'.','.'"'.$ctr.'"'.','.'"'.$model->getIgnorecount($result->ad_id).'"'.',';
				$csvData .= "\n";

        }
		print $csvData;
	exit();
	}
	function cancel()
	{

		$msg = JText::_( 'FIELD_CANCEL_MSG' );
		$this->setRedirect( 'index.php?option=com_socialads', $msg );
	}




}
?>
