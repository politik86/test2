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

class adagencyAdminControlleradagencyAdvertisers extends adagencyAdminController {
	var $model = null;

	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("addadv", "wizard");
		$this->registerTask ("", "listAdvertisers");
		$this->registerTask ("approve_task", "approveAction");
		$this->registerTask ("decline_task", "declineAction");
		$this->_model = $this->getModel('adagencyAdvertiser');
	}

	function listAdvertisers() {
		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function wizard() {
		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setLayout("wizard");
		$view->setModel($this->_model, true);
		$view->wizard();
	}

	function temp(){
		$data = JRequest::get('post');
		if(isset($data['tmpl'])&&($data['tmpl'] == 'component')) {
			JRequest::setVar('tmpl','component','GET');
		}
		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setLayout("temp");
		$view->temp();
	}

	function ajax_adv(){
		$get_aid = JRequest::getInt('aid','0','get');
		$sendmail = JRequest::getInt('sendmail','1','get');
		if($get_aid != '0'){
			$cid = NULL;
			$cid[] = $get_aid;
			$ok = $this->_model->approve('Y',$cid, $sendmail);
		}
		if($ok){
			echo "ok";
		} else {
			echo "Error!";
		}
		die();
	}

	function existing(){
		$data = JRequest::get('post');
		if(isset($data['tmpl'])&&($data['tmpl'] == 'component')) {
			JRequest::setVar('tmpl','component','GET');
		}
		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setLayout("existing");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$model2 = $this->getModel("adagencyPlugin");
		$view->setModel($model2);
		$view->existing();
	}

	function edit () {
		$data = JRequest::get('post');
		if(isset($data['tmpl'])&&($data['tmpl'] == 'component')) {
			JRequest::setVar('tmpl','component','GET');
		}
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyAdvertisers", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$model2 = $this->getModel("adagencyPlugin");
		$view->setModel($model2);
		$view->editForm();
	}

	function getAdvertisersAjax(){
		$this->_model->getAdvertisersAjax();
	}

	function provinces() {
		$this->_model->getProvinces();
	}

	function save () {
		$error = "";
		if ($this->_model->store($error) ) {
			$msg = JText::_('ADVSAVED');
		} else {
			$msg = JText::_('ADVSAVEFAILED');
			$msg .= $error;
		}
		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg);
	}

	function save_graybox (){
		$data = JRequest::get('post');
		$get = JRequest::get('post');
		$error = "";

		if(isset($get['gb_aid'])){
			$id = intval($get['gb_aid']);
		} elseif(!isset($data['gb_existing'])) {
			$id = $this->_model->store($error);
		} else {
			$this->_model->storeExistent();
			$id = $this->_model->getLastAdvertiser();
		}
		echo '<div style="font-weight: bold; font-size: 18px; text-align:center;">'.JText::_('ADVSAVED').'</div>';
		echo "<script type='text/javascript'>
				window.onload = gb_fcs;
			 	function gb_fcs(){
					window.parent.refreshAdv('".intval($id)."');
					window.setTimeout(function(){
						window.parent.document.getElementById('close_domwin').click();
					},1500);
				}
			 </script>";
		die();
	}

	function storeExistent () {
		if ($this->_model->storeExistent() ) {
			$msg = JText::_('ADVSAVED');
		} else {
			$msg = JText::_('ADVSAVEFAILED_EXST');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg);
	}

	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('ADVENTREMODEFAILED');
		} else {
		 	$msg = JText::_('ADVENTREMODESUCC');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg);
	}

	function cancel () {
	 	$msg = JText::_('ADVCANCELED');
		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg);
	}
	
	function approveAction(){
		$type = "message";
		if($this->_model->approveAction()){
			$msg = JText::_('ADAG_APPROVED_SUCCESSFULLY');
		}
		else{
			$type = "error";
			$msg = JText::_('ADAG_APPROVED_UNSUCCESSFULLY');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg, $type);
	}
	
	function declineAction(){
		$type = "message";
		if($this->_model->declineAction()){
			$msg = JText::_('ADAG_DECLINE_SUCCESSFULLY');
		}
		else{
			$type = "error";
			$msg = JText::_('ADAG_DECLINE_UNSUCCESSFULLY');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg, $type);
	}

	function approve () {
		$res = $this->_model->approve();
		if (!$res) {
			$msg = JText::_('ADVUNSPECERROR');
		} else {
           	$msg = JText::_('ADVAPPROVED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg);
	}

	function unapprove () {
		$res = $this->_model->approve();
		if (!$res) {
			$msg = JText::_('ADVUNSPECERROR');
		} else {
           	$msg = JText::_('ADVUNAPPROVED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg);
	}

	function pending() {
		$res = $this->_model->approve();

		if (!$res) {
			$msg = JText::_('ADVUNSPECERROR');
		} else {
           	$msg = JText::_('ADAG_PENMSG');
		}

		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg);
	}

	function block () {
		$res = $this->_model->block();
		if (!$res) {
			$msg = JText::_('ADVUNSPECERROR');
		} else {
           	$msg = JText::_('ADVLOCKED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg);
	}

	function unblock () {
		$res = $this->_model->block();
		if (!$res) {
			$msg = JText::_('ADVUNSPECERROR');
		} else {
           	$msg = JText::_('ADVUNLOCKED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAdvertisers";
		$this->setRedirect($link, $msg);
	}

	public function saveOrderAjax(){
		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("adagencyAdvertiser");
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
