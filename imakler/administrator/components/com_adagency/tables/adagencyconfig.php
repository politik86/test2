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



class TableadagencyConfig extends JTable {
	var $id = null;
	var $lastsend = null;
	var $adminemail = null;
	var $fromemail = null;
	var $fromname = null;
	var $imgfolder = null;
	var $maxchars = null;                       
	var $allowstand = null;
	var $allowadcode = null;
	var $allowpopup = null;
	var $allowswf = null;
	var $allowtxtlink = null;
	var $allowtrans = null;
	var $allowfloat = null;
	var $allowsocialstream = null;
	var $txtafterreg = null;
	var $bodyafterreg = null;
	var $sbafterreg = null;
	var $bodyactivation = null;
	var $sbactivation = null;
	var $bodyrep = null;
	var $sbrep = null;
	var $bodycmpappv = null;
	var $sbcmpappv = null;
	var $bodycmpdis = null;
	var $sbcmpdis = null;
	var $bodyadappv = null;
	var $sbadappv = null;
	var $bodyaddisap = null;
	var $sbaddisap = null;
	var $bodyadvdis = null;
	var $sbadvdis = null;
	var $bodynewad = null;
	var $sbnewad = null;
	var $bodynewcmp = null;
	var $sbnewcmp = null;
	var $bodycmpex = null;
	var $sbcmpex = null;
	var $bodynewuser = null;
	var $sbnewuser = null;
	var $currencydef = null;
	var $askterms = null;
	var $termsid = null;
	var $overviewcontent = null;
	var $captcha = null;
	var $showpreview = null;
	var $show = null;
	var $mandatory = null;
	var $params = null;
	var $sbafterregaa = null;
	var $bodyafterregaa = null;
	var $countryloc = null;
	var $cityloc = null;
	var $codeloc = null;
	var $limit_ip = null;
	var $payment = null;
	var $bodycmpexpadm = null;
	var $sbcmpexpadm = null;
	var $jomfields = null;
	var $allow_add_keywords = null;
	var $imagetools = null;
	var $sbadchanged = null;
	var $boadchanged = null;
	var $showpromocode = null;

	function TableadagencyConfig (&$db) {
		parent::__construct('#__ad_agency_settings', 'id', $db);
		$sql = "SELECT COUNT(*) FROM #__ad_agency_settings WHERE id=1";
		$db->setQuery($sql);
		$c = $db->loadResult();
		if ($c < 1) {
			$sql = "INSERT INTO #__ad_agency_settings(`id`) VALUES (1)";
			$db->setQuery($sql);
			$db->query();
		}
	}

};

?>