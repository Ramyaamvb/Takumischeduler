<?php

class Cellmetrics_model extends CI_Model {
    
	
    function __construct(){
		parent::__construct();
		
		$this->m1db = $this->load->database('M1', TRUE);
	}
	
	function get_cell_machines() {
        
        $ret = array();
        
                    
        $queryTypes = $this->db->order_by('m_type_name')->get('machine_types');
        
        foreach ($queryTypes->result() as $type) { 
        
            $queryCells = $this->db->where('m_cell_type_id',$type->m_type_id)->get('machine_cells');
            
            foreach ($queryCells->result() as $cell) {  
                if ($cell->m_cell_pit_show) {
                    
                    if (!isset($ret[$type->m_type_name]))
                        $ret[$type->m_type_name] = array();
                    $ret[$type->m_type_name][$cell->m_cell_name] = array();
                    
                    $queryMachines = $this->db->where('machine_cell_id',$cell->m_cell_id)->get('machines');
                    
                    foreach ($queryMachines->result() as $machine) {
                        $ret[$type->m_type_name][$cell->m_cell_name]['{'.$machine->machine_unique.'}'] = $machine->machine_name; 
                    }
                    
                }
            }
        }
        
        return $ret;
    }	
	function get_m1_cells_ids($cell_name) {
        
        $ret = array();
                         
        $query = $this->db->where('m_cell_name',$cell_name)->get('machine_cells');
        
		if ($query != null && $query->num_rows() > 0) {

                $row = $query->row();
                return $row->m_cell_m1name;

            } else {

                return null;
            }
        
        
        return $ret;
    }
	function get_latejobs($cell) {
        
        $data = array();
         	  
        if (constant('USE_DB')) {

			$query = $this->m1db->query("
  
  Select FORMAT(imttransactiondate,'yyyy-MM-dd') as materialissueddate,'-' as WIP,'black' as color,jmpProductionQuantity,jmoworkcentermachineid,jmpJobID as id,cmoname as Customer , jmpPartID , jmpPartShortDescription as Description , FORMAT(jmpScheduledStartDate,'yyyy-MM-dd') as schedulestartdate , 
FORMAT(omdDeliveryDate, 'yyyy-MM-dd') as ShipDate,FORMAT(jmoDueDate,'yyyy-MM-dd') as scheduleenddate,
 Format(uomdCustomerDeliveryDate,'yyyy-MM-dd') as customerdeliverydate, CASE WHEN uajIssuedToJob is not null OR imttransactiondate IS NOT NULL THEN 'Issued'
  WHEN rmlReceiptID is not null OR ujmpNestingJobID <> '' OR jmmReceivedComplete = -1 THEN 'Nest/Rec' ELSE 'Not Here' END AS 'Material',xaqDescription,CONVERT(varchar(5),DATEADD(minute, (jmoProductionStandard) * jmpProductionQuantity , 0), 114) as hours from jobs 
  OUTER APPLY (SELECT top 1 CASE 
  WHEN (jmoWorkCenterID = 'TWIN' ) THEN 'twin' 
  WHEN (jmoworkcenterid = 'deco') THEN 'deco'
  WHEN (jmoWorkCenterID = 'mill1') THEN 'MILL1' 
  WHEN (jmoWorkCenterID = 'mill2') THEN 'MILL2' 
  WHEN (jmoWorkCenterID = 'mill3') THEN 'MILL3'  
  WHEN (jmoWorkCenterID = 'mill4') THEN 'MILL4' 
 WHEN (jmoWorkCenterID = 'mill5') THEN 'MILL5' 
 
  ELSE 'UNKNOWN' END as machinetype, jmoDueDate,
  jmoWorkCenterMachineID ,xaqDescription,jmoProductionStandard from joboperations LEFT OUTER JOIN WorkCenterMachines ON jmoworkcentermachineid=xaqWorkCenterMachineID and jmoWorkCenterID = xaqWorkCenterID Where jmpJobID = jmoJobID and jmoJobAssemblyID = 0 
  and (jmoWorkCenterID like 'MILL%' OR jmoWorkCenterID  in ('deco' ,'twin')) 
  order by jmoJobOperationID ASC ) OPS OUTER APPLY ( Select count(*) as outcount from joboperations where jmoJobID = jmpJobID and jmoJobAssemblyID = 0 and jmoOperationType = 2 )TREATS  
  left outer join Parts on JMPPARTID = IMPPARTID Left Outer Join Organizations on JMPCUSTOMERORGANIZATIONID = CMOORGANIZATIONID Left Outer Join SalesOrderJobLinks on JMPJOBID = OMJJOBID 
  Left Outer Join SalesOrderDeliveries on OMJSALESORDERID = OMDSALESORDERID and OMJSALESORDERLINEID = OMDSALESORDERLINEID and OMJSALESORDERDELIVERYID = OMDSALESORDERDELIVERYID 
  LEFT OUTER JOIN (SELECT pmljobid, Rtrim(Max( pmlpurchaseorderid )) AS pmlpurchaseorderid , Format(Max( pmlduedate ),'dd/MM/yyyy') AS pmlduedate , Max(rmlreceiptid) AS rmlreceiptid , 
  Format(Max(rmlreceiptdate),'dd/MM/yyyy') AS rmlreceiptdate FROM purchaseorderlines LEFT OUTER JOIN receiptlines ON rmlpurchaseorderid = pmlpurchaseorderid AND 
  rmlpurchaseorderlineid = pmlpurchaseorderlineid WHERE pmljobtype = 1 GROUP BY pmljobid ) AS c ON pmljobid = jmpjobid  outer apply (Select max( uajPartBinID ) as stockBin, 
  max(uajIssuedToJob) as uajIssuedToJob from uLotNumJobs where uajJobID = jmpJobID )Issue 
  outer apply (Select top 1 imttransactiondate from PartTransactions where imtTransactionType =2 and imtInventoryQuantityReceived > 0 and imtJobID = jmpJobID )A
outer apply (Select top 1 jmmReceivedComplete from jobMaterials where 
  jmmJobID = jmpJobID and jmmJobAssemblyID = 0 )Material WHERE jmpClosed <> -1 and jmpProductionComplete <> -1 and jmpScheduledStartDate < getdate()-1 and 
  jmpjobid in ( Select omjJobID from salesorders Left Outer Join SalesOrderLines on OMLSALESORDERID = OMPSALESORDERID 
  Left Outer Join SalesOrderDeliveries on OMDSALESORDERID = OMLSALESORDERID and OMDSALESORDERLINEID = OMLSALESORDERLINEID Left Outer Join SalesOrderJobLinks on OMJSALESORDERID = OMDSALESORDERID 
  and OMJSALESORDERLINEID = OMDSALESORDERLINEID and OMJSALESORDERDELIVERYID = OMDSALESORDERDELIVERYID  Where ompClosed <> -1 and omlClosed <> -1 and omdClosed <> -1 and omdShippedComplete <> -1
   and omdQuantityShipped < omdDeliveryQuantity UNION select jmpjobid from jobs where jmpCustomerOrganizationID = 'FIGEAC' and jmpJobID > '6%' and jmpProductionComplete <> -1 ) 
	and not exists (Select lmlTimecardID from TimecardLines where lmlJobID = jmpJobID ) and impPartGroupID <> 'GMRS' and impPartClassID <> '01m'  and imppartclassid <> '01P' 
	and jmpquantityShipped = 0 AND machinetype = '".$cell."' order by material asc");
			
			$issue = 'Issued';
				$temparray = array_filter( $query->result(),function($resultvar) use($issue)  {
					return $resultvar->Material == $issue;					
				});
			
			$data = count($temparray); 

			
			
        } 
		return $data;
        
		
    }
	function get_wip_machine_jobs($type, $cell) {
        
        $data = array();
               
        if (constant('USE_DB')) {
			
			$cell_machines = $this->get_cell_machines();
            $these_machines = $cell_machines[$type][$cell];

            $machine_ids = '';

            foreach($these_machines as $machine_id => $machine_name) {
                $machine_ids .= "'" . $machine_id . "'"  . ",";
            }
            $machine_ids = substr($machine_ids,0,-1);

            if (strlen($machine_ids)) {

                $query = $this->m1db->query("select lmeEmployeeName as lastclockin,uniquetimeid,  unique_id, lmlUniqueID, RTRIM(cmoName) as customer,lastworkcenter,RTRIM(jmpJobID) as jobid, 
				 Case when lastactive  >= DATEADD(day, -1, GETDATE()) THEN '<24' ELSE '>24' END AS hours,Cast(jmpProductionQuantity as INT) as qty ,jmpCustomerOrganizationID, jmpPartID as partnum, jmpPartShortDescription as description,firstclockin, ulmlIssues as comment ,CASE ulmlfincatagory WHEN 'Blue' THEN '#03a9f4' WHEN 'Green' THEN '#3ddc43' when 'Red' THEN '#ff0000' ELSE ulmlfincatagory END as ulmlfincatagory , FORMAT(lastactive,'yyyy-MM-dd HH:mm') as lastactive  , FORMAT( omdDeliveryDate ,'yyyy-MM-dd') as [deliverydate], case ulmlfincatagory when 'Red' THEN 1 WHEN 'Blue' THEN 2 WHEN 'Green' THEN 3 WHEN 'Yellow' THEN 4 END as Ordering, CASE WHEN lastactive < getdate()-1 THEN 'OVERDUE' ELSE '' END as status    from TimecardLines Left Outer Join Jobs on LMLJOBID = JMPJOBID Left Outer Join Organizations on JMPCUSTOMERORGANIZATIONID = CMOORGANIZATIONID left outer join SalesOrderJobLinks on JMPJOBID = OMJJOBID left outer join SalesOrderDeliveries on OMJSALESORDERID = OMDSALESORDERID and OMJSALESORDERLINEID = OMDSALESORDERLINEID and OMJSALESORDERDELIVERYID = OMDSALESORDERDELIVERYID outer apply( Select top 1 xaqUniqueID as unique_id, lmlWorkCenterID as lastworkcenter, lmlRoundedEndTime as blastactive , lmlUniqueID as uniquetimeid from TimecardLines Left Outer Join WorkCenterMachines on lmlWorkCenterID = xaqWorkCenterID and ULMLMACHINE = xaqDescription where lmlJobID = jmpjobid and lmlWorkCenterID in ('MILL1','MILL2','MILL3','MILL4','MILL5','DECO','TWIN','PLAS') order by lmlCreatedDate DESC )A outer APPLY( Select top 1 lmlRoundedStartTime as lastactive from timecardlines where lmljobid = jmpJobID and lmlWorkCenterID = 'FIN' order by lmlCreatedDate DESC)B   OUTER APPLY (Select top 1 lmeEmployeeName from TimecardLines Left Outer Join Employees on LMLEMPLOYEEID = LMEEMPLOYEEID where lmlJobID = jmpjobid and lmlWorkCenterID not IN ('FIN') order by lmlCreatedDate DESC)Z
				OUTER apply (Select top 1 lmeEmployeeName AS firstclockin from TimecardLines Left Outer Join Employees on LMLEMPLOYEEID = LMEEMPLOYEEID where lmlJobID = jmpjobid order by lmlCreatedDate asc)t	
				 where lmlRoundedEndTime is Null and lmlWorkCenterID = 'FIN' AND unique_id in ( ".$machine_ids.") ORDER by Ordering ASC, lastactive ASC");
                            
             //print $this->db->last_query();	exit;		
			              
                if ($query != false && $query->num_rows() > 0) {
                    $data = $query->result();
                }

            }

        } else {
            $dummy_data = $this->data->get_dummy_data();
            $data = array();
            for ($i=0;$i<rand(0,5);$i++) {
                array_push($data, $dummy_data);
            }
        }
                
        return $data;
    }
	function count_not_right_first_time($cell) {
				
		
		$query = $this->db->select('check_id')
			->join('machines', 'machine_unique=check_machine')
			->join('machine_cells', 'm_cell_id=machine_cell_id')
			->where('m_cell_name', $cell)
			->where('check_release_date',null)
			->get('quality_checks');
		

		return $query->num_rows();
	}
	function get_copq_count($cell) {
        
        $data = array();
               
        if (constant('USE_DB')) {

			$query = $this->m1db->query("Select  count(*) as num_counted from NonConformances Left Outer Join WorkCenterMachines on uqarWorkCenterID = xaqWorkCenterID and uqarWorkCenterMachine = xaqDescription Left Outer Join Employees l2 on QARREPORTEDBYEMPLOYEEID = l2.LMEEMPLOYEEID Left Outer Join NonConformanceCauses on QARNONCONFORMANCECAUSEID = QAUNONCONFORMANCECAUSEID Left Outer Join WorkCenters on uqarWorkCenterID = xawWorkCenterID Left Outer Join employees l1 on uxawManufacturingEng = l1.lmeEmployeeID where uqarWorkCenterID = '". $cell ."' and qarCorrectiveActionComplete <> -1 AND (uqarInOROut = 'OUT' OR uqarInOROut = 'IN' ) AND qarcreateddate > DATEADD(DAY, -2, CAST(GETDATE() AS date)) ");		
			//print $this->db->last_query();	exit;			
            $row = $query->row();
            return $row->num_counted;

        }

        return 0;
    }
	function get_cellmetricscount($type,$cell)
	{
		$data = array();
		$m1cellid = $this->get_m1_cells_ids($cell);
		
		/**Material issue to floor*/
		$m1cellid = $this->get_m1_cells_ids($cell);

		$material_all = $this->cellpit($m1cellid,3);
		
		if($material_all==false)		
		{
			return false;
		}
		else{
		
		$datafilter = array('RED','ORANGE');
		$temparray1 = array_filter( $material_all,function($resultvar) use($datafilter)  {
		return (in_array($resultvar->material,$datafilter));					
			});
		$material = array_values($temparray1); 
	
		
		$dataarray['material'] = count($material) ;
		
		/**Latejobs count**/
		$latejobs = $this->get_latejobs($m1cellid);
		
		$dataarray['latejobs'] = $latejobs; 
		
		
		
		
		/*Wip */
		$wipdata = $this->get_wip_machine_jobs($type,$cell);
		$hour = '>24';
		$count = array_filter( $wipdata,function($resultvar) use($hour)  {
			return $resultvar->hours == $hour;	
			
		});
		$dataarray['wip'] = count($count); 
		
		
		/**NRFT **/
		$dataarray['nrft'] = $this->count_not_right_first_time($cell);
		
		/*copq*/ 
		$dataarray['copq'] = $this->get_copq_count($m1cellid);
			
		$data = $dataarray;
		return $data;
		}	
		
		
	}
	function getmachinehours($cell)
	{
		
		$query = $this->db->query("SELECT CONCAT('WK',WEEK(metrics_created,0)) as week,metrics_created, metrics_machinehours,metrics_setuphours FROM metrics WHERE metrics_workcenterid = '".$cell."' and WEEKOFYEAR(metrics_created) >= WEEKOFYEAR(NOW())-5 ORDER BY metrics_created DESC LIMIT 5");
		//print $this->db->last_query();
		$row = $query->result();	
        return $row;
	}
	
	
	function get_cell_machines_pit($cell_name) {
        
        $ret = array();
        
                  
        $queryTypes = $this->db->order_by('m_type_name')->get('machine_types');
        
        foreach ($queryTypes->result() as $type) { 
        
            $queryCells = $this->db->where('m_cell_type_id',$type->m_type_id)
			->where('m_cell_name',$cell_name)
			->get('machine_cells');
            
            foreach ($queryCells->result() as $cell) {  
                if ($cell->m_cell_pit_show) {
                    
                    if (!isset($ret[$type->m_type_name]))
                       // $ret[$type->m_type_name] = array();
                   // $ret[$type->m_type_name][$cell->m_cell_name] = array();
                    
                    $queryMachines = $this->db->where('machine_cell_id',$cell->m_cell_id)->get('machines');
                    
                    foreach ($queryMachines->result() as $machine) {
                        $ret['{'.$machine->machine_unique.'}'] = $machine->machine_name; 
                    }
                    
                }
            }
        }
        
        return $ret;
    }
    function getmachinestatus($machines_array,$cell)
	{
		$m1cell = $this->get_m1_cells_ids($cell);
		 if (constant('USE_DB')) {
						
			$next_query = $this->cellpit($m1cell,6);
			if($next_query==false)		
			{
				return false;
				exit;
			}
			else{
			
//print $this->m1db->last_query(); 
				for ($i=1;$i<=6;$i++) {
				
					$temparray = array_filter( $next_query,function($resultvar) use ($i){
							return $resultvar->RowNo == $i;
						});
					$data['next_'.$i] = array_values($temparray);
					
					//print $this->m1db->last_query(); 
				}
			}
		 }
		 
		$newarray= array();		
		foreach($machines_array as $machine_id => $machine_name){
			foreach($data as $dataarray => $datavalue){
				if( empty($datavalue)){
					$newarray[$machine_id][$dataarray] = array();
				}
				else{	
					$temparray = array_filter($datavalue,function ($var) use ($machine_id) {
						
						return ("{".$var->machineid."}" == $machine_id);});
						
					$newarray[$machine_id][$dataarray] = array_values($temparray);
				}
			}
		}		
		return $newarray;
	}
	
	
	function cellpit($cell,$row)
	{
		$query = $this->m1db->query("WITH TOPSEVEN AS (
													  SELECT 
														CASE WHEN ujmpLatestStartDate < getdate() THEN CAST(
														  CAST(
															GETDATE() AS DATE
														  ) AS DATETIME
														) ELSE ujmpLatestStartDate END as ujmpLatestStartDate, 
														xaquniqueid as machineid, 
														CASE WHEN jmpOnHold = -1 
														OR uimpOnHold = -1 THEN 'btn-hold' WHEN jmpCustomerOrganizationID = 'BOM001' THEN bom_npi WHEN shippedbefore = 0 THEN 'btn-npi' ELSE 'btn-dark' END as cardclass, 
														jmpJobID as [jobid], 
														stockbin, 
														cmoName as [customer], 
														jmpPartID as [partnum], 
														RTRIM(jmpPartShortDescription) as description, 
														FORMAT(jmpProductionQuantity, '#') as [orderqty], 
														FORMAT(omdDeliveryDate, 'yyyy-MM-dd') as [deliverydate], 
														CASE WHEN uajIssuedToJob IS NOT NULL 
														OR imtPartTransactionID IS NOT NULL THEN 'GREEN' WHEN rmlReceiptID is not null 
														OR unjProcessedComplete = -1 
														OR jmmReceivedComplete = -1 THEN 'ORANGE' ELSE 'RED' END AS 'material', 
														programcomplete, 
														jmpScheduledStartDate, 
														ROW_NUMBER() over (
														  PARTITION BY jmoWorkCenterMachineID 
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
															CAST(uifPartFaiStatusID AS INT) DESC
														) G 
														LEFT outer join uNestingJob on ujmpNestingJobID = unjNestingJobID 
													  WHERE 
														jmpProductionComplete <> -1 
														and jmpClosed <> -1 
														and omdClosed <> -1 
														and jmpQuantityShipped = 0 
														and jmpQuantityCompleted = 0 
														and xaqWorkCenterID = '".$cell."' 
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
													  RowNo <= '".$row."'
													  
													");
													
	$error = $this->m1db->error();
		
	
	if (in_array("", $error))
	{
		$row = $query->result();
	}
	else{
		$row = false;
	}
	
													
	return  $row;
	}
}