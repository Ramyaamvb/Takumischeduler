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
	function cell_machines($cell=false)
	{		
			
		$query = $this->db->query("SELECT machine_unique,machine_name,m_cell_m1name,case when m_cell_m1name IN ('TWIN','MILL3','MILL1','MILL5') then '5' when m_cell_m1name IN ('DECO','PLAS','MILL2','MILL4') THEN '4' ELSE '' END  AS totalmachine FROM machines LEFT OUTER JOIN machine_cells ON m_cell_id=machine_cell_id where m_cell_pit_show =1");		
				
		return $query->result();
		
		
	}
	public function getjobtatus()
	{
		$query = $this->m1db->query("select CASE WHEN lmljobid IS NOT NULL THEN 'Clocked in ' ELSE 'No Clock-in' END as [ClockinsStatus],FORMAT(lmlactualstarttime,'dd/MM/yyyy') as starttime,FORMAT(ujmpbucketweek,'dd/MM/yyyy') as jobbucketweek,jmpJobID,jmoJobOperationID,jmoWorkCenterID as workcenter,xaqDescription,ujmobucketweek,ujmoScheduleQueue  FROM  Jobs 
									LEFT OUTER JOIN joboperations ON jmojobid=jmpjobid 
									LEFT OUTER JOIN WorkCenterMachines ON jmoWorkCenterID = xaqWorkCenterID AND jmoWorkCenterMachineID=xaqWorkCenterMachineID
									LEFT OUTER JOIN TimecardLines on lmljobid=jmpjobid AND lmlJobOperationID=jmoJobOperationID
									WHERE jmpJobId='".$_POST['getjob']."'");		
		
		
		$row = $query->result();
		
		return $row;
	}
}