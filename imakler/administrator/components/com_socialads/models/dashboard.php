<?php
/**
 * @version    SVN:<SVN_ID>
 * @package    Com_Socialads
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * Methods supporting a list of Socialads records.
 *
 * @since  1.6
 */
class SocialadsModelDashboard extends JModelLegacy
{
	/**
	 * Constructor.
	 *
	 * @see  JController
	 *
	 * @since  1.6
	 */
	public function __construct()
	{
		$mainframe = JFactory::getApplication();

		// Get download id
		$params           = JComponentHelper::getParams('com_socialads');
		$this->downloadid = $params->get('downloadid');

		// Setup vars
		$this->updateStreamName = 'Socialads';
		$this->updateStreamType = 'extension';
		$this->updateStreamUrl  = "https://techjoomla.com/component/ars/updates/components/socialads?format=xml&dummy=extension.xml";
		$this->extensionElement = 'com_socialads';
		$this->extensionType    = 'component';

		// Call the parents constructor
		parent::__construct();
	}

	/**
	 * Method to get box.
	 *
	 * @param   string   $title    title of box
	 * @param   string   $content  content of box
	 * @param   boolean  $type     type of box
	 *
	 * @return  html
	 *
	 * @since  1.6
	 */
	public function getbox($title, $content, $type = null)
	{
		$html = '
			<table cellspacing="0px" cellpadding="0px" border="0" class="tbTitle">
			<tbody>
				<tr>
				<td width="" class="tbTitleMiddle">
					<h5>' . $title . '</h5>
				</td>

			</tr>
			<tr>
				<td class="boxBody"><div >' . $content . '</div></td>
			</tr>
			<tr>

				<td width="" class="tbBottomMiddle">&nbsp;</td>
			</tr>
		</tbody>
		</table>
		';

		return $html;
	}

	/**
	 * To get order income
	 *
	 * @return  string
	 *
	 * @since  1.6
	 */
	public function getAllOrderIncome()
	{
		$query = "SELECT
		FORMAT(SUM(ad_amount),2) FROM #__ad_payment_info WHERE status ='C' AND (processor NOT IN('jomsocialpoints','alphauserpoints')
		OR extras='points') AND comment!='AUTO_GENERATED'";
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();

		return $result;
	}

	/**
	 * To get month income
	 *
	 * @return  string
	 *
	 * @since  1.6
	 */
	public function getMonthIncome()
	{
		$db   = JFactory::getDBO();

		// $backdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 30 days'));

		$curdate = date('Y-m-d');
		$back_year = date('Y') - 1;
		$back_month = date('m') + 1;
		$backdate = $back_year . '-' . $back_month . '-' . '01';

		// Changed by vm :
		$query = "SELECT FORMAT( SUM( ad_amount ) , 2 ) AS ad_amount, MONTH( mdate ) AS MONTHSNAME, YEAR( mdate ) AS YEARNM
			FROM #__ad_payment_info
			WHERE DATE( mdate )
			BETWEEN  '" . $backdate . "'
			AND   '" . $curdate . "'
			AND COMMENT !=  'AUTO_GENERATED'
			AND (
			processor NOT
			IN (
			 'jomsocialpoints',  'alphauserpoints'
			)
				OR extras =  'points'
			)
			AND STATUS =  'C'
			GROUP BY YEARNM, MONTHSNAME
			ORDER BY YEAR( mdate ) , MONTH( mdate ) ASC ";

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * To get all months income
	 *
	 * @return  string
	 *
	 * @since  1.6
	 */
	public function getAllmonths()
	{
		$date2 = date('Y-m-d');
		$back_year = date('Y') - 1;
		$back_month = date('m') + 1;
		$date1 = $back_year . '-' . $back_month . '-' . '01';

		// Convert dates to UNIX timestamp
		$time1  = strtotime($date1);
		$time2  = strtotime($date2);
		$tmp    = date('mY', $time2);

		$months[] = array("month" => date('F', $time1), "year" => date('Y', $time1));

		while ($time1 < $time2)
		{
			$time1 = strtotime(date('Y-m-d', $time1) . ' +1 month');

			if (date('mY', $time1) != $tmp && ($time1 < $time2))
			{
				$months[] = array("month"    => date('F', $time1), "year"    => date('Y', $time1));
			}
		}

		// $months[] = array("month"    => date('F', $time2), "year"    => date('Y', $time2));
		$months[] = array("month" => date('F', $time2), "year" => date('Y', $time2));

		return $months;

		// Returns array of month names with year
	}

	/**
	 * Function for line chart
	 *
	 * @return  string
	 *
	 * @since  1.6
	 */
	public function statsforbar()
	{
		$db = JFactory::getDBO();
		$where = '';
		$year1 = '';
		$session = JFactory::getSession();
		$input = JFactory::getApplication()->input;
		$ad_id = $session->get('socialads_adid');
		$socialads_from_date = $session->get('socialads_from_date');
		$socialads_end_date = $session->get('socialads_end_date');

		if ($socialads_from_date)
		{
			$year1 = " ,YEAR(time) as year ";
			$where = " AND DATE(time) BETWEEN DATE('" . $socialads_from_date . "') AND DATE('" . $socialads_end_date . "')";
		}
		else
		{
			$ad_id = $input->get('adid', 0, 'INT');
			$session->set('socialads_adid', $ad_id);
				$j = 0;
				$d = 0;

				$day = date('d');
				$month = date('m');
				$year = date('Y');
				$statistics = array();

					// Print_r($statistics);die;
		}

			$query = " SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month " . $year1 .
			" FROM #__ad_stats WHERE display_type = 0   " . $where . "    GROUP BY DATE(time) ORDER BY DATE(time)";

			$db->setQuery($query);
			$statistics[] = $db->loadObjectList();

			$query = "SELECT COUNT(id) as value,DAY(time) as day,MONTH(time) as month " . $year1 .
			"  FROM #__ad_stats WHERE display_type = 1  " . $where . "  GROUP BY DATE(time)  ORDER BY DATE(time)";
			$db->setQuery($query);
			$statistics[] = $db->loadObjectList();

		return $statistics;
	}
	// Function statsforbar ends here

	/**
	 * Function for pie chart
	 *
	 * @return  string
	 *
	 * @since  1.6
	 */
	public function statsforpie()
	{
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
		$input = JFactory::getApplication()->input;
		$socialads_from_date = $session->get('socialads_from_date');
		$socialads_end_date = $session->get('socialads_end_date');
		$where = '';
		$groupby = '';

		if ($socialads_from_date)
		{
			// For graph
			$ad_id = $session->get('socialads_adid');
			$where = " AND DATE(mdate) BETWEEN DATE('" . $socialads_from_date . "') AND DATE('" . $socialads_end_date . "')";
		}
		else
		{
			$day = date('d');
			$month = date('m');
			$year = date('Y');
			$statsforpie = array();
			$ad_id = $input->get('adid', 0, 'INT');
			$backdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 30 days'));
			$groupby = "";
		}

			$query = " SELECT COUNT(id) AS orders FROM #__ad_payment_info WHERE status LIKE 'P'
			AND comment!='AUTO_GENERATED' AND (processor NOT IN('jomsocialpoints','alphauserpoints') OR extras='points') "
			. $where;
			$db->setQuery($query);
			$statsforpie[] = $db->loadResult();

			// Pending
			$query = " SELECT COUNT(id) AS orders FROM #__ad_payment_info WHERE status LIKE 'C'
			AND comment!='AUTO_GENERATED' AND (processor NOT IN('jomsocialpoints','alphauserpoints') OR extras='points') "
			. $where;
			$db->setQuery($query);
			$statsforpie[] = $db->loadResult();

			// Confirm

			$query = " SELECT COUNT(id) AS orders FROM #__ad_payment_info WHERE status NOT IN('C','P')
			AND comment!='AUTO_GENERATED' AND (processor NOT IN('jomsocialpoints','alphauserpoints') OR extras='points') "
			. $where;
			$db->setQuery($query);
			$statsforpie[] = $db->loadResult();

			// Rejected

			return $statsforpie;
	}

	/**
	 * Function to get ignore count
	 *
	 * @return  string
	 *
	 * @since  1.6
	 */
	public function getignoreCount()
	{
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
		$socialads_from_date = $session->get('socialads_from_date');
		$socialads_end_date = $session->get('socialads_end_date');
		$where = '';

		if ($socialads_from_date)
		{
			$where = "WHERE  DATE(idate) BETWEEN DATE('" . $socialads_from_date . "') AND DATE('" . $socialads_end_date . "')";
		}

		$query = "SELECT COUNT(*) as ignorecount,DATE(idate) as idate FROM #__ad_ignore  " . $where . " GROUP bY DATE(idate) ORDER BY DATE(idate)";

		$this->_db->setQuery($query);
		$cnt = $this->_db->loadObjectList();

		return $cnt;
	}

	/**
	 * Function for periodic orders count
	 *
	 * @return  string
	 *
	 * @since  1.6
	 */
	public function getperiodicorderscount()
	{
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
		$socialads_from_date = $session->get('socialads_from_date');
		$socialads_end_date = $session->get('socialads_end_date');
		$where = '';
		$groupby = '';

		if ($socialads_from_date)
		{
			$where = " AND DATE(mdate) BETWEEN DATE('" . $socialads_from_date . "') AND DATE('" . $socialads_end_date . "')";
		}
		else
		{
			$socialads_from_date = date('Y-m-d');
			$backdate = date('Y-m-d', strtotime(date('Y-m-d') . ' - 30 days'));
			$where = " AND DATE(mdate) BETWEEN DATE('" . $backdate . "') AND DATE('" . $socialads_from_date . "')";
			$groupby = "";
		}

			$query = "SELECT FORMAT(SUM(ad_amount),2) FROM #__ad_payment_info WHERE status ='C'
			AND comment!='AUTO_GENERATED' AND (processor NOT IN('jomsocialpoints','alphauserpoints') OR extras='points') "
			. $where;
			$this->_db->setQuery($query);
			$result = $this->_db->loadResult();

			return $result;
	}

	/**
	 * Function for periodic orders count
	 *
	 * @return  string
	 *
	 * @since  1.6
	 */
	public function getExtensionId()
	{
		$db = $this->getDbo();

		// Get current extension ID
		$query = $db->getQuery(true)
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type') . ' = ' . $db->q($this->extensionType))
			->where($db->qn('element') . ' = ' . $db->q($this->extensionElement));
			$db->setQuery($query);
			$extension_id = $db->loadResult();

		if (empty($extension_id))
		{
			return 0;
		}
		else
		{
			return $extension_id;
		}
	}

	/**
	 * Refreshes the Joomla! update sites for this extension as needed
	 *
	 * @return  void
	 *
	 * @since  1.6
	 */
	public function refreshUpdateSite()
	{
		// Extra query for Joomla 3.0 onwards
		$extra_query = null;

		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $this->downloadid))
		{
			$extra_query = 'dlid=' . $this->downloadid;
		}

		// Setup update site array for storing in database
		$update_site = array(
			'name' => $this->updateStreamName,
			'type' => $this->updateStreamType,
			'location' => $this->updateStreamUrl,
			'enabled' => 1,
			'last_check_timestamp' => 0,
			'extra_query' => $extra_query
		);

		// For joomla versions < 3.0
		if (version_compare(JVERSION, '3.0.0', 'lt'))
		{
			unset($update_site['extra_query']);
		}

		$db = $this->getDbo();

		// Get current extension ID
		$extension_id = $this->getExtensionId();

		if (!$extension_id)
		{
			return;
		}

		// Get the update sites for current extension
		$query = $db->getQuery(true)
			->select($db->qn('update_site_id'))
			->from($db->qn('#__update_sites_extensions'))
			->where($db->qn('extension_id') . ' = ' . $db->q($extension_id));
		$db->setQuery($query);
		$updateSiteIDs = $db->loadColumn(0);

		if (!count($updateSiteIDs))
		{
			// No update sites defined. Create a new one.
			$newSite = (object) $update_site;
			$db->insertObject('#__update_sites', $newSite);
			$id = $db->insertid();
			$updateSiteExtension = (object) array(
				'update_site_id' => $id,
				'extension_id'   => $extension_id,
			);

			$db->insertObject('#__update_sites_extensions', $updateSiteExtension);
		}
		else
		{
			// Loop through all update sites
			foreach ($updateSiteIDs as $id)
			{
				$query = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__update_sites'))
					->where($db->qn('update_site_id') . ' = ' . $db->q($id));
				$db->setQuery($query);
				$aSite = $db->loadObject();

				// Does the name and location match?
				if (($aSite->name == $update_site['name']) && ($aSite->location == $update_site['location']))
				{
					// Do we have the extra_query property (J 3.2 ) and does it match?
					if (property_exists($aSite, 'extra_query'))
					{
						if ($aSite->extra_query == $update_site['extra_query'])
						{
							continue;
						}
					}
					else
					{
						// Joomla! 3.1 or earlier. Updates may or may not work.
						continue;
					}
				}

				$update_site['update_site_id'] = $id;
				$newSite = (object) $update_site;
				$db->updateObject('#__update_sites', $newSite, 'update_site_id', true);
			}
		}
	}

	/**
	 * to get latest version
	 *
	 * @return  void
	 *
	 * @since  1.6
	 */
	public function getLatestVersion()
	{
		// Get current extension ID
		$extension_id = $this->getExtensionId();

		if (!$extension_id)
		{
			return 0;
		}

		$db = $this->getDbo();

		// Get current extension ID
		$query = $db->getQuery(true)
			->select($db->qn(array('version', 'infourl')))
			->from($db->qn('#__updates'))
			->where($db->qn('extension_id') . ' = ' . $db->q($extension_id));
		$db->setQuery($query);
		$latestVersion = $db->loadObject();

		if (empty($latestVersion))
		{
			return 0;
		}
		else
		{
			return $latestVersion;
		}
	}
}
