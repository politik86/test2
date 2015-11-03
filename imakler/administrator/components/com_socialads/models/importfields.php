<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
//require(JPATH_SITE.DS."components".DS."com_socialads".DS.'helper.php');
//define('CONFIG_FILE', JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

class socialadsModelImportfields extends JModelLegacy
{
	/* Constructor that retrieves the ID from the request*/
	function __construct()
	{
		parent::__construct();
		$input=JFactory::getApplication()->input;
		$array = $input->get('cid',0,'ARRAY');
		$this->setId((int)$array[0]);
	}

	/* Method to set the import fields id*/
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	//function to check addata
	function getAdData()
	{
		$query = "SELECT count(*) FROM #__ad_data";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	function getcolfields()
	{
		$db = JFactory::getDBO();
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
					. "ad_fields'";
		$db->setQuery( $query );
		$coltable = $db->loadResult();
		if (!empty($coltable))
		{
			$query = "SHOW COLUMNS  FROM #__ad_fields";
			$this->_db->setQuery($query);
			return $this->_db->loadobjectList();
		}
		else {
			return $return;
		}
	}
//function to check pluginsdata
	function getPluginData()
	{
		$condtion = array(0 => '\'socialadstargeting\'');
		$condtionatype = join(',',$condtion);
		if(JVERSION >= '1.6.0')
		{
		$query = "SELECT extension_id as id,name,element,enabled as enabled FROM #__extensions WHERE folder in ($condtionatype)";
		}
		else
		{
		$query = "SELECT id as id,name,element,published as enabled FROM #__plugins WHERE folder in ($condtionatype)";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadobjectList();

	}
	function updatePluginData($data)
	{
		for($count=0; $count<count($data['plugin']); $count++)
		{
			$chk=0;
			if(isset($data['pluginchk'][$count]))
			{
				if($data['pluginchk'][$count] == "on")
					$chk=1;
				else
					$chk=0;
			}
			$res = new stdClass();
			$pluginname="";
			$pk="";
			if(JVERSION >= '1.6.0')
			{
				$pluginname='#__extensions';
				$pk='extension_id';
				$res->extension_id = str_replace("plugin", "", $data['plugin'][$count]);
				$res->enabled = $chk;
			}
			else
			{
				$pluginname='#__plugins';
				$pk='id';
				$res->id = str_replace("plugin", "", $data['plugin'][$count]);
				$res->published = $chk;
			}
			if(!$this->_db->updateObject( $pluginname, $res, $pk ))
				{
							echo $this->_db->stderr();
							return false;
				}

		}

		return true;

	}

	//get Easy social fields
	function _getESFields()
	{
		//TODO: Use ignore field list from defines
		$socialadshelper = new socialadshelper();
		$eschk = $socialadshelper->eschk();

		if(!empty($eschk)){

			//relationship not working
			$query="SELECT element FROM #__social_apps WHERE element IN ('boolean','checkbox','birthday','calendar','birthday','country','datetime','dropdown','email','gender','multilist','textarea','textbox','multidropdown','url')";
			$this->_db->setQuery($query);
			$socialtypes= $this->_db->loadColumn();

			$socialtypes=implode("','",$socialtypes);

				$qry = "SELECT m.*, f.title AS field_label, f.unique_key as mapping_fieldname,a.element AS type, f.id AS id
						FROM #__social_fields AS f
						LEFT JOIN #__ad_fields_mapping AS m ON f.id = m.mapping_fieldid
						LEFT JOIN #__social_apps AS a ON a.id=f.app_id
						WHERE f.state = 1
						AND a.type = 'fields'
						AND a.element IN ('".$socialtypes."')
						ORDER BY f.id
						";

				$this->_db->setQuery($qry);

				$steps= $this->_db->loadobjectList();

		}//empty check
		//print_r($steps); die('asdasd');
		return $steps;
	}

	function _getJSFields()
	{
		//TODO: Use ignore field list from defines
		$socialadshelper = new socialadshelper();
		$jschk = $socialadshelper->jschk();

		if(!empty($jschk)){
		$qry = "SELECT m.*, f.name AS field_label, f.fieldcode as mapping_fieldname, f.type, f.id AS id
						FROM #__community_fields AS f
						LEFT JOIN #__ad_fields_mapping AS m ON f.id = m.mapping_fieldid
						WHERE f.type <> 'group' AND f.published = 1
						ORDER BY f.id
						";

		$this->_db->setQuery($qry);

		return $this->_db->loadobjectList();
		}//empty check
	}

	function _getCBFields()
	{
		//TODO: Use plugin id field list from defines
		$socialadshelper = new socialadshelper();
		 $cbchk = $socialadshelper->cbchk();

		if(!empty($cbchk)){
		$qry = "SELECT m.*, f.title AS field_label, f.name AS mapping_fieldname, f.type, f.fieldid AS id
						FROM #__comprofiler_fields AS f
						LEFT JOIN #__ad_fields_mapping AS m ON f.fieldid = m.mapping_fieldid
						WHERE f.published = 1 AND f.sys <> 1	";

		$this->_db->setQuery($qry);

		return $this->_db->loadobjectList();
		}//empty check
	}

	//function for getting jomsocial fields
	function getImportFields()
	{

		include(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		if ($socialads_config['integration'] == 0)
			$socialads_config['integration']="CB";
		else if($socialads_config['integration'] == 1)
				$socialads_config['integration']="JS";
		else if($socialads_config['integration'] == 3)
				$socialads_config['integration']="ES";

		switch ($socialads_config['integration'])
		{
			case 'CB':
			return $this->_getCBFields();
			break;

			case 'JS':
			return $this->_getJSFields();
			break;

			case 'ES':
			return $this->_getESFields();
			break;

		}
		return;

	}// function getImportFields ends here


	function getFieldOptions($type='text')
	{
		include(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		if ($socialads_config['integration'] == 0)
			$socialads_config['integration']="CB";
		else
				$socialads_config['integration']="JS";
		switch ($socialads_config['integration'])
		{
			case 'CB':
			return $this->_getCBOptions($type);
			break;

			case 'JS':
			return $this->_getJSOptions($type);
			break;
		}

	}

	function _getJSOptions($type)
	{
		switch ($type)
		{
			case 'text':
			case 'textarea':
			case 'email':
			case 'url':
			return 'mappinglistt';
			break;

			case 'date':
			case 'time':
			case 'integer':
			case 'birthdate';
			return 'mappinglistd';
			break;

			default:
			return 'mappinglists';
			break;
		}

	}

	function _getCBOptions($type)
	{
		switch ($type)
		{
			case 'text':
			case 'textarea':
			case 'editorta';
			case 'emailaddress':
			case 'webaddress':
			return 'mappinglistt';
			break;

			case 'date':
			case 'time':
			return 'mappinglistd';
			break;

			default:
			return 'mappinglists';
			break;
		}

	}
	function create_ad_fields(){
		$query= "CREATE TABLE IF NOT EXISTS `#__ad_fields` (
			  `adfield_id` int(11) NOT NULL auto_increment,
			  `adfield_ad_id` int(11) NOT NULL,
			  PRIMARY KEY  (`adfield_id`)
			) ENGINE=MyISAM";
			$this->_db->setQuery($query);
			$this->_db->execute();
			return;
	}
	//function store importfields starts here
	function store()
	{
		$data 	= JRequest::get( 'post' );
    //print_r($data);	die;

		$this->updatePluginData($data);

		if($data['resetall']==1)
		{
			$query= "DROP TABLE IF EXISTS `#__ad_fields`";
			$this->_db->setQuery($query);
			$this->_db->execute();

			$query= "TRUNCATE TABLE `#__ad_fields_mapping`";
			$this->_db->setQuery($query);
			$this->_db->execute();

			return true;
		}
		if($data['boxchecked']==0)
		{
			//return false;
		}
		$i=0;

		$iarray	= array();
		$addcol ='';

		foreach($data['mappinglist'] as $mapping)
		{

			// Prepare import fields for saving into ad_fields_mapping table
			$field 	= $this->getTable('importfields');

			$field->mapping_fieldid = $mapping['fieldid'];
			$field->mapping_fieldtype = $mapping['fieldtype'];

			if(!$mapping['fieldtype'])
				continue;

			$field->mapping_label 	= $mapping['label'];
	 		$field->mapping_options	= $mapping['options'];
	 		$field->mapping_match 	= $data['match'][$mapping['fieldid']];
			//preg_replace('/\s*[^a-zA-Z0-9]+\s*/i', '_', $mapping['name'])
			$fieldname = strtolower(preg_replace('/\s*[^a-zA-Z0-9]+\s*/i', '_', $data['mappinglist'][$mapping['fieldid']]['fieldcode']));
			if($mapping['fieldtype'] == "numericrange")
			{
				$addcol = " add ".$fieldname.'_low'." int(11), add ".$fieldname.'_high'." int(11) ";
				$field->mapping_fieldname = $fieldname;
			}
			else if($mapping['fieldtype'] == "daterange")
			{
				$addcol = " add ".$fieldname.'_low'." datetime, add ".$fieldname.'_high'." datetime ";
				$field->mapping_fieldname = $fieldname;
			}
			else if($mapping['fieldtype'] == "date")
			{
				$addcol = " add ".$fieldname." datetime ";
				$field->mapping_fieldname = $fieldname;
			}
			else if($mapping['fieldtype'] == "singleselect")
			{
				$addcol = " add ".$fieldname." text ";
				$field->mapping_fieldname = $fieldname;
		 		$iarray[]	= $fieldname;
			}
			else if($mapping['fieldtype'] == "multiselect")
			{
				$addcol = " add ".$fieldname." text ";
				$field->mapping_fieldname = $fieldname;
				$iarray[]	= $fieldname;
			}
			else
			{
				$addcol = " add ".$fieldname." text ";
				$field->mapping_fieldname = $fieldname;
				$iarray[]	= $fieldname;
			}

			// Store the table to the database
			if (!$field->store())
			{
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}

			$this->create_ad_fields();



			// write query to add the column
			$query = "ALTER table #__ad_fields $addcol ";
			$this->_db->setQuery($query);
			$this->_db->execute();

      if(($mapping['fieldtype'] != 'numericrange') && ($mapping['fieldtype'] != 'daterange'))
			{
			//write query to set data type to none
      $query = "ALTER TABLE `#__ad_fields` CHANGE `$fieldname` `$fieldname` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
      $this->_db->setQuery($query);
			$this->_db->execute();
			}

		}// end foreach

		return true;
	} // end function store()


} // end class
