(function($){

document.getElementById("set_height").style.height = $(window).height()-70;	
window.onresize = function (event) {
document.getElementById("set_height").style.height = $(window).height()-70;	
    
}


document.getElementById("set_height").style.height = $(window).height()-70;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];


$(".tablebody").each(function() {	    
	unplannedfilter();         
});

$('#getjobs').click(function(){	
	getmachines();
	window.location = baseUrl+'/index/'+$('#cell_select').val();
})
function getmachines()
{
	var cell = $('#cell_select').val();	
	
}
function unplannedfilter()
{
	var cell = $('#cell_select').val();	
	var machineid =$('#machines_select').val();		
	$(".bucket_filter option[value="+cell+"]").prop("selected", "selected");	
	
	get_unplannedjobs(cell,machineid); 
}

function get_unplannedjobs(cell,machineid,material_status,materialtype,reload)
{	
	
	//table = $('#unschedule').DataTable();	 
	//table.clear().destroy();
	var numberOfRows = Math.floor(window.innerHeight);	
	var height = (numberOfRows)-200;
	
	table = $("#unschedule").DataTable({
		//"dom": 'Bfirtlp',
		//"destroy": true,			
		"scrollY": "75vh",		
		"scrollX":     true,
		"scrollCollapse": true,
		"paging": false,		
        //"processing": false, // for show progress bar        
        //"filter": true, // this is for disable filter (search box)        
		//"order": [],		
		"columnDefs": [
			{
                target:[3],
                visible: false
            },
        ],
		"responsive": false,
		"oLanguage": {
		   "sInfo" : " Total _TOTAL_ jobs",// text you want show for info section
		},
		fixedColumns:   {
            left: 3
        },		
		select: {
			style:    'multi',            
        },	
		
	  "createdRow": function( row, data, dataIndex ) {
			$(row).addClass("open_jobcard");
			$(row).addClass("plan_jobs");
			//$(row).attr('data-id',data.jobid).addClass("open_jobcard");		
			},		
		
	});
	$(".selectAll").on( "click", function(e) {
		if ($(this).is( ":checked" )) {
		  table.rows({page:'current'}  ).select();        
		} else {
		  table.rows({page:'current'}  ).deselect(); 
		}
	});
	
}

$("body").on('click', '.plan_jobs', function() {		
	table = $('#unschedule').DataTable();	 
	var rows = table.rows( '.selected' ).indexes();
	if(rows.count()==0)
		$('.clearcalc').html('');		
	var res = getslectedrows();
	appendhours(res);
	
})
$("body").on('click', '.selectAll', function() {	
	table = $('#unschedule').DataTable();	 
	var rows = table.rows( '.selected' ).indexes();
	if(rows.count()==0)
		$('.clearcalc').html('');		
	var res = getslectedrows();
	appendhours(res);
	
})
	
function appendhours(data)
{	
	$.each(data, function(i, res){	
	$('.test_'+res.uniqueid).html('<h5 class="text-light bg-info pt-1 pb-1 text-center" style="font-size:20px;">'+parseFloat(res.prodhrs).toFixed(2)+'</h5>');		
	})	
}
$('.planjobssubmit').click(function(e){
	/** for button color start**/
	var allclass = document.getElementsByClassName('planjobssubmit');
	for(var i = 0; i < allclass.length; i++) { 
	  allclass[i].style.backgroundColor='';
	  allclass[i].addClass='bg-info';
	}
	var selectedclass = $(this);
	for(var i = 0; i < selectedclass.length; i++) { 
	  selectedclass[i].style.backgroundColor='#d1e6dc';
	  selectedclass[i].style.color='black';
	}	
	/** for button color end **/
	
	var myValues= $('#unschedule').DataTable();	
	var rows = table.rows( '.selected' ).indexes();
	var ids = $.map(myValues.rows(rows).data(), function (item) {
	return {
			jobid: item[2]			
			};		
	});
	
	var week = $(this).html();
	$.ajax({
		type:"post",
		url : baseUrl+'/planjobssubmit',
		data:{		
				data : ids,
				week : week
			 },
		dataType:'json',
		success:function (res){
			location.reload();
		},
		 error: function() {                       
			alert("An unknown error occured! Your change was not saved.\nClick ok to refresh page to ensure correct data.");
			location.reload();
		},
		complete: function() {
			$('#preloader').fadeOut('slow', function () {
				$(this).hide();
			});
		}
	})
	
})
function getslectedrows()
{
	var myValues= $('#unschedule').DataTable();	
	var rows = table.rows( '.selected' ).indexes();
	var ids = $.map(myValues.rows(rows).data(), function (item) {
	return {
			  uniqueid: item[19],
			   prodhrs:item[9],
			};		
	});	
	
	const res = Array.from(ids.reduce(
	  (m, {uniqueid, prodhrs}) => m.set(uniqueid,(m.get(uniqueid) || 0) + parseFloat(prodhrs)), new Map
	), ([uniqueid, prodhrs]) => ({uniqueid,prodhrs}));  
	
	return res;
}

$('.setactualhours').click(function(){
	$('input[name=standhr][value="78"]').prop('checked', true);
	$('input[name=workeffi][value="80"]').prop('checked', true);
	$('input[name=bankholiday][value="0"]').prop('checked', true);
	machineid = $(this).attr('data-machineid');
	week = $(this).attr('data-week');	
	$("#setactualhour").modal('show');
	$('.submitactualhours').attr('data-machineid',$(this).attr('data-machineid')); //set the machienid
	$('.submitactualhours').attr('data-week',$(this).attr('data-week')); //set the week
})
$('.submitactualhours').click(function(){
	var machineid = $(this).attr('data-machineid'); //get the mahcineid
	var week = $(this).attr('data-week'); //get the week
	var cell = $(this).attr('data-cell'); //get the cell
	var actualvalue = (($('input[name="standhr"]:checked').val()) - ($('input[name="bankholiday"]:checked').val())) * (($('input[name="workeffi"]:checked').val()) / 100);	
	 $.ajax({
            type: "POST",
            url: baseUrl+'/setactualvalue',
            data: {
                machineid: machineid,
                week: week,
                actualvalue: actualvalue.toFixed(2)
            },
            dataType: 'json',
            success: function (res) {	
				
				test(machineid,week,actualvalue,cell)
			},
			error:function(){}
	 });
	
})

function test(machineid,week,actualvalue,cell)
{
	$.ajax({
            type: "POST",
            url: baseUrl+'/machines',
            data: {
                cell: cell,
            },
            dataType: 'json',
            success: function (res) {	
				res.forEach(function(row){
					
					if(row.machine_unique == machineid)
						var test = row.machine_unique;
					
					if(row.week == week)
						var test2 = row.week;
					
					$('.acthr_'+test+'_'+test2).html(actualvalue.toFixed(2)); 
					$("#setactualhour").modal('hide');
				})
			},
			error:function(){}
	 });
}
$('.getbacklogjobs').click(function(){
	$("#gethrscommitweekjob").modal('show');
	gethrscommitweekjob($(this).attr('data-cell'));
})
$('.getjobshrscommit').click(function(){
	$("#gethrscommitweekjob").modal('show');
	gethrscommitweekjob($(this).attr('data-cell'),$(this).attr('data-machine'),$(this).attr('data-week'));
})
function gethrscommitweekjob(cell,machine='',week=false)
{		
console.log(machine);
	table = $('.getbacklogweekjobdatatable').DataTable();
	table.clear().destroy();		
	$(".getbacklogweekjobdatatable").DataTable({		
		"destroy": true,
        "processing": false, // for show progress bar
        "serverSide": false, // for process server side
        "filter": true, // this is for disable filter (search box)
        "orderMulti": false, // for disable multiple column at once
        "pageLength": 15,		
		//"order": [[ 7, "desc" ]],		
		"dom": 'B<"toolbar">frtip',
		/* "columnDefs": [
            
			{
					"targets": 5, // your case first column
					"className": "text-center",
					"width": "2%"
			   },
			   { "width": 1, "targets": 4 },
			   { "width": 3, "targets": 6 },
        ], */		
        "ajax": {
            "url": baseUrl+'/gethrscommitweekjob',
            "type": "POST",
			"data": {"cell":cell,"machine":machine,"week":week},
            "datatype": "json",
			"dataSrc": ""
        },
        "columns": [
			
            { "data": "jobid", "name": "jobid", "autoWidth": true },
            { "data": "partid", "name": "partid", "autoWidth": true },			
			{ "data": "partdesc", "name": "partdesc", "autoWidth": true ,
				"render": function (data, type, row) {
							 return  data.substring(0,10);
						  }
			},
			{ "data": "customer", "name": "customer", "autoWidth": true,
				"render": function (data, type, row) {
							 if(row.customer!=null)								
								return  data.substring(0,10);
							 else
								 return data;
						  }
			},				
			{ "data": "week", "name": "week", "autoWidth": true },		
			{ "data": "machine", "name": "machine", "autoWidth": true },
			{ "data": "cycletime", "name": "cycletime", "autoWidth": true },
        ],
"buttons":[]		

    });
}
Date.prototype.getWeekNumber = function(){
  var d = new Date(Date.UTC(this.getFullYear(), this.getMonth(), this.getDate()));
  var dayNum = d.getUTCDay() || 7;
  d.setUTCDate(d.getUTCDate() + 4 - dayNum);
  var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
  return Math.ceil((((d - yearStart) / 86400000) + 1)/7)
};

$('.closesetactualhour').click(function(){
	$('#setactualhour').modal('hide');
})
$('.closegethrscommitweekjob').click(function(){
	$('#gethrscommitweekjob').modal('hide');
})
})(jQuery);