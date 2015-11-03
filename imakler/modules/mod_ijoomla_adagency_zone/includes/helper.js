function preloadAdImages(x) {
	if (document.images) {
		var i = 0;
		var imageArray = new Array();
		imageArray = x.split(',');
		var imageObj = new Image();
		for(i=0; i<=imageArray.length-1; i++) {
			imageObj.src=x[i];
		}
	}
}

function countClicks(url){
	var myAjax = new Request.HTML({
		method: 'get',
		asynchronous: 'true',
		url: url,
		data: { 'do' : '1' },
		onSuccess: function(data) {
		},
		onCreate: function(){
		}			
	}).send();	
	return true;
}