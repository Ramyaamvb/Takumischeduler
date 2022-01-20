(function($){
	/* $(document).ready(function() {
	   setTimeout(function(){
  window.location.reload(1);
}, 10000);
	}); */ 
	
$('#screen').each(function(){
	$('#screen').each(function(){
		var content='';
		content += screen.width+'*'+screen.height;					
		$(this).html(content);
	})
})
	
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

$('.get_cell_content').each(function()
{
var type = $('#type').val();
var cell = $('#cell').val();
	$.ajax({
    url: baseUrl+'/cellmetricscount',
	type: 'POST',
	dataType: 'json',
	data:{cell: cell, type: type},
    success: function(row) {
				 console.log(row);
	 for(var i=0;i<=4;i++)
	 {
		$('#'+Object.keys(row)[i]).each(function(){
		$(this).empty(); 
		var content='';
		if(Object.values(row)[i] == '')
		{
			Object.values(row)[i] = 0;
		}
		console.log(Object.keys(row)[i]);
		content += Object.values(row)[i];					
		$(this).html(content);
	   });
	 } 		  			  
	
    },
	 error: function() {
        console.log('Cannot retrieve data.');
    }
  });
});
$('#qualityticket').each(function()
{

if(screen.width>=4000)
{
console.log(screen.width);	
document.getElementById("qualityticket").style.width = "700%";
document.getElementById("qualityticket").style.height = "640%";
fontsizeset = 36;
}
if(screen.width==1280)
{
console.log(screen.width);	
document.getElementById("qualityticket").style.width = "210%";
document.getElementById("qualityticket").style.height = "330%";
fontsizeset = 15;
}
if(screen.width==1920)
{
console.log(screen.width);	
document.getElementById("qualityticket").style.width = "500%";
document.getElementById("qualityticket").style.height = "350%";
fontsizeset = 20;
}
if(screen.width==1920 && screen.height==949)
{
document.getElementById("qualityticket").style.width = "480%";
document.getElementById("qualityticket").style.height = "301%";
fontsizeset = 22;
}
if(screen.width==1098 && screen.height==618)
{
document.getElementById("qualityticket").style.width = "480%";
document.getElementById("qualityticket").style.height = "301%";
fontsizeset = 15;
}
if(screen.width==1305 && screen.height==734)
{
document.getElementById("qualityticket").style.width = "525%";
document.getElementById("qualityticket").style.height = "280%";
fontsizeset = 15;
}
updatechart(fontsizeset);			
});

function updatechart(fontsizeset)
{
var cell = $('#cell').val();
var d = new Date();
var n = d.getDay();     
console.log(n);
	   
	$.ajax({
			url:baseUrl+"/getmachinehours",    
			type: "post",    
			dataType: 'json',
			data: {cell: cell},
			success:function(result){
				var markline=0;max = 0;
				if(cell == 'Twin Spindle' || cell == 'Sliding Head' || cell == 'Plastics' || cell == 'Mill 2' || cell == 'Mill 4')
				{
					markline = 250;
					max = 350;
				}else if(cell == 'Mill 1' || cell == 'Mill 3'){
					markline=312;
					max = 350;
				}else if(cell == 'Mill 5'){ markline = 374; max = 400; }
				var week = [];
				var machinehours = [];
				var setuphours = [];
				var tooltipweek=0; var weekday=0;
				var currentweek = 'WK'+new Date().getWeekNumber();
				result.forEach(function(row)				 
				 { 	 
					 if(n==1){weekday = 0;}else if(n==2){weekday=18;} else if(n==3){weekday=36;}else if(n==4){weekday=54;}else if(n==5){weekday=72;}		
					 console.log(n);
					 
					 if(row.week==currentweek)
					 {
						 if(cell == 'Twin Spindle' || cell == 'Sliding Head' || cell == 'Plastics' || cell == 'Mill 2' || cell == 'Mill 4')
						 {
						 markline = ((weekday*4) * 80)/100; 						
						 }
						 else if(cell == 'Mill 1' || cell == 'Mill 3' ||  cell == 'Mill 5')
						 {
					     markline = ((weekday*5) * 80)/100; 						 
						 }
					 } 
					 week.push(row.week);
					 machinehours.push(row.metrics_machinehours);
					 var x = markline - row.metrics_machinehours;
					 if(x <0)
					 {
						 var t = 0
					 }
					 else{
						 var t = x.toFixed(2);
					 }
					 setuphours.push(t);
				})
				var chart1 = echarts.init(document.getElementById('qualityticket'));
				var option = {					
						title:{
						
						layout:{
							padding:{
								left:10,
							}
						}
						},		
						
						tooltip: {
							trigger: "axis",
							axisPointer: {
							 type: "shadow",
							},
						},
						
						stack: 'anyString',
						xAxis: {
							axisLabel: {
									fontSize: fontsizeset
								  },
							type: 'category',							
							data: week
						},
						yAxis: {	
								axisLabel: {
									fontSize: fontsizeset
								  },						
							type: 'value',
							max:max
						},						
						series: [ {
							name: 'Contribution Lost',
							data: setuphours,
							type: 'bar',
							color:'#c23531',
							areaStyle: {},
							
							
							label: {								
							normal: {
								show: true,
								position: 'top',
								color:'black',
								fontSize:fontsizeset,
							  }
						  },
						} ],
						
					};	
				 chart1.setOption(option,true);	
			}
			
			
	});
}
  
		
		 $(window).on('resize', function(){
			 
        $("[_echarts_instance_]").each(function(){
            window.echarts.getInstanceById($(this).attr('_echarts_instance_')).resize()
        });
    });
	
	
Date.prototype.getWeekNumber = function(){
  var d = new Date(Date.UTC(this.getFullYear(), this.getMonth(), this.getDate()));
  var dayNum = d.getUTCDay() || 7;
  d.setUTCDate(d.getUTCDate() + 4 - dayNum);
  var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
  return Math.ceil((((d - yearStart) / 86400000) + 1)/7)
};

$('.get_cell_content').each(function() {
   //executeQuery();
}); 
$('.get_pit_content').each(function() {
   //executeQuery1();
}); 
 
function executeQuery() {
var type = $('#type').val();
var cell = $('#cell').val();

   setTimeout(function(){
				window.location = '/cellmetrics/pit/'+type+'/'+cell;
				//window.location = 'http://machines.takumiprecision.com/?t=lathes&c=Twin%20Spindle&d=cellmetrics';
				
         }, 20000);
  
}  
function executeQuery1() {
var type = $('#type').val();
var cell = $('#cell').val();

   setTimeout(function(){
				window.location = '/cellmetrics/cell/'+type+'/'+cell;
				//window.location = 'http://machines.takumiprecision.com/?t=lathes&c=Twin%20Spindle&d=cellmetrics';
				
         }, 20000);
  
  
} 

})(jQuery);