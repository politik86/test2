<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
//return;
if( empty($this->ads) )
	return;
$doc =JFactory::getDocument();
$ads = $this->ads;
$moduleid = $this->moduleid;
$zone_id = $this->zone;
$params = $this->adRetriever ;
$adRetriever = $this->adRetriever ;
$lang =  JFactory::getLanguage();
$lang->load('mod_socialads', JPATH_ROOT);

require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
$socialadshelper = new socialadshelper();
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base().'modules/mod_socialads/css/style.css');

if($params->get('ad_rotation',0) == 1)
{
	$socialadshelper->loadScriptOnce(JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'flowplayer-3.2.9.min.js');
	$socialadshelper->loadScriptOnce(JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'adrotation.js');
	$ad_rotationdelay	=	$params->get('ad_rotation_delay',10);
	?>
	<script>
		var site_link="";
		var user_id="";

		techjoomla.jQuery(document).ready(function()
		{
			var countdown;
			var module_id =	'<?php echo $moduleid?>';
			var ad_rotationdelay	=	<?php echo $ad_rotationdelay?>;
			techjoomla.jQuery(".sa_mod_<?php echo $moduleid?> .ad_prev_main").each(function() {
				if(techjoomla.jQuery(this).attr('ad_entry_number')){
					sa_init(this,module_id, ad_rotationdelay);
				}
			});

		});
	</script>
<?php
}
	//display ad html
$user	= JFactory::getUser();
$reqURI	=	JUri::root();
?>

<div class="sa_mod_<?php echo $moduleid?>"  havezone="<?php echo $zone_id?>" >
<?php

foreach($ads as $ad)
{
	$addata = $adRetriever->getAdDetails($ad);
	echo $adRetriever->getAdHTML($addata,0,$params->get('ad_rotation',0),$moduleid);

}

?>
</div>

