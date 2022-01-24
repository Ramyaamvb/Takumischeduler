<?php 
foreach($machines as $k)
{
	if($k->machine_unique==$mid)
	{
		$mname = $k->machine_name;
	}
}
?>
<style>
.dataTables_scrollBody::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    background-color: #F5F5F5;
    border-radius: 10px;
}

.dataTables_scrollBody::-webkit-scrollbar {
    width: 6px;
    background-color: #F5F5F5;
}

.dataTables_scrollBody::-webkit-scrollbar-thumb {
    background-color: #777;
    border-radius: 10px;
}
.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody {
  overflow-y: scroll !important;
}
.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody ::-webkit-scrollbar-track{
  
   width: 6px;
    background-color: #F5F5F5;
}
div.pager {
    text-align: center;
    margin: 1em 0;
}

div.pager span {
    display: inline-block;
    width: 1.8em;
    height: 1.8em;
    line-height: 1.8;
    text-align: center;
    cursor: pointer;
    background: #000;
    color: #fff;
    margin-right: 0.5em;
}

div.pager span.active {
    background: #c00;
}
#unscheduledjobs_filter
{
	padding-right:11px;
}
table.schedulejobs td
{
	border-right:1px solid #442136;
}
table.schedulejobs th
{
	border-right:1px solid #442136;
}
table.schedulejobs thead tr th
{
	background-color:#442136;
	color:white;
	text-align:center;
}

table.schedulejobs thead tr th:first-child
{
	padding:5px;
}
table.schedulejobs thead tr th select
{
	width:90%;
	border-radius:6px;
	
}
.schedulejobs td.crossed
{
   background-image: linear-gradient(to bottom right,  transparent calc(50% - 1px), #3e1b30, transparent calc(50% + 1px)); 
}
.schedulejobs td.crossedleft
{
   background-image: linear-gradient(to bottom left,  transparent calc(50% - 1px), #3e1b30, transparent calc(50% + 1px)); 
}
.crossed
{
   background-image: linear-gradient(to bottom right,  transparent calc(50% - 1px), #3e1b30, transparent calc(50% + 1px)); 
}
.crossedleft
{
   background-image: linear-gradient(to bottom left,  transparent calc(50% - 1px), #3e1b30, transparent calc(50% + 1px)); 
}
.machines-4
{
	color:#442136;
	height:164px;
	border-bottom:1px solid #442136;
}
.machines-4  th{
	padding-left:10px;
	font-size:1.5rem
}
.machines-5
{
	color:#442136;
	height:131px;
	border-bottom:1px solid #442136;
}
.machines-5  th{
	padding-left:10px;
	font-size:1.5rem
}
.filtersclass
{
	border-right:2px solid #442136;
}
.filtersclass select{
	width:100%;
	border-radius:5px;
	height:40px;
	
}
.filtersclass select:hover {
    box-shadow: 0 0 10px 100px #7fb1c3 inset;
	background-color:white;
	border-radius:10px;
	
}

 .green:hover {background: green;}

.header-filter{
	background-color:#F68D2E;
	padding-top:10px;
	padding-bottom:10px;
}
.calculationheader
{
	background-color:#F68D2E;
	color:white;
	font-size:1.5rem;
	padding:2px;
}
.material_table td.crossed
{
   background-image: linear-gradient(to bottom right,  transparent calc(50% - 1px), #3e1b30, transparent calc(50% + 1px)); 
}
table.material_table thead tr
{
	border-bottom:1px solid #442136;
}
table.material_table thead th
{
	border-right:1px solid white;
}
table.material_table tbody td
{
	border-right:1px solid #442136;
	border-bottom:1px solid #442136;
	padding-bottom:1px;
}
table.material_table tbody th
{
	border-right:1px solid #442136;
	border-bottom:1px solid #442136;	
	padding-bottom:1px;
}
.material_info
{
	height:46vh;
	overflow:scroll;
}
.material_info thead th { position: sticky; top: 0; z-index: 1;background-color:#442136;color:white;}

table.hours_table tbody tr.machines-5
{
	height:80.5px;	
}
table.hours_table tbody tr.machines-5 th
{
	color:#442136;
	font-size:1.05rem;
}

table.hours_table tbody tr.machines-4
{
	height:100.5px;	
}
table.hours_table tbody tr.machines-4 th
{
	color:#442136;
	font-size:1.05rem;
}
table.hours_table tbody tr td
{
	border-right:1px solid #442136;
	border-bottom:1px solid #442136;
}
table.hours_table tbody tr th
{
	padding-left:10px;
	border-bottom:1px solid #442136;
}
table.hours_table tbody tr td
{
	height:46px;
}
.hours_table thead th { position: sticky; top: 0; z-index: 1;background-color:#442136;color:white;}
.hours_table tbody th
{
	border-right:1px solid #442136;
}
.hours_table td.crossed
{
   background-image: linear-gradient(to bottom right,  transparent calc(50% - 1px), #3e1b30, transparent calc(50% + 1px)); 
}
.hours_table td
{

font-size:0.95rem;
}
.hours_table td div
{

font-size:0.95rem;
}

table.dataTable tbody th, table.dataTable tbody td
{
	padding:4px 10px;
}
table.dataTable thead th, table.dataTable thead td
{
	padding:5px 15px;
}
.unschedule_header
{
	background-color: #0093B4B0;
    color: #442136;
	padding-top:10px;
	padding-bottom:7px;
	text-align:center;
}
#unschedule_filter
{
	padding-right:10px;
}
.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody {
  overflow-y: scroll !important;
}

.cross_height_4
{
	height:158px;
}
.cross_height_5
{
	height:128px;
}
.pb-6
{
	padding-bottom:3.51rem;
}
.modal-xl{max-width:1036px}}@media (min-width:1200px){.modal-xl{max-width:1140px}}
.modal-header
{
justify-content:none;
}
.border-bottom
{
border-bottom:1px solid black
}
table.dataTable.no-footer
{
	border-bottom:0px;
}
table.dataTable td.dataTables_empty {
    text-align: left;
	padding-left:150px;
}


</style>
<div class="row m-0" style="height:100vh">
	<div class="filtersclass col-12 p-0">
	
		<div class="header-filter row w-100 m-0">					
			<a href="<?=base_url('scheduler/schedule'); ?>" class="text-dark"><i class="fa fa-home"></i><span class="p-0" style="font-size:13px;font-weight:500">Scheduler</span></a>
		</div>
		
		<div class="row m-0">
			<div class="col-2 p-0" style="border-right:1px solid black" id="set_heights">
				<table class="schedulejobs bucket"  id="machine_hours_row" width="100% " height="100%">
				<thead>
					<tr>
						<th><select class="bucket_filters">
						<?php foreach($cell as $k){ ?>
						<option value="<?=$k->m_cell_m1name; ?>" <?php print ($k->m_cell_m1name==$cellname)?'selected':'';?>><?=$k->m_cell_name; ?></option>
						<?php } ?>
						</select></th>			
						
					</tr>
				</thead>
					<tbody>
					<?php foreach($machines as $k) { ?>
						<tr class="machines-<?=$k->totalmachine;?>" data-machine="<?=$k->m_cell_m1name; ?>"> <!--scheduledjobs-->
							<th class=" col-md-4 p-0" data-machine="<?=$k->m_cell_m1name; ?>" data-uniqueid="<?=$k->machine_unique;?>"  data-machine="<?=$k->m_cell_m1name; ?>" data-machinename="<?=$k->machine_name;?>">
								<a href="<?=base_url('scheduler/scheduledjobs?&c='.$k->m_cell_m1name.'&mid='.$k->machine_unique)?>" class="text-dark"><?=$k->machine_name;?></a>
							</th>
							
							
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
			<div class="col-10 p-0" style="height:auto">
				<div class="row w-100 m-0">
					<div class="col-12 unschedule_header">
						<h5>Scheduled Jobs - <?=$mname;?></h5>
					</div>
				</div>
				<div class="row w-100 m-0">
					
					<div class="col-5 mt-1 ml-1 mb-1 col-4 p-0">
						<button id="" class="unschedule_udpate btn btn-success text-light">UnSchedule</button>
						<span class="chooseweek_msg text-danger"></span>
					</div>
					<div class="col-12 p-0 tablecontent" style="overflow:scroll;">						
						<table id="scheduledjob" clientidmode="Static" class="tablebody display nowrap hover w-100">
						<thead>
							<tr class="gridStyle">
								<th></th>
								<th style="width:90px">JobID</th>
								<th style="width:90px">PartID</th>
								<th>PartDescription</th>
								<th style="width:90px">Schedule Start</th>
								<th style="width:90px">Prod Duedate</th>
								<th>Customer</th>
								<th>Week</th>								
								<th>Ope. Id</th>								
								<th>Qty</th>								
								<th>Material Status</th>
								<th>Material ID</th>
								<th>Sheet Req.</th>																
								
								
							</tr>
						</thead>					
						<tbody></tbody></table>
						<!--<table id="unschedule" class="unschedule tablebody display nowrap" cellspacing="0" width="100%">
								
							</tbody>
						</table>-->
						<input type="hidden" class="machineid" value="<?=$mid; ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	
			
</div>



