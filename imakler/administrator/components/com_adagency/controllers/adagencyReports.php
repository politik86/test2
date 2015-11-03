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

class adagencyAdminControlleradagencyReports extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "listReports");
		$this->registerTask ("overview", "overview");
		$this->registerTask ("advertisers", "advertisers");
		$this->registerTask ("campaigns", "campaigns");
		$this->registerTask ("overview_csv", "overviewCSV");
		$this->registerTask ("advertisers_csv", "advertisersCSV");
		$this->registerTask ("campaigns_csv", "campaignsCSV");
		$this->registerTask ("overview_pdf", "overviewPDF");
		$this->registerTask ("advertisers_pdf", "advertisersPDF");
		$this->registerTask ("campaigns_pdf", "campaignsPDF");
		
		$this->_model = $this->getModel("adagencyReports");
	}

	function listReports() {
		$view = $this->getView("adagencyReports", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}
	
	function overview(){
		$view = $this->getView("adagencyReports", "html");
		$view->setLayout("overview");
		$view->setModel($this->_model, true);
		$view->overview();
	}
	
	function campaigns(){
		$view = $this->getView("adagencyReports", "html");
		$view->setLayout("campaigns");
		$view->setModel($this->_model, true);
		$view->campaigns();
	}
	
	function advertisers(){
		$view = $this->getView("adagencyReports", "html");
		$view->setLayout("advertisers");
		$view->setModel($this->_model, true);
		$view->advertisers();
	}
	
	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyLanguages", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->editForm();
	}
	
	function creat () { 
		$view = $this->getView("adagencyReports", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}
	
	function emptyrep () { 
		if ($this->_model->emptyrep() ) {
			$msg = JText::_('ADAG_EMPTYSUC');
		} else {
			$msg = JText::_('ADAG_EMPTYFAIL');
		}
		$link = "index.php?option=com_adagency&controller=adagencyReports";
		$this->setRedirect($link, $msg);
	}

	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('LANGSAVED');
		} else {
			$msg = JText::_('LANGSAVEFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyLanguages";
		$this->setRedirect($link, $msg);
	}

	function upload() {
		$msg = $this->_model->upload();
		$link = "index.php?option=com_adagency&controller=adagencyLanguages";
		$this->setRedirect($link, $msg);
	}

	function remove() {
		if (!$this->_model->delete()) {
			$msg = JText::_('LANGREMERROR');
		} else {
		 	$msg = JText::_('LALNGREMSUCC');
		}
		$link = "index.php?option=com_adagency&controller=adagencyLanguages";
		$this->setRedirect($link, $msg);
	}

	function cancel() {
	 	$msg = JText::_('LANGCANCELED');	
		$link = "index.php?option=com_adagency&controller=adagencyLanguages";
		$this->setRedirect($link, $msg);
	}

    function compdata() {
        $this->_model->compdata();
        die();
    }

    function compress() {
        $view = $this->getView("adagencyReports", "html");
        $view->setLayout("compress");
        $view->compress();
    }
    
	function publish() {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('LANGPUBLICHERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('LANGUNPUBSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('LANGPUBSUCC');
		} else {
                 	$msg = JText::_('LANGUNSPECERROR');
		}
		$link = "index.php?option=com_adagency&controller=adagencyLanguages";
		$this->setRedirect($link, $msg);
	}
	
	function overviewCSV(){
		$this->_model->overviewCSV();
	}
	
	function advertisersCSV(){
		$this->_model->advertisersCSV();
	}
	
	function campaignsCSV(){
		$this->_model->campaignsCSV();
	}
	
	function overviewPDF(){
		$this->_model->overviewPDF();
	}
	
	function advertisersPDF(){
		$this->_model->advertisersPDF();
	}
	
	function campaignsPDF(){
		$this->_model->campaignsPDF();
	}
};
?>
