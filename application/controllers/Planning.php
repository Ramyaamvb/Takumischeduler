<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Planning extends MY_Controller {
	
	public function __construct() {
		parent::__construct(); 
		
		
		$this->D['v'] = '';	
		
		$this->D['title'] = 'Takumi Planning';	
		
		$this->D['cells'] = $this->base->machine_cell();	
		
		
    }

    public function index($m1=false)
    {
		$d = $this->D;
			
		$d['v'] = 'planning';
		
		if($m1 == false)
			$d['cell'] = 'twin';
		else
			$d['cell'] = $m1;
		
		$d['machines'] = $this->planning->cell_machines($d['cell']);		
		
		
		$d['rows'] = $this->planning->unplanned_jobs($d['cell']);

		//var_dump($d['rows']);
		
		//$d['week_nums'] = array();
		
		$d['weekdata'] = $this->planning->machines_weekdata();
		
		
		$d['week_num'] = array();
		
		for($i=0;$i<8;$i++)					
			array_push($d['week_num'],date('W')+$i);			
				
		$this->load->view('pages/template',$d);
    }
	public function machines()
	{
		$row = $this->planning->machines($_POST['cell']);		
		
		print json_encode($row);
		
		exit;
	}
	public function setactualvalue ()
	{
		$row = $this->planning->setactualvalue();		
		
		print json_encode($row);
		
		exit;
	}
	public function planjobssubmit()
	{
		$row = $this->planning->planjobssubmit();		
		
		print json_encode($row);
		
		exit;
	}
	function gethrscommitweekjob()
	{
		$row = $this->planning->gethrscommitweekjob();
		
		print json_encode($row);
		
		exit;
		
	}
	function jobs_changeweek()
	{
		$row = $this->planning->jobs_changeweek();
		
		print json_encode($row);
		
		exit;
	}
	

}