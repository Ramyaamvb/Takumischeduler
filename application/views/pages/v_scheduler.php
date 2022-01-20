<?php 					
$fromweek=date("W", strtotime(date('Y-m-d')));
$fromyear=date("Y", strtotime(date('Y-m-d')));
$toweek=1;
$toyear=date("Y", strtotime(date('Y-m-d'))) + 1;
$interval = new DateInterval('P7D');

$start = (new \DateTime())->setISODate($fromyear, $fromweek);
$end = (new \DateTime())->setISODate($toyear, $toweek);

$material_end = (new \DateTime())->setISODate($toyear, 1);

$period = new \DatePeriod($start, $interval, $end->add($interval));
$period_mat = new \DatePeriod($start, $interval, $material_end->add($interval));
$week_nums = array();
$week_num=array();

foreach($period as $date)
{
	$a =  $date->format('W');
	
	array_push($week_nums,$a);
}
$week_num = array_slice($week_nums,0,3);
//var_dump($week_num);
?>
<style>
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
	<div class="filtersclass col-9 p-0">
	
		<div class="header-filter row w-100 m-0">					
			<div class="col p-0 pl-2">
				<select name="cell" id="cell_select">			
					<?php foreach($cell as $k){ ?>
						<option value="<?=$k->m_cell_m1name; ?>" ><?=$k->m_cell_name; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col">
				<select name="machineid" id="machines_select">
					<option data-cell="all" value="all" required>--Select Machine--</option>
					<?php foreach($machines as $k) {?>
					<option value="<?=$k->machine_unique;?>"  data-cell="<?=$k->m_cell_m1name;?>" ><?=$k->machine_name;?></option>				
					<?php }?>
					<input type="hidden" value="" id="machineid">
				</select>
			</div>			
			<div class="col">
				<select name="material_status" id="material_status">
					<option data-cell="all" value="all">--Materials Status--</option>
					<option value="GREEN" >Green</option>				
					<option value="ORANGE" >Orange</option>				
					<option value="RED">Red</option>	
				</select>
			</div>
			<div class="col">
				<select name="materialtype" id="materialtype">
					<option value="all">--Select Material--</option>				
					<?php foreach($materials as $k) {?>
					<option value="<?=trim(str_replace('"', "'", $k->material));?>"><?=$k->material;?></option>				
					<?php }?>				
				</select>
			</div>
			<div class="col-3">
			<button class="btn btn-lg btn-info" id="getjobs" type="submit" name="">Submit	</button>
			
			<button class="btn btn-lg btn-info" name=""><a class="text-light" href="<?=base_url('scheduler/schedule');?>">Clear</a>	</button>
			</div>
		</div>
		
		<div class="row m-0">
			<div class="col-5 p-0" style="border-right:1px solid black" id="set_height">
			<table class="schedulejobs bucket"  id="machine_hours_row" width="100% " height="100%">
			<thead>
				<tr>
					<th><select class="bucket_filter">
					<?php foreach($cell as $k){ ?>
					<option value="<?=$k->m_cell_m1name; ?>"><?=$k->m_cell_name; ?></option>
					<?php } ?>
					</select></th>			
					<?php foreach($week_num as $date){ ?>
						<th><?php echo 'Week '.$date; ?></th>					
					<?php } ?>						
				</tr>
			</thead>
				<tbody>
				<?php foreach($machines as $k) { ?>
					<tr class="machines-<?=$k->totalmachine;?>" data-machine="<?=$k->m_cell_m1name; ?>"> <!--scheduledjobs-->
						<th class="scheduledjobs col-md-4 p-0" data-machine="<?=$k->m_cell_m1name; ?>" data-uniqueid="<?=$k->machine_unique;?>"  data-machine="<?=$k->m_cell_m1name; ?>" data-machinename="<?=$k->machine_name;?>">
							<a href="<?=base_url('scheduler/scheduledjobs?c='.$k->m_cell_m1name.'&mid='.$k->machine_unique)?>" class="text-dark"><?=$k->machine_name;?></a>
						</th>
						<td class="col-md-2 p-0" data-uniqueid="<?=$k->machine_unique;?>"  data-machine="<?=$k->m_cell_m1name; ?>">
							<table class="w-100" height="100%">
								<tr style="height:3vh"><td colspan="2" class="border border-success text-center">							 	
									<div class="row w-100 m-0" style="height:100%">
								 	<div class="col-6 p-0 scheduledhrs_null totalhours<?=$week_num[0];?>_<?=$k->machine_unique;?>" data-unique="<?=$k->machine_unique;?>" style="border-right:1px solid #442136"></div>
											<div td class="col-6 p-0 machine_hrs_calc totalhours_calc<?=$week_num[0];?>_<?=$k->machine_unique;?>" data-unique="<?=$k->machine_unique;?>" style="border:0px"></div>
									</td>
								</tr>
								<tr>
								<td class="scheduledhrs_null latehours<?=$week_num[0];?>_<?=$k->machine_unique;?> crossed w-50 pb-6 text-danger" style="border-right:0;" data-unique="<?=$k->machine_unique;?>" data-week="<?=date("W"); ?>">-
								</td>
								<td class="crossedleft" style="border-right:0">
									<div class="row m-0">
										<div class="scheduledhrs_null futurehours<?=$week_num[0];?>_<?=$k->machine_unique;?> col-12 p-0 pb-4 pr-1 text-right text-success" data-unique="<?=$k->machine_unique;?>">
											-
										</div>
										<div class="scheduledhrs_null currenthours<?=$week_num[0];?>_<?=$k->machine_unique;?> col-12 p-0 pb-2 pr-2" style="margin-left:-13px" data-unique="<?=$k->machine_unique;?>">
											-
										</div>
									</div>
								</td>
								</tr>
							</table>
						</td>
						<td class="col-md-2 p-0" data-uniqueid="<?=$k->machine_unique;?>" data-machine="<?=$k->m_cell_m1name; ?>">
							<table class="w-100" style="height:100%">
								<tr style="height:3vh"><td colspan="2" class="border border-success text-center">
									<div class="row w-100 m-0"  style="height:100%">
								 	<div class="col-6 p-0 scheduledhrs_null totalhours<?=$week_num[1];?>_<?=$k->machine_unique;?>" data-unique="<?=$k->machine_unique;?>" style="border-right:1px solid #442136"></div>
											<div td class="col-6 p-0 machine_hrs_calc totalhours_calc<?=$week_num[1];?>_<?=$k->machine_unique;?>" data-unique="<?=$k->machine_unique;?>" style="border:0px"></div>
								</td></tr>
								<tr>
								<td class="scheduledhrs_null latehours<?=$week_num[1];?>_<?=$k->machine_unique;?> crossed w-50 pb-6 text-danger" style="border-right:0;" data-unique="<?=$k->machine_unique;?>" data-week="<?=date("W"); ?>">-
								</td>
								<td class="crossedleft" style="border-right:0">
									<div class="row m-0">
										<div class="scheduledhrs_null futurehours<?=$week_num[1];?>_<?=$k->machine_unique;?> col-12 p-0 pb-4 pr-1 text-right text-success" data-unique="<?=$k->machine_unique;?>">
											-
										</div>
										<div class="scheduledhrs_null currenthours<?=$week_num[1];?>_<?=$k->machine_unique;?> col-12 p-0 pb-2 pr-2" style="margin-left:-13px" data-unique="<?=$k->machine_unique;?>">
											-
										</div>
									</div>
								</td>
								</tr>
							</table>
						</td>
						<td class="col-md-2 p-0" data-uniqueid="<?=$k->machine_unique;?>"data-machine="<?=$k->m_cell_m1name; ?>">
							<table class="w-100" style="height:100%">
								<tr style="height:3vh"><td colspan="2" class="border-bottom border-success text-center">
									<div class="row w-100 m-0"  style="height:100%">
								 	<div class="col-6 p-0 scheduledhrs_null totalhours<?=$week_num[2];?>_<?=$k->machine_unique;?>" data-unique="<?=$k->machine_unique;?>" style="border-right:1px solid #442136"></div>
											<div td class="col-6 p-0 machine_hrs_calc totalhours_calc<?=$week_num[2];?>_<?=$k->machine_unique;?>" data-unique="<?=$k->machine_unique;?>" style="border:0px"></div>
								</td></tr>
								<tr>
								<td class="scheduledhrs_null latehours<?=$week_num[2];?>_<?=$k->machine_unique;?> crossed w-50 pb-6 text-danger" style="border-right:0;" data-unique="<?=$k->machine_unique;?>" data-week="<?=date("W"); ?>">-
								</td>
								<td class="crossedleft" style="border-right:0">
									<div class="row m-0">
										<div class="scheduledhrs_null futurehours<?=$week_num[2];?>_<?=$k->machine_unique;?> col-12 p-0 pb-4 pr-1 text-right text-success" data-unique="<?=$k->machine_unique;?>">
											-
										</div>
										<div class="scheduledhrs_null currenthours<?=$week_num[2];?>_<?=$k->machine_unique;?> col-12 p-0 pb-2 pr-2" style="margin-left:-13px" data-unique="<?=$k->machine_unique;?>">
											-
										</div>
									</div>
								</td>
								</tr>
							</table>
						</td>
						
					</tr>
				<?php } ?>
				</tbody>
			</table>
			</div>
			<div class="col-7 p-0" style="height:auto">
				<div class="row w-100 m-0">
					<div class="col-12 unschedule_header">
						<h5>Unscheduled Jobs</h5>
					</div>
				</div>
				<div class="row w-100 m-0">
					<div class="col-3 mt-1 ml-1 mb-1 col-4 p-0">
						
						<select name="chooseweek" class="chooseweek" id="chooseweek">
							<option value="">-choose week-</option>
							<?php foreach($week_num as $date){ ?>
								<option value="<?php echo $date; ?>"><?php echo 'Week '.$date; ?></option>
								
							<?php } ?>
							
						</select>
					</div>
					<div class="col-5 mt-1 ml-1 mb-1 col-4 p-0">
						<button id="schedule_submit" class="btn btn-success text-light">Schedule</button>
						<span class="chooseweek_msg text-danger"></span>
					</div>
					<div class="col-12 p-0 tablecontent" style="overflow:scroll;">						
						<table id="unschedule" clientidmode="Static" class="unschedule tablebody display nowrap hover w-100">
						<thead>
							<tr class="gridStyle">
								<th></th>
								<th style="width:90px">JobID</th>
								<th style="width:90px">PartID</th>
								<th style="width:90px">Production Date</th>
								<th>Customer</th>
								<th>Ope. Id</th>								
								<th>Production Qty</th>
								<th>Est. Production Hrs</th>
								<th>Material Status</th>
								<th>Material ID</th>
								<th>Sheet Req.</th>
								<th>PartDescription</th>
								<th>Machine Name</th>
								<th>uniqueid</th>
								
							</tr>
						</thead>					
						<tbody></tbody></table>
						<!--<table id="unschedule" class="unschedule tablebody display nowrap" cellspacing="0" width="100%">
								
							</tbody>
						</table>-->
					
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-3 p-0">
	<!--Material Info -->
		<div class="row w-100 m-0" style="height:45vh;">
			<div class="col p-0">
				<div class="calculationheader"><h4>Material</h4></div>
				<div class="material_info">
					<table class="w-100 material_table" id="material_row" height="100%">
						<thead>
							<tr>
								<th>Material Name</th>	
								<?php foreach($week_num as $date){ ?>
									<th><?php echo 'Week '.$date; ?></th>
									<?php } ?>									
							</tr>
						</thead>
						<tbody class="material_body " id="sheetsadd">
							<?php foreach($materials as $row) { ?>
							<tr>
								<th class="materialdetail" data-material="<?=trim(str_replace('"',"'",$row->material));?>"><?=$row->material;?></th>								
								<td class="crossed">
									<div class="sheet_onhand_<?=$week_num[0];?>_<?=base64_encode($row->material);?> row m-0 pl-2" data-sheetonhand="" data-week="">
											0									
									</div>
									<div class="row w-100 m-0 justify-content-right">
										<div class="sheet_null sheet_used_<?=$week_num[0];?>_<?=base64_encode($row->material);?> col-12 p-0 pr-1 text-right" data-sheet="" data-sheetused="0">
											0
										</div>
									</div>
								</td>
								<td class="crossed">
									<div class="sheet_onhand_<?=$week_num[1];?>_<?=base64_encode($row->material);?> row m-0 pl-2 "  data-sheetonhand="" data-week="">
										
									</div>
									<div class="row w-100 m-0 justify-content-right">
										<div class="sheet_null sheet_used_<?=$week_num[1];?>_<?=base64_encode($row->material);?> col-12 p-0 pr-1 text-right" data-sheet="" data-sheetused="0">
										0
										</div>
									</div>
								</td>
								<td class="crossed">
									<div class="sheet_onhand_<?=$week_num[2];?>_<?=base64_encode($row->material);?> row m-0 pl-2"  data-sheetonhand="" data-week="">
										
									</div>								
									<div class="row w-100 m-0 justify-content-right">
										<div class="sheet_null sheet_used_<?=$week_num[2];?>_<?=base64_encode($row->material);?> col-12 p-0 pr-1 text-right" data-sheet="" data-sheetused="0">
											0									
									</div>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<input type="hidden" id="checkmat_used" >
				</div>
			</div>			
		</div>
	<!--Hours Info -->
		<div class="row w-100 m-0" style="height:45vh;">
			<div class="col p-0">
				<div class="calculationheader"><h4>Hours</h4></div>
				<div class="hours_info">
					<table class="hours_table"  width="100% " height="100%">
					<thead>
						<tr>
							<th></th>
							<th class="text-center">BL</th>					
							<?php foreach($week_num as $date){ ?>
							<th class="text-center"><?php echo 'WK'.$date; ?></th>
							<?php } ?>	
						</tr>
					</thead>
						<tbody>
						<?php $a=0; foreach($cell as $k) {  if($a<=6) { ?>
							
							<tr class="">
								<th><?=$k->m_cell_m1name;?></td>
								<td class="col-4 p-0 bl_<?=$k->m_cell_m1name; ?>" data-cellname="<?=$k->m_cell_m1name;?>">
									<div class="row w-100 m-0 p-0" style="height:100%">
										<div class="pl-1 cell_hours_bl_scheduled_<?=$k->m_cell_m1name; ?> crossed col-6 p-0 text-success" data-cellname="<?=$k->m_cell_m1name;?>" >
											-
										</div>
										<div class="crossedleft col-6 p-0">
											<div class="pl-1 cell_hours_bl_unscheduled_<?=$k->m_cell_m1name; ?> row w-100 justify-content-right text-danger" style="float:right;text-align:right;">
												-
											</div>
											<div class="cell_hours_bl_total_<?=$k->m_cell_m1name; ?> row w-100 ">
												-
											</div>
										</div>
									</div>
								</td>	
								<td class="col-4 p-0 week_<?=$week_num[0];?>_<?=$k->m_cell_m1name; ?>" data-cellname="<?=$k->m_cell_m1name;?>">
									<div class="row w-100 m-0 p-0" style="height:100%">
										<div class="pl-1 cell_hours_<?=$week_num[0];?>_scheduled_<?=$k->m_cell_m1name;?> crossed col-6 p-0 text-success" data-cellname="<?=$k->m_cell_m1name;?>" >
											-
										</div>
										<div class="crossedleft col-6 p-0">
											<div class="pl-1 cell_hours_<?=$week_num[0];?>_unscheduled_<?=$k->m_cell_m1name;?> row w-100 justify-content-right text-danger"  style="float:right;text-align:right;">
												-
											</div>
											<div class="cell_hours_<?=$week_num[0];?>_total_<?=$k->m_cell_m1name;?> row w-100 ">
												-
											</div>
										</div>
									</div>
								</td>
								<td class="col-2 p-0 crossed week_<?=$week_num[1];?>_<?=$k->m_cell_m1name; ?>" data-cellname="<?=$k->m_cell_m1name;?>">
									<div class="cell_hours_<?=$week_num[1];?>_unscheduled_<?=$k->m_cell_m1name;?> row m-0 pl-2 text-danger" data-materialid="" data-sheetonhand="">
										-
									</div>								
									<div class="row w-100 m-0 justify-content-right">
										<div class="cell_hours_<?=$week_num[1];?>_total_<?=$k->m_cell_m1name;?> col-12 p-0 pr-1 text-right" data-sheet="">
										-										
									</div>
								</td>
								<td class="col-3 p-0 crossed week_<?=$week_num[2];?>_<?=$k->m_cell_m1name; ?>" data-cellname="<?=$k->m_cell_m1name;?>">
									<div class="cell_hours_<?=$week_num[2];?>_unscheduled_<?=$k->m_cell_m1name;?> text-danger row m-0 pl-2"  data-sheetonhand="">
										-
									</div>								
									<div class="row w-100 m-0 justify-content-right">
										<div class="cell_hours_<?=$week_num[2];?>_total_<?=$k->m_cell_m1name;?> col-12 p-0 pr-1 text-right" data-sheet="">
										-										
									</div>
								</td>
								
							</tr>
						<?php } $a++; } ?>
						</tbody>
					</table>
				</div>
			</div>	
		</div>
	</div>
</div>

<div class="modal fade" id="jobcardModal" tabindex="-1" role="dialog" aria-labelledby="jobcardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="justify-content:right">
                <!--<button type="button" class="btn btn-warning btn-lg float-left" data-dismiss="modal"><i class="fa fa-times"></i></button>-->
                <h2 class="modal-title" id="jobcardModalLabel"><span id=""></span></h2>
                <button type="button" class="btn btn-warning btn-lg float-right" data-bs-dismiss="modal"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body row">                
                <div class="col-12">
                    <table class="table table-striped">
                        <tr id="tc_row_status"><th>JobCard</th></tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class=" table table-striped">
                        <tr><th>CUSTOMER</th><td id="tc_customer"></td></tr>
                        <tr><th>JOB ID</th><td><span id="tc_jobid"></span><button class="btn float-right" id="jobclipboard" data-clipboard-text=""><i class="fa fa-clipboard"></i></button></td></tr>
                        <tr><th>PART NUMBER</th><td><span id="tc_partnum"></span><button class="btn float-right" id="clipboard" data-clipboard-text=""><i class="fa fa-clipboard"></i></button></td></tr>
                        <tr><th>DESCRIPTION</th><td id="tc_description"></td></tr>
                        <tr><th>ORDER QTY.</th><td id="tc_orderqty"></td></tr>
                        <tr><th>DELIVERY DATE</th><td id="tc_delivery"></td></tr>
                 </table>
                </div>
                
                <div class="col-lg-3">
                    
					<div>
						<table id="mat" class= "table table-striped"><thead><tr><th>Material ID</th><th>Sheet</th><th>Nesting ID</th></tr> </thead>
							<tbody></tbody>
						</table>
						<table id="matdue" class= "table table-striped"><thead><tr><th>POI</th><th>DueDate</th><th>Receipt Date</th></tr> </thead>
							<tbody></tbody>
						</table>
					</div>
                </div>
				<div class="col-3">
					<div id="tc_image" class="text-center">
                        <a id="tc_image_a" href="" data-lightbox=""><img id="tc_image_img" src="" class="img-fluid"></a>
                    </div>
				</div>
                <div class="col-12">
                    <table id="detail" class= "table table-striped"><thead><tr><th>JobOperationid</th><th>Process</th><th>Cycletime</th><th>Machine</th></tr></thead>
					<tbody></tbody>
					</table>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="scheduledModal" tabindex="-1" role="dialog" aria-labelledby="scheduledModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="justify-content:right">                
                <div class="row m-0 w-100">           
					<div class="col-10 p-0 text-center">
					<div class="unschedule_udpate btn btn-warning btn-sm float-left">Unschedule</div>
					<h4 class="modal-title" id="materialModalLabel"><span id="jc_machine" class="text-light" ></span></h4>
					
					</div>
					<div class="col-2 p-0">
					<button type="button" class="btn btn-warning btn-lg float-right" data-bs-dismiss="modal"><i class="fa fa-times"></i></button>
					</div>
				</div>
            </div>
            <div class="modal-body row">
               <div class="col-12">
                    <!--<table id="jobdetail" class="table table-striped">
						<thead>
							<tr id="tc_row_status"><th>JobID</th><th>PartID</th><th>Customer</th><th>Description</th><th>Order Quantity</th></tr>
						</thead>
						
                        <tbody>
						</tbody>
						
                    </table>
					<select id="cell-filter mb-1">
							<option value="">--select--</option>
							<option value="week50">week50</option>
							<option >week51</option>
							<option>week52</option>
							<option>week01</option>							
						</select>-->
					<table clientidmode="Static" class="jobdetails hover w-100">
					<thead>
						<tr class="gridStyle">
						<th></th>
							<th style="width:90px">JobID</th>
							<th style="width:90px">PartID</th>
							<th>Customer</th>
							<th>Description</th>
							<th>Ope. ID</th>
							<th>Quantity</th>
							<th>Schedule Date</th>
							<th>week</th>
						</tr>
					</thead>					
					<tbody></tbody>
				</table>
				<input type="hidden" id="testunique">
				<div style='margin-top: 10px;' id='pagination'></div>
                </div>                

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="materialModal" tabindex="-1" role="dialog" aria-labelledby="materialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">     
				<div class="row m-0 w-100">           
					<div class="col-10 p-0 text-center"><h4 class="modal-title" id="materialModalLabel"><span id="tc_machine" class="text-light" ></span></h4>
					</div>
					<div class="col-2 p-0">
					<button type="button" class="btn btn-warning btn-lg float-right" data-bs-dismiss="modal"><i class="fa fa-times"></i></button>
					</div>
				</div>
            </div>			
            <div class="modal-body row">  			   
				<div class="col-12">
					<table class="table table-striped">
						<tr id="tc_row_status" class="bg-info"><th>Material Due In</th></tr>
					</table>
				</div>			
               <div class="col-12">
                    <table id="mat_detail" class="table table-striped">
						<thead>
							<tr id="tc_row_status"><th>Purchase Order ID</th><th>Part Revision</th><th>Sheets</th><th>Due Date</th></tr>
						</thead>
						
                        <tbody>
						</tbody>
						
                    </table>
                </div>                
				<div class="col-12">
					<table class="table table-striped">
						<tr id="tc_row_status" class="bg-info"><th>Jobs</th></tr>
					</table>
				</div>
				<div class="col-12">
                    <table id="mat_job_detail" class="mat_job_detail table table-striped">
						<thead>
							<tr id="tc_row_status"><th>JobID</th><th>PartID</th><th>Customer</th><th>Schedule Date</th><th>Sheet Required</th></tr>
						</thead>
						
                        <tbody>
						</tbody>
						
                    </table>
                </div> 
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="machineupdateModal" tabindex="-1" role="dialog" aria-labelledby="machineupdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content bg-secondary">
            <div class="modal-header w-100">     
					<div class="col-12 p-0 text-center">
						<span style="font-size:1.5rem;" id="tc_machine" class="text-light" >Change Machine Name</span>					
					<button type="button" class="btn btn-warning btn-lg float-right hidemachineupdate"><i class="fa fa-times"></i></button>					
					</div>
            </div>			
            <div class="modal-body row m-0">  			   
				<div class="col-12 p-0">
					<div class="select-cell ml-3">
							<div><select style="width:250px;height:37px" class="form-select form-select-md select-celltext"></select></div>
					</div>
					<table class="table table-striped">
						
						<tr id="tc_row_status" class="">
							<th><select style="width:250px;height:37px"class="form-select form-select-md select-text"></select></th>
							<th class=""><button class="btn btn-info update_machine" data-jobid="" data-uniqueid="">Update</button></th>							
						</tr>
					</table>
					<!--<div class="machines row m-0 w-100">
						<div class="col-6 p-0"></div>
						<div class="col-6"><button class="btn btn-info update_machine" data-jobid="" data-uniqueid="">Update</button></div>
					</div>	-->
					</div>
					<input type="hidden">
				</div>			
                
            </div>
        </div>
    </div>
</div>