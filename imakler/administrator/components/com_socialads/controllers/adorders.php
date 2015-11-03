<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class socialadsControllerAdorders extends socialadsController
{
	function __construct() 	{

		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add' , 'edit' );
	}

	function save() {

		$model = $this->getModel('adorders');
		$post	= JRequest::get('post');

		$model->setState( 'request', $post );
		$ret = $model->store();
		if ($ret==1) {
			$msg = JText::_( 'FIELD_SAVING_MSG' );
		}
		elseif($ret == 3)
		{
			$msg = JText::_( 'REFUND_SAVING_MSG' );
		}
		elseif($ret == 4)
		{
			$msg = JText::_( 'SA_CANCEL_SAVING_MSG' );
		}
		else
		{
			$msg = JText::_( 'FIELD_ERROR_SAVING_MSG' );
		}

		$link = 'index.php?option=com_socialads&view=adorders';
		$this->setRedirect($link, $msg);
	}
		//export ads payment stats into a csv file
	function payment_csvexport(){
		$db = JFactory::getDBO();
		$query = "SELECT d.ad_id, d.ad_title, d.ad_payment_type, d.ad_creator,d.ad_startdate, d.ad_enddate, i.processor, i.ad_credits_qty, i.cdate, i.ad_amount,i.status,i.id FROM #__ad_data AS d RIGHT JOIN #__ad_payment_info AS i ON d.ad_id = i.ad_id";
		$db->setQuery($query);
		$results = $db->loadObjectList();
		$csvData = null;
        $csvData.= "Order_Id,Ad_Id,Ad_Title,Payment_Type,Owner,Payment_Gateway,Order_Date,Amount,Status";
        $csvData .= "\n";
        $filename = "SAPayment_".date("Y-m-d_H-i",time());
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m") .".csv");
        header("Content-disposition: filename=".$filename.".csv");
        foreach($results as $result ){
        	if( ($result->ad_id ) && ($result->id) ){
	       		$csvData .= '"'.$result->id.'"'.','.'"'.$result->ad_id.'"'.','.'"'.( $result->ad_title == '' ? JText::_('IMGAD') : $result->ad_title ).'"'.',';

	       		switch($result->ad_payment_type)
				 {
				 	case 0 :
						$csvData .= '"'. JText::_('IMPRS').'"'.',';
					break;
					case 1 :
				 		$csvData .= '"'.JText::_('CLICKS').'"'.',';
			 		break;
			 		case 2 :
			 			$csvData .= '"'. JText::_('PERDATE').'"'.',';
			 		break;
				 }
				$csvData .= '"'.JFactory::getUser($result->ad_creator)->username.'"'.','.'"'.$result->processor.'"'.','.'"'.$result->cdate.'"'.','.'"'.$result->ad_amount.'"'.',';
        		switch($result->status)
				 {
				 		case 'P' :
				 			$csvData .= '"'.JText::_('SA_PENDIN').'"'.',';
				 		break;
				 		case 'C' :
				 			$csvData .= '"'.JText::_('SA_CONFR').'"'.',';
				 		break;
				 		case 'RF' :
				 			$csvData .= '"'. JText::_('SA_REFUN').'"'.',';
				 		break;
				 }
				$csvData .= "\n";
        	}
        }
		print $csvData;
	exit();
	}

	function cancel() {

		$msg = JText::_( 'FIELD_CANCEL_MSG' );
		$this->setRedirect( 'index.php?option=com_socialads', $msg );
	}
}
