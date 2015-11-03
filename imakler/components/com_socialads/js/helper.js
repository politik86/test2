var pathname;
jQuery(document).ready(function() {
     pathname= window.location.pathname;
});

/* all content including images has been loaded */
window.onload = function() {
	var queries = (window.location.search || '?').substr(1).split("&"), params = {};

	/* Convert the array of strings into an object */
	for ( i = 0, l = queries.length; i < l; i++ ) {
		temp = queries[i].split('=');
		params[temp[0]] = temp[1];
	}

	if(params['option'] == 'com_socialads' & params['view'] == 'ads' & params['template'] == 'system' )
	{
		var sbody = document.body,
		html = document.documentElement,
		height = sbody.scrollHeight;
		/*var height = Math.max(sbody.scrollHeight, sbody.offsetHeight,
		html.clientHeight, html.scrollHeight, html.offsetHeight); */

	/*
		console.log( jQuery(html).height() + "PX innerHeight");
		console.log( sbody.scrollHeight +" PX sbody scrollHeight");
		console.log( sbody.offsetHeight +" PX sbody offsetHeight");
		console.log( html.clientHeight +" PX html clientHeight");
		console.log( html.scrollHeight +" PX html. scrollHeight");
		console.log( html.offsetHeight +" PX html offsetHeight");
		console.log("body "+ height +" PX");
	*/

	/* post our message to the parent */
		window.parent.postMessage(
			/* get height of the content */
			height
			/* set target domain */
			,"*"
		)
	}
};

function undo_ignore(el,adid)
  {
      jQuery.ajax({
			url: pathname,
			type: 'GET',
			dataType: 'html',
			data : {	option:'com_socialads',task:'undoignoreAd',ad_ignore_id:adid	},
			error: function ( xhr, errorType, exception ) {
				var errorMessage = exception || xhr.statusText;
				alert( "There was an error : " + errorMessage );
			},
			success: function(someResponse)
			{
				if(someResponse)
				{
					jQuery(el).parent().hide("slow");
      		jQuery(el).parent().prev().prev().show("slow");
				}
			}
		});

  }

function ignore_ads(el,adid,remove)
{
      jQuery.ajax({
			url: pathname,
			type: 'GET',
			dataType: 'html',
			data : {	option:'com_socialads',task:'ignoreAd',ad_ignore_id:adid	},
			error: function ( xhr, errorType, exception ) {
				var errorMessage = exception || xhr.statusText;
				alert( "There was an error : " + errorMessage );
			},
			success: function(someResponse)
			{
				if(someResponse)
				{
					var el_par ;
					if(remove){
					el_par = jQuery(el).closest(".ad_prev_main[preview_for='"+adid+"']");
					el_par.hide("slow");
					document.getElementById("feedback"+adid).style.display = 'block';
					}
					else
					{
					el_par = jQuery(el).closest(".ad_prev_main[preview_for='"+adid+"']");
					el_par.hide("slow");
					}
				}
			}
		});
}


function sads_ignore(el,adid)
{
  var v = el.value ;
  jQuery.ajax({
	url: pathname,
	type: 'GET',
	dataType: 'html',
	data : {option:'com_socialads',task:'ignoreAd',ad_ignore_id:adid,ad_feedback:v},
	timeout: 3500,
	error: function ( xhr, errorType, exception ) {
		var errorMessage = exception || xhr.statusText;
		alert( "There was an error : " + errorMessage );
	},
	success: function(someResponse)
	{
		if(someResponse)
		{
			var el_par = jQuery(el).parent();
			el_par.hide("slow");
			document.getElementById("feedback_msg"+adid).style.display = 'block';
		}
	}
	});
}
