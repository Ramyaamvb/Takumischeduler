<script src="<?=base_url('assets/js/jquery-3.5.1.min.js');?>"></script>
<script src="<?=base_url('assets/js/jquery.ml-keyboard.js');?>"></script>
<script src="<?=base_url('assets/js/sweetalert2.all.min.js');?>"></script>
<script src="<?=base_url('assets/js/demo.js');?>"></script>
<script src="<?=base_url('assets/datatables/datatables.min.js');?>"></script>
<script src="<?=base_url('assets/datatables/dataTables.fixedColumns.js');?>"></script>
<script src="<?=base_url('assets/js/bootstrap.bundle.min.js');?>"></script>
<script src="<?=base_url('assets/js/bootstrap.min.js');?>"></script>
<script src="<?=base_url('assets/js/custom.js');?>"></script>
<?php if (file_exists(constant('APPPATH').'../assets/js/custom_'.$v.'.js')) { ?>
<script src="<?=base_url('assets/js/custom_'.$v.'.js?v='.constant('APP_VERSION'));?>"></script>
<?php } ?>