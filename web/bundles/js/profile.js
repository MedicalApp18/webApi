function fetchEducationInfo(url, event){
	$.ajax({
		type:'GET',
		url:url,
		async: false,
		success:function(responsedata){
			var response=$.parseJSON(responsedata);
			var data = response.data;
			$("#education-info").html('').html(data);
			event.preventDefault();
		}
	});
}
function saveToDB(fieldName, filedValue, id, baseUrl, token){
	//console.log('Saving to the db');
	var submitData = 'id='+id+'&'+fieldName+'='+filedValue;
    $.ajax({
		url: baseUrl,
		type: "POST",
		async: true,
		//data: {fieldName: filedValue,},
		data: submitData,
		beforeSend: function(xhr) {
            // Let them know we are saving
			xhr.setRequestHeader('Authorization', 'Bearer ' + token);
			$('.form-status-holder').html('Saving...');
		},
		success: function(data) {
			//var jqObj = jQuery(data); // You can get data returned from your ajax call here. ex. jqObj.find('.returned-data').html()
            // Now show them we saved and when we did
            var d = new Date();
            $('.form-status-holder').html('Saved! Last: ' + d.toLocaleTimeString());
		},
	});
}