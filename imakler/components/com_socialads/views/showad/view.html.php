<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.parameter' );

class socialadsViewShowad extends JViewLegacy
{
    /* Showad view display method */



	function display($tpl = null)
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$user= JFactory::getUser();
		if(!$user->id==0)
		{

				global $mainframe;
				$mainframe = JFactory::getApplication();
				$buildadsession = JFactory::getSession();

				//print_r($buildadsession->get('camp')); die("view.html");


				$this->ncamp = $buildadsession->get('camp');
				$this->pricecamp = $buildadsession->get('pricing_opt') ;
				/*if($socialads_config['bidding']==1)
				{
				$this->assignRef( 'bid_value',$buildadsession->get('bid_value') );
				}*/
				$plugin = JPluginHelper::getPlugin( 'payment',$buildadsession->get('ad_gateway'));

				if( $socialads_config['select_campaign']==0)
				{
					$pluginParams = json_decode( $plugin->params );
					$this->assignRef( 'ad_gateway', $pluginParams->plugin_name);
						//added by sagar//
					$arb_enforce='';
					$this->assignRef( 'arb_enforce', $pluginParams->arb_enforce);
					$arb_enforce='';
					$this->assignRef( 'arb_support', $pluginParams->arb_support);
					//end added by sagar//
					$points1=0;
					if(isset($pluginParams->points))
					{
						if($pluginParams->points=='point')
						{
							$points1=1;
							//$points1=$this->get('JomSocialPoints');
							$this->assignRef( 'ad_points', $points1);
							$this->assignRef( 'ad_jconver',$pluginParams->conversion);
						}
					}
				}//if ends

					$this->ad_data = $buildadsession->get('ad_data');
					$this->ad_fields = $buildadsession->get('ad_fields') ;
					$this->chargeoption = $buildadsession->get('ad_chargeoption') ;
					$this->ad_totaldisplay = $buildadsession->get('ad_totaldisplay') ;// clicks or impr
					$this->ad_totalamount = $buildadsession->get('totalamount') ;
					$this->ad_img = $buildadsession->get('ad_image');
					$this->ad_totaldays = $buildadsession->get('ad_totaldays');
					$this->sa_recuring = $buildadsession->get('sa_recuring');
					$this->user_points = $buildadsession->get('user_points');
					$preview = $this->get('Ads');
					$this->preview = $preview ;
				//}
				// get payment HTML
					/*JLoader::import('payment', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
					$paymodel = new Quick2cartModelpayment();
					$payhtml = $paymodel->getHTML($order['order_info'][0]->processor,$orderid);
					*/
					parent::display($tpl);

		}
	}







}// class end
