<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );


/**
 * buildad Table class
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class TableBuildad extends JTable
{

	var $adfields_id = null;	
	var $adfield_ad_id=null;
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableBuildad (& $database) {
		parent::__construct('#__ad_fields', '$adfiels_id', $database);
	}
}
?>
