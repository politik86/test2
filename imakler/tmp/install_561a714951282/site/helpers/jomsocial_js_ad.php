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

class JomSocialTargetingJSAd {
	
	public static function exists() {
		$db = JFactory::getDBO();
		$sql = "SHOW tables";
		$db->setQuery($sql);
		$res_tables = $db->loadColumn();
		
		$jconfigs = new JConfig();
		$dbprefix = $jconfigs->dbprefix;
		
		if(!in_array($dbprefix."community_fields",$res_tables)) {		
			return false;
		}
		if(!self::exists_backend() && !self::exists_frontend()) {
			return false;
		}
		return true;
	}
	
	public static function getFields() {
		$db = JFactory::getDBO();
		$sql = "
			SELECT * FROM #__community_fields 
			WHERE 
			`published` = 1 AND
			`type` IN (
				'select', 'singleselect', 'list', 'radio', 'checkbox', 'birthdate', 'gender'
			) 
			ORDER BY `ordering` ASC
			LIMIT 100
		";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	public static function getSelected() {
		$db = JFactory::getDBO();
		$sql = "SELECT jomfields FROM #__ad_agency_settings";
		$db->setQuery($sql);
		// Get the fields that were selected for targeting
		$selected = $db->loadResult();
		if ($selected) {
			$selected = json_decode($selected);
			$ids = implode(",", $selected);
			$sql = "SELECT * FROM #__community_fields WHERE id IN (" . $ids . ") ORDER BY ordering ASC";
			$db->setQuery($sql);
			return $db->loadObjectList();
		} else {
			$selected = array();
		}
		return $selected;
	}
	
	public static function getOptsParents($opts) {
		$db = JFactory::getDBO();
		//$sql = "SELECT name, ordering, id FROM #__community_fields WHERE `type` = 'group' ORDER BY id ASC";
		$sql = "SELECT name, ordering, id FROM #__community_fields WHERE `type` = 'group' ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$groups = $db->loadObjectList();
		for($j=0; $j < count($groups); $j++) {
			for($i=0;$i < count($opts); $i++) {
				if($opts[$i]->ordering > $groups[$j]->ordering && 
					(!isset($groups[$j+1]) || ($opts[$i]->ordering < $groups[$j+1]->ordering))
					)
				{
					$groups[$j]->opts[] = $opts[$i];
				}
			}
		}
		return $groups;
	}
	
	private static function exists_frontend() {
		$db = JFactory::getDBO();
		$sql = "SELECT `params` FROM #__ad_agency_settings";
		$db->setQuery($sql);
		$params = $db->loadColumn();
		$params = @unserialize($params["0"]);
		
		if (isset($params['jom_front'])) {
			return true;
		} else {
			return false;
		}
	}

	private static function exists_backend() {
		$db = JFactory::getDBO();
		$sql = "SELECT `params` FROM #__ad_agency_settings";
		$db->setQuery($sql);
		$params = $db->loadColumn();
		$params = @unserialize($params["0"]);
		
		if (isset($params['jom_back'])) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function renderJsAd($ad_id = 0, $type){
		if($type == "list"){
			if (self::exists()) {
				if(!self::exists_backend()){
					return false;
				}
				else{
					echo '<li><a href="#jomsoc-info" data-toggle="tab">'.JText::_('ADAG_JOMSOC_TARGETING').'</a></li>';
				}
			}
		}
		else{
			if (self::exists()) {
				if(!self::exists_backend()){
					return false;
				}
				$options = self::getSelected();
				
				if (count($options) == 0) {
					echo JText::_('ADAG_JOMSOC_ENBL');
					return false;
				}
				
				$groups = self::getOptsParents($options);
				foreach ($groups as $group) {
					if (isset($group->opts) && (count($group->opts) > 0)) {
						echo "<fieldset>";
						self::render_group_info($group);
						self::render_opts($ad_id, $group->opts);
						echo "</fieldset>"; 
					}
				}
			}
		}
	}
	
	public static function render_group_info($group) {
		echo "<legend>" . $group->name . "</legend>";
	}
	
	public static function render_opts($ad_id, $opts) {
		foreach ($opts as $opt) {
			self::render_opt($ad_id, $opt);
		}
	}
	
	public static function render_opt($ad_id, $opt) {
		echo "<div class='uk-form-row'>";
		if ($opt->name == 'Birthdate') { $opt->name = JText::_('ADAG_AGE'); }
		echo "<label class='uk-form-label'>" .JText::_($opt->name). "</label>";
		
		if ($opt->type != 'birthdate') {
			self::render_checkbox($ad_id, $opt);
		} else {
			self::render_age($ad_id, $opt);
		}
		echo "</div>";
	}
	
	public static function render_select($opt, $attr = NULL, $multiple = NULL) {
		$_opts = self::extract_opts($opt->options);
		$output = "<div class='uk-form-controls'>";
		$output .= "<select class='uk-display-inline-block' id='jomsocial_".$opt->id."' name='jomsocial[" . $opt->id . "]" . $multiple . "' " . $attr . " onchange='javascript:changeTarget();'>";
		foreach ($_opts as $op) {
			$output .= "<option value='" . $op . "' />" .JText::_($op). "</option>";
		}
		$output .= "</select><input type='hidden' value='".$opt->id."' name='hidden_ids' /></div>";
		echo $output;
	}
	
	public static function render_singleselect($opt) {
		self::render_select($opt, " size='4' ");
	}
	public static function render_list($opt) {
		self::render_select($opt, " size='4' multiple='multiple' ", "[]");
	}	
	public static function render_radio($opt) {
		$_opts = self::extract_opts($opt->options);
		$output = "<div class='uk-form-controls'>";
		$i=1;
		foreach ($_opts as $op) {
			$output .= "<input type='radio' id='jomsocial_".$opt->id."_".$i."' name='jomsocial[" . $opt->id . "]' value='" . $op . "' onclick='javascript:changeTarget();' />";
			$output .= "<span>" .JText::_($op). "</span>";
			$output .= "<input type='hidden' value='".$opt->id."_".$i."' name='hidden_ids' />";
			$i++;
		}
		$output .= "</div>";
		echo $output;
	}

	private static function getOptVal($ad_id = 0, $field_id = 0) {
		if ($ad_id == 0 || $field_id == 0) return array();
		$db = JFactory::getDBO();
		$sql = "SELECT value FROM #__ad_agency_jomsocial WHERE ad_id = '" . intval($ad_id) . "' AND field_id = '" . intval($field_id) . "' ";
		$db->setQuery($sql);
		$res = $db->loadResult();
		
		if (!$res) { return array(); }
		else { return json_decode($res); }
	}
	
	public static function render_checkbox($ad_id, $opt) {
		$_opts = self::extract_opts($opt->options);
		$vals = self::getOptVal($ad_id, $opt->id);
		
		$output = "<div class='uk-form-controls'>";
		$i=1;
		foreach ($_opts as $op) {
			if (in_array($op,$vals)) { $checked = " checked='checked' "; } else { $checked = NULL; }
			$output .= "<label class='uk-form-label'>";
			$output .= "<input type='checkbox' {$checked} class='uk-margin-right' id='jomsocial_".$opt->id."_".$i."' name='jomsocial[" . $opt->id . "][]' value='" . $op . "' onclick='javascript:changeTarget();' /><span class='lbl'></span>";
			$output .= "<span>" . JText::_($op). "</span>";
			$output .= "<input type='hidden' value='".$opt->id."_".$i."' name='hidden_ids' />";
			$output .= "</label>";
			$i++;
		}
		$output .= "</div>";
		echo $output;
	}

	private static function getAges($with_any = false, $selected) {
		$out = "";
		if ($with_any) {
			$out .= "<option value='120'>" . JText::_('ADAG_ANY') . "</option>";
		}
		for ($i = 1; $i <= 99; $i++) {
			if ($i == $selected) {
				$sel = " selected='selected' ";
			} else { $sel = ""; }
			$out .= "<option value='" . $i . "' {$sel}>" . $i . "</option>";
		}
		return $out;
	}	
	
	public static function render_age($ad_id, $opt) {
		$vals     = self::getOptVal($ad_id, $opt->id);
		//echo "<hr /><pre>";	var_dump($vals);	echo "<pre><hr />";
		if (!is_array($vals) || count($vals) < 2) { $vals = array("-1", "-1"); }
		$output  = "<div class='uk-form-controls'>";
		$output .= "<select class='uk-display-inline-block uk-form-width-mini' id='jomsocial_".$opt->id."_1' name='jomsocial[" . $opt->id . "][]' onchange='javascript:changeTarget();'>";
		$output .= self::getAges(false, $vals[0]);
		$output .= "</select> - <select class='uk-display-inline-block uk-form-width-mini' id='jomsocial_".$opt->id."_2' name='jomsocial[" . $opt->id . "][]' onchange='javascript:changeTarget();'>";
		$output .= self::getAges(true, $vals[1]);
		$output .= "</select>";
		$output .= "<input type='hidden' value='".$opt->id."_1' name='hidden_ids' /><input type='hidden' value='".$opt->id."_2' name='hidden_ids' /></div>";
		echo $output;		
	}
	
	public static function render_birthdate($opt) {
		$_months = array("ADJAN", "ADFEB", "ADMAR", "ADAPR", "ADMAY", "ADJUN", "ADJUL", "ADAUG", "ADSEP", "ADOCT", "ADNOV", "ADDEC");
		$months = "";
		$count = 1;
		foreach ($_months as $month) {
			$months .= "<option value='" . $count . "'>" . JText::_($month) . "</option>";
			$count++;
		}
		$output = "<div class='uk-form-controls'>";
		$output .= "<input type='text' size='2' maxlength='2' />&nbsp;";
		$output .= "<select class='uk-display-inline-block' id='jomsocial_".$opt->id."' name='jomsocial[" . $opt->id . "]' onchange='javascript:changeTarget();'>";
		$output .= $months;
		$output .= "</select>&nbsp;";
		$output .= "<input type='text' size='4' maxlength='4' />&nbsp;";
		$output .= "<input type='hidden' value='".$opt->id."' name='hidden_ids' /></div>";
		echo $output;
	}
	
	

	 function visible($ad_id){
		if(!self::exists()){
			return true;
		}

		$db = JFactory::getDBO();
		$my	= JFactory::getUser();
		$uid = intval($my->id);

		// select all except birthdate, since that one has a special handler
		$sql = "
			SELECT j.field_id, j.value 
			FROM #__ad_agency_jomsocial AS j
			LEFT JOIN #__community_fields AS c
			ON c.id = j.field_id
			WHERE 
			c.type <> 'birthdate' AND
			j.ad_id = '" . intval($ad_id) . "' 
		";

		$db->setQuery($sql);
		$data = $db->loadObjectList("field_id");

		$sql = "
			SELECT j.field_id, j.value 
			FROM #__ad_agency_jomsocial AS j
			LEFT JOIN #__community_fields AS c
			ON c.id = j.field_id
			WHERE 
			c.type = 'birthdate' AND
			j.ad_id = '" . intval($ad_id) . "' 
		";

		$db->setQuery($sql);
		$data2 = $db->loadObjectList();
		
		if (count($data) == 0 && (@$data2[0]->value == '["1","120"]' || count($data) == 0)){
			// nothing is selected for filtering in Ad Agency so return visible always
			return true;

		}
		elseif($uid <= 0){
			// user not logged in, always return not visible 
			return false;
		}
		else{
			// check for user's data from JomSocial
			$sql = "SELECT field_id, value FROM #__community_fields_values WHERE user_id = '" . intval($uid) . "' ";
			$db->setQuery($sql);
			$user_data = $db->loadObjectList("field_id");

			$sql = "SELECT v.field_id, v.value FROM #__community_fields AS f
						LEFT JOIN #__community_fields_values AS v ON f.id = v.field_id
						WHERE v.user_id = '{$uid}' AND f.`type` = 'birthdate'";
			$db->setQuery($sql);
			$user_data2 = $db->loadObjectList();

			$fields = true;
			$birth = true;
			
			foreach($data as $d){
				if($d->value != NULL && trim($d->value) != ""){
					$value_array = json_decode($d->value);
					$user_opt = $user_data[$d->field_id]->value;
					
					if(!in_array($user_opt, $value_array)){
						$fields = false;
						break;
					}
				}
			}

			//for birthdate
			if($data2[0]->value != '["1","120"]'){
				foreach($user_data2 as $user){
					$temp = self::GetAge($user->value);
						foreach ($data2 as $birthday) {
								$birthday_values = json_decode($birthday->value);
								if ((intval($birthday_values[0]) <= intval($temp)) && (intval($temp) <= intval($birthday_values[1]))) {
									$birth = false;							
								}
						}
				}
			}
			
			if(!$fields && $birth){
				return false;
			}
			else{
				return true;
			}
		}
	}

	
	public static function GetAge($Birthdate) {
        // Explode the date into meaningful variables
        list($BirthYear, $BirthMonth, $BirthDay) = explode("-", $Birthdate);
		
        // Find the differences
        $YearDiff = date("Y") - $BirthYear;
        $MonthDiff = date("m") - $BirthMonth;
        $DayDiff = date("d") - $BirthDay;
		// If the birthday has not occured this year
        if ($MonthDiff < 0)
          $YearDiff--;
        return $YearDiff;
	}
	
	public static function save($ad_id){
		if(!self::exists()){
			return;
		}
		$db = JFactory::getDBO();
		$data = JRequest::get('post');
		
		$sql = "DELETE FROM #__ad_agency_jomsocial WHERE `ad_id` = '" . intval($ad_id) . "' ";
		$db->setQuery($sql);
		$db->query();
		if (is_array($data['jomsocial'])) {
			foreach ($data['jomsocial'] as $key => $val) {
				$temp_val  = '["';
				$temp_val .= implode('","', $val);
				$temp_val .= '"]';
				
				$sql = "
					INSERT INTO `#__ad_agency_jomsocial` (`id` ,`ad_id` ,`field_id` ,`value`) 
					VALUES (NULL , '" . intval($ad_id) . "', '" . intval($key) . "', '" . stripslashes($temp_val) . "');";
				$sqlz[] = $sql;
				$db->setQuery($sql);
				$db->query();
			}
		}
	}
	
	public static function extract_opts($opts) {
		$temp = array();
		$ops = explode("\n", $opts);
		foreach ($ops as $op) {
			if (trim($op) != '') { $temp[] = $op; };
		}
		return $temp;
	}
}