<?php $this->load->view('pages/head.php'); ?>

<input type="hidden" id="type" value="<?=$type; ?>">
<input type="hidden" id="cell" value="<?=$cell; ?>">
<div class="container p-0 m-0">
	<div class="row header g-0 w-100 m-0">
		<div class="col-5">
		<img class="logowidth" src="<?=base_url('assets/images/takumi_logo_2021.png');?>">
		</div>
		<div class="col-3 mt-2 text-light">
			<span class="cellname"><?=ucfirst($cell);?><span>
		</div>
		<div class="col-4 text-right mt-2 text-light" id="screen">			
		</div>
	</div>
	<div class="get_pit_content row m-0 ">
	<table class="get_pit_content table data-table table-striped" style="height:92.5vh">
        <thead class="text-center bg-warning">
            <tr>
                <th></th>               
                <th>Queue1</th>
                <th>Queue2</th>
				<th>Queue3</th>                
				<th>Queue4</th>
				<th>Queue5</th>
            </tr>
        </thead>
        <tbody class="table-light">		
			 <?php  foreach($machines as $m_id => $m_name) { 
			
				$row = $rows[$m_id]; ?> 
				<tr class="machines-<?php print count($machines); ?>">
				
				<td class="machine_name" style="border-bottom:1px solid black">
					<?php print $m_name; ?>
				</td>
				  <?php for($n=1;$n<=5;$n++) { ?>
                
                    <?php if (isset($row['next_'.$n]) && is_array($row['next_'.$n])) { ?>
                        <td style="border-bottom:1px solid black"><div>
                            <?php foreach($row['next_'.$n] as $i=>$col) { ?>
                                <button class="boxstyle-<?php print count($machines); ?> btn btn-block <?php print $col->cardclass; ?>">                                    
																
                                    <div class="row justify-content-center">
										
										<div class="col-12">
											<div>
												<?php print substr($col->customer,0,20); ?><br/>
												<span class="pit-icon top-left">
												
												<i class="icons fa fa-thumbs<?php print ($col->programcomplete=='GOOD' ? '-up' : '-down');?>"></i>&nbsp;
											</span><i>(<?php print $col->jobid;?>)</i><span class="pit-icon bottom-left">                         &nbsp;               
												<i class="fa fa-cubes <?php print ($col->material=='RED' ? 'bg-light text-danger' : ($col->material=='GREEN'?'bg-success text-dark':'bg-warning text-dark')); ?>"></i>
												
											</span>	
											</div>
											<div><small>PART: </small><?php print $col->partnum;?></div>
										</div>
									</div>
										<!--<div class="row">
										<div class="col-3 text-left">
											<span class="pit-icon top-left">
												
												<i class="icons fa fa-thumbs<?php print ($col->programcomplete=='GOOD' ? '-up' : '-down');?>"></i>
												
											</span>
											</div>
											<div class="col-9 text-right">
											<span class="pit-icon bottom-left">                                        
												<i class="fa fa-cubes <?php print ($col->material=='RED' ? 'bg-light text-danger' : ($col->material=='GREEN'?'bg-success text-dark':'bg-warning text-dark')); ?>"></i>
												<i><small><?php print $col->stockbin ?></small></i>
											</span>	
										</div>
                                    </div>-->
                                </button>
                            <?php } ?>
                           
                        </div></td>
                    <?php }

					else { ?>
                        <td class="<?=($n>1)?'pit_futr_cell_'.$n.' pit_future_cell':'';?> <?=($n>1)?'d-none':'';?>">&nbsp;</td> 
                    <?php } ?>
                
                <?php } ?>
				
			</tr>
			 <?php } ?>
		</tbody>
	</table>
	</div>
</div>
<?php $this->load->view('pages/footer.php'); ?>
