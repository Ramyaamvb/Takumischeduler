<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Base extends MY_Controller {
	
	public function __construct() {
		parent::__construct(); 
		
		$this->D['v'] = '';	
		
		$this->D['title'] = 'Takumi Scheduler and Plaaning Dashboard';	
		
		 $this->load->model('base_model', 'base');
		
    }

    public function index()
    {			
		$d = $this->D;
		
		$d['v'] = 'choose';	
					
		$this->load->view('pages/template', $d);
			
    }
	public function getjobtatus()
	{
				
		$row = $this->base->getjobtatus();		
		
		print json_encode($row);
		
		exit;
	}
	
	

}