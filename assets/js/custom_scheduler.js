(function($){

document.getElementById("set_height").style.height = $(window).height()-70;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
$(".bucket_filter").change(function(e) {
		event.preventDefault();
        selectcellmachine();
    });
	$('.bucket_filter').each(function(e){		
        selectcellmachine();
    });
	function selectcellmachine()
	{
		var filter = $(".bucket_filter :selected").val();		
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
	$('#cell_select').change(function(e){	
		$('#machines_select').val('');
		selectmachines();
	})
	$('#cell_select').each(function(){	
		selectmachines();	
		
	})
	function selectmachines()
	{
		$("#machines_select").removeAttr('disabled');
		var filter = $("#cell_select :selected").val();
			
			$("#machines_select option").each(function(e) {								
                if ($(this).attr('data-cell') == filter)						
				    $(this).show();	 
                else
                    $(this).hide();					
            });
			//event.preventDefault();	
        
	}
/**Schedule jobs 14/12**/
var test = false;
$('#sheetsadd').each(function(){
	
	material_used();	
	material_onhand_check();	
	
	
});

$('.schedulejobs').each(function(){
	scheduledjobs_update();
})
function scheduledjobs_update()
{
	$.ajax({
		type: "POST",
		url: baseUrl+'/getscheduledhours/',
		data: {
			
		},
		dataType:'json',
		success: function(res){		
			var total=0;
			$.each(res, function(i, res){	
				var week = res.bucketweek.split('-');	
				if(($('.latehours'+week[1]+'_'+res.machine_unique).attr('data-unique')) == res.machine_unique)
					if(res.latehours == '')	
						var latehours = 0;					
					else
						var latehours = res.latehours;					
					total = parseFloat(res.latehours) + parseFloat(res.futurehours) + parseFloat(res.cwhours);					
					$('.totalhours'+week[1]+'_'+res.machine_unique).html('-');	
					$('.latehours'+week[1]+'_'+res.machine_unique).html('-');
					$('.currenthours'+week[1]+'_'+res.machine_unique).html('-');
					$('.futurehours'+week[1]+'_'+res.machine_unique).html('-');					
					$('.totalhours'+week[1]+'_'+res.machine_unique).html(parseFloat(total).toFixed(2));	
					$('.latehours'+week[1]+'_'+res.machine_unique).html(parseFloat(latehours).toFixed(2));
					$('.currenthours'+week[1]+'_'+res.machine_unique).html(parseFloat(res.futurehours).toFixed(2));
					$('.futurehours'+week[1]+'_'+res.machine_unique).html(parseFloat(res.cwhours).toFixed(2));	
			})							
		},
		error: function() {
		}
	})
}

$('.hours_table').each(function(){
	cellhours_update();
})

function cellhours_update()
{
	$.ajax({
		type: "POST",
		url: baseUrl+'/scheduledhours_cell/',
		data: {			
		},
		dataType:'json',
		success: function(res){	
			
			$.each(res, function(i, res){
				var week = res.bucketweek.split('-');							
				if($('.bl_'+(res.workcenter)).attr('data-cellname') == res.workcenter.trim()){
					$('.cell_hours_bl_unscheduled_'+res.workcenter).html(parseFloat(res.unscheduledhrs).toFixed(0));
					$('.cell_hours_bl_scheduled_'+res.workcenter).html(parseFloat(res.scheduledhrs).toFixed(0));
					$('.cell_hours_bl_total_'+res.workcenter).html(parseFloat(res.totalhours).toFixed(0));
				}
				if($('.week_'+week[1]+'_'+(res.workcenter)).attr('data-cellname') == res.workcenter.trim()){
					$('.cell_hours_'+week[1]+'_unscheduled_'+res.workcenter).html(parseFloat(res.unscheduledhrs).toFixed(0));
					$('.cell_hours_'+week[1]+'_scheduled_'+res.workcenter).html(parseFloat(res.scheduledhrs).toFixed(0));
					$('.cell_hours_'+week[1]+'_total_'+res.workcenter).html(parseFloat(res.totalhours).toFixed(0));
				}
						
			})							
		},
		error: function() {
		}
	})
}

function get_unschedule_jobs(cell,machineid,material_status,materialtype,reload)
{	
	
	table = $('#unschedule').DataTable();	 
	//table.clear().destroy();
	var numberOfRows = Math.floor(window.innerHeight);	
	var height = (numberOfRows)-200;
	
	
	
	$("#unschedule").DataTable({
	"destroy": true,		
		"scrollX":     true,	
        "processing": false, // for show progress bar
        "serverSide": false, // for process server side
        "filter": true, // this is for disable filter (search box)
        "orderMulti": false, // for disable multiple column at once
        "pageLength": 15,	
		"bLengthChange": false,		
		"columnDefs": [
            {
                    
				"targets": [ 13 ],
                "visible": false,      
            },
			{
				orderable: false,
				className: 'schedule_jobs',
				targets:   0
			}
            
        ],
		fixedColumns:   {
            left: 2
        },	
		select: {
            style:    'multi',
            
        },		
        "ajax": {
            "url": baseUrl+'/schedulefilter/',
            "type": "POST",
			"data": {"cell" : cell,"machineid":machineid,"material_status":material_status,"materialtype":materialtype,},
            "datatype": "json",
			"dataSrc": ""
        },
        "columns": [
			 {
                data: '',
                className: "dt-center editor-delete",
                defaultContent: '',
                orderable: false,				
            },
            { "data": "jobid", "name": "Jobid", "autoWidth": true },
            { "data": "partid", "name": "partid", "autoWidth": true },
			{ "data": "schedulestart", "name": "customer", "autoWidth": true },
			{ "data": "customer", "name": "description", "autoWidth": true
			},
			{ "data": "operationid", "name": "operationid", "autoWidth": true,
				"render": function (data, type, row) {
							if(row.checkoperation!='') { return  Math.round(data)+'<span class="text-info"> Next OP</span>'; }
							else { return  Math.round(data); }
						  }
			},			
			{ "data": "jmpProductionQuantity", "name": "jmpProductionQuantity", "autoWidth": true,
				"render": function (data, type, row) {
							return  Math.round(data);
						  }
			},
			{ "data": "Estimatedprodhrs", "name": "Estimatedprodhrs", "autoWidth": true,
				"render": function (data, type, row) {
							return  parseFloat(data).toFixed(2);
						  }
			},
			{ "data": "Material_status", "name": "Material_status", "autoWidth": true },
			{ "data": "materialid", "name": "materialid", "autoWidth": true,				
						  
			},
			{ "data": "sheetrequired", "name": "sheetrequired", "autoWidth": true ,
			},
			{ "data": "partdesc", "name": "partdesc", "autoWidth": true },
			{ "data": "machine", "name": "machine", "autoWidth": true },
			{ "data": "xaqUniqueID", "name": "xaqUniqueID", "autoWidth": true },
			
			
			
        ],
		createdRow: function( row, data, dataIndex ) {
             

		$(row).addClass("open_jobcard");
		$(row).addClass("schedule_jobs");
		$(row).attr('data-id',data.jobid).addClass("open_jobcard");
		
		}
		

    });
}
function week(year,month,day) {
    function serial(days) { return 86400000*days; }
    function dateserial(year,month,day) { return (new Date(year,month-1,day).valueOf()); }
    function weekday(date) { return (new Date(date)).getDay()+1; }
    function yearserial(date) { return (new Date(date)).getFullYear(); }
    var date = year instanceof Date ? year.valueOf() : typeof year === "string" ? new Date(year).valueOf() : dateserial(year,month,day), 
        date2 = dateserial(yearserial(date - serial(weekday(date-serial(1))) + serial(4)),1,3);
    return ~~((date - date2 + serial(weekday(date2) + 5))/ serial(7));
}

$(".tablebody").each(function() {
	
	var cell = 'mill5';
	var machineid ='all';
	var materialtype ='all';
	var material_status='all';      
	get_unschedule_jobs(cell,machineid,material_status,materialtype,0);         
});
$('#getjobs').click(function(){	
	unschedulefilter();
})

function unschedulefilter()
{
	var cell = $('#cell_select').val();	
	var machineid =$('#machines_select').val();
	var material_status =$('#material_status').val();	
	var materialtype=$('#materialtype').val();;      
	$(".bucket_filter option[value="+cell+"]").prop("selected", "selected");	
	selectcellmachine();
	get_unschedule_jobs(cell,machineid,material_status,materialtype,1); 
}

/**15/12**/
$("body").on('click', '.schedule_jobs', function() {	

	materialupdate();
	hoursmachineupdate();		
});

document.getElementById("chooseweek").onchange = function(){
    materialupdate();
	hoursmachineupdate();
};

function materialupdate()
{	

	theArray=[];
	var myValues= $('#unschedule').DataTable();

	var ids = $.map(myValues.rows('.selected').data(), function (item) {					
	return {
			  materialuniqueid: Base64.encode(item.materialid),
			  sheet:item.sheetrequired
			};		 
	});
		
   const res = Array.from(ids.reduce(
		  (m, {materialuniqueid, sheet}) => m.set(materialuniqueid,(m.get(materialuniqueid) || 0) + parseFloat(sheet)), new Map
		), ([materialuniqueid, sheet]) => ({materialuniqueid,sheet}));  
		
		//console.log(res);
	appendmaterial(res);
}

function hoursmachineupdate()
{
	
	theArray=[];
	var myValues= $('#unschedule').DataTable();

	var ids = $.map(myValues.rows('.selected').data(), function (item) {					
	return {
			  uniqueid: item.xaqUniqueID,
			  prodhrs:item.Estimatedprodhrs,
			  week:item.schedulestart
			};		 
	});
		
   const res = Array.from(ids.reduce(
		  (m, {uniqueid, prodhrs,week}) => m.set(uniqueid,(m.get(uniqueid) || 0) + parseFloat(prodhrs),week), new Map
		), ([uniqueid, prodhrs,week]) => ({uniqueid,prodhrs,week}));  
		
		//console.log(res);
	appendhours(res);	
	
}


function getWeekNumber(d) {
   
    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));   
    d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));   
    var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
    return weekNo;
}
function appendhours(data)
{
	var todays = new Date();
	var dds = String(todays.getDate()).padStart(1, '0');
	var mms = String(todays.getMonth()).padStart(1, '0'); //January is 0!
	var yyyys = todays.getFullYear();

	/*today = mm + '/' + dd + '/' + yyyy;
	document.write(today); */
var sum=0;var holder = {};
	$.each(data, function(i, res){	
	var today = new Date(res.week);
	var dd = String(today.getDate()).padStart(1, '0');
	var mm = String(today.getMonth()).padStart(1, '0'); //January is 0!
	var yyyy = today.getFullYear();	
	
		datestart = new Date(yyyys,mms,dds); // today date
		dateend = new Date(yyyy,mm,dd);		// res date
		
		datestartweek = getWeekNumber(datestart);
		dateendweek = getWeekNumber(dateend);		
		var a =[res.prodhrs];
		for (var i = 0; i < a.length; i++) {
			if (a[i] == 1) a.push(5);	
			//if(Date.parse(datestart) < Date.parse(dateend))		
			//if(($('.latehours_'+(res.uniqueid)).attr('data-unique')) == res.uniqueid)
				//myFunction ('2022-01-06','2021-12-20');
			 if (holder.hasOwnProperty(res.uniqueid)) {
				holder[res.uniqueid] = parseFloat(holder[res.uniqueid]) + parseFloat(res.prodhrs);
			  } else {
				holder[res.uniqueid] = res.prodhrs;
			}			
			//sum+=parseFloat(a[i]);			
		}		
		//console.log(sum);
	})
	var obj2 = [];
	for (var prop in holder) {
	  obj2.push({ uniqueid: prop, prodhrs: holder[prop] });
	}	
	
	$('.machine_hrs_calc').html('');	
	var chooseweek = $('.chooseweek').val();		
	$.each(obj2, function(i, res){	
	$('.totalhours_calc'+chooseweek+'_'+res.uniqueid).html(parseFloat(res.prodhrs).toFixed(2));	
	})
}
function myFunction(d1, d2) {

var year1 = parseInt(d1.substring(0, 4)); //2018
var year2 = parseInt(d2.substring(0, 4));
var yearDiff = Math.abs(year1 - year2);
var weeksInYears = 52 * yearDiff;

var day1 = parseInt(d1.substring(6, ));  //01
var day2 = parseInt(d2.substring(6, ));  //05

var difference = Math.abs(weeksInYears - (day2 - day1));  // handled dates with different years
var week1 = day2 ;
var week2 = week1 + difference;
week1 = ((week1 < 10 ? '0' : '') + week1);
week2 = ((week2 < 10 ? '0' : '') + week2)
var res = year1 + "-" + "W" + (week1); // ?
var res1 = year2 + "-" + "W" + (week2);
}
function appendmaterial(data)
{
	
	
	var text = $('#material_row tr th:nth-child(2)').text();
	var fr = text.split(" ");				
	var text2 = $('#material_row tr th:nth-child(3)').text();
	var sr = text2.split(" ");	
	var text3 = $('#material_row tr th:nth-child(4)').text();
	var tr = text3.split(" ");	
	
	$.each(data, function(i, res){
	 
	 var chooseweek = $('.chooseweek').val();	
	 var firstrow_sheetonhand = $('.sheet_onhand_'+fr[1]+'_'+res.materialuniqueid).attr('data-sheetonhand');	
	 var firstrow_sheetused = $('.sheet_used_'+fr[1]+'_'+res.materialuniqueid).attr('data-sheetused');
	 var secondrow_sheetonhand = $('.sheet_onhand_'+sr[1]+'_'+res.materialuniqueid).attr('data-sheetonhand');
	 var secondrow_sheetused = $('.sheet_used_'+sr[1]+'_'+res.materialuniqueid).attr('data-sheetused');
	 var thirdrow_sheetonhand = $('.sheet_onhand_'+tr[1]+'_'+res.materialuniqueid).attr('data-sheetonhand');
	 var thirdrow_sheetused = $('.sheet_used_'+tr[1]+'_'+res.materialuniqueid).attr('data-sheetused');
	
	if(chooseweek=='')
	{
		$('.sheet_used_'+fr[1]+'_'+res.materialuniqueid).html(parseFloat(firstrow_sheetused).toFixed(2));
		$('.sheet_used_'+sr[1]+'_'+res.materialuniqueid).html(parseFloat(secondrow_sheetused).toFixed(2));
		$('.sheet_used_'+tr[1]+'_'+res.materialuniqueid).html(parseFloat(thirdrow_sheetused).toFixed(2));
		$('.sheet_onhand_'+fr[1]+'_'+res.materialuniqueid).html(parseFloat(firstrow_sheetonhand).toFixed(2));		
		$('.sheet_onhand_'+sr[1]+'_'+res.materialuniqueid).html(parseFloat(secondrow_sheetonhand).toFixed(2));
		$('.sheet_onhand_'+tr[1]+'_'+res.materialuniqueid).html(parseFloat(thirdrow_sheetonhand).toFixed(2));
	}
	if(fr[1]==chooseweek) 
	{		
		$('.sheet_used_'+fr[1]+'_'+res.materialuniqueid).html(parseFloat(parseFloat(firstrow_sheetused) + parseFloat(res.sheet)).toFixed(2));		
		var calculate = parseFloat(			
			parseFloat(firstrow_sheetonhand) - parseFloat(parseFloat(firstrow_sheetused) + parseFloat(res.sheet))
			).toFixed(2);			
		$('.sheet_onhand_'+sr[1]+'_'+res.materialuniqueid).html(calculate);
		$('.sheet_onhand_'+tr[1]+'_'+res.materialuniqueid).html(parseFloat(parseFloat(calculate)-parseFloat(secondrow_sheetused)).toFixed(2));
		
	}
	if(sr[1]==chooseweek) 
	{
		$('.sheet_used_'+fr[1]+'_'+(res.materialuniqueid)).html(parseFloat(firstrow_sheetused).toFixed(2));
		$('.sheet_used_'+sr[1]+'_'+(res.materialuniqueid)).html(parseFloat(thirdrow_sheetused).toFixed(2));
		$('.sheet_onhand_'+sr[1]+'_'+(res.materialuniqueid)).html(parseFloat(secondrow_sheetonhand).toFixed(2));
		var ret_calc = parseFloat(secondrow_sheetused) + parseFloat(res.sheet);		
		$('.sheet_used_'+sr[1]+'_'+(res.materialuniqueid)).html(parseFloat(ret_calc).toFixed(2));			
		$('.sheet_onhand_'+tr[1]+'_'+(res.materialuniqueid)).html(parseFloat(parseFloat(parseFloat(secondrow_sheetonhand)-parseFloat(ret_calc))).toFixed(2));
	}
	if(tr[1]==chooseweek) 
	{
		
		$('.sheet_used_'+fr[1]+'_'+(res.materialuniqueid)).html(parseFloat(firstrow_sheetused).toFixed(2));
		$('.sheet_used_'+sr[1]+'_'+(res.materialuniqueid)).html(parseFloat(secondrow_sheetused).toFixed(2));
		$('.sheet_onhand_'+sr[1]+'_'+(res.materialuniqueid)).html(parseFloat(secondrow_sheetonhand).toFixed(2));
		$('.sheet_onhand_'+fr[1]+'_'+(res.materialuniqueid)).html(parseFloat(firstrow_sheetonhand).toFixed(2));
		$('.sheet_onhand_'+tr[1]+'_'+(res.materialuniqueid)).html(parseFloat(thirdrow_sheetonhand).toFixed(2));
		var ret_calc = parseFloat(thirdrow_sheetused) + parseFloat(res.sheet);		
		$('.sheet_used_'+tr[1]+'_'+(res.materialuniqueid)).html(parseFloat(ret_calc).toFixed(2));	
		
	}
	
	})
}



function material_onhand_check()
{
	$.ajax({
		type: "POST",
		url: baseUrl+'/material/',
		data: {			
		},
		dataType:'json',
		success: function(res){						
			var text = $('#material_row tr th:nth-child(2)').text();
			var firstrowweek = text.split(" ");	
			var text2 = $('#material_row tr th:nth-child(3)').text();
			var secondrow = text2.split(" ");	
			var text3 = $('#material_row tr th:nth-child(4)').text();
			var thirdrowweek = text3.split(" ");	
			
			$.each(res, function(i, res){
				if(res.SheetsOnHand!=null)
				{
					sheetstotal= parseFloat(res.SheetsOnHand).toFixed(2);
				} else {
					sheetstotal= 0;
				}				
				$('.sheet_onhand_'+firstrowweek[1]+'_'+Base64.encode(res.material)).html(sheetstotal);		
				$('.sheet_onhand_'+firstrowweek[1]+'_'+Base64.encode(res.material)).attr('data-sheetonhand',sheetstotal);
					
			})	
		},
		error: function() {
		}
	})
	

}
function material_onhand()
{
	$.ajax({
		type: "POST",
		url: baseUrl+'/material_list/',
		data: {			
		},
		dataType:'json',
		success: function(res){						
			var text = $('#material_row tr th:nth-child(2)').text();
			var firstrowweek = text.split(" ");	
			var text2 = $('#material_row tr th:nth-child(3)').text();
			var secondrow = text2.split(" ");	
			var text3 = $('#material_row tr th:nth-child(4)').text();
			var thirdrowweek = text3.split(" ");	
			
			$.each(res, function(i, res){							
				
				/** fetch used row value to calculate **/
				
				var firstrowweek_sheetused = $('.sheet_used_'+firstrowweek[1]+'_'+Base64.encode(res.material)).attr('data-sheetused');
				var secondrow_sheetused = $('.sheet_used_'+secondrow[1]+'_'+Base64.encode(res.material)).attr('data-sheetused');
				
				/** assign value for next rows **/				
				console.log($('.sheet_onhand_'+firstrowweek[1]+'_'+Base64.encode(res.material)).attr('data-sheetonhand'));
				console.log(firstrowweek_sheetused);
				console.log($('.sheet_onhand_'+firstrowweek[1]+'_'+Base64.encode(res.material)).attr('data-sheetonhand')-firstrowweek_sheetused);
				$('.sheet_onhand_'+secondrow[1]+'_'+Base64.encode(res.material)).html(
				
					parseFloat(parseFloat($('.sheet_onhand_'+firstrowweek[1]+'_'+Base64.encode(res.material)).attr('data-sheetonhand')) - parseFloat(firstrowweek_sheetused)).toFixed(2)
				);
				$('.sheet_onhand_'+secondrow[1]+'_'+Base64.encode(res.material)).attr('data-sheetonhand',
				
					parseFloat(parseFloat($('.sheet_onhand_'+firstrowweek[1]+'_'+Base64.encode(res.material)).attr('data-sheetonhand')) - parseFloat(firstrowweek_sheetused)).toFixed(2)
				);				
				$('.sheet_onhand_'+thirdrowweek[1]+'_'+Base64.encode(res.material)).html(
				
					parseFloat(parseFloat($('.sheet_onhand_'+secondrow[1]+'_'+Base64.encode(res.material)).html()) - parseFloat(secondrow_sheetused)).toFixed(2)
				);	
				$('.sheet_onhand_'+thirdrowweek[1]+'_'+Base64.encode(res.material)).attr('data-sheetonhand',
				
					parseFloat(parseFloat($('.sheet_onhand_'+secondrow[1]+'_'+Base64.encode(res.material)).html()) - parseFloat(secondrow_sheetused)).toFixed(2)
				);
				
					
			})	
		},
		error: function() {
		}
	})
	

}
function material_used()
{
	$.ajax({
		type: "POST",
		url: baseUrl+'/material_sheetused/',
		data: {			
		},
		dataType:'json',
		success: function(res){			
			var next_sheet_1 = 0, next_sheet_2=0;
			//test();
			//console.log(res);
			$.each(res, function(i, res){
				var week = res.week.split('-');				
				
				$('.sheet_used_'+week[1]+'_'+Base64.encode(res.imrPartID)).html(parseFloat(res.sheetweek).toFixed(2));				
				
				$('.sheet_used_'+week[1]+'_'+Base64.encode(res.imrPartID)).attr('data-sheetused',parseFloat(res.sheetweek).toFixed(2));
										
				//console.log((Base64.encode(res.imrPartID))+'-'+res.sheetweek);
//console.log('sheet_onhand_'+week[1]+'_'+Base64.encode(res.imrPartID));
				
				material_onhand();	
				
			})	
		},
		error: function() {
		}
	})
}

function convert(str)
{
if(str==false)
{
	return false;
}
else
{
str = str.replace(/ /g,"_");
str = str.replace(/\./g,"_");
str = str.replace(/\./g,"_");
str = str.replace(/'/g,"_");
str = str.replace(/\//g,"_");
str = str.replace(/"/g,"_");
return str;
}

}

$("body").on('dblclick', '.open_jobcard', function(e) {
	e.preventDefault();	
	var where = $(this).data('id');
	openjobcard(where);
})
function openjobcard(data)
{
	$.ajax({
		type: "POST",
		url: baseUrl+'/open_jobcard/',
		data: {
			jobid : data
		},
		dataType:'json',
		success: function(res){			
				res_job = res.jobdata;								
				$("#tc_customer").html(res_job.customer);				
				$("#tc_partnum").html(res_job.partid.trim());
				$("#tc_jobid").html(res_job.jobid);
				$("#tc_description").html(res_job.partdesc);
				$("#tc_orderqty").html(res_job.orderqty);
				$("#tc_delivery").html(res_job.deliverydate);
				$('#clipboard').attr('data-clipboard-text',res_job.partid.trim()); 
				$('#jobclipboard').attr('data-clipboard-text',res_job.jobid.trim());
				$("#tc_image").hide();
                    if (res.image != null) {
                        $("#tc_image_a").attr('href', res.image);
                        $("#tc_image_a").attr('data-lightbox', res.partnum);
                        $("#tc_image_img").attr('src', res.image);
                        $("#tc_image").show();
                    } 
			
				res_material = res.jobmaterialdata;				
				$("#mat > tbody").empty();				
					$('#mat').append('<tr><td>'+ res_material.materialid +'</td><td>'+ parseFloat(res_material.sheetrequired).toFixed(2) +'</td><td>'+ res_material.nestingid +'</td></tr>'); 
				$("#matdue > tbody").empty();	
					$('#matdue').append('<tr><td>'+ res_material.POI +'</td><td>'+ res_material.duedate +'</td><td>'+ res_material.receiptdate +'</td></tr>'); 
				detailarray = res.joboperationdata;	
				$("#detail > tbody").empty();			
				detailarray.forEach(function(row){
					if(row.operation==1)
						operation = '<button type="btn" class="updatemachine btn btn-info" data-uniqueid="'+row.jmouniqueid+'" data-cell="'+row.workcenterid+'" data-jobid="'+data+'" data-machine="'+row.uniqueid +'">Update</button>';
					else
						operation='';
					$('#detail').append('<tr><td>'+ row.oprationid +'</td><td>'+ row.processdesc +'</td><td>'+ row.cycletime +'</td><td>'+row.machine+'  '+operation+'</td></tr>');
				});
			
			
			$("#jobcardModal").modal('show');
		},
		error: function() {
		}
	})
}
$("body").on('click', '.updatemachine', function(e) {
	e.preventDefault();		
	uniqueid = $(this).attr('data-uniqueid');
	jobid = $(this).attr('data-jobid');	
	
	
	changecell($(this).attr('data-cell') );
	chagemachine( $(this).attr('data-cell'),$(this).attr('data-machine') );
	$('.update_machine').attr('data-jobid',jobid);
	$('.update_machine').attr('data-uniqueid',uniqueid);
	$("#machineupdateModal").modal('show');
})
function changecell(data)
{
		
	$.ajax({
		type: "POST",
		url: baseUrl+'/getcells/',
		data: {},
		dataType:'json',
		success: function(res){	
		let sel = document.querySelector('.select-celltext');
		$(".select-celltext").empty();
		res.forEach((users)=>{
			  let opt = document.createElement('option');
			  opt.value=users.m_cell_m1name;
			  let mcellname=document.createTextNode(users.m_cell_m1name);
			  opt.appendChild(mcellname);
			  sel.appendChild(opt);
		  });
		 $('.select-celltext option[value='+data+']').attr('selected','selected');		 
			
		}
	})		
	
	
}
function chagemachine(data,datamachine)
{
	if(datamachine==null)
	{
		var selectop = '';
	}
	else{
		var selectop = datamachine;
	}
	$.ajax({
		type: "POST",
		url: baseUrl+'/getmachines/',
		data: {
			workcenterid :data
		},
		dataType:'json',
		success: function(res){	
		let sel = document.querySelector('.select-text');
		$(".select-text").empty();
			  
		res.forEach((users)=>{			
			  let opt = document.createElement('option');
			  opt.value=users.xaquniqueid;
			  let userName=document.createTextNode(users.xaqDescription);
			  opt.appendChild(userName);
			  sel.appendChild(opt);
		  });
		 $('.select-text option[value='+selectop+']').attr('selected','selected');		 	
		}
	})	
}
$("body").on('click', '.select-celltext', function(e) {	
	//$('.updatemachine').attr('data-cell',$(this).val());
	chagemachine($(this).val());
})
$("body").on('click', '.update_machine', function(e) {	
	
	e.preventDefault();	
	
	uniqueid = $(this).attr('data-uniqueid');
	jobid = $(this).attr('data-jobid');
	$.ajax({
		type: "POST",
		url: baseUrl+'/updatemachine/',
		data: {
			workcenterid : $('.select-celltext').val(),
			machineid : $('.select-text').val(),
			uniqueid : uniqueid
		},
		dataType:'json',
		success: function(res){	
			
			$("#machineupdateModal").modal('hide');
			
			openjobcard(jobid);
		}
	})
	
})

var clipboard = new ClipboardJS('#clipboard',{
		container: document.getElementById('jobcardModal')
	});
	
	clipboard.on('success', function(e) {
		$('#clipboard').tooltip({title: "Copied",delay: { "hide": 500 }});
		e.clearSelection();
	});
	
	var jobclipboard = new ClipboardJS('#jobclipboard',{
		container: document.getElementById('jobcardModal')
	});
	
	jobclipboard.on('success', function(e) {
		$('#jobclipboard').tooltip({title: "Copied",delay: { "hide": 500 }});
		e.clearSelection();
	});

//Scheduled Jobs Show modal on 22/12
$('.scheduledjobs').click(function(e){
	e.preventDefault();	
	var where = $(this).data('uniqueid');
	$('#testunique').val(where);
	scheduled_jobs_update(where);
	
})
function scheduled_jobs_update(where)
{
	table = $('.jobdetails').DataTable();	 
	table.clear().destroy();	
	$("#scheduledModal").modal('show');	
	//$('#jc_machine').html($(this).data('machinename'));
	$(".jobdetails").DataTable({
	"destroy": true,
        "processing": false, // for show progress bar
        "serverSide": false, // for process server side
        "filter": true, // this is for disable filter (search box)
        "orderMulti": false, // for disable multiple column at once
        "pageLength": 12,		
		"columnDefs": [
            {
                "targets": [ 5 ],
                "visible": false,                
            },
            
        ],
        "ajax": {
            "url": baseUrl+'/scheduledjob/',
            "type": "POST",
			"data": {"machineid":where},
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
			{ "data": "cmoName", "name": "customer", "autoWidth": true,
				"render": function (data, type, row) {
							return data.substring(0,12);
						  }	
			},
			{ "data": "jmpPartShortDescription", "name": "description", "autoWidth": true,
				"render": function (data, type, row) {
							return data.substring(0,12);
						  }
			},
			{ "data": "operationid", "name": "operationid", "autoWidth": true },
			{ "data": "quantity", "name": "orderqty", "autoWidth": true },
			{ "data": "schedulestart", "name": "schedulestart", "autoWidth": true },
			{ "data": "Week", "name": "Week", "autoWidth": true },
			
        ]

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
			$('.scheduledhrs_null').html('');				
			scheduled_jobs_update($('#testunique').val());
			$('.sheet_null').html('0');		
			$('.sheet_null').attr('data-sheetused',0);		
			material_used();		
			material_onhand();					
			scheduledjobs_update();				
			unschedulefilter();	
		}
	});
})
$('#cell-filter').on('change', function(){
       table.search($(this).val()).draw();   	  
}); 
$('.materialdetail').click(function(e){
	e.preventDefault();	
	var where = $(this).data('material');
	materialdetail(where);
})
function materialdetail(data)
{
	$.ajax({
		type: "POST",
		url: baseUrl+'/materialdetail/',
		data: {
			material : data
		},
		dataType:'json',
		success: function(res){
			$('#tc_machine').html(data);
			detailarray = res.materialdue;	
			$("#mat_detail > tbody").empty();						
				detailarray.forEach(function(row){
					
					$('#mat_detail').append('<tr><td class="p-2">'+ row.pmlPurchaseOrderID +'</td><td class="p-2">'+ row.pmlPartRevisionID +'</td><td class="p-2">'+ parseFloat(row.sheets).toFixed(2) +'</td><td class="p-2">'+row.duedate+'</td></tr>');
				});
			detailarray1 = res.material_jobs;			
			$('.mat_job_detail').DataTable().clear().destroy();			
			$("#mat_job_detail > tbody").empty();			
				detailarray1.forEach(function(row){
					$('#mat_job_detail').append('<tr><td class="p-2">'+ row.jmpJobID +'</td><td class="p-2">'+ row.jmpPartID +'</td><td class="p-2">'+ row.customer +'</td><td class="p-2">'+row.scheduledate+'</td><td class="p-2">'+parseFloat(row.sheetreq).toFixed(2)+'</td></tr>');
				});
				$('.mat_job_detail').DataTable({"pageLength": 12});
			if(detailarray=='')
			{
				//$("#mat_detail > thead").empty();
				$('#mat_detail').append('<tr><td colspan="4" class="p-2">No Purchase Order for this Material</td></tr>');
			}
			
			$("#materialModal").modal('show');
			
		}
	})
}

 
$('#schedule_submit').click(function(){
	var e = document.getElementById("chooseweek");	
	var str = e.options[e.selectedIndex].value;	
	if(str==0)
	{
		$('.chooseweek_msg').html('Please select a week');
	} else {
		$('.chooseweek_msg').html('');
		
		var table = $('#unschedule').DataTable();
		var cellsSelected = table.rows({ selected: true }).data();
		cellsSelected[0];
		
		theArray=[];
		var myValues= $('#unschedule').DataTable();
		var ids = $.map(myValues.rows('.selected').data(), function (item) {					
		return {
				  ids: item.jobid+' '+item.operationid+' '+$('.chooseweek').val()				  
				  
				};		
		});
		
				
		var mids = $.map(myValues.rows('.selected').data(), function (item) {					
		return {
				  materialuniqueid: Base64.encode(item.materialid)				  
				  
				};		
		});
		
		
		updatebucketweek(ids,mids);				
		
		var table = $('#unschedule').DataTable();
		var cellsSelected = table.rows({ selected: true }).remove().draw();
	}
}) 
function updatebucketweek(ids,mids)
{
		
	 $.ajax({
		type: "POST",
		url: baseUrl+'/schedule_job_bucket/',
		data: {
			ids : ids
		},
		dataType:'json',
		success: function(res){		
			//$('.sheet_null').html('0');		
			//$('.sheet_null').attr('data-sheetused',0);
			//$('.sheet_null').attr('data-sheetonhand',0);
			
			
			material_used();		
			material_onhand();		
			
			//material_used_test(mids);
			//material_onhand_test(mids);
			
			scheduledjobs_update();
			$('.machine_hrs_calc').html('');
			
			
		}
	}) 
}



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