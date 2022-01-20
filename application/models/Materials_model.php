<?php

class Materials_model extends CI_Model {
    
	
    function __construct(){
		parent::__construct();
		
		$this->m1db = $this->load->database('M1', TRUE);
	}
		
	
	function getmachines()
	{
		if (constant('USE_DB')) {
						
			$query = $this->db->query('SELECT * from machines LEFT OUTER JOIN machine_cells  on m_cell_id = machine_cell_id ORDER BY machine_id ASC');
			
			return $query->result();
		}	
	}
    function get_cell_machines() {
        
        $ret = array();
        
                  
        $queryTypes = $this->db->order_by('m_type_name')->get('machine_types');
        
        foreach ($queryTypes->result() as $type) { 
        
            $queryCells = $this->db->where('m_cell_type_id',$type->m_type_id)->get('machine_cells');
            
            foreach ($queryCells->result() as $cell) {  
                if ($cell->m_cell_pit_show) {
                    
                    if (!isset($ret[$type->m_type_name]))
                       // $ret[$type->m_type_name] = array();
                   // $ret[$type->m_type_name][$cell->m_cell_name] = array();
                    
                    $queryMachines = $this->db->where('machine_cell_id',$cell->m_cell_id)->get('machines')->order('machine_id');
                    
                    foreach ($queryMachines->result() as $machine) {
                        $ret['{'.$machine->machine_unique.'}'] = $machine->machine_name; 
                    }
                    
                }
            }
        }
        
        return $ret;
    }
	 function get_cell_machines1() {
        
        $ret = array();
        
                  
        $queryTypes = $this->db->order_by('m_type_name')->get('machine_types');
        
        foreach ($queryTypes->result() as $type) { 
        
            $queryCells = $this->db->where('m_cell_type_id',$type->m_type_id)->get('machine_cells');
            
            foreach ($queryCells->result() as $cell) {  
                if ($cell->m_cell_pit_show) {
                    
                    if (!isset($ret[$type->m_type_name]))
                       // $ret[$type->m_type_name] = array();
                   // $ret[$type->m_type_name][$cell->m_cell_name] = array();
                    
                    $queryMachines = $this->db->query("SELECT machine_unique,m_cell_name,machine_name FROM machines LEFT OUTER JOIN machine_cells ON m_cell_id=machine_cell_id 
LEFT OUTER JOIN machine_types ON m_type_id=m_cell_type_id WHERE  m_cell_name !='WaterJet' and m_cell_pit_show=1 ORDER BY m_cell_id, machine_id");
                    
						
                    foreach ($queryMachines->result() as $machine) {
                        $ret['{'.$machine->machine_unique.'}'] = $machine; 
                    }
                    
                }
            }
        }
		        
        return $ret;
    }
    function getmachinestatus($machines)
	{
		 if (constant('USE_DB')) {
						
			$next_query = $this->m1db->query("WITH TOPSEVEN AS (
											  SELECT 
												jmpReadyToPrint as jobprintstatus, 
												xaqworkcenterid, 
												xaqDescription, 
												xaquniqueid as machineid, 
												jmpJobID as [jobid], 
												cmoName as [customer], 
												jmpPartID as [partnum], 
												RTRIM(jmpPartShortDescription) as description, 
												FORMAT(jmpProductionQuantity, '#') as [orderqty], 
												FORMAT(omdDeliveryDate, 'yyyy-MM-dd') as [deliverydate], 
												CASE WHEN uajIssuedToJob IS NOT NULL 
												OR imtPartTransactionID IS NOT NULL THEN '#28a745' WHEN rmlReceiptID is not null 
												OR unjProcessedComplete = -1 
												OR jmmReceivedComplete = -1 THEN '#ffc107' ELSE '#fc2719' END AS 'material', 
												ROW_NUMBER() over (
												  PARTITION BY xaqWorkCenterID, 
												  jmoWorkCenterMachineID 
												  order by 
													ujmoScheduleQueue ASC
												) AS RowNo 
											  from 
												Jobs as j1 
												Left Outer Join JobOperations on JMOJOBID = JMPJOBID 
												and JMOJOBASSEMBLYID = 0 
												LEFT OUTER JOIN Parts ON jmppartid = imppartid 
												Left Outer Join WorkCenterMachines on JMOWORKCENTERID = XAQWORKCENTERID 
												and JMOWORKCENTERMACHINEID = XAQWORKCENTERMACHINEID 
												Left Outer Join Organizations on JMPCUSTOMERORGANIZATIONID = CMOORGANIZATIONID 
												Left Outer Join SalesOrderJobLinks on JMPJOBID = OMJJOBID 
												Left Outer Join SalesOrderDeliveries on OMDSALESORDERID = OMJSALESORDERID 
												and OMDSALESORDERLINEID = OMJSALESORDERLINEID outer apply (
												  Select 
													top 1 imtPartTransactionID 
												  from 
													PartTransactions 
												  where 
													imtTransactionType = 2 
													and imtInventoryQuantityReceived > 0 
													and imtJobID = jmpJobID
												) A outer apply (
												  Select 
													top 1 rmlReceiptID 
												  from 
													ReceiptLines 
												  where 
													rmlJobID = jmpJobID 
													and rmlJobAssemblyID = 0
												) B outer apply (
												  Select 
													top 1 jmmReceivedComplete 
												  from 
													jobMaterials 
												  where 
													jmmJobID = jmpJobID 
													and jmmJobAssemblyID = 0
												) C outer apply (
												  Select 
													case when min(ujmpProgramComplete) = -1 
													or min(uimrProgramComplete) = -1 then 'GOOD' else 'NOTGOOD' END as programcomplete 
												  from 
													jobs as j2 
													left outer join PartRevisions on j2.JMPPARTID = IMRPARTID 
													and j2.JMPPARTREVISIONID = IMRPARTREVISIONID 
												  where 
													j2.jmpPartID = j1.jmpPartID 
													and j2.jmpPartRevisionID = j1.jmpPartRevisionID
												) D outer apply (
												  Select 
													max(uajPartBinID) as stockBin, 
													max(uajIssuedToJob) as uajIssuedToJob 
												  from 
													uLotNumJobs 
												  where 
													uajJobID = jmpJobID
												) E outer apply (
												  Select 
													count(*) as shippedbefore 
												  from 
													shipmentlines 
												  where 
													smlPartID = jmpPartID 
													and smlPartRevisionID = jmpPartRevisionID
												) F OUTER APPLY (
												  Select 
													top 1 CASE when omlPartID = jmpPartID 
													AND jmpCustomerOrganizationID = 'BOM001' 
													AND RTRIM(uifEPRrevision) = 'EPR ' + RTRIM(uomlbombardierERP) THEN 'btn-dark' when omlPartID <> jmpPartID 
													AND jmpCustomerOrganizationID = 'BOM001' 
													AND RTRIM(uifEPRrevision) = 'EPR ' + RTRIM(ujmpEPRRev) THEN 'btn-dark' ELSE 'btn-npi' END as bom_npi 
												  from 
													jobs 
													inner Join uPartFaiStatus on jmpPartID = uifPartID 
													and LTRIM(
													  REPLACE(
														REPLACE(jmpPartRevisionID, '-', ''), 
														'NC', 
														''
													  )
													) = LTRIM(
													  REPLACE(
														REPLACE(uifPartRevisionID, '-', ''), 
														'NC', 
														''
													  )
													) 
													Left Outer Join SalesOrderJobLinks on OMJJOBID = JMPJOBID 
													Left Outer Join SalesOrderLines on OMJSALESORDERID = OMLSALESORDERID 
													and OMJSALESORDERLINEID = OMLSALESORDERLINEID 
												  where 
													omjJobID = jmpJobID 
													and uifFairStatus = 3 
												  order by 
													uifPartFaiStatusID DESC
												) G 
												LEFT outer join uNestingJob on ujmpNestingJobID = unjNestingJobID 
											  WHERE 
												jmpProductionComplete <> -1 
												and jmpClosed <> -1 
												and omdClosed <> -1 
												and jmpQuantityShipped = 0 
												and jmpQuantityCompleted = 0 
												and (
												  xaqWorkCenterID IN (
													'MILL1', 'MILL2', 'MILL3', 'MILL4', 
													'MILL5', 'TWIN', 'DECO', 'PLAS'
												  )
												) 
												and ujmoScheduleQueue <> 0 
												and jmoJobAssemblyID = 0 
												and not exists (
												  Select 
													* 
												  from 
													timecardlines 
												  where 
													lmlJobID = jmoJobID
												)
											) 
											SELECT 
											  * 
											FROM 
											  TOPSEVEN 
											WHERE 
											  RowNo <= 4");

				for ($i=1;$i<=4;$i++) {
				
					$temparray = array_filter( $next_query->result(),function($resultvar) use ($i){
							return $resultvar->RowNo == $i;
						});
					$data['next_'.$i] = array_values($temparray);
					
					//print $m1db->last_query(); 
				}
		 }
		 
		foreach($machines as $machine_id => $machine_name){
			foreach($data as $dataarray => $datavalue){
					
					$temparray = array_filter($datavalue,function ($var) use ($machine_id) {
						
						return ("{".$var->machineid."}" == $machine_id);});
						
					$newarray[$machine_id][$dataarray] = array_values($temparray);
				
			}
		}
		//var_dump($newarray);
		return $newarray;
	}
	function getcellname()
	{
		$row = $this->db->query("SELECT machine_unique,m_cell_name,machine_name FROM machines LEFT OUTER JOIN machine_cells ON m_cell_id=machine_cell_id 
LEFT OUTER JOIN machine_types ON m_type_id=m_cell_type_id WHERE m_cell_pit_show=1 GROUP BY m_cell_name");
		return $row->result();
		
	}
	
	function get_timecard($machine_id, $job_id) {
        
        if (constant('USE_DB')) {
            
            $query = $this->m1db->query("Select   jmpReadyToPrint as jobprintstatus,CAST((jmpProductionQuantity * jmmQuantityPerAssembly ) AS DECIMAL(16,2))  as material_qty,rmlReceiptID,uajLotNumberID,stockBin,FORMAT(imttransactiondate,'yyyy-MM-dd') as materialissueddate,imtPartTransactionID,xaquniqueid,cmoName as customer, jmpjobid as jobid ,RTRIM(jmpPartID) as partnum,jmpPartShortDescription as description , Format(jmpProductionQuantity,'#') as orderqty, 
FLOOR(54/NULLIF(jmoProductionStandard,0)) as target, format(jmoProductionStandard,'#.#') as [cycle], format( omdDeliveryDate,'yyyy-MM-dd') as [deliverydate], CASE WHEN uajIssuedToJob IS NOT NULL OR imtPartTransactionID IS NOT NULL THEN 
 'green' WHEN rmlReceiptID is not null OR ujmpNestingJobID <> '' OR jmmReceivedComplete = -1 THEN 'orange' ELSE 'red' END AS 'material' from Jobs 
Left Outer Join JobOperations on JMOJOBID = JMPJOBID and JMOJOBASSEMBLYID = 0 Left Outer Join SalesOrderJobLinks on JMPJOBID = OMJJOBID Left Outer Join SalesOrderDeliveries ON 
OMJSALESORDERID = OMDSALESORDERID and OMJSALESORDERLINEID = OMDSALESORDERLINEID and OMJSALESORDERDELIVERYID = OMDSALESORDERDELIVERYID Left Outer Join Organizations 
on JMPCUSTOMERORGANIZATIONID = CMOORGANIZATIONID Left Outer Join WorkCenterMachines on JMOWORKCENTERID = XAQWORKCENTERID and JMOWORKCENTERMACHINEID = XAQWORKCENTERMACHINEID 
outer apply (Select top 1 imttransactiondate,imtPartTransactionID from PartTransactions where imtTransactionType =2 and imtInventoryQuantityReceived > 0 and imtJobID = jmpJobID )A 
outer apply (Select max( uajPartBinID ) as stockBin, max(uajIssuedToJob) as uajIssuedToJob,uajLotNumberID from uLotNumJobs where uajJobID = jmpJobID group by uajLotNumberID )E
outer apply (Select top 1 rmlReceiptID from ReceiptLines where rmlJobID = jmpJobID and rmlJobAssemblyID = 0 )B 
outer apply (Select top 1 jmmQuantityPerAssembly,jmmReceivedComplete from jobMaterials  where jmmJobID = jmpJobID and jmmJobAssemblyID = 0 )C  where xaqUniqueID = '". $machine_id ."' and jmpJobID = '". $job_id ."'");
//print $this->m1db->last_query(); exit;
            if ($query != null && $query->num_rows() > 0) {

                $row = $query->row();
					
			
                return $row;

            } else {

                return null;
            }
            
        } else {
            return $this->get_dummy_data();
        }
		
	}
	
	function materialparttransaction($jobid)
	{
		$query = $this->m1db->query("select jmmJobMaterialID, jmmPartID, jmmPartRevisionID, uajPartWarehouseLocationID ,uajIssuedtoJob , uajPartBinID ,uajQuantity, uajLotNumberID, imtPartTransactionID from JobMaterials
 Inner Join uLotNumJobs on jmmPartID = uajPartID and jmmPartRevisionID = uajPartRevisionID and jmmJobID = uajJobID outer apply( Select imtPartTransactionID from PartTransactions 
 Left Outer Join LotNumberTransactions on ABTPARTTRANSACTIONID = IMTPARTTRANSACTIONID where ABTJOBID = JMMJOBID and ABTJOBASSEMBLYID = 0 and uajLotNumberID = abtLotNumberID  )A  where
 jmmJobID = '".$jobid."' and jmmJobAssemblyID = 0");
 
 
		return $query->result();
	}
	
	
	
	
	
}