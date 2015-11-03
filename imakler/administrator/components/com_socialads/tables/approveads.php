<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );


/* importfields Table class */
class TableApproveads extends JTable
{
	//var $ad_id = null;
	//var $ad_creator=null;
	//var $ad_title=null;
	//var $ad_approved=null;
	//var $mapping_fieldname=null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableApproveads (& $db) 
	{
		parent::__construct('#__ad_data', 'ad_id', $db);
	}
}
?>
