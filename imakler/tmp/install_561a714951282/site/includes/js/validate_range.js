function validateFrom(msg_from_less_start, msg_from_greater_stop, msg_invalid_date){
	re = /^(\d{4})-(\d{1,2})-(\d{1,2})$/;

	min_value = new Date(document.getElementById("min").value);
	max_value = new Date(document.getElementById("max").value);
	
	start_date = new Date(document.getElementById("start_date").value);
	stop_date = new Date(document.getElementById("stop_date").value);
	
	if(!document.getElementById("start_date").value.match(re)){
		alert(msg_invalid_date);
		document.getElementById("start_date").value = "";
		return false;
	}
	else if(start_date < min_value){
		alert(msg_from_less_start);
		document.getElementById("start_date").value = document.getElementById("min").value;
		return false;
	}
	else if(start_date > max_value){
		alert(msg_from_greater_stop);
		document.getElementById("start_date").value = document.getElementById("max").value;
		return false;
	}
}

function validateTo(msg_to_greater_stop, msg_to_less_start, msg_to_less_from, msg_invalid_date){
	re = /^(\d{4})-(\d{1,2})-(\d{1,2})$/;
	
	min_value = new Date(document.getElementById("min").value);
	max_value = new Date(document.getElementById("max").value);
	
	start_date = new Date(document.getElementById("start_date").value);
	stop_date = new Date(document.getElementById("stop_date").value);
	
	if(!document.getElementById("stop_date").value.match(re)){
		alert(msg_invalid_date);
		document.getElementById("stop_date").value = "";
		return false;
	}
	else if(stop_date > max_value){
		alert(msg_to_greater_stop);
		document.getElementById("stop_date").value = document.getElementById("start_date").value;
		return false;
	}
	else if(stop_date < start_date){
		alert(msg_to_less_from);
		document.getElementById("stop_date").value = document.getElementById("start_date").value;
		return false;
	}
	else if(stop_date < min_value){
		alert(msg_to_less_start);
		document.getElementById("stop_date").value = document.getElementById("start_date").value;
		return false;
	}
}