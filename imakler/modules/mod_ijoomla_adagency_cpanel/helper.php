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

class modAdAgencyCPanelHelper
{
	function getParams(&$params)
	{
		$my	  	         	     = JFactory::getUser();
		$mosConfig_absolute_path =JPATH_BASE; 
		$mosConfig_live_site     =JURI::base();
		$database                = JFactory :: getDBO();
		
		// ADAGENCYONEFIVE-104 - new way of making Advertisers
		
		// check to see if a user is an advertiser (if he is approved as an advertiser)
		$sql = "SELECT `aid` FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."' ";
		$database->setQuery($sql);
		$adv_id= $database->loadResult();
		return $adv_id;
	}
    
    function getItemids(){
        $db = JFactory::getDBO();
        $controllers = array('adagencycpanel', 'adagencyadvertisers', 
                                    'adagencyads', 'adagencypackage', 'adagencycampaigns', 
                                    'adagencyreports', 'adagencyorders' );
        $itemid = new stdClass();
        foreach($controllers as $controller) {
            $sql =  "SELECT id FROM `#__menu` 
                        WHERE `menutype` = 'adagency' 
                        AND `link` LIKE '%index.php?option=com_adagency&view=".$controller."%' ";
            //echo "** " . $sql . " **";die();
            $db->setQuery($sql);
            $res = (int)$db->loadResult();
            if($res == 0) {
                $res = JRequest::getInt('Itemid','0');
            }
            $itemid->$controller = $res;
        }
        //echo "<pre>";var_dump($itemid);echo "</pre><hr />";
        return $itemid;
    }

    
}
?>