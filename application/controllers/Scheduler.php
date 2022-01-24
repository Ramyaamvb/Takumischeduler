<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduler extends MY_Controller {
	
	public function __construct() {
		parent::__construct(); 
		
		      
		$this->load->model('scheduler_model', 'scheduler');  
		
		$this->D['v'] = '';	
		
		$this->D['title'] = 'Takumi Scheduler';	
		
		$this->D['cell'] = $this->scheduler->machine_cell();	
		
		$this->D['materials'] = $this->scheduler->material_list();
		
		$this->D['machines'] = $this->scheduler->cell_machines();
    }

    public function index()
    {
		$d = $this->D;
			
    }
	
	public function schedule($getjobs=false)
	{
		$d = $this->D;	

		$d['v'] = 'scheduler';	
					
		$this->load->view('pages/template', $d);
	}
	
	public function scheduledjobs()
	{
		$d = $this->D;		
		
		$d['v'] = 'scheduled';
		
		$d['cellname'] = $this->input->get('c');
		
		$d['mid'] = $this->input->get('mid');
		
		$this->D['machines'] = $this->scheduler->cell_machines();
		
		$this->load->view('pages/template', $d);
			
	}
	
	/**get the unschedule jobs**/
	public function schedulefilter()
	{
		
		$cell=$this->input->post('cell'); 
		$materialtype=$this->input->post('materialtype');
		$material_status=$this->input->post('material_status');
		$machineid = $this->input->post('machineid'); 
		
		$row = $this->scheduler->unschedule_jobs($cell,$machineid,$materialtype,$material_status);		
		print json_encode($row);
		exit;	
		
	}
	
	/** all the material funciton starts **/

	function material_list() /** get material list - material names**/
	{
		$row = $this->scheduler->material_list();
		print json_encode($row);
		exit;
	}
	function material_startweekdate() /** get material list - startweekdate**/
	{
		$row = $this->scheduler->material_startweekdate();
		print json_encode($row);
		exit;
	}
	function material() /*Material sheet on hand **/
	{
		$row = $this->scheduler->materials();
		print json_encode($row);
		exit;
	}
	
	function material_sheetused() /** material sheet used **/
	{
		$row = $this->scheduler->material_sheetused();
		print json_encode($row);
		exit;
	}
	
	function materialdetail() /** material card**/
	{
		$row = $this->scheduler->materialdetail();
		print json_encode($row);
		exit;
	}
	
	/** all the material funciton ends **/
	
	
	/** get all scheduled hours machine**/
	function getscheduledhours()
	{
		$row = $this->scheduler->getscheduledhours();
		print json_encode($row);
		exit;
	}
	
	/* get all scheduled hours cell*/
	function scheduledhours_cell()
	{
		$row = $this->scheduler->scheduledhours_cell();
		print json_encode($row);
		exit;
	}
	
	/** open job card - each job **/
	function open_jobcard()
	{
		$row = $this->scheduler->open_jobcard();
		//$img_dir = "Screen Grabs/";
		$img_dir = "http://machines.takumiprecision.com/Screen%20Grabs/";
		$trimmedpartnum = trim($row->partid);
		
		$path = $img_dir.$trimmedpartnum.".JPG";
		$img_search =  glob($path);
		if (isset($img_search)) {
			$row->image = $path;
		} else {
			$row->image = null; //'https://via.placeholder.com/150';
		}
		print json_encode($row);
		exit;
	}
	/** scheduled jobs card-by machine**/
	function scheduledjob()
	{
		$row = $this->scheduler->scheduledjob();
		print json_encode($row);
		exit;
	}
	
		
	/** submit to schedule jobs **/
	function schedule_job_bucket()
	{
		$row = $this->scheduler->schedule_job_bucket();
		print json_encode($row);
		exit;
	}
	
	/** submit to unscheduled jobs **/
	function unschedule_jobs()
	{
		$row = $this->scheduler->unschedule_jobs_update();
		print json_encode($row);
		exit;		
	}
		
	
	/** get machines for change machine name each job starts**/
	function getcells()
	{
		$row = $this->scheduler->machine_cell();
		print json_encode($row);
		exit;		
	}	
	function getmachines()
	{
		$row = $this->scheduler->getmachines();
		print json_encode($row);
		exit;
	}	
	function updatemachine() //update machinename
	{
		$row = $this->scheduler->updatemachine();
		print json_encode($row);
		exit;
	}
	/** get machines for change machine name each job ends**/

	

}