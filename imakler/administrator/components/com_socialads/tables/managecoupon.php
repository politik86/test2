<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );


/**
 * buildad Table class
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class TableManagecoupon	 extends JTable
{

	var $id 			= 0;	
	var $published		=null;
	var $name			=null;
	var $code 			=null;
	var $value 			=null;
	var $val_type		=null;
	var $max_use		=null;
	var $max_per_user 	=null;
	var $description 	=null;
	var $params  		=null;
	var $from_date   	=null;
	var $exp_date   	=null;
	
		
	 					 					
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableManagecoupon (& $database) {
		parent::__construct('#__ad_coupon', 'id', $database);
	}
}
?>
