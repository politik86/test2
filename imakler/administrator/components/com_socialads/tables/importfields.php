<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );


/* importfields Table class */
class TableImportfields extends JTable
{
	var $mapping_id = null;
	var $mapping_fieldid=null;
	var $mapping_fieldtype=null;
	var $mapping_label=null;
	var $mapping_fieldname=null;
	var $mapping_match=null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableImportfields (& $db) 
	{
		parent::__construct('#__ad_fields_mapping', 'mapping_id', $db);
	}
}
?>
