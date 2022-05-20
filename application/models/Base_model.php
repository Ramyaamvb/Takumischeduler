<?php

class Base_model extends CI_Model {
    
	
    function __construct(){
		parent::__construct();
		
		$this->m1db = $this->load->database('M1', TRUE);
	}
	
	function machine_cell() {
        
        $ret = array();
        
                    
        $querycell = $this->db->select('m_cell_name,m_cell_m1name')
							->join('machine_cells','m_type_id=m_cell_type_id','left outer')
							->where('m_cell_pit_show',1)
							->get('machine_types');
							
		$ret = $querycell->result();						
                
        return $ret;
    }	
	function cell_machines()
	{
		$query = $this->db->query("SELECT machine_unique,machine_name,m_cell_m1name,case when m_cell_m1name IN ('TWIN','MILL3','MILL1','MILL5') then '5' when m_cell_m1name IN ('DECO','PLAS','MILL2','MILL4') THEN '4' ELSE '' END  AS totalmachine FROM machines LEFT OUTER JOIN machine_cells ON m_cell_id=machine_cell_id where m_cell_pit_show =1");		
				
		return $query->result();
		
		
	}
}