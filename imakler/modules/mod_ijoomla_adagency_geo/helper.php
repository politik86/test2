<?php 
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html) * @author  iJoomla.com webmaster@ijoomla.com
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

class modAdAgencyGeoHelper{
	function getParams(&$params){
		
		$CONTINENT_NAME = array("AS"=>"Asia",
								"AF"=>"Africa",
								"EU"=>"Europe",
								"OC"=>"Australia/Oceania",
								"CA"=>"Caribbean",
								"SA"=>"South America",
								"NA"=>"North America");
	
		$my	  	         	     = JFactory::getUser();
		$mosConfig_absolute_path =JPATH_BASE; 
		$mosConfig_live_site     =JURI::base();
		$database                = JFactory :: getDBO();
		$rotator_content=NULL;
		$script=NULL;
		$http_host = explode(':', $_SERVER['HTTP_HOST'] );
		$the_rot_type = '';
		$module = $params;
		$it_id_con = NULL;
		$adv_here_bottom = NULL;
		$geo_output = "";
		
		if( (!empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) != 'off' || isset( $http_host[1] ) && $http_host[1] == 443) && substr( $mosConfig_live_site, 0, 8 ) != 'https://' ) {
			$mosConfig_live_site1 = 'https://'.substr( $mosConfig_live_site, 7 );
		} 
		else{
			$mosConfig_live_site1 = $mosConfig_live_site;
		}
		
		$database = JFactory::getDBO();
		$sql = "SELECT cityloc FROM #__ad_agency_settings LIMIT 1";
		$database->setQuery($sql);
		$files = $database->loadResult();
		//echo'<pre>'; print_r($files); die();
		if(file_exists(JPATH_BASE."/".$files)) {				
			require_once(JPATH_BASE."/components/com_adagency/helpers/geoip.inc");
			require_once(JPATH_BASE."/components/com_adagency/helpers/geoipcity.inc");
			include(JPATH_BASE."/components/com_adagency/helpers/geoipregionvars.php");
			
			if (!function_exists('json_encode')) {
				require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'jsonwrapper.php');
			}
			$gi = geoip_open(JPATH_BASE."/".$files, GEOIP_STANDARD);
            
            if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
            {
                $ip=$_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
            {
                $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            // check if isset REMOTE_ADDR and != empty
            elseif(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '') && ($_SERVER['REMOTE_ADDR'] != NULL))
            {
                $ip = $_SERVER['REMOTE_ADDR'];
            // you're probably on localhost
            } else {
                $ip = "127.0.0.1";
            }
            
			$record = geoip_record_by_addr($gi, $ip);

			$geo_output .= '<div id="geo_module" class="adg_table clearfix">';			
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_CONTINENT_NAME").'</div></div></div>';
			if(isset($record->continent_code)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$CONTINENT_NAME[$record->continent_code].'</div></div></div>';				
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_CONTINENT_CODE").'</div></div></div>';
			if(isset($record->continent_code)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->continent_code.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------			
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_COUNTRY_NAME").'</div></div></div>';
			if(isset($record->country_name)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->country_name.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------			
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 		'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_COUNTRY_CODE").'</div></div></div>';
			if(isset($record->country_code)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->country_code.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}
			$geo_output .= '</div>';
			
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 		'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_COUNTRY_CODE2").'</div></div></div>';
			if(isset($record->country_code)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->country_code3.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}
			$geo_output .= '</div>';		
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_REGION_NAME").'</div></div></div>';
			if(isset($record->region)){													   
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$GEOIP_REGION_NAME[$record->country_code][$record->region].'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
									
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_REGION_CODE").'</div></div></div>';
			if(isset($record->region)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->region.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_CITY_NAME").'</div></div></div>';
			if(isset($record->city)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->city.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_LATITUDE").'</div></div></div>';
			if(isset($record->latitude)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->latitude.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_LONGITUDE").'</div></div></div>';
			if(isset($record->longitude)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->longitude.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_POSTAL_CODE").'</div></div></div>';
			if(isset($record->postal_code)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->postal_code.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_AREA_CODE").'</div></div></div>';
			if(isset($record->area_code)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->area_code.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_DMA_CODE").'</div></div></div>';
			if(isset($record->dma_code)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->dma_code.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------
			$geo_output .= '<div class="adg_row adg_table_row">';
			$geo_output .= 	'<div class="type_information adg_cell span6 adg_table_cell"><div><div>'.JText::_("ADAGENCY_METRO_CODE").'</div></div></div>';
			if(isset($record->metro_code)){
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div>'.$record->metro_code.'</div></div></div>';
			}
			else{
				$geo_output .= 	'<div class="information adg_cell span6 adg_table_cell"><div><div> - </div></div></div>';
			}					
			$geo_output .= '</div>';
			//---------------------------------------------------------------------------------------	
			$geo_output .= '</div>';
			//$geo_output .= '<hr/>';
			
			return $geo_output;
		}		
	}	
};
?>