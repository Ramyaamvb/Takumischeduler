<?php $this->load->view('pages/head.php'); ?>
 <div id="preloader">
        <div class="pre-load">窓</div>
    </div>
<div class="wrapper" style="height:100vh;overflow:hidden;">
	<?//php $this->load->view('pages/scheduler.php'); ?>
	<?php $this->load->view('pages/v_'.$v); ?>
</div>
<script src="<?=base_url('assets/js/jquery-3.5.1.min.js');?>"></script>
<script src="<?=base_url('assets/js/jquery-ui.min.js');?>" type="text/javascript"></script>
<script src="<?=base_url('assets/datatables/datatables.1.min.js');?>"></script>
<script src="<?=base_url('assets/js/popper.min.js');?>"></script>
<script src="<?=base_url('assets/js/bootstrap.bundle.min.js');?>"></script>
<script src="<?=base_url('assets/js/bootstrap.min.js');?>"></script>
<script src="<?=base_url('assets/js/clipboard.min.js');?>"></script>
<script src="<?=base_url('assets/js/base64.min.js');?>"></script>
<script src="<?=base_url('assets/js/custom.js?v='.constant('APP_VERSION'));?>"></script>
<?php if (file_exists(constant('APPPATH').'../assets/js/custom_'.$v.'.js')) { ?>
<script src="<?=base_url('assets/js/custom_'.$v.'.js?v='.constant('APP_VERSION'));?>"></script>
<?php } ?>

