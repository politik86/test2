<?php
/**
 * @package Social Ads
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$lang =  JFactory::getLanguage();
$lang->load('mod_socialads', JPATH_ROOT);

require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base().'modules/mod_socialads/css/style.css');

$socialadshelper = new socialadshelper();
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
			var module_id =	<?php echo $moduleid?>;
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
<div class="techjoomla-bootstrap">
<div class="sa_mod_<?php echo $moduleid?>" havezone="<?php echo $zone_id?>" >
<?php
$cache = JFactory::getCache('mod_socialads');

if ($socialads_config['enable_caching'] == 1 )
	$cache->setCaching( 1 );
else
	$cache->setCaching( 0 );


foreach($ads as $ad)
{
	$addata  = $cache->call( array( $adRetriever, 'getAdDetails' ), $ad);
	$adHTML  = $cache->call( array( $adRetriever, 'getAdHTML' ), $addata,0,$params->get('ad_rotation',0),$moduleid );
	echo $adHTML;

	//this is for feedback
	if($socialads_config['feedback'] !=0){
	?>
	<div id = "feedback_msg<?php echo $addata->ad_id; ?>" class="ad_prev_main_feedback alert alert-info  alert-help-inline" style="display:none;">
		<?php echo JText::_('FEEDBACK_MSG'); ?>
	</div>
	<div id = "feedback<?php echo $addata->ad_id; ?>" class="well well-small" style="display:none;">
	<input id = "undo" type="button" name="undo" value = "<?php echo JText::_('MOD_UNDO'); ?>" onclick ="undo_ignore(this,<?php echo $addata->ad_id; ?>)" class="btn btn-primary"/>
	<input type="radio" name="group1" value="<?php 	echo JText::_('UNINT'); ?>" onclick ="sads_ignore(this,<?php echo $addata->ad_id; ?>)" /><?php echo JText::_('UNINT') ; ?><br />
	<input type="radio" name="group1" value="<?php echo JText::_('IRR'); ?>" onclick="sads_ignore(this,<?php echo $addata->ad_id; ?>)" /><?php echo JText::_('IRR') ; ?><br />
	<input type="radio" name="group1" value="<?php echo JText::_('MISLEAD'); ?>" onclick="sads_ignore(this,<?php echo $addata->ad_id; ?>)" /><?php echo JText::_('MISLEAD') ; ?><br />
	<input type="radio" name="group1" value="<?php echo JText::_('OFFEN'); ?>" onclick="sads_ignore(this,<?php echo $addata->ad_id; ?>)" /><?php echo JText::_('OFFEN') ; ?><br />
	<input type="radio" name="group1" value="<?php echo JText::_('REPET'); ?>" onclick="sads_ignore(this,<?php echo $addata->ad_id; ?>)" /><?php echo JText::_('REPET') ; ?><br />
	<input type="radio" name="group1" value="<?php echo JText::_('OTHER'); ?>" onclick="sads_ignore(this,<?php echo $addata->ad_id; ?>)" /><?php echo JText::_('OTHER') ; ?><br />
	</div>
	<?php
	}

	//sa jbolo integration
	if($socialads_config['se_jbolo'])
	{
		$jbolo_js=JUri::root().'modules/mod_socialads/js/jbolo.js';
		$socialadshelper= new socialadshelper();
		$socialadshelper->loadScriptOnce($jbolo_js);
		$integr_jbolo=$socialads_config['se_jbolo'];//$params->get('integr_jbolo',0);
		if(JFolder::exists(JPATH_SITE.DS.'components'.DS.'com_jbolo'))
		{
			$params=JComponentHelper::getParams('com_jbolo');
			$show_username_jbolo=$params->get('chatusertitle');
			$currentuser = JFactory::getUser();
			$adCreator=$modSocialadsHelper->getAdcreator($addata->ad_id);
			$adCreatordata = JFactory::getUser($adCreator);
			if($show_username_jbolo==1)
				$adcreatorname=$adCreatordata->username;
			else
				$adcreatorname=$adCreatordata->name;

			$caltype=0;
			$caltype=$modSocialadsHelper->getAdChargetype($addata->ad_id);
			if(!$caltype)
				$caltype=0;
			$adcreatorOnline=$modSocialadsHelper->isOnline($adCreator);
			$currentuseronline=$modSocialadsHelper->isOnline($currentuser->id);
			if($integr_jbolo and ($currentuser->id!=$adCreator) and $currentuseronline)
			{
				?>
				<div id="jbolo_sa_intgr_Chat_<?php echo $addata->ad_id;?>" class="jbolo_sa_intgr_Chat">
				<?php
					/*********** If Ad creator Online the Show Green Icon! **********/
					if($adcreatorOnline==1)
					{
						/*********** If Ad creator Online the Show Green Icon! **********/
						?>
						<span class="mf_chaton">
							<a onclick="javascript:returnval=chatFromAnywhere(<?php echo $currentuser->id.','.$adCreator; ?>);if(parseInt(returnval)!=0){countClickforchat('<?php echo $addata->ad_id; ?>','<?php echo $caltype; ?>',1);}" href="javascript:void(0)" title="<?php echo JText::_('MOD_SA_CHAT_WITH_AD_CREATOR'); ?>">
								<?php echo JText::_('MOD_SA_CHAT_WITH_AD_CREATOR'); ?>
							</a>
						</span>
							<?php
					}
					/*********** If Ad creator Online the Show Black Icon! **********/
					else
					{
						?>
						<span class="mf_chatoff">
							<a href='javascript:void(0)'><?php echo JText::_('MOD_SA_AD_CREATOR_OFFLINE') ; ?></a>
						</span>
						<?php
					}
						?>
				</div>
					<?php
			}
		}//if jbolo not found
	}
		?>

	<?php
	//generate unique ad url for social sharing
  	$ad_url='index.php?option=com_socialads&view=lightbox&layout=default&id='.$addata->ad_id;
  	//Integration with Jlike
		$jlikehtml=$adRetriever->DisplayjlikeButton($ad_url,$addata->ad_id,$addata->ad_title);

		if($jlikehtml)
		{
		?>
		<div style="clear:both;"></div>
		<div class="sa_ad_after_display">
		<?php

		echo $jlikehtml;
		?>
		</div>
		<?php

		}

       //Integration with Jlike
  	$ad_url=JUri::root().substr(JRoute::_($ad_url),strlen(JUri::base(true))+1);
	$add_this_share='';
	if($socialads_config['se_addthis'])
	{
		$socialadshelper= new socialadshelper();
		$add_this_share='
		<!-- AddThis Button BEGIN -->
		<div class="addthis_toolbox addthis_default_style ">
		<a href="https://www.addthis.com/bookmark.php"
		class="addthis_button"
		addthis:url="'.$ad_url.'"
		></a>
		</div>
		<!-- AddThis Button END -->
		' ;

		$pid = '';
		if($socialads_config['sa_addthis_pub'] != '')
		{
			$pid = '#pubid='.$socialads_config['sa_addthis_pub'];
		}

		$add_this_js='https://s7.addthis.com/js/250/addthis_widget.js'.$pid;

		$socialadshelper->loadScriptOnce($add_this_js);

		//output all social sharing buttons
		echo '<div class="social_share_container">
			<div class="social_share_container_inner">'.
		   		$add_this_share.
		   	'</div>
		</div>';
	}

	//for jomsocial like
	//if($socialads_config['se_jomsocial'])
	//{

		/*$jspath=JPATH_ROOT.DS.'components'.DS.'com_community';
		if(JFolder::exists($jspath)){
			include_once($jspath.DS.'libraries'.DS.'core.php');
		}
		CFactory::load( 'libraries' , 'like' );
		$likes	    = new CLike();
		//$likesHTML  = ($isMember && !$isBanned) ? $likes->getHTML( 'groups', $group->id, $my->id ) : $likes->getHtmlPublic( 'groups', $group->id );
		//$likesHTML  =  $likes->getHtmlPublic( 'groups', 2,42 );
		//$likesHTML  = $likes->getHTML( 'socialads', $addata->ad_id, 42);// : $likes->getHtmlPublic( 'groups', $group->id );
		//$likesHTML  = $likes->getHTML('socialads',$addata->ad_id,42);


		?>

		<!-- Event Top: App Like -->
		<div class="jsApLike">
			<span id="like-container">
				<?php // echo $likesHTML; ?>
			</span>
			<div class="clr"></div>
		</div>
		<!-- end: App Like -->

	<?php
	*/
	//}

}
//to show create ad link
if($params->get('create',1)){
	if( $params->get('create_page',0) == 0)
		$createpage = "";
	else
		$createpage = "_blank";
	$my = JFactory::getUser();
	$link = JUri::root().substr(JRoute::_('index.php?option=com_socialads&view=buildad&adtype='.$ad_type.'&adzone='.$zone_id.'&Itemid='.$Itemid),strlen(JUri::base(true))+1);
	if( $params->get('adlink_secure',0) == 1){
		$link = str_replace('http:','https:',$link );
	}
	if(!$my->id){
		if( $params->get('create_guest',0) == 1){
	?>
	<div style="clear:both;"></div><div class="ad_create_link"><a class ="create" target="<?php echo $createpage; ?>" href="<?php echo $link; ?>"><?php echo $params->get('create_text','Create Ad'); ?></a></div>
	<?php
		}
	}
	else{
	?>
	<div style="clear:both;"></div><div class="ad_create_link"><a class ="create" target="<?php echo $createpage; ?>" href="<?php echo $link; ?>"><?php echo $params->get('create_text','Create Ad'); ?></a></div>
	<?php
	}
}
?>
<div style="clear:both;"></div>
</div>
</div>
