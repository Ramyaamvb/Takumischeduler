<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="row mt-5 w-100  m-0 justify-content-center">
	<div class="col-3 p-5 text-center bg-info ">
		<a href="<?=base_url("Planning");?>" class="text-light"><h1>Planning</h1></a>
	</div>	
</div>
<div class="row mt-3 w-100  m-0 justify-content-center">
	<div class="col-3 p-5 text-center bg-info text-light">
		<a href="<?=base_url("scheduler/schedule");?>" class="text-light"><h1>Scheduler</h1></a>
	</div>
</div>

<div class="row mt-3 w-100  m-0 justify-content-center">
	<div class="col-3 p-5 text-center bg-info text-light">
		<h4 class="text-dark">Get the Status of the Job</h4>
		<div class="row w-100 " style="padding-left:5.5rem">			
			<div class="col-8 text-left">
			<input type="text" class="form-control getjob">
			</div>
			<div class="col-4 text-left text-light showheader">
			<button class="btn btn-success getjobdetail">get</button>						
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="getjobtatus" tabindex="-1" role="dialog" aria-labelledby="getjobtatusjobLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="justify-content:right">                
                <div class="row m-0 w-100">           
					<div class="col-10 p-0 text-center">
					<div class="getjobtatusjobheader float-left"></div>
					<h4 class="modal-title" id="getjobtatusjobLabel">					
					</h4>
					</div>
					<div class="col-2 p-0">
					<button type="button" class="btn btn-warning btn-lg float-right closegethrscommitweekjob" data-dismiss="bs-modal"><i class="fa fa-times"></i></button>
					</div>
				</div>
            </div>
            <div class="modal-body row">
               <div class="col-12"> 					
					<table id="getjobtatusdetail" clientidmode="Static" class="table datatable hover w-100">
					<thead>
						<tr class="gridStyle">								
							<th>JobID</th>							
							<th>Op.ID</th>														
							<th>Workcenter</th>
							<th>Machine</th>
							<th>Job bucketweek</th>							
							<th style="width:80px">Jobop.bucketweek</th>							
							<th style="width:80px">queueno.</th>
							<th style="width:80px">Clockin</th>								
							<th style="width:80px">Starttime</th>								
						</tr>
					</thead>					
					<tbody></tbody>
					</table>					
                </div>                
            </div>						
        </div>
    </div>
</div>