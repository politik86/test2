<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );


/**
 * buildad Table class
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class Tablemanagezone	 extends JTable
{

	var $id 			= 0;	
	var $zone_name		=null;
	var $zone_type		=null;
	var $max_title 		=null;
	var $max_des 		=null;
	var $img_width		=null;
	var $img_height		=null;
	var $per_click 		=null;
	var $per_imp 		=null;
	var $per_day  		=null;
	var $num_ads   		=null;
	var $layout   		=null;
	var $ad_type   		=null;
	var $published   	=null;
		
	 			
	 				 
	 					
	 					 	
	 					 		
	 					 			
	 					 				
	 					 					
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function Tablemanagezone (& $database) {
		parent::__construct('#__ad_zone', 'id', $database);
	}
}
?>
