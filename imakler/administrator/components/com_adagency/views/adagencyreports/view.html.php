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

jimport ("joomla.application.component.view");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminViewadagencyReports extends JViewLegacy {
	
	function overview($tpl = null){
		JToolBarHelper::title(JText::_('AD_REPORTS'));
		
		$number_of_active_advertisers = $this->get("NumberOfActiveAdvertisers");
		$number_of_inactive_advertisers = $this->get("NumberOfInactiveAdvertisers");
		$number_of_active_campaigns = $this->get("NumberOfActiveCampaigns");
		$number_of_inactive_campaigns = $this->get("NumberOfInactiveCampaigns");
		$number_of_active_ads = $this->get("NumberOfActiveAds");
		$number_of_inactive_ads = $this->get("NumberOfInactiveAds");
		$revenue_earned_last_month = $this->get("RevenueEarnedLastMonth");
		$revenue_earned_this_month = $this->get("RevenueEarnedThisMonth");
		$most_paying_advertiser = $this->get("MostPayingAdvertiser");
		$highest_click_ratio_ad = $this->get("HighestClickRatioAd");
		$most_successful_campaign = $this->get("MostSuccessfulCampaign");
		$least_successful_campaign = $this->get("LeastSuccessfulCampaign");
		$currency = $this->get("Currency");
		$all_advertisers = $this->get("AllAdvertisers");
		$all_campaigns = $this->get("AllCampaignsByAdvTo");
		$table_content = $this->get("TableContent");
		$min = $this->get("MinReportDate");
		$max = $this->get("MaxReportDate");
		
		$pagination = $this->get('Pagination');
		$this->assignRef('pagination', $pagination);
		
		$this->assign("number_of_active_advertisers", $number_of_active_advertisers);
		$this->assign("number_of_inactive_advertisers", $number_of_inactive_advertisers);
		$this->assign("number_of_active_campaigns", $number_of_active_campaigns);
		$this->assign("number_of_inactive_campaigns", $number_of_inactive_campaigns);
		$this->assign("number_of_active_ads", $number_of_active_ads);
		$this->assign("number_of_inactive_ads", $number_of_inactive_ads);
		$this->assign("revenue_earned_last_month", $revenue_earned_last_month);
		$this->assign("revenue_earned_this_month", $revenue_earned_this_month);
		$this->assign("most_paying_advertiser", $most_paying_advertiser);
		$this->assign("highest_click_ratio_ad", $highest_click_ratio_ad);
		$this->assign("most_successful_campaign", $most_successful_campaign);
		$this->assign("least_successful_campaign", $least_successful_campaign);
		$this->assign("currency", $currency);
		$this->assign("all_advertisers", $all_advertisers);
		$this->assign("all_campaigns", $all_campaigns);
		$this->assign("table_content", $table_content);
		$this->assign("min", $min);
		$this->assign("max", $max);

		parent::display($tpl);
	}
	
	function advertisers($tpl = null){
		JToolBarHelper::title(JText::_('VIEWTREEADVERTISERS'));
		
		$number_of_active_campaigns = $this->get("NumberOfActiveCampaigns");
		$number_of_inactive_campaigns = $this->get("NumberOfInactiveCampaigns");
		$number_of_active_ads = $this->get("NumberOfActiveAds");
		$number_of_inactive_ads = $this->get("NumberOfInactiveAds");
		$revenue_earned_last_month = $this->get("RevenueEarnedLastMonth");
		$revenue_earned_this_month = $this->get("RevenueEarnedThisMonth");
		$highest_click_ratio_ad = $this->get("HighestClickRatioAd");
		$most_successful_campaign = $this->get("MostSuccessfulCampaign");
		$least_successful_campaign = $this->get("LeastSuccessfulCampaign");
		$currency = $this->get("Currency");
		$all_advertisers = $this->get("AllAdvertisers");
		$all_campaigns = $this->get("AllCampaignsByAdvTo");
		$table_adv_content = $this->get("TableAdvContent");
		$min = $this->get("MinReportDate");
		$max = $this->get("MaxReportDate");
		
		$pagination = $this->get('Pagination');
		$this->assignRef('pagination', $pagination);
		
		$this->assign("number_of_active_campaigns", $number_of_active_campaigns);
		$this->assign("number_of_inactive_campaigns", $number_of_inactive_campaigns);
		$this->assign("number_of_active_ads", $number_of_active_ads);
		$this->assign("number_of_inactive_ads", $number_of_inactive_ads);
		$this->assign("revenue_earned_last_month", $revenue_earned_last_month);
		$this->assign("revenue_earned_this_month", $revenue_earned_this_month);
		$this->assign("highest_click_ratio_ad", $highest_click_ratio_ad);
		$this->assign("most_successful_campaign", $most_successful_campaign);
		$this->assign("least_successful_campaign", $least_successful_campaign);
		$this->assign("currency", $currency);
		$this->assign("all_advertisers", $all_advertisers);
		$this->assign("all_campaigns", $all_campaigns);
		$this->assign("table_adv_content", $table_adv_content);
		$this->assign("min", $min);
		$this->assign("max", $max);

		parent::display($tpl);
	}
	
	function campaigns($tpl = null){
		JToolBarHelper::title(JText::_('VIEWTREECAMPAIGNS'));
		
		$number_of_active_ads = $this->get("NumberOfActiveAds");
		$number_of_inactive_ads = $this->get("NumberOfInactiveAds");
		$highest_click_ratio_ad = $this->get("HighestClickRatioAd");
		$lowest_click_ratio_ad = $this->get("LowestClickRatioAd");
		$all_advertisers = $this->get("AllAdvertisers");
		$all_campaigns = $this->get("AllCampaigns");
		$all_campaigns_by_adv = $this->get("AllCampaignsByAdv");
		$all_ads = $this->get("AllAds");
		$table_cmp_content = $this->get("TableCmpContent");
		$min = $this->get("MinReportDate");
		$max = $this->get("MaxReportDate");
		
		$pagination = $this->get('Pagination');
		$this->assignRef('pagination', $pagination);
		
		$this->assign("number_of_active_ads", $number_of_active_ads);
		$this->assign("number_of_inactive_ads", $number_of_inactive_ads);
		$this->assign("highest_click_ratio_ad", $highest_click_ratio_ad);
		$this->assign("lowest_click_ratio_ad", $lowest_click_ratio_ad);
		$this->assign("all_advertisers", $all_advertisers);
		$this->assign("all_campaigns", $all_campaigns);
		$this->assign("all_campaigns_by_adv", $all_campaigns_by_adv);
		$this->assign("all_ads", $all_ads);
		$this->assign("table_cmp_content", $table_cmp_content);
		$this->assign("min", $min);
		$this->assign("max", $max);

		parent::display($tpl);
	}
	
	
	function display ($tpl =  null ) {
		$helper = new adagencyAdminModeladagencyReports();
		$data = JRequest::get('post');
		if(!isset($data['adag_datepicker'])) {$data['adag_datepicker'] = 0;}

		if(isset($data['tfa'])){
			if(isset($data['start_date'])&&(isset($data['end_date']))&&($data['end_date']!='')&&($data['start_date']!='')) {
				$_SESSION['rep_start_date'] = $data['start_date'];
				$_SESSION['rep_end_date'] = $data['end_date'];
			}
		}

		if(!isset($_SESSION['rep_start_date'])) {$_SESSION['rep_start_date'] = NULL;}
		if(!isset($_SESSION['rep_end_date'])) {$_SESSION['rep_end_date'] = NULL;}
		$this->assign("start_date", $_SESSION['rep_start_date']);
		$this->assign("end_date", $_SESSION['rep_end_date']);

		//echo "<pre>";var_dump($data);echo "<hr />";die();
		JToolBarHelper::title(JText::_('AD_REPORTS'), 'generic.png');
		JToolBarHelper::addNew('creat', 'AD_RUN_REPORT');
		JToolBarHelper::addNew('emptyrep', 'AD_EMPTY');
		JToolBarHelper::addNew('compress', 'ADAG_COMPRESS_DATA');	
		
		$aid = JRequest::getVar('aid', '', 'post');
		$cid = JRequest::getVar('cid', '', 'post');
		$type = JRequest::getVar('type', '', 'post');
		$task = JRequest::getVar('task', '', 'post');

        if ($cid != '' && $aid === '') {
            $database->setQuery("SELECT aid FROM #__ad_agency_campaign WHERE id = {$cid} ");
            $aid = $database->loadResult();
            JRequest::setVar('aid', $aid);
        }

		$chkAdvertiser=intval(JRequest::getVar('chkAdvertiser', '', 'post','0'));
		$chkCampaign=intval(JRequest::getVar('chkCampaign', '', 'post','0'));
		$chkBanner=intval(JRequest::getVar('chkBanner', '', 'post','0'));
		$chkDay=intval(JRequest::getVar('chkDay', '', 'post','0'));

		$database = JFactory::getDBO();
		$javascript = 'onchange="submitform(\'creat\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('AD_AGENCY_ALL_ADV'), 'aid', 'company' );
	    $advertisersloaded = $helper->getreportsAdvertisers();
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    $lists['aid']  =  JHTML::_( 'select.genericlist', $advertisers, 'aid', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $aid);

	    $campaigns[] 	= JHTML::_('select.option',  "0", JText::_('AD_AGENCY_ALL_CAMPAIGNS'), 'id', 'name' );
	    if ($aid) {
            $where_aid = "WHERE aid='".$aid."'";
        } else { $where_aid = NULL; }
        $sql	= "SELECT id, name FROM #__ad_agency_campaign {$where_aid} ORDER BY name ASC";
        $database->setQuery($sql);
        if (!$database->query()) {
            echo $database->stderr();
            return;
        }
        $campaigns 	= array_merge( $campaigns, $database->loadObjectList() );

	    $javascripts='onchange="return changetype();"';
		$lists['cid']  =  JHTML::_( 'select.genericlist', $campaigns, 'cid', 'class="inputbox" size="1"'.$javascript,'id', 'name', $cid);
		$javascripts='onchange="return changetype();"';
		$types[] 	=  JHTML::_( 'select.option', 'Summary', JText::_('AD_SUMMARY'), 'value', 'option' );
		$types[] 	=  JHTML::_( 'select.option', 'Click Detail',JText::_('AD_CLICK_DETAIL'), 'value', 'option' );
		$lists['type']  =  JHTML::_( 'select.genericlist', $types, 'type', 'class="inputbox" size="1"'.$javascripts,'value', 'option', $type);

		$start_now_year=JRequest::getVar( 'start_year',date("Y"), 'post');
		$stop_now_year=JRequest::getVar(  'stop_year', date("Y"), 'post');
		$start_now_month=JRequest::getVar(  'start_month', date("m"), 'post');
		$stop_now_month=JRequest::getVar(  'stop_month', date("m"), 'post');
		$start_now_day=JRequest::getVar(  'start_day', date("d"), 'post');
		$stop_now_day=JRequest::getVar(  'stop_day', date("d"), 'post');

		// year listbox
		$array = array();
		for ($i=1999; $i<2020; $i++) {
			$array[]=JHTML::_( 'select.option', $i, $i, 'value', 'option');
		}
		$lists['start_year']=JHTML::_( 'select.genericlist',$array, 'start_year','class="inputbox"','value', 'option', $start_now_year);
		$lists['stop_year']=JHTML::_( 'select.genericlist', $array, 'stop_year','class="inputbox"','value', 'option', $stop_now_year);

		$months[] = JHTML::_('select.option',  "1", JText::_('ADJAN'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "2", JText::_('ADFEB'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "3", JText::_('ADMAR'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "4", JText::_('ADAPR'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "5", JText::_('ADMAY'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "6", JText::_('ADJUN'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "7", JText::_('ADJUL'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "8", JText::_('ADAUG'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "9", JText::_('ADSEP'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "10", JText::_('ADOCT'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "11", JText::_('ADNOV'), 'value', 'option' );
		$months[] = JHTML::_('select.option',  "12", JText::_('ADDEC'), 'value', 'option' );

		$lists['start_month']=JHTML::_( 'select.genericlist',$months, 'start_month', 'class="inputbox"','value', 'option', $start_now_month );
		$lists['stop_month']=JHTML::_( 'select.genericlist',$months, 'stop_month', 'class="inputbox"','value', 'option', $stop_now_month );

		// day listbox
		$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
		$array=array();
		foreach ( $days as $val) {
			$array[]=JHTML::_( 'select.option',$val, $val, 'value', 'option');
		}
		$lists['start_day']=JHTML::_( 'select.genericlist',$array, 'start_day','class="inputbox"','value', 'option', $start_now_day);
		$lists['stop_day']=JHTML::_( 'select.genericlist',$array, 'stop_day','class="inputbox"','value', 'option', $stop_now_day);

		$this->assign("lists", $lists);
		$this->assign("task", $task);

			$filds_out=array();
	if ('creat' == $task) {
		switch ($type) {
			case "Summary":
				if ( $aid ) $where[] = "s.advertiser_id = $aid";
				if ( $cid ) $where[] = "s.campaign_id = $cid";
				if ( $chkAdvertiser ) {
					$group[]="s.advertiser_id";
					$filds_out[]=JText::_('REPADVERTISER');
					$select[]="a.aid";
					$join[]="LEFT JOIN #__ad_agency_advertis AS a ON a.aid=s.advertiser_id";
				}
				if ( $chkCampaign ) {
					$group[]="s.campaign_id";
					$filds_out[]=JText::_('REPCAMPAIGN');
					$select[]="c.name";
					$join[]="LEFT JOIN #__ad_agency_campaign AS c ON c.id=s.campaign_id";
				}
				if ( $chkBanner ) {
					$group[]="s.banner_id";
					$filds_out[]=JText::_('REPBANNER');
					$select[]="b.title";
					$join[]="LEFT JOIN #__ad_agency_banners AS b ON b.id=s.banner_id";
				}
				if ( $chkDay ) {
					$group[]="DAYOFYEAR(s.entry_date)";
					$filds_out[]=JText::_('ADAG_DATE');
					$select[]="s.entry_date";
				}

				$filds_out[]=JText::_("VIEWADIMPRESSIONS");
				$filds_out[]=JText::_("VIEWADCLICKS");
				$filds_out[]=JText::_("AD_CLICK_RATE");
				$select[]="sum(case s.type when 'impressions' then s.how_many else 0 end) as impressions";
				$select[]="sum(case s.type when 'click' then s.how_many else 0 end) as click";
				$select[]="TRUNCATE((sum(case s.type when 'click' then s.how_many else 0 end)/(sum(case s.type when 'impressions' then s.how_many else 0 end)/100)),2) as click_rate";

				//date/time if the report is for one day

				if (($start_now_year == $stop_now_year) AND ($start_now_month == $stop_now_month) AND ($start_now_day == $stop_now_day)) {
					global $stop_now_day1;
					$stop_now_day1 = $stop_now_day + 1;
					$where[]="s.entry_date > '".date("Y-m-d", strtotime($data['start_date']))." 00:00:00' AND s.entry_date < '".date("Y-m-d", strtotime($data['end_date']))." 23:59:59'";
				}
				else{
					$where[]="s.entry_date > '".date("Y-m-d", strtotime($data['start_date']))." 00:00:00' AND s.entry_date < '".date("Y-m-d", strtotime($data['end_date']))." 23:59:59'";
				}

				// concat SELECT
				if ( isset( $select ) ) {
					$select = implode( ', ', $select );
				} else {
					$select = '';
				}

				// concat WHERE
				if ( isset( $where ) ) {
					$where = "\n WHERE ". implode( ' AND ', $where );
				} else {
					$where = '';
				}

				// concat JOIN
				if ( isset( $join ) ) {
					$join = implode( ' ', $join );
				} else {
					$join = '';
				}

				// concat GROUP

				if ( isset( $group ) ) {
					$group = 'GROUP BY '.implode( ', ', $group );
				} else {
					$group = '';
				}

				$sql="SELECT $select
						FROM #__ad_agency_stat as s
						$join
						$where $group";
				//echo $sql."<hr />";
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
				$data_row=$database->loadAssocList();
			break;

			case "Click Detail":
				// v.1.5.3 update - the user_agent is not used anymore
				$filds_out=array(JText::_('REPCAMPAIGN'), JText::_('ADAG_DATETIME'), JText::_('ADAG_IPADDR'), JText::_('ADAG_NUMBER'));
				$filds_select=array('campaign_id', 'entry_date', 'ip_address', 'how_many');
				if ( $aid ) $where[] = "s.advertiser_id = $aid";
				if ( $cid ) $where[] = "s.campaign_id = $cid";
				if (($start_now_year == $stop_now_year) AND ($start_now_month == $stop_now_month) AND ($start_now_day == $stop_now_day)) $stop_now_day = $stop_now_day;
				//$where[]="s.entry_date BETWEEN '$start_now_year-$start_now_month-$start_now_day 00:00:00' AND '$stop_now_year-$stop_now_month-$stop_now_day 23:59:59'";
				$where[]="s.entry_date > '".$data['start_date']." 00:00:00' AND s.entry_date < '".$data['end_date']." 23:59:59'";
				$where[]="s.type='click'";
				if ( isset( $where ) ) {
					$where = "\n WHERE ". implode( ' AND ', $where );
				} else {
					$where = '';
				}
				// v.1.5.3 update - the user_agent is not used anymore
				$sql="SELECT c.name, s.entry_date, s.ip_address, s.how_many
						FROM #__ad_agency_stat as s
						LEFT JOIN #__ad_agency_campaign AS c ON c.id=s.campaign_id
						$where";
				//echo "<pre>";var_dump($sql);echo "</pre>";//die();
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
				$data_row=$database->loadAssocList();
			break;
		}
	}
		if (!isset($data_row)) $data_row='';

		$query = "SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$database->setQuery($query);
		$params = $database->loadResult();
		$params = @unserialize($params);
		if(isset($params['timeformat'])){
			$params = $params['timeformat'];
		} else { $params = "10"; }

		$this->assign("adag_datepicker", $data['adag_datepicker']);
		$this->assign("params", $params);
		$this->assign("filds_out", $filds_out);
		$this->assign("data_row", $data_row);
		parent::display($tpl);
	}

    function compress($tpl = null) {
		JToolBarHelper::title( JText::_('ADAG_COMPRESS_DATA') );
        parent::display($tpl);
    }
}
?>
