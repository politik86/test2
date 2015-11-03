<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class socialadsControllerManagezone extends socialadsController
{
	/**
	 * save a ad fields 
	 */
	 
	function save()
	{
	  // Check for request forgeries
	  
		//JSession::checkToken() or jexit( 'Invalid Token' );
		$model	= $this->getModel( 'managezone' );
		$post	= JRequest::get('post');
		$input=JFactory::getApplication()->input;
		// allow name only to contain html
	
		$model->setState( 'request', $post );	

		if ($model->store()) 
		{
			$msg = JText::_( 'C_SAVE_M_S' );
		}
		else 
		{
			$msg = JText::_( 'C_SAVE_M_NS' );
		}
		$task=$input->get('task');
		
		switch ( $task ) 
		{
			case 'cancel':
			$cancelmsg = JText::_( 'FIELD_CANCEL_MSG' );
			$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=managezone&layout=default", $cancelmsg );
			break;
			case 'save':
			$this->setRedirect( JUri::base()."index.php?option=com_socialads&view=managezone", $msg );
			break;
		}
			
		
	}
	//function save ends
	function getZonead()
	{
	
		$model	=& $this->getModel( 'managezone' );
		$createid=$model->getZoneaddata();
		echo $createid;
		exit();
		
	}
	
	function getList()
	{ 
		$input=JFactory::getApplication()->input;
		$addtype = $input->get('addtype');				//echo "1-----".
		$zonlay = $input->get('zonelayout' ,'');	//echo "2-----".
		$selected_layout1 = array();
		if($zonlay )
		{	JRequest::setVar( 'layout',$zonlay );
			
			$selected_layout_arr=explode('|',$zonlay);
			$i=0;
			foreach($selected_layout_arr as $selected_layout)
			{
				
				$selected_layout1[$i]=$selected_layout;
				$i++;
			}
		
		}
		if($addtype=='text')
		{
			$layout_type="Text";
		}
		else if($addtype=='img')
		{
			$layout_type="Image";
		}
		else if($addtype=='text_img')
		{
			$layout_type="Text And Image";
		}
		else
		{
			$layout_type="";
			$add_type[] = JHtml::_('select.option', '0', 'select');
			JHtml::_('select.genericlist', $add_type, 'layout_select', 'class="inputbox"  size=1', 'value', '' );
			exit;
		}
		$add_type= '';//'<table><tbody><tr>';
		$newvar = JPluginHelper::getPlugin( 'socialadslayout' );
		$sel_layout1 = array_values($selected_layout1);
		foreach($newvar as $k=>$v){

			$params = explode("\n",$v->params);
			

			foreach($params as $pa=>$p){
					if(JVERSION >= '1.6')
					{
									$lay=json_decode($p);
									if(isset($lay->layout_type))
									{
				
										if ($layout_type==$lay->layout_type)
										{

											$chk = '';
											$nam = substr($v->name,5);
											if(in_array($nam,$sel_layout1))
												$chk = 'checked="yes"';
											else if($layout_type == 'Image')
												$chk = 'checked="yes"';

												$add_type .= '<span style = "vertical-align:text-top;">
															<input type="checkbox" '.$chk.' name="layout_select" class="inputbox" value="'.$nam.'" />
															<img src="'.JUri::root().'plugins/socialadslayout/plug_'.$nam.'/plug_'.$nam.'/layout.png" >
															</span>&nbsp;&nbsp;&nbsp;';
															//JHtml::_('select.option', $nam , '<div class ="optimg" >'.$nam.'</div>' );
										}
									}
						}
						else
						{
							$lay = explode("=",$p);
									if($lay[0] == 'layout_type' )
									{
										if ($layout_type == $lay[1])
										{
											$chk = '';
											$nam = substr($v->name,5);
											if(in_array($nam,$sel_layout1))
												$chk = 'checked="yes"';
											else if($layout_type == 'Image')
												$chk = 'checked="yes"';
								$add_type .= '<span style = "vertical-align:text-top;">
											<input type="checkbox" '.$chk.' name="layout_select" class="inputbox" value="'.$nam.'" />
											<img src="'.JUri::Root().'plugins/socialadslayout/plug_'.$nam.'/layout.png" ></span>&nbsp;&nbsp;&nbsp;';
											//JHtml::_('select.option', $nam , '<div class ="optimg" >'.$nam.'</div>' );
										}
									}
						}
			}
		
		} 
//$add_type .='</tr></tbody></table>' ;
if($add_type == '')
	echo JText::_( 'NO_LAY');
else
echo $add_type; 
exit;


	}
	
}
?>
