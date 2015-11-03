<?php				//MODEL FILE
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.utilities.utility' );
jimport( 'joomla.html.parameter' );
require_once JPATH_COMPONENT.DS.'helper.php';

/* Showad Model */
class socialadsModelcampaign extends JModelLegacy
{

	function save()
	{
		$post=JRequest::get('post');
		$user=JFactory::getUser();
		$userid=$user->id;
		if(!$userid)
		{
			$userid=0;
			return false;
		}

		$ins_camp=$this->insert_camp($post);
		if(!$ins_camp){
			return false;
		}




	}


	function insert_camp($post)
	{
		$camp_cnt=count($post['camp_amount']);
		$user=JFactory::getUser();
		$userid=$user->id;
		for ($i = 0; $i < $camp_cnt; $i++)
		{
			$obj = new stdClass();

			$obj->user_id=$userid;
			$obj->campaign=$post['camp_name'][$i];
			$obj->daily_budget=$post['camp_amount'][$i];
			$obj->camp_published="1";
			//if campaign id present update campain
			if(!empty($post['campid']))
			{

				if($obj->campaign!='' && $obj->daily_budget!='')
				{
					$obj->camp_id=$post['campid'];
					if(!$this->_db->updateObject( '#__ad_campaign',$obj,'camp_id'))
					{
						echo $this->_db->stderr();
						return false;
					}
				}
			}
			else
			{			//save only if amount and description is not empty

				if($obj->campaign!='' && $obj->daily_budget!='')
				{
					$obj->camp_id='';
					if(!$this->_db->insertObject( '#__ad_campaign',$obj,'camp_id'))
					{
						echo $this->_db->stderr();
						return false;
					}
				}
			}
		}
		return true;
	}


	function getdisplay_camp()
	{
		$user=JFactory::getUser();
		$userid=$user->id;
		$query = "SELECT camp_id,campaign,daily_budget FROM #__ad_campaign WHERE user_id=$userid";
		$this->_db->setQuery($query);
		$camp_value = $this->_db->loadobjectList();
		return $camp_value;
	}


	function getlist()
	{
		$mainframe=JFactory::getApplication();
		$user=JFactory::getUser();
		$userid=$user->id;

		$filter_order_Dir=$mainframe->getUserStateFromRequest('com_socialads.filter_order_Dir','filter_order_Dir','desc','word');
		$filter_type=$mainframe->getUserStateFromRequest('com_socialads.filter_order','filter_order','campaign','string');

		//print_r($filter_order_Dir);
		//print_r($filter_type); //die('asdas');

		$query = "SELECT c.camp_id,c.camp_published,c.campaign,c.daily_budget,COUNT(a.camp_id) as no_of_ads  FROM `#__ad_campaign` as c LEFT JOIN #__ad_data as a on a.camp_id=c.camp_id WHERE c.user_id = $userid GROUP BY c.camp_id ";

		if(!empty($filter_type) && !empty($filter_order_Dir) ){
			$query.= ' ORDER BY '.$filter_type.' '.$filter_order_Dir;
		}
		$this->_db->setQuery($query);
		$camp_value = $this->_db->loadobjectList();



		foreach($camp_value as $key)
			{
			$date1 = Date('y-m-d');

			$key->c_date=$date1;


					$query = "select COUNT(display_type) as clicks FROM #__ad_stats as s LEFT JOIN #__ad_data as d ON d.ad_id=s.ad_id WHERE d.camp_id=$key->camp_id AND s.ad_id IN (d.ad_id) AND display_type=0";
					$this->_db->setQuery($query);
					$final_clicks = $this->_db->loadresult();
					$key->imp = $final_clicks;

					$query = "SELECT SUM(earn) FROM `#__ad_camp_transc` WHERE user_id=$user->id";
					$this->_db->setQuery($query);
					$total_amt = $this->_db->loadresult();
					$key->total_amt = $total_amt;


					$query = "select COUNT(display_type) as imp FROM #__ad_stats as s LEFT JOIN #__ad_data as d ON d.ad_id=s.ad_id WHERE d.camp_id=$key->camp_id AND s.ad_id IN (d.ad_id) AND display_type=1";
					$this->_db->setQuery($query);
					$final_imp = $this->_db->loadresult();
					$key->clicks = $final_imp;



					$query = "SELECT spent FROM `#__ad_camp_transc` WHERE user_id=".$user->id." AND DATE(FROM_UNIXTIME(time)) = ".$key->c_date;
					$this->_db->setQuery($query);
					$camp_spent = $this->_db->loadresult();
					$key->spent = $camp_spent;



						$query = "SELECT ad_published FROM #__ad_data WHERE camp_id=$key->camp_id";
						$this->_db->setQuery($query);
						$p=$this->_db->loadobjectlist();



						$v = '';
						foreach($p as $k)
						{


								if($k->ad_published=='0')
								{
										$v=0;
								}
								else
								{
										$v=1;
										break;
								}


						}
			$key->ad_p = $v;

			if($key->imp==0)
				$key->ctc = 0;
				else
				{
					$ctc = $key->clicks/$key->imp;
					$d = round($ctc, 3); // 4.123
					$key->ctc = $d;
				}


				if($key->camp_published == 1)
				{

							if(!empty($key->spent))
							{

								if($key->spent >= $key->total_amt)
										{
												$key->status=0;
										}
									else if($key->spent < $key->total_amt || $key->ad_p==1)
									{
												$key->status=1;
									}
									else
									{
										$key->status=0;
									}
							}
						else
						{
							$key->status=1;
						}

				}
				else
				{
					$key->status=0;
				}
			}//foreach end

		return $camp_value;
	}



	function status($cid){


			$user=JFactory::getUser();
			$userid=$user->id;

			$db = JFactory::getDBO();
			$query = "SELECT balance FROM `#__ad_camp_transc` where time = (select MAX(time) from #__ad_camp_transc where user_id =".$user->id.")";
			$db->setQuery($query);
			$remaining_amt = $db->loadresult();


			$v=0;


			if($remaining_amt)
			{

				$query = "SELECT camp_published FROM #__ad_campaign WHERE camp_id=$cid";
				$this->_db->setQuery($query);
				$p=$this->_db->loadresult();



			if($p==0)
				{
						$v=1;
				}
			else
				{
						$v=0;
				}

				$query = "UPDATE #__ad_campaign SET camp_published=$v WHERE camp_id=$cid";
				$this->_db->setQuery($query);
				$this->_db->execute($query);
			}

			return $v;
		}

	function deletecampaign($id)
	{

		$campid=implode(',',$id);
		$db=JFactory::getDBO();
		$query="delete FROM #__ad_campaign where camp_id IN(".$campid.")";

		$db->setQuery($query);
		if(!$db->execute()){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		//delete all ads against that campaign
		$query="delete FROM #__ad_data where camp_id IN(".$campid.")";

		$db->setQuery($query);
		if(!$db->execute()){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		//delet stats of all the ad in that campaign

		$query="delete s.* FROM #__ad_stats as s
				LEFT JOIN #__ad_data as d
				ON d.ad_id=s.ad_id
				where d.camp_id IN(".$campid.")";
		$db->setQuery($query);
		if(!$db->execute()){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function getcamp_info($camp_id){
		$query = "SELECT camp_id,campaign,daily_budget FROM #__ad_campaign WHERE camp_id=".$camp_id;
		$this->_db->setQuery($query);
		$camp_info = $this->_db->loadobjectList();
		return $camp_info;
	}


}
?>
