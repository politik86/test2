<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class socialadsControllerImportfields extends socialadsController
{
	/**
	 * save a ad fields 
	 */
	function save()
	{
	    global $fieldtype;
	    $input=JFactory::getApplication()->input;
		$jAp= JFactory::getApplication();
		if ($_POST['check']!=JSession::getFormToken()) {
			$link = 'index.php?option=com_socialads&view=importfields';
			$this->setRedirect($link);
		   	return false;
		}

	    $db = JFactory::getDBO();
		$model = $this->getModel('importfields');
		$importfields = $input->get('$data');
		
		if ($model->store()) {
			$msg = JText::_('IMPORT_FIELDS_SAVING_MSG');
		} else {
			$msg = JText::_('IMPORT_FIELDS_ERROR_SAVING_MSG');
		}
		//redirect to importfields
		$link = 'index.php?option=com_socialads&view=importfields';
		$this->setRedirect($link, $msg);
	}//function save ends here

	/**
	 * cancel editing a ad fields
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'FIELD_CANCEL_MSG' );
		$this->setRedirect( 'index.php?option=com_socialads', $msg );
	}
    function getTable(){ 
    	return JTable::getInstance('importfields', 'Table'); 
    }
	
	function addcolumn()
	{
		$input=JFactory::getApplication()->input;
	$model = $this->getModel('importfields');
	$model->create_ad_fields();
		$field_array=array();
		$img_ERROR='';
		$col_name = $input->get('col_name','','STRING');
		//$xml = JFactory::getXML('Simple');
		$currentversion = '';
		//Load the xml file
		if(JVERSION >= '1.6.0')
		{
		 $vfile1=JPATH_SITE."/plugins/socialadstargeting/$col_name/$col_name.xml";
		}
		else
		{
		 $vfile1=JPATH_SITE."/plugins/socialadstargeting/$col_name.xml";
		}
		$xml = JFactory::getXML($vfile1);
		$col_value = array();
		if($xml)
		{
			//FIXED FOR #29045..BUT NEEDS TO BE VERIFY
			$xml = json_decode(json_encode((array)$xml), TRUE);
			foreach($xml as $key=>$var)
			{
				if($key == 'satargeting')
				{
					foreach ($var as $minikey=>$val)
					{
						if($minikey =='plgfield')
						{
							//$val1=(array)($val);
							$col_value[] = $val; //FIXED FOR #29045..BUT NEEDS TO BE VERIFY
						}
					}
				}
			}
		}
		if(!empty($col_value))
		{
			$db= JFactory::getDBO();
			$query = "SHOW COLUMNS FROM `#__ad_fields`";
			$db->setQuery($query);
			$columns = $db->loadobjectlist();
			$z = array();
			for ($i = 0; $i < count($columns); $i++) {
						$field_array[] = $columns[$i]->Field;
			}
			foreach ($col_value as $field_value)
			{ 
				if (!in_array($field_value, $field_array)) {
					$field 	=$this->getTable();
					$field->mapping_fieldid = 0;
					$field->mapping_fieldtype = 'targeting_plugin';
					$field->mapping_label 	= $field_value;
			 		$field->mapping_match 	= 2;
			 		$field->mapping_fieldname = $field_value;
			 		if (!$field->store()) 
					{
						$this->setError( $this->_db->getErrorMsg() );
						return false;
					}	
					$query="ALTER TABLE #__ad_fields  ADD $field_value VARCHAR(255) NOT NULL";
					$db->setQuery($query);
					if(!$db->execute() )
					{
						echo $img_ERROR.JText::_('Unable to Alter ad_data table').$BR;
						echo $db->getErrorMsg();
						$z = array("inmessage"=>'false',"smessage"=>JText::_("TARG_UNSUS"));
					}
					else
					$z = array("inmessage"=>'true',"smessage"=>JText::_("TARG_SUS"));
					break;
				}
			}
		
		}
		
		echo json_encode($z);
		jexit();
	}
}
?>
