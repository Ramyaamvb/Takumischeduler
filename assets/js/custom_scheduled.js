(function($){

document.getElementById("set_heights").style.height = $(window).height();
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
$(".bucket_filters").change(function(e) {
		event.preventDefault();
        selectcellmachine();
    });
	$('.bucket_filters').each(function(e){		
        selectcellmachine();
    });
	function selectcellmachine()
	{
		var filter = $(".bucket_filters :selected").val();		
		var SearchFieldsTable = $(".bucket tbody");		
		var trows = SearchFieldsTable[0].rows;
        if (filter == 'all') {
			$.each(trows, function (index, row) {				
				var ColumnName=$(row).attr("data-machine");
            $(row).show();
			});			
        } else {
			$.each(trows, function (index, row) {				
				var ColumnName=$(row).attr("data-machine");				
				if(ColumnName==filter) 
					
					$(row).show();		

				else
					$(row).hide();
			});
			
			
        }
	}
	
$('#scheduledjob').each(function(){
	scheduledjobs($('.machineid').val());	
})
function scheduledjobs(machineid)
{
	table = $('#scheduledjob').DataTable();	 
	//table.clear().destroy();
	var numberOfRows = Math.floor(window.innerHeight);	
	var height = (numberOfRows)-200;
	
	
	
	$("#scheduledjob").DataTable({
	"destroy": true,		
		"scrollX":     true,	
        "processing": false, // for show progress bar
        "serverSide": false, // for process server side
        "filter": true, // this is for disable filter (search box)
        "orderMulti": false, // for disable multiple column at once
        "pageLength": 16,	
		"bLengthChange": false,		
		/* "columnDefs": [
            {
                    
				"targets": [ 13 ],
                "visible": false,      
            },
			{
				orderable: false,
				className: 'schedule_jobs',
				targets:   0
			}
            
        ] ,*/
		fixedColumns:   {
            left: 2
        },	
			
        "ajax": {
            "url": baseUrl+'/scheduledjob/',
            "type": "POST",
			"data": {"machineid" : machineid},
            "datatype": "json",
			"dataSrc": ""
        },
        "columns": [
			 {
                data: null,
                className: "dt-center editor-delete",
                defaultContent: '',
                orderable: false,
				"render": function (data, type, row) {
							return '<input type="checkbox" class="unschedulecheckjobs" data-unschedulejob="'+row.jmpjobid+'" data-unscheduleoperid="'+row.operationid+'" name="unschedule_udpate"/>';
						  }
            },
            { "data": "jmpjobid", "name": "Jobid", "autoWidth": true },
            { "data": "jmppartid", "name": "partid", "autoWidth": true },
			{ "data": "jmpPartShortDescription", "name": "description", "autoWidth": true,
				"render": function (data, type, row) {
							return data.substring(0,12);
						  }
			},			
			{ "data": "schedulestart", "name": "partid", "autoWidth": true },
			{ "data": "cmoName", "name": "customer", "autoWidth": true,
				"render": function (data, type, row) {
							return data.substring(0,12);
						  }	
			},			
			{ "data": "Week", "name": "Week", "autoWidth": true },
			{ "data": "operationid", "name": "operationid", "autoWidth": true },
			{ "data": "quantity", "name": "orderqty", "autoWidth": true },
			
			{ "data": "Material_status", "name": "Material_status", "autoWidth": true },
			{ "data": "materialid", "name": "materialid", "autoWidth": true,				
						  
			},
			{ "data": "sheetrequired", "name": "sheetrequired", "autoWidth": true ,
			},			
			
			
        ],
		

    });
}

$('.unschedule_udpate').click(function(){
	var unschedulejob = $(this).data('unschedulejob');
	var unscheduleoperid = $(this).data('unscheduleoperid');
	var jobs = [];
	$("input[name=unschedule_udpate]:checked").each(function() { 
            jobs.push($(this).attr('data-unschedulejob')+' '+$(this).attr('data-unscheduleoperid'));			
     });	 
	$.ajax({
		type: "POST",
		url: baseUrl+'/unschedule_jobs/',
		data: {
			jobs : jobs
		},
		dataType:'json',
		success: function(res){
			scheduledjobs($('.machineid').val());
				
			
		}
	});
})


})(jQuery);

(function () {
    if (typeof EventTarget !== "undefined") {
        let func = EventTarget.prototype.addEventListener;
        EventTarget.prototype.addEventListener = function (type, fn, capture) {
            this.func = func;
            if(typeof capture !== "boolean"){
                capture = capture || {};
                capture.passive = false;
            }
            this.func(type, fn, capture);
        };
    };
}());