<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>

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

.calculationheader
{
	background-color:#F68D2E;
	color:white;
	font-size:1.5rem;
	padding:2px;
}
.DTFC_LeftBodyLiner{
      background : white !important;
      overflow : hidden !important;
    }
	.selectAll 
	{
		margin-left:10px;
	}
table.setdividerheight td
{
	height:67px;
}
		table.setdividerheight td div{    
    height:100%;
}
</style>
<div class="pagedisplay row m-0" style="height:100vh">
	<div class="filtersclass col-12 p-0">
		<div class="header-filter row w-100 m-0">					
			<div class="col-2 p-0 pl-2">
				<select name="cell" id="cell_select">			
					<?php foreach($cells as $k){ ?>
						<option value="<?=$k->m_cell_m1name; ?>" <?=($cell==$k->m_cell_m1name)?'selected':'';?>><?=$k->m_cell_name; ?></option>
					<?php } ?>
				</select>
			</div>			
			<div class="col-3">
			<button class="btn btn-lg btn-info" id="getjobs" type="submit" name="">Get Jobs</button>
			</div>
			<div class="col-3 text-center pt-1">
				<h4 class="text-light">P<small>LANNING</small> F<SMALL>OR</SMALL> <?=$cell;?> Cell</h4>
			</div>
		</div>	
		<div class="row m-0">
			<div class="col-5 p-0" style="border-right:1px solid black" id="set_height">
			<table class="schedulejobs bucket"  id="machine_hours_row" width="100% " height="100%">
			<thead>
				<tr>
					<th class=""><button class="btn btn-danger getbacklogjobs btn-sm" data-cell=<?=$cell;?>>Previous Week</button></th>
					<th style="border-right:0px;"></th>
					<?php foreach($week_num as $date){ ?>
						<th class="" style="border-right:0px;padding-top:2px;padding-bottom:2px;"><button class="planjobssubmit btn btn-success w-100" style="border-radius:0px;font-size:1.25rem"><?php echo $date; ?></button></th>					
					<?php } ?>						
				</tr>
			</thead>
				<tbody>
				<?php foreach($machines as $k) { ?>
					<tr class="machines-<?=$k->totalmachine;?>" data-machine="<?=$k->m_cell_m1name; ?>"> <!--scheduledjobs-->
						<th class="scheduledjobs col-md-2 p-0" data-machine="<?=$k->m_cell_m1name; ?>" data-uniqueid="<?=$k->machine_unique;?>"  data-machine="<?=$k->m_cell_m1name; ?>" data-machinename="<?=$k->machine_name;?>">
							<a href="<?=base_url('scheduler/scheduledjobs?c='.$k->m_cell_m1name.'&mid='.$k->machine_unique)?>" style="color:#442136"><?=$k->machine_name;?></a>
							<div class="mt-2  clearcalc test_<?=$k->machine_unique;?>"></div>
						</th>
						<td>
							<table class="w-100 setdividerheight" style="height:100%">
								<tr><td style="font-weight:700;border:0px;border-bottom:1px solid #878991" class="text-dark pt-1 pb-3">Ideal</td></tr>
								<tr><td style="font-weight:700;border:0px;border-bottom:1px solid #878991" class="pt-1 pb-3">Actual</td></tr>
								<tr><td style="font-weight:700;border:0px;background-color:#FFE699" class="pt-1 pb-2">Hours</td></tr>
							</table>
						</td>
						<?php foreach($week_num as $date){ ?>
						<td style="width:98px;">
							<table class="w-100 setdividerheight" style="height:100%">
								<tr><td style="border:0px;border-bottom:1px solid #878991" class="pt-3 pb-3">
									<div>--</div>
								</td></tr>	
								<tr>
								<td style="font-size:15px;border:0px;border-bottom:1px solid #878991"  class="pt-3 pb-3 acthr acthr_<?=$k->machine_unique;?>_<?=$date;?>" data-machineid="<?=$k->machine_unique;?>" data-week="<?=$date;?>">
									<div><?php									
									$issue = $k->machine_unique;
											$temparray1 = array_filter( $weekdata['actual'],function($resultvar) use($issue)  {												
												 return $resultvar->machine_unique == $issue;													
											});
									foreach($temparray1 as $i=>$test)
									{
										$t='w'.$date;										
										if(isset($test->$t))
										{
										if(($test->machine_unique == $k->machine_unique))
											echo $test->$t;	
										
										}
										else{
											echo '<button class="btn-info setactualhours" data-machineid="'.$k->machine_unique.'" data-week="'.$date.'">set</button>';
										}										
									}
									//echo '<button class="btn-info setactualhours" data-machineid="'.$k->machine_unique.'" data-week="'.$date.'">set</button>';
									?></div>
								</td></tr>
								<tr>
								<td style="font-weight:700;border:0px;background-color:#FFE699"  class="pt-4 pb-2 getjobshrscommit" data-cell=<?=$k->m_cell_m1name;?> data-machine=<?=$k->machine_unique;?> data-week="<?=$date;?>">
									<div><?php									
									$issue = $k->machine_unique;
									$temparray1 = array_filter( $weekdata['hourscommit'],function($resultvar) use($issue)  {												
										 return $resultvar->xaquniqueid == $issue;
											//return $resultvar->machine_unique;
									});
									$row = 'notexist';
									foreach($temparray1 as $i=>$test)
									{
										$t='w'.$date;										
										if(isset($test->$t))
										{
										$row = 'exist';
										if(($test->xaquniqueid == $k->machine_unique))
											echo round($test->$t,2);//.'<li class="fa fa-database" aria-hidden="true"></li>';										
										}
										/* else{
											echo 0;
										}	 */									
									}
									if($row=='notexist')
										echo 0;
									?>
								</td></div>
								</tr>
							</table>
							
						</td>					
						<?php } ?>						
						
					</tr>
				<?php } ?>
				</tbody>
			</table>
			</div>
			<div class="col-7 p-0" style="height:auto">				
				<div class="row w-100 m-0">										
					<div class="col-12 p-0 tablecontent" style="overflow:hidden;">		
					<div class="row w-100 mt-1">
						<div class="col-2">
							<input type="checkbox" class="selectAll" name="selectAll" value="all"> Select All					
						</div>
						<!--<div class="col-2">
							<select id="admin_filter">
								<option>--All--</option>
								<option value="Week-14">Week-14</option>
							<select>
						</div>-->
					</div>
					<style>
					tfoot input {
							  width: 100%;
							  padding: 3px;
							  box-sizing: border-box;
							}
							table.dataTable.no-footer
							{
								border-bottom:0px;
							}
							table.dataTable thead th, table.dataTable thead td
							{
								border-bottom: 1px solid #e3e6f0;
								border-top: 1px solid #e3e6f0;
							}
					</style>
						<!--<table id="unschedule"  >-->
						<table id="unschedule" clientidmode="Static" class="unschedule tablebody display nowrap hover w-100" style="width:100%">
						<thead class="bg-dark">
							<tr class="gridStyle">
								<th></th>								
								<th style="width:90px">PartID</th>
								<th style="width:90px">JobID</th>								
								<th>PartDescription</th>
								<th>Rema. Qty</th>
								<th style="width:90px">Scheduled Date</th>
								<th>Customer Delv. Date</th>
								<!--<th style="width:90px">Schedule Start</th>-->
								<th>Workcenter</th>
								<th>Machine</th>
								<th>Cycle Time</th>	
								<th>Customer</th>		
								<th>Original Prod. Week</th>
								<th>Current Prod. Week</th>								
								<th>Material ID</th>
								<th>Material PartDes.</th>
								<th>Mat Status.</th>
								<th>Nesting Jobid</th>								
								<th>SalesOrderdate</th>
								<th>Program</th>
								<th>Xaquniqueid</th>
							</tr>			
							<!--<tr>
								<td></td>								
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<!--<td style="widtd:90px">Schedule Start</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>-->
								
							</tr>
						</thead>					
						<tbody>
							<?php foreach($rows as $v) { ?>
							<tr>
								<td></td>
								<td><?=$v->PartID;?></td>
								<td><?=$v->jmpJobID;?></td>
								<td><?=substr($v->PartDescription,0,10);?></td>
								<td><?=round($v->remaining_Quantity,0);?></td>
								<td><?=date("d/m/Y", strtotime($v->jmpscheduledstartdate));?></td>
								<td><?=date("d/m/Y", strtotime($v->uomdcustomerdeliverydate));?></td>
								<td data-name="workcenter"><?=$v->workcenter;?></td>		
								<td><?=$v->MACHINE;?></td>		
								<td><?=round($v->cycletime,2);?></td>		
								<td><?=$v->Customer;?></td>		
								<td><?=($v->ujmporiginalprodweek!=0)?'W-'.$v->ujmporiginalprodweek:'';?></td>		
								<td><?=($v->ujmpcurrentprodweek!=0)?'W-'.$v->ujmpcurrentprodweek:'';?></td>										
								<td><?=$v->jmmpartid;?></td>		
								<td><?=$v->jmmpartshortdescription;?></td>		
								<td><?=$v->MatStatus;?></td>		
								<td><?=$v->ujmpNestingJobID;?></td>		
								<td><?=date("d/m/Y", strtotime($v->omporderdate));?></td>
								<td><?=$v->program;?></td>		
								<td><?=$v->xaquniqueid;?></td>			
							</tr>
							<?php } ?>
						</tbody></table>
						<!--<table id="unschedule" class="unschedule tablebody display nowrap" cellspacing="0" width="100%">
								
							</tbody>
						</table>-->
					
					</div>
				</div>
			</div>
		</div>
	
	</div>
</div>
<style>
.checkbox-impact:checked + label,
.checkbox-impact:not(:checked) + label{
	position: relative;
	display: inline-block;
	padding: 0;
	padding-top: 13px;
	width: 50px;
	height:50px;
	letter-spacing: 1px;
	margin:-11px auto;	
	text-align: center;
	border-radius: 4px;
	overflow: hidden;
	cursor: pointer;
	text-transform: uppercase;
	-webkit-transition: all 300ms linear;
	transition: all 300ms linear; 
	<!---webkit-text-stroke: 1px var(--white);-->
    text-stroke: 1px var(--white);
    <!---webkit-text-fill-color: transparent;-->
    text-fill-color: transparent;
    color: #646570;
}
.checkbox-impact:not(:checked) + label{
	background-color: #bebece;
	box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
}
.checkbox-impact:checked + label{
	background-color: #3b8ba6!important;
	box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
}
.checkbox-impact:not(:checked) + label:hover{
	box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
}
.checkbox-impact:checked + label::before,
.checkbox-impact:not(:checked) + label::before{
	position: absolute;
	content: '';
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	border-radius: 4px;
	background-image: linear-gradient(138deg, var(--red), var(--yellow));
	z-index: -1;
}
.checkbox-impact:checked + label span,
.checkbox-impact:not(:checked) + label span{
	position: relative;
	display: block;
}
.checkbox-impact:checked + label span::before,
.checkbox-impact:not(:checked) + label span::before{
	position: absolute;
	content: attr(data-hover);
	top: 0;
	left: 0;
	width: 100%;
	overflow: hidden;
	-webkit-text-stroke: transparent;
    text-stroke: transparent;
    -webkit-text-fill-color: var(--white);
    text-fill-color: var(--white);
    color: var(--white);
	-webkit-transition: max-height 0.3s;
	-moz-transition: max-height 0.3s;
	transition: max-height 0.3s;
}
.checkbox-impact:not(:checked) + label span::before{
	max-height: 0;
}
.checkbox-impact:checked + label span::before{
	max-height: 100%;
}

.checkbox:checked ~ .section .container .row .col-xl-10 .checkbox-impact:not(:checked) + label{
	background-color: var(--light);
	-webkit-text-stroke: 1px var(--dark-blue);
    text-stroke: 1px var(--dark-blue);
	box-shadow: 0 1x 4px 0 rgba(0, 0, 0, 0.05);
}

[type="radio"]:checked,
[type="radio"]:not(:checked){
	<!--position: absolute;
	left: -9999px;
	width: 0;
	height: 0;
	visibility: hidden;-->
}
</style>
<div class="modal fade" id="setactualhour" tabindex="-1" role="dialog" aria-labelledby="setactualhourLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" style="justify-content:right">                
                <div class="row m-0 w-100">           
					<div class="col-10 p-0 text-center">
					<div class="setactualhourheader float-left"></div>
					<h4 class="modal-title" id="setactualhourLabel"><span id="jc_machine" class="text-light" ></span></h4>
					
					</div>
					<div class="col-2 p-0">
					<button type="button" class="btn btn-warning btn-lg float-right closesetactualhour" data-dismiss="bs-modal"><i class="fa fa-times"></i></button>
					</div>
				</div>
            </div>
            <div class="modal-body row">
               <div class="col-12"> 
					 <div class="form-group">						
						<h4 class="bg-secondary p-1 text-light"><label for="">Standard Hour</label></h4><br/>
						<input class="checkbox-impact" type="radio" name="standhr" id="impact-5"  value="39">
						<label class="radiospan for-checkbox-impact" for="impact-5">							
							<span data-hover="39">39</span>
						</label>
						<input class="checkbox-impact" type="radio" name="standhr" id="impact-6" checked value="78">
						<label class="radiospan for-checkbox-impact" for="impact-6">							
							<span data-hover="78">78</span>
						</label>
					 </div>
					 <div class="form-group">
						<h4 class="bg-secondary p-1 text-light"><label for="">Work Efficiency</label></h4><br/>
						<input class="checkbox-impact" type="radio" name="workeffi" id="impact-3" value="75">
						<label class="radiospan for-checkbox-impact" for="impact-3">							
							<span data-hover="75%">75%</span>
						</label>
						<input class="checkbox-impact" type="radio" name="workeffi" id="impact-7" checked value="80">
						<label class="radiospan for-checkbox-impact" for="impact-7">							
							<span data-hover="80%">80%</span>
						</label>
						<input class="checkbox-impact" type="radio" name="workeffi" id="impact-4" value="85">
						<label class="radiospan for-checkbox-impact" for="impact-4">							
							<span data-hover="85%">85%</span>
						</label>
					 </div>
					 <div class="form-group">
						<h4 class="bg-secondary p-1 text-light"><label for="">Bank Holiday</label></h4><br/>
						<input class="checkbox-impact" type="radio" name="bankholiday" id="impact-1" value="9">
						<label class="radiospan for-checkbox-impact" for="impact-1">							
							<span data-hover="Yes">Yes</span>
						</label>
						<input class="checkbox-impact" type="radio" name="bankholiday" id="impact-2" checked value="0">
						<label class="radiospan for-checkbox-impact" for="impact-2">							
							<span data-hover="No">No</span>
						</label>
					 </div>					  
					 <button type="submit" class="mt-1 btn btn-success submitactualhours" data-cell="<?=$cell;?>">Submit</button>					
                </div>                
            </div>						
        </div>
    </div>
</div>
<div class="modal fade" id="gethrscommitweekjob" tabindex="-1" role="dialog" aria-labelledby="getbacklogweekjobLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="justify-content:right">                
                <div class="row m-0 w-100">           
					<div class="col-10 p-0 text-center">
					<div class="getbacklogweekjobheader float-left"></div>
					<h4 class="modal-title" id="getbacklogweekjobLabel"><span id="jc_machine" class="text-light" ></span></h4>
					
					</div>
					<div class="col-2 p-0">
					<button type="button" class="btn btn-warning btn-lg float-right closegethrscommitweekjob" data-dismiss="bs-modal"><i class="fa fa-times"></i></button>
					</div>
				</div>
            </div>
            <div class="modal-body row">
               <div class="col-12"> 
					<table clientidmode="Static" class="table datatable hover w-100 getbacklogweekjobdatatable">
					<thead>
						<tr class="gridStyle">							
							<th style="width:70px">JobID</th>
							<th style="width:80px">PartID</th>	
							<th style="width:80px">Desc.</th>															
							<th style="width:80px">Customer</th>
							<th style="width:80px">ProWeekno</th>
							<th style="width:80px">Machine</th>							
							<th style="width:80px">Cycle Time</th>							
						</tr>
					</thead>					
					<tbody></tbody>
					</table>					
                </div>                
            </div>						
        </div>
    </div>
</div>