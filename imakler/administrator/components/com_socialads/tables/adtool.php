<?php
/**
 * @version $Id: header.php 248 2008-05-23 10:40:56Z elkuku $
 * @package		adtool
 * @subpackage	
 * @author		EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author		Tekdi Web Solutions {@link http://www.nik-it.de}
 * @author		Created on 05-Mar-2010
 */

// no direct access
defined( '_JEXEC' ) or die( ';)' );


/**
 * adtool Table class
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class Tableadtool extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	/**
	 * @var string
	 */
	var $greeting = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function Tableadtool(& $db) {
		parent::__construct('#__adtool', 'id', $db);
	}
}
?>
