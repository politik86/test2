 <?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 



class TableadagencyModules extends JTable {
	var $id = null;
	var $title = null;
	var $published = null;
	var $module = null;
	var $ordering = null;
	var $position = null;
	var $showtitle = null;
	var $params = null;

	function TableadagencyModules (&$db) {
		parent::__construct('#__modules', 'id', $db);
	}

};

?>