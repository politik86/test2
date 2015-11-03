Joomla.submitbutton = function(action){
		
   		var form = document.adminForm;
		clearinvalid();
		if( action == 'cancel')
		{
			submitform( action );
			return;
		}
		
		
		
		var add_type_label=form.add_type
		
		var addtype=add_type_label.options[ add_type_label.selectedIndex].value;
		
		var zone_type_label=form.zone_type
		var zonetype=parseInt(zone_type_label.options[zone_type_label.selectedIndex].value);
		var flag=0
		// do field validation
		if (trim(form.zone_name.value) == ""  )
		{
			document.getElementById("validate_name").innerHTML="<?php echo JText::_('CC_YOU_MUST_PROVIDE_A_ ZONE_ NAME'); ?>"
			flag=1;
	
		}
		if (parseInt(zonetype)==0) 
		{
			 document.getElementById("validate_zone_type").innerHTML="<?php echo JText::_('CC_YOU_MUST_SELECT_ZONE_ORIENTATION'); ?>"
			 flag=1;
		}
		
		
		if (addtype=='text') 
		{
		 document.getElementById("validate_img_width").innerHTML="";
		 document.getElementById("validate_img_height").innerHTML="";
		
		 if ((trim(form.max_title.value) == "") )
			{
				 document.getElementById("validate_max_title").innerHTML="<?php echo JText::_('CC_YOU_MUST_PROVIDE_A_MAX_TITLE_CHAR'); ?>"						 
				 flag=1;
			}
			else
		    {
		    	if(isNaN(trim(form.max_title.value))|| (parseInt(form.max_title.value)==0))
		    	{
		    	 document.getElementById("validate_max_title").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
				 flag=1;
		    	}
		    	
		
	    	}
			
			
			if (trim(form.max_des.value) == "") 
				{
					 document.getElementById("validate_max_des").innerHTML="<?php echo JText::_('CC_YOU_MUST_PROVIDE_A_MAX_DESC_CHAR'); ?>"
					 flag=1;
				}
			else
				{
					if(isNaN(trim(form.max_des.value))|| (parseInt(form.max_des.value)==0))
					{
					 document.getElementById("validate_max_des").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
					 flag=1;
					}
							
				}		
		}
				
		if (addtype=='img') 
		{
				 document.getElementById("validate_max_title").innerHTML="";
				 document.getElementById("validate_max_des").innerHTML="";
				
				 if (trim(form.img_width.value) == "") 
					{
						 document.getElementById("validate_img_width").innerHTML="<?php echo JText::_('CC_YOU_PROVIDE_A_IMG WIDTH'); ?>"						 
						 flag=1;
					}
			    	else
				    {
				    	if(isNaN(trim(form.img_width.value))|| (parseInt(form.img_width.value)==0))
				    	{
				    	 document.getElementById("validate_img_width").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
						 flag=1;
				    	}
				    	
				
			    	}
				if (trim(form.img_height.value) == "") 
					{
						 document.getElementById("validate_img_height").innerHTML="<?php echo JText::_('CC_YOU_PROVIDE_A_IMG_HEIGHT'); ?>"
						 flag=1;
					}	
				else
				    {
				    	if(isNaN(trim(form.img_height.value))|| (parseInt(form.img_height.value)==0))
				    	{
				    	 document.getElementById("validate_img_height").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
						 flag=1;
				    	}
				    	
				
			    	}
		}
				
		if (addtype=='text_img') 
		{
			document.getElementById("validate_img_width").innerHTML="";
			document.getElementById("validate_img_height").innerHTML="";
			document.getElementById("validate_max_title").innerHTML="";
			document.getElementById("validate_max_des").innerHTML="";
			if (trim(form.max_title.value) == "") 
			{
				document.getElementById("validate_max_title").innerHTML="<?php echo JText::_('CC_YOU_MUST_PROVIDE_A_MAX_TITLE_CHAR'); ?>"						 
				flag=1;
			}
			else
		    {
		    	if(isNaN(trim(form.max_title.value))|| (parseInt(form.max_title.value)==0))
		    	{
		    	 document.getElementById("validate_max_title").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
				 flag=1;
		    	}
		    	
		
	    	}
			if (trim(form.max_des.value) == "") 
			{
				document.getElementById("validate_max_des").innerHTML="<?php echo JText::_('CC_YOU_MUST_PROVIDE_A_MAX_DESC_CHAR'); ?>"
				flag=1;
			}
			else
		    {
		    	if(isNaN(trim(form.max_des.value))|| (parseInt(form.max_des.value)==0))
		    	{
		    	 document.getElementById("validate_max_des").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
				 flag=1;
		    	}
		    	
		
	    	}	
			if (trim(form.img_width.value) == "") 
			{
			 	document.getElementById("validate_img_width").innerHTML="<?php echo JText::_('CC_YOU_PROVIDE_A_IMG_WIDTH'); ?>"						 
			 	flag=1;
			}
			else
		    {
		    	if(isNaN(trim(form.img_width.value))|| (parseInt(form.img_width.value)==0))
		    	{
		    	 document.getElementById("validate_img_width").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
				 flag=1;
		    	}
		    	
		
	    	}
			if (trim(form.img_height.value) == "") 
			{
			 	document.getElementById("validate_img_height").innerHTML="<?php echo JText::_('CC_YOU_PROVIDE_A_IMG_HEIGHT'); ?>"
			 	flag=1;
			}	
			else
		    {
		    	if(isNaN(trim(form.img_height.value))|| (parseInt(form.img_height.value)==0))
		    	{
		    	 document.getElementById("validate_img_height").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
				 flag=1;
		    	}
		    	
		
	    	}

				
		}
	///////////Code to Populate Layout
	
		var txtSelectedValuesObj = document.getElementById('layout');
		txtSelectedValuesObj.value='';			
		///////////Code to Populate Layout
		txtSelectedValuesObj.value = populatelayout();

	//////////////////////////
		if (txtSelectedValuesObj.value==0)
		{
			document.getElementById("validate_layout").innerHTML="<?php echo JText::_('CC_YOU_MUST_PROVIDE_A_LAYOUT'); ?>"
			flag=1;
	
		}
		
		var allow_pricing	= "<?php echo $socialads_config['zone_pricing']; ?>";
		if(parseInt(allow_pricing))
		{
			if (trim(form.per_click.value) == "")
			{
				document.getElementById("validate_per_click").innerHTML="<?php echo JText::_('CC_YOU_MUST_PROVIDE_A_PRICE_PER_CLICK'); ?>"
				flag=1;
	
			}
			else
		    {
		    	if(isNaN(trim(form.per_click.value))|| (parseInt(form.per_click.value==0)))
		    	{
		    	 document.getElementById("validate_per_click").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
				 flag=1;
		    	}
		    	
		
	    	}
			if (trim(form.per_imp.value) == "")
			{
				document.getElementById("validate_per_imp").innerHTML="<?php echo JText::_('CC_YOU_MUST_PROVIDE_A_PRICE_PER_IMPR'); ?>"
				flag=1;
	
			}
			else
		    {
		    	if(isNaN(trim(form.per_imp.value))|| (parseInt(form.per_imp.value==0)))
		    	{
		    	 document.getElementById("validate_per_imp").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
				 flag=1;
		    	}
		    	
		
	    	}
			if (trim(form.per_day.value) == "")
			{
				document.getElementById("validate_per_day").innerHTML="<?php echo JText::_('CC_YOU_MUST_PROVIDE_A_PRICE_PER_DAY'); ?>"
				flag=1;
	
			}
			else
		    {
		    	if(isNaN(trim(form.per_day.value))|| (parseInt(form.per_day.value==0)))
		    	{
		    	 document.getElementById("validate_per_day").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
				 flag=1;
		    	}
		    	
		
	    	}
		}
		if (trim(form.num_ads.value) == "")
		{
			
			document.getElementById("validate_num_ads").innerHTML="<?php echo JText::_('CC_YOU_MUST_PROVIDE_A_NO_OF_ADS'); ?>"
			flag=1;
	
		}
		else
		    {
		    	if(isNaN(trim(form.num_ads.value))|| (parseInt(form.num_ads.value==0)))
		    	{
		    	 document.getElementById("validate_num_ads").innerHTML="<?php echo JText::_('VALIDATE_NON_ZERO_NUMERIC'); ?>"						 
				 flag=1;
		    	}
		    	
		
	    	}
		if(!flag)
		{
			
			submitform( action );
		}
   
   
}
function populatelayout()
{
			var txtSelectedValuesObj = document.getElementById('layout');
			txtSelectedValuesObj.value=='';
			var selectedArray = new Array();
			var selObj = document.getElementById('layout_select');
			if(selObj)
			{
				var i;
				var count = 0;
				for (i=0; i<selObj.options.length; i++) {
					if (selObj.options[i].selected) {
					selectedArray[count] = selObj.options[i].value;
					count++;
					}
				}
				return selectedArray
			}
			

}
function clearinvalid()
{
	document.getElementById("validate_name").innerHTML="";
	document.getElementById("validate_zone_type").innerHTML="";
	document.getElementById("validate_add_type").innerHTML="";
	document.getElementById("validate_max_title").innerHTML="";
	document.getElementById("validate_max_des").innerHTML="";
	document.getElementById("validate_img_width").innerHTML="";
	document.getElementById("validate_img_height").innerHTML="";
	document.getElementById("validate_layout").innerHTML="";
	
		var allow_pricing	= "<?php echo $socialads_config['zone_pricing']; ?>";
		
		if(parseInt(allow_pricing))
		{
			document.getElementById("validate_per_click").innerHTML="";
			document.getElementById("validate_per_imp").innerHTML="";
			document.getElementById("validate_per_day").innerHTML="";
		}
	document.getElementById("validate_num_ads").innerHTML="";
	


}

function display(field)
{
	clearinvalid();
//	callajax(field.value);
	
			
		if(field.value == "text") {
		
			
			document.getElementById('img_width_row').style.display="none";
			document.getElementById('img_height_row').style.display="none";
			document.getElementById("img_width").value="";
		 	document.getElementById("img_height").value="";
			document.getElementById('max_title_char_row').style.display="";
			document.getElementById('max_desc_char_row').style.display="";
			document.getElementById('layout_row').style.display="";
			
			
		///////////Code to Populate Layout
	
		var txtSelectedValuesObj = document.getElementById('layout');
		txtSelectedValuesObj.value='';			
		

	//////////////////////////
			
		

	}
	else if(field.value == "img") { 
	
	  		document.getElementById('img_width_row').style.display="";
			document.getElementById('img_height_row').style.display="";
			document.getElementById("max_title").value="";
		 	document.getElementById("max_des").value="";
			document.getElementById('max_title_char_row').style.display="none";
			document.getElementById('max_desc_char_row').style.display="none";
			document.getElementById('layout_row').style.display="none";
			var txtSelectedValuesObj = document.getElementById('layout');
			txtSelectedValuesObj.value='';		
				
	}
	else if(field.value == "text_img") { 
	
	  		document.getElementById('max_title_char_row').style.display="";
			document.getElementById('max_desc_char_row').style.display="";
			document.getElementById('img_width_row').style.display="";
			document.getElementById('img_height_row').style.display="";
			document.getElementById("img_width").style.display="";
		 	document.getElementById("img_height").style.display="";
			var txtSelectedValuesObj = document.getElementById('layout');
			document.getElementById('layout_row').style.display="";
			txtSelectedValuesObj.value='';		
	}
	
	
}
