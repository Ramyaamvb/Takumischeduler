
<body>

<input type="hidden" id="type" value="<?=$type; ?>">
<input type="hidden" id="cell" value="<?=$cell; ?>">
<div class="container p-0 m-0">
	<div class="row header g-0 w-100 m-0">
		<div class="col-5">
		<img class="logowidth" src="<?=base_url('assets/images/takumi_logo_2021.png');?>">
		</div>
		<div class="col-3 mt-2 text-light">
			<span class="cellname"><?=$cell;?><span>
		</div>
		<div class="col-4 text-right mt-2 text-light" id="screen">
			
		</div>
	</div>
	<div class="get_cell_content row m-0 mt-4">
		<div class="col-sm cellcontainer" style="background-color:white;">
			<div class="text-light row celltopic justify-content-center" style="background-color:#46353f">
				<span class="topicheader">Contribution Lost</span>
			</div>
			<div class="row cellbody">
				<div id="qualityticket">
				</div>
			</div>
		</div>
		<div class="col-sm cellcontainer" style="">
			<div class="row celltopic justify-content-center">
				<span class="topicheader">Late Jobs</span></h1>
			</div>
			<div class="row cellbody justify-content-center">
				<span class="cellcontent " id="latejobs"></span>
			</div>
		</div>
		<div class="col-sm cellcontainer" style="">
			<div class="row celltopic justify-content-center">
				<span class="topicheader">Materials</span></h1>
			</div>
			<div class="row cellbody justify-content-center">
				<span class="cellcontent" id="material"></span>
			</div>
		</div>
	</div>
	<div class="get_cell_content row m-0 mt-4">
		<div class="col-sm cellcontainer">
			<div class="row celltopic justify-content-center">
				<span class="topicheader">WIP</span>
			</div>
			<div class="row cellbody justify-content-center">
				<span class="cellcontent" id="wip"></span>
			</div>
		</div>
		<div class="col-sm cellcontainer" style="">
			<div class="row celltopic justify-content-center">
				<span class="topicheader">NRFT</span></h1>
			</div>
			<div class="row cellbody justify-content-center">
				<span class="cellcontent" id="nrft"></span>
			</div>
		</div>
		<div class="col-sm cellcontainer" style="">
			<div class="row celltopic justify-content-center">
				<span class="topicheader">COPQ</span></h1>
			</div>
			<div class="row cellbody justify-content-center">
				<span class="cellcontent" id="copq"></span>
			</div>
		</div>
	</div>
</body>


