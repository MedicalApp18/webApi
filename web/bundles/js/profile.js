function fetchEducationInfo(url, event){
	$.ajax({
		type:'GET',
		url:url,
		async: false,
		success:function(responsedata){
			var response=$.parseJSON(responsedata);
			var data = response.data;
			$("#education-info").html('').html(data);
		}
	});
}
function saveToDB(fieldName, filedValue, id, baseUrl, token){
	//console.log('Saving to the db');
	$(".success-msg1").removeClass('hide');
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
			setTimeout(function(){
				$(".success-msg1").addClass('hide');
			}, 2000);
			
			//var jqObj = jQuery(data); // You can get data returned from your ajax call here. ex. jqObj.find('.returned-data').html()
            // Now show them we saved and when we did
            var d = new Date();
            $('.form-status-holder').html('Saved! Last: ' + d.toLocaleTimeString());
		},
	});
}

function addToDB(fieldName, filedValue, baseUrl, token, count){
	//console.log('Saving to the db');
	$(".success-msg1").removeClass('hide');
	var submitData = fieldName+'='+filedValue;
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
			var response=$.parseJSON(data);
			$("#edu-id-"+count).val(response.id);
			setTimeout(function(){
				$(".success-msg1").addClass('hide');
			}, 2000);
			
			//var jqObj = jQuery(data); // You can get data returned from your ajax call here. ex. jqObj.find('.returned-data').html()
            // Now show them we saved and when we did
            var d = new Date();
            $('.form-status-holder').html('Saved! Last: ' + d.toLocaleTimeString());
		},
	});
}

function getNewEducationColumn(baseUrl, count){
	//console.log('Saving to the db');
	var cnt;
	cnt	= count;
	cnt++;
	$(".success-msg1").removeClass('hide');
	$.ajax({
		url: baseUrl+'/'+cnt,
		type: "GET",
		async: true,
//		beforeSend: function(xhr) {
//        	xhr.setRequestHeader('Authorization', 'Bearer ' + token);
//		},
		success: function(responsedata){
			$("#edu-count").val(cnt);
			var response=$.parseJSON(responsedata);
			var data = response.data;
			$(".after-add-more").after(data);
		},
	});
}