<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduler extends MY_Controller {
	
	public function __construct() {
		parent::__construct(); 
		
		$this->D['v'] = '';	
		
		$this->D['title'] = 'Takumi Scheduler';	
		
		$this->D['cell'] = $this->base->machine_cell();	
		
		$this->D['machines'] = $this->base->cell_machines();
		
		$this->D['materials'] = $this->scheduler->material_list();
		
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
		$row = $this->base->machine_cell();
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