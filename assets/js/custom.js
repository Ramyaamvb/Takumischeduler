(function($){
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

console.log(baseUrl);

$('.getjobdetail').click(function(){
	var val =  $('.getjob').val();
	$.ajax({
		type: "POST",
		url : baseUrl+'base/getjobtatus',
		data:{ getjob : val},
		dataType:'json',
		success:function (res){	
			$('#getjobtatus').modal('show');
			$("#getjobtatusdetail > tbody").empty();			
			res.forEach(function(row){
					$('#getjobtatusdetail').append('<tr><td class="p-2">'+ row.jmpJobID +'</td><td class="p-2">'+ row.jmoJobOperationID +'</td>\
													<td class="p-2">'+ row.workcenter +'</td><td class="p-2">'+row.xaqDescription+'</td><td class="p-2">'+row.jobbucketweek+'</td>\
													<td class="p-2">'+row.ujmobucketweek+'</td><td class="p-2">'+row.ujmoScheduleQueue+'</td>\
													<td class="p-2">'+row.ClockinsStatus+'</td><td class="p-2">'+row.starttime+'</td></tr>');
				});
		},
			
	})
})
setTimeout(function() { 
        $('#preloader').fadeOut('slow', function () {
           $(this).remove();
        });
    }, 500);
})(jQuery);