<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');
jimport( 'joomla.plugin.helper' );

require_once JPATH_COMPONENT . DS . 'helper.php';

class socialadsViewadsummary extends JViewLegacy
{
  /* Showad view display method */
	function display($tpl = null)
	{
		$input=JFactory::getApplication()->input;
		$adid = $input->get('adid',0,'INT');

		$user = JFactory::getUser();

		if($user->id)
		{
			//User authorized to view chat history
			if(! JFactory::getUser($user->id)->authorise('core.ad_summary', 'com_socialads'))
			{
				$app=JFactory::getApplication();
				$app->enqueueMessage(JText::_('COM_SOCIALADS_AUTH_ERROR'),'warning');
				return false ;
			}
		}

		//ad preview calling from helper function
		$preview = $this->get('Ads');

		$this->assignRef( 'preview',$preview );
		$session = JFactory::getSession();
		$model	= $this->getModel( 'adsummary');
		$session->clear('socialads_from_date');
		$session->clear('socialads_end_date');
		$session->clear('statsforbar');
		$session->clear('statsforpie');
		$session->clear('ignorecnt');
		$session->clear('statsfor_line_day_str_final');
		$session->clear('statsfor_line_imprs');
		$session->clear('statsfor_line_clicks');
		//calling line-graph function
		$statsforbar= $model->statsforbar();
		$this->assignRef( 'statsforbar',$statsforbar );

		//calling line-graph function
		$statsforpie= $model->statsforpie();
		$this->assignRef( 'statsforpie',$statsforpie );

		//ad details for ad history
		$payinfo = $this->get( 'Adpaymentinfo' );
		$this->assignRef( 'payinfo',	$payinfo );

		//check valid ad
		$adcheck = $this->get( 'adcheck' );
		$this->assignRef( 'adcheck',	$adcheck );

		//get charge option
		$chargeoption = $this->get( 'chargeoption' );
		$this->assignRef( 'chargeoption',	$chargeoption );

		//get ad type
		$adtype = $this->get( 'adtype' );
		$this->assignRef( 'adtype',	$adtype );

		//get ad-approval
	  $adapp = $this->get( 'adapproval' );
		$this->assignRef( 'adapp',	$adapp );
		//Extra code to get Zone price

		$zoneprice = $this->get('zoneprice' );

		if(!isset($zoneprice[0]->per_click))
		{
			$zoneprice[0]->per_click=0;
		}
		if(!isset($zoneprice[0]->per_imp))
		{
			$zoneprice[0]->per_imp=0;
		}
		if(!isset($zoneprice[0]->per_day))
		{
			$zoneprice[0]->per_day=0;
		}
		$this->assignRef( 'zoneprice',	$zoneprice );
		//Extra code to get Zone price

		$ignoreCount= $this->get('ignoreCount');
		$this->assignRef( 'ignoreCount123',	$ignoreCount );

		/* //vm:
		 * $socialadshelper = new socialadshelper;
		$user= JFactory::getUser();
		$socialadshelper->adStateForAddMoreCredit($adid,$user->id);*/

		parent::display($tpl);

	}//function ends here

}// view class ends here
