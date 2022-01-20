
<style>
table.timecard th
{
	font-size:1.25rem;
}
table.timecard td
{
	font-size:1.25rem;
}
table.materialstatus thead th
{
	font-size: 1.60rem;
	text-align:center;
}
.table td, .table th
{
	padding:0.5px;
}
table.materialstatus tbody td
{
	font-size: 1rem;
	text-align:center;
	vertical-align: middle;
	
}
table.materialstatus tbody td div
{
	font-size: 1.05rem;
	text-align:center;
}

.materialstatus {
  table-layout: fixed;
  width: 100% !important;
  border:2px solid #8c8686
}
.materialstatus td,
.materialstatus th{
  width: auto !important;
  white-space: normal;
  text-overflow: ellipsis;
  overflow: hidden;
}
.takumibgcolor
{
background-color: #442136;
}
.takumibg_border
{
  border:2px solid #442136;
}
</style>
<?php 
function test($row,$a)
{ ?>
			<tr>						
			<td colspan=3 class="bg-info text-light" >						 
					<?php print $a; ?>				
				
			</td>
			</tr>
			<tr>
			 <?php for($n=1;$n<=3;$n++) { ?>						
				<?php if (isset($row['next_'.$n]) && is_array($row['next_'.$n]) && ($row['next_'.$n]!='')) { ?>
					<td>
					<div>
						<?php 
						if(empty($row['next_'.$n])) { ?>
							<button class="open_timecard btn btn-block">No Jobs
							</button>
							<?php } 
							foreach($row['next_'.$n] as $i=>$col) {											
							if(empty($col)) { echo 't'; }
							?>
							<button class="text-light open_timecard btn btn-block" style="line-height:1.2rem;font-size:1.05rem;background-color:<?=$col->material;?>">
							   
								<div style="font-size:1.05rem">
									<span class=" p-1 bg-light float-right">
										<i style="font-size:8px"class="text-<?php if($col->jobprintstatus=='-1') {echo 'danger';} else { echo 'success'; }?>  fa fa-print" aria-hidden="true"></i>
										</span><?php print $col->jobid;?>
								</div>											
								<div style="font-size:0.75rem"><?php print ucwords(strtolower($col->description));?> 
									</div>
								
								
							   </button>
						<?php } ?>
					   
					</div></td>
				<?php }
				
			} ?>
			
		</tr>
<?php }
?>
<h2 class="mb-0  bg-danger text-white text-center d-flex justify-content-center" style="padding-bottom:0.5px">
     <div class="row w-100">
		 <div class="col-11">
		 Material Status   
		 </div>
		<div class="col-1 text-right mt-2 text-light" id="screen">	
		</div>
	 </div>
<h2>
<div class="row m-2 materialcontent" id="set_heights">
	<div class="col-sm m-0 takumibg_border rounded mr-2" >
		<div class=" row justify-content-center text-light takumibgcolor">
			Mill 4			
		</div>
		<div class="row" style="height:90%">
			<table class="materialstatus table my-0 w-100">	
				 <thead  class="thead-dark">
					<tr>
						
					</tr>
				</thead>
				<tbody class="table-light">		
					 <?php   
						$category = 'Mill 4';
							$temparray1 = array_filter( $machines,function($resultvar) use($category)  {
								return $resultvar->m_cell_name == $category;					
							});
						$a = array_values($temparray1);						
						foreach($a as $m_id => $m_name) {						
						$row = $rows['{'.$m_name->machine_unique.'}']; 						
						test($row,$m_name->machine_name);
						?> 
						
					 <?php   } ?>
				</tbody>
			</table>
		</div>
	</div>	
	<div class="col-sm m-0 takumibg_border rounded mr-2" >
		<div class="  row justify-content-center text-light takumibgcolor">
			Sliding Head			
		</div>
		<div class="row" style="height:90%">
				<table class="materialstatus table my-0 w-100">	
				 <thead  class="thead-dark">
					<tr>
						
					</tr>
				</thead>
				<tbody class="table-light">		
					 <?php   
						$category = 'Sliding Head';
							$temparray1 = array_filter( $machines,function($resultvar) use($category)  {
								return $resultvar->m_cell_name == $category;					
							});
						$a = array_values($temparray1);						
						foreach($a as $m_id => $m_name) {						
						$row = $rows['{'.$m_name->machine_unique.'}']; 						
						test($row,$m_name->machine_name);
						?> 
						
					 <?php   } ?>
				</tbody>
			</table>
		</div>
	</div>
<div class="col-sm m-0 rounded takumibg_border mr-2" >
		<div class="  row justify-content-center text-light takumibgcolor">
			Plastic		
		</div>
		<div class="row" style="height:90%">
			<table class="materialstatus table my-0 w-100">	
				 <thead  class="thead-dark">
					<tr>
						
					</tr>
				</thead>
				<tbody class="table-light">		
					 <?php   
						$category = 'Plastics';						
							$temparray1 = array_filter( $machines,function($resultvar) use($category)  {
								return $resultvar->m_cell_name == $category;					
							});
						$a = array_values($temparray1);						
						foreach($a as $m_id => $m_name) {						
						$row = $rows['{'.$m_name->machine_unique.'}']; 						
						test($row,$m_name->machine_name);
						?> 
						
					 <?php   } ?>
				</tbody>
			</table>
		</div>
	</div>	
		
	<div class="col-sm m-0 takumibg_border rounded " >
		<div class="  row justify-content-center text-light takumibgcolor">
			Mill 2			
		</div>
		<div class="row" style="height:90%">
			<table class="materialstatus table my-0 w-100">	
				 <thead  class="thead-dark">
					<tr>
						
					</tr>
				</thead>
				<tbody class="table-light">		
					 <?php   
						$category = 'Mill 2';
							$temparray1 = array_filter( $machines,function($resultvar) use($category)  {
								return $resultvar->m_cell_name == $category;					
							});
						$a = array_values($temparray1);						
						foreach($a as $m_id => $m_name) {						
						$row = $rows['{'.$m_name->machine_unique.'}']; 						
						test($row,$m_name->machine_name);
						?> 
						
					 <?php   } ?>
				</tbody>
			</table>
		</div>
	</div>		
</div>

<div class="row m-2 materialcontent1" id="set_heights1">
	<div class="col-sm m-0 takumibg_border rounded mr-2" >
		<div class=" row justify-content-center text-light takumibgcolor">
			Mill 3		
		</div>
		<div class="row" style="height:90%">
			<table class="materialstatus table my-0 w-100">	
				 <thead  class="thead-dark">
					<tr>
						
					</tr>
				</thead>
				<tbody class="table-light">		
					 <?php   
						$category = 'Mill 3';
							$temparray1 = array_filter( $machines,function($resultvar) use($category)  {
								return $resultvar->m_cell_name == $category;					
							});
						$a = array_values($temparray1);						
						foreach($a as $m_id => $m_name) {						
						$row = $rows['{'.$m_name->machine_unique.'}']; 						
						test($row,$m_name->machine_name);
						?> 
						
					 <?php   } ?>
				</tbody>
			</table>
		</div>
	</div>	
	<div class="col-sm m-0 takumibg_border rounded mr-2" >
		<div class="   row justify-content-center text-light takumibgcolor">
			Mill 1			
		</div>
		<div class="row" style="height:90%">
			<table class="materialstatus table my-0 w-100">	
				 <thead  class="thead-dark">
					<tr>
						
					</tr>
				</thead>
				<tbody class="table-light">		
					 <?php   
						$category = 'Mill 1';
							$temparray1 = array_filter( $machines,function($resultvar) use($category)  {
								return $resultvar->m_cell_name == $category;					
							});
						$a = array_values($temparray1);						
						foreach($a as $m_id => $m_name) {						
						$row = $rows['{'.$m_name->machine_unique.'}']; 						
						test($row,$m_name->machine_name);
						?> 
						
					 <?php   } ?>
				</tbody>
			</table>
		</div>
	</div>	
		
	<div class="col-sm m-0 takumibg_border rounded mr-2" >
		<div class="  row justify-content-center text-light takumibgcolor">
			Mill 5			
		</div>
		<div class="row" style="height:90%">
			<table class="materialstatus table my-0 w-100">	
				 <thead  class="thead-dark">
					<tr>
						
					</tr>
				</thead>
				<tbody class="table-light">		
					 <?php   
						$category = 'Mill 5';
							$temparray1 = array_filter( $machines,function($resultvar) use($category)  {
								return $resultvar->m_cell_name == $category;					
							});
						$a = array_values($temparray1);						
						foreach($a as $m_id => $m_name) {						
						$row = $rows['{'.$m_name->machine_unique.'}']; 						
						test($row,$m_name->machine_name);
						?> 
						
					 <?php   } ?>
				</tbody>
			</table>
		</div>
	</div>		
	<div class="col-sm m-0 takumibg_border rounded " >
		<div class="  row justify-content-center text-light takumibgcolor">
			Twin Spindle			
		</div>
		<div class="row" style="height:90%">
				<table class="materialstatus table my-0 w-100">	
				 <thead  class="thead-dark">
					<tr>
						
					</tr>
				</thead>
				<tbody class="table-light">		
					 <?php   
						$category = 'Twin Spindle';
							$temparray1 = array_filter( $machines,function($resultvar) use($category)  {
								return $resultvar->m_cell_name == $category;					
							});
						$a = array_values($temparray1);						
						foreach($a as $m_id => $m_name) {						
						$row = $rows['{'.$m_name->machine_unique.'}']; 						
						test($row,$m_name->machine_name);
						?> 
						
					 <?php   } ?>
				</tbody>
			</table>
		</div>
	</div>			
</div>

