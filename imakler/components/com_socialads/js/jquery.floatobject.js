
(function(jQuery){
/*----------------------------------------------------------------------------------
Class: FloatObject
-------------------------------------------------------------------------------------*/
	function FloatObject(jqObj, params)
	{
		this.jqObj = jqObj;
		params.speed='slow'
		switch(params.speed)
		{
			case 'fast': this.steps = 5; break;
			case 'normal': this.steps = 10; break;
			case 'slow': this.steps = 20; break;
			default: this.steps = 10;
		};
		
		var offset = this.jqObj.offset();
		
		this.currentX = offset.left;
		this.currentY = offset.top;
		
		
		this.origX = typeof(params.x) == "string" ?  this.currentX : params.x;
		this.origY = typeof(params.y) == "string" ?  this.currentY : params.y;
		//if( params.y) this.origY = params.y;

		//Added by sagar 
	
		this.divheight			= jQuery("#lowerdiv").height();
		this.currentX				= parseInt(jQuery("#mapping-field-table").width())+15;
		
		this.divheight_min	= parseInt(jQuery('#fixedElement').css('top'), 10);
		this.addetails			=  parseInt(jQuery('#ad-details-id').height())+parseInt(jQuery('#default_zone').height())+parseInt(jQuery('#componentheading-id').height())+parseInt(jQuery('#componentheading_field_id').height())+30;

		
		if(parseInt(this.currentY)>(parseInt(this.divheight)+parseInt(this.divheight_min)))
			this.currentY=parseInt(this.divheight)+parseInt(this.divheight_min);
		if(parseInt(this.currentY)<parseInt(this.addetails))  //this is top of #fixedElement
			this.currentY=parseInt(this.addetails);

		//Added by sagar

		//now we make sure the object is in absolute positions.
		this.jqObj.css({'position':'absolute' , 'top':this.currentY ,'right':10});
	}
	
	FloatObject.prototype.updateLocation = function()
	{
		this.updatedX = jQuery(window).scrollLeft() + this.origX;
		this.updatedY = jQuery(window).scrollTop()+ this.origY;
		
		this.dx = Math.abs(this.updatedX - this.currentX );
		this.dy = Math.abs(this.updatedY - this.currentY );
		
		return this.dx || this.dy;
	}
	
	FloatObject.prototype.move = function()
	{
		if( this.jqObj.css("position") != "absolute" ) return;
		var cx = 0;
		var cy = 0;

//		alert('aa=========='+this.divheight);
		if( this.dx > 0 )
		{			
			if( this.dx < this.steps / 2 )
				cx = (this.dx >= 1) ? 1 : 0;
			else
				cx = Math.round(this.dx/this.steps);
			
			if( this.currentX < this.updatedX )
				this.currentX += cx;
			else
				this.currentX -= cx;
		}
		
		if( this.dy > 0 )
		{
			if( this.dy < this.steps / 2 )
				cy = (this.dy >= 1) ? 1 : 0;
			else
				cy = Math.round(this.dy/this.steps);
		
			if( this.currentY < this.updatedY)
			{
				this.currentY += cy;
			}
			else
			{
				this.currentY -= cy;
				if(parseInt(this.currentY)>parseInt(this.addetails))
				this.currentY =parseInt(this.currentY) -200
				
			}
			
		}
		this.divheight=jQuery("#lowerdiv").height();
		if(parseInt(this.currentY)>(parseInt(this.divheight)+parseInt(this.divheight_min)) )
		this.currentY=parseInt(this.divheight)+parseInt(this.divheight_min);
		if(parseInt(this.currentY)<parseInt(this.addetails))  //this is top of #fixedElement
			this.currentY=parseInt(this.addetails);
		
		this.currentX				= parseInt(jQuery("#mapping-field-table").width())+15;
		this.jqObj.css({'right':10, 'top':this.currentY});			
	}

	
	
/*----------------------------------------------------------------------------------
Object: floatMgr
-------------------------------------------------------------------------------------*/		
	jQuery.floatMgr = {
		
		FOArray: new Array() ,
		
		timer: null ,
		
		initializeFO: function(jqObj,params) 
		{
			var settings =  jQuery.extend({
				x: 0 ,
				y: 0 ,
				speed: 'normal'	},params||{});
			var newFO = new FloatObject(jqObj,settings);
			
			jQuery.floatMgr.FOArray.push(newFO);
			
			if( !jQuery.floatMgr.timer )
			 jQuery.floatMgr.adjustFO();
			
			//now making sure we are registered to all required window events
			if( !jQuery.floatMgr.registeredEvents ) 
			{
					jQuery(window).bind("resize", jQuery.floatMgr.onChange);
					jQuery(window).bind("scroll", jQuery.floatMgr.onChange);
					jQuery.floatMgr.registeredEvents = true;
			}		
		} , 
		
		adjustFO: function() 
		{
			jQuery.floatMgr.timer = null;
			
			var moveFO = false;
			
			for( var i = 0 ; i < jQuery.floatMgr.FOArray.length ; i++ )
			{
				 FO = jQuery.floatMgr.FOArray[i];
				 if( FO.updateLocation() )  moveFO = true;
			}
			
			if( moveFO )
			{
				for( var i = 0 ; i < jQuery.floatMgr.FOArray.length ; i++ )
				{
					FO = jQuery.floatMgr.FOArray[i];
					FO.move();
				}
				
				if( !jQuery.floatMgr.timer ) jQuery.floatMgr.timer = setTimeout(jQuery.floatMgr.adjustFO,50);
			}
		}	,
		
		onChange: function()
		{
			if( !jQuery.floatMgr.timer ) jQuery.floatMgr.adjustFO();
		}
	};
	
/*----------------------------------------------------------------------------------
Function: makeFloat
-------------------------------------------------------------------------------------*/		
	jQuery.fn.makeFloat = function(params) {
		var obj = this.eq(0); //we only operate on the first selected object;
		jQuery.floatMgr.initializeFO(obj,params); 
		if( jQuery.floatMgr.timer == null ) jQuery.floatMgr.adjustFO();
		return obj;
	};
})(jQuery);
