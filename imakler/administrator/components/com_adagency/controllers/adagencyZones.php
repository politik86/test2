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

jimport ('joomla.application.component.controller');

class adagencyAdminControlleradagencyZones extends adagencyAdminController {
    var $_model = null;

    function __construct () {
        parent::__construct();
        $this->registerTask ("", "listZones");
        $this->_model = $this->getModel("adagencyZone");
        $this->registerTask ("unpublish", "publish");
    }

    function add() {
        global $mainframe;
        $mainframe->redirect(
            'index.php?option=com_adagency&controller=adagencyZones&task=edit&cid[]=0'
        );
    }
    function listZones() {
        $this->_model->cleanup();
        $view = $this->getView("adagencyZones", "html");
        $view->setModel($this->_model, true);
        $model = $this->getModel("adagencyConfig");
        $view->setModel($model);
        $view->display();
    }

    function edit () {
        JRequest::setVar ("hidemainmenu", 1);
        $view = $this->getView("adagencyZones", "html");
        $view->setLayout("editForm");
        $view->setModel($this->_model, true);
        $model = $this->getModel("adagencyConfig");
        $view->setModel($model);
        $view->editForm();
    }

    function save () {
        if ($nz = $this->_model->store() ) {
            $msg = JText::_('ZONESAVED');
        } else {
            $msg = JText::_('ZONEFAILED');
        }
        $session = JFactory::getSession();
        $new_zone_id = $session->get("newzone-{$nz}", NULL, 'adag');
        if ($new_zone_id) {
            $link = "index.php?option=com_adagency&controller=adagencyPackages&newzone=" . intval($nz);
            $this->setRedirect($link);
        } else {
            $link = "index.php?option=com_adagency&controller=adagencyZones";
            $this->setRedirect($link, $msg);
        }
    }

    function apply () {
        if ($this->_model->store() ) {
            $msg = JText::_('ZONESAVED');
        } else {
            $msg = JText::_('ZONEFAILED');
        }

        if(isset($_POST['zoneid'])&&($_POST['zoneid']!=0)) {$id=$_POST['zoneid'];} else {
            $id=$this->_model->getLastZoneId();
        }
        $link = "index.php?option=com_adagency&controller=adagencyZones&task=edit&cid[]=".intval($id);
        $this->setRedirect($link, $msg);
    }

    function duplicate (){
        if($this->_model->duplicate()){
            $msg = JText::_('ADAG_ZONE_DPLSC');
        } else {
            $msg = JText::_('ADAG_ZONE_DPLFAIL');
        }
        $link = "index.php?option=com_adagency&controller=adagencyZones";
        $this->setRedirect($link, $msg);
    }

    function remove () {
        if (!$this->_model->delete()) {
            $msg = JText::_('ZONEREMERR');
        } else {
            $msg = JText::_('ZONEREMSUCC');
        }
        $link = "index.php?option=com_adagency&controller=adagencyZones";
        $this->setRedirect($link, $msg);
    }

    function cancel () {
        $msg = JText::_('ZONECANCEL');
        $link = "index.php?option=com_adagency&controller=adagencyZones";
        $this->setRedirect($link, $msg);
    }

    function publish () {
        $res = $this->_model->publish();
        if (!$res) {
            $msg = JText::_('ZONEBLOCKERR');
        } elseif ($res == -1) {
            $msg = JText::_('ZONEUNPUBSUCC');
        } elseif ($res == 1) {
            $msg = JText::_('ZONEPPUBSUCC');
        } else {
            $msg = JText::_('ZONEUNSPEC');
        }
        $link = "index.php?option=com_adagency&controller=adagencyZones";
        $this->setRedirect($link, $msg);
    }

    function addzone () {
        JRequest::setVar ("hidemainmenu", 1);
        $view = $this->getView("adagencyZones", "html");
        $view->setLayout("editFormbox");
        $view->setModel($this->_model, true);
        $model = $this->getModel("adagencyConfig");
        $view->setModel($model);
        $view->editForm();
    }

	public function saveOrderAjax(){
		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("adagencyZone");
		// Save the ordering
		$return = $model->saveorder($pks, $order);
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}

};
?>
