var modalWindow = {
	parent:"body",
	windowId:null,
	content:null,
	width:null,
	height:null,
	left:null,
	right:null,
	top:null,
	bigmodal:null,
	close:function()
	{
		jQuery(".modal-window").remove();
		jQuery(".modal-overlay").remove();
	},
	open:function()
	{
		modal_style = "";
		
		if(this.width != null && this.width != 0){
			screen_height = window.innerHeight;
			topval = 10;
			
			if( ((10/100)*screen_height) + this.height > screen_height){
				topval = 1;
			}
			
			modal_width = this.width + 65;
			if(this.bigmodal == 1){
				modal_width = this.width;
			}
			
			modal_style = 'style="height:'+(this.height + 10)+'px; width:'+modal_width+'px; margin-left:-'+(this.width / 2)+'px; top:'+topval+'%;"';
		}
		else{
			modal_style = 'style="left:'+this.left+'%; right:'+this.right+'%;"';
		}
		
		var modal = "";
		modal += "<div class=\"modal-overlay\"></div>";
		modal += "<div id=\"" + this.windowId + "\" "+modal_style+" class=\"modal-window modal p_modal \">";
		modal += this.content;
		modal += "</div>";
		jQuery(this.parent).append(modal);


		jQuery(".modal-window").append("<a class=\"close-window\" id=\"close-window\"></a>");
		jQuery(".close-window").click(function(){modalWindow.close();});
		jQuery(".modal-overlay").click(function(){modalWindow.close();});
	}
};

var openMyModal = function(width, height, source){
	modalWindow.windowId = "myModal";
	
	iframe_style = "";
	if(width != 0 && height != 0){
		screen_width = window.innerWidth;
		screen_height = window.innerHeight;
		
		if(screen_width > width && screen_height > height){
			modalWindow.width = width;
			modalWindow.height = height;
			modalWindow.bigmodal = 0;
		}
		else{
			modalWindow.width = screen_width - 60;
			modalWindow.height = screen_height - 30;
			modalWindow.bigmodal = 1;
			width = screen_width;
			height = screen_height - 35;
			source += '&change=1';
		}
		
		iframe_style = 'style="width:'+width+'px; height:'+(height+10)+'px;"';
	}
	else{
		modalWindow.width = 0;
		modalWindow.height = 0;
		iframe_style = 'style=""';
	}
	
	modalWindow.content = "<iframe "+iframe_style+" class='pub_modal_frame' src='" + source + "'>ceva</iframe>";
	modalWindow.open();
};