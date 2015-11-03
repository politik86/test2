<?php
/*
  @package	Jticketing
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
//jimport('joomla.filesystem.file');


class socialadsModelbilling extends JModelLegacy
{
		function getbilling()
		{
				//print_r($month); die('asd');
				$mainframe= JFactory::getApplication();
				$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
				$option = $input->get('option','','STRING');
				$month = $mainframe->getUserStateFromRequest( $option.'month', 'month','', 'int' );
				$year = $mainframe->getUserStateFromRequest( $option.'year', 'year','', 'int' );

				$whr='';
				$whr1='';
				if($month && $year)
					{
						$whr = "  AND month(cdate) =" .$month."   AND year(cdate) =".$year."  " ;
						$whr1 = "  AND month(DATE(FROM_UNIXTIME(a.time))) =" .$month."  AND year(DATE(FROM_UNIXTIME(a.time))) =" .$year."  ";
					}
				else if($month=='' && $year)
					{
						$whr = "    AND year(cdate) =".$year."  " ;
						$whr1 = "   AND year(DATE(FROM_UNIXTIME(a.time))) =" .$year."  ";
					}
				$user =JFactory::getUser();
				$all_info = array();
			/*
				$query="SELECT DATE_FORMAT(cdate, '%Y-%m-%d') as cdate,ad_original_amt,comment FROM `#__ad_payment_info` where status='C' AND ad_id=0 AND payee_id=".$user->id." ".$whr;
				$this->_db->setQuery($query);
				$payment_info = $this->_db->loadobjectList();



				array_push($all_info,$payment_info);
*/
				$query = "SELECT DATE(FROM_UNIXTIME(a.time)) as time,a.spent as spent,type_id,a.earn as credits,balance,comment FROM #__ad_camp_transc as a WHERE a.user_id = ".$user->id." ".$whr1." ORDER BY a.time ASC";

				$this->_db->setQuery($query);
				$ad_stat = $this->_db->loadobjectList();
				$camp_name =$coupon_code = $ad_title =array();
			if(!empty($ad_stat)){
				foreach($ad_stat as $key)
				{
						// to get campaign name
					$query = "SELECT campaign FROM #__ad_campaign WHERE camp_id=".$key->type_id;
					$this->_db->setQuery($query);
					$camp_name[$key->type_id] = $this->_db->loadresult();

					//to get coupon code
					$query = "SELECT ad_coupon FROM #__ad_payment_info WHERE id=".$key->type_id;
					$this->_db->setQuery($query);
					$coupon_code[$key->type_id] = $this->_db->loadresult();

					$ad_til = explode('|',$key->comment);
					if(isset($ad_til[1]))
						{
							$query = "SELECT ad_title FROM #__ad_data WHERE ad_id=".$ad_til[1];
							$this->_db->setQuery($query);
							$ad_title[$ad_til[1]] = $this->_db->loadresult();
						}
				}
			}
			//print_r($ad_title); die('asdas');
				array_push($all_info,$ad_stat,$camp_name,$coupon_code,$ad_title);
				/*array_push($all_info,$camp_name);
				array_push($all_info,$coupon_code);
				array_push($all_info,$ad_title);*/


				return $all_info;


			//	return $this->_db->loadobjectList();

		}

}
