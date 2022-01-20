<div class="container-fluid choose-content cell-select h-auto" >
	<!--<div>
		<a href="http://machines.takumiprecision.com/?t=lathes&c=Twin%20Spindle&n=new" class="text-light">
		New
		</a>
	</div>-->
    <?php foreach ($cell_machines as $machine_type => $cells) { ?>
    
    <div class="card card-body text-center">
    
        <h1><?php print strtoupper($machine_type); ?></h1>

        <div class="row">

            <?php foreach ($cells as $cell_name => $machines) { ?>

            <div class="col-12 col-md-6 my-2 mx-auto">

                <a href="./cellmetrics/cell/<?php print $machine_type; ?>/<?php print $cell_name; ?>"><button class="btn btn-block btn-lg shadow-lg btn-primary"><?php print $cell_name;?></button></a>

            </div>

            <?php } ?>

        </div>
                   
    </div>
    
    <?php } ?>

</div>


