<?php

class Planning_model extends CI_Model {
    
	
    function __construct(){
		parent::__construct();
		
		$this->m1db = $this->load->database('M1', TRUE);
	}
	function cell_machines($cell)
	{	
		$query = $this->db->query("SELECT machine_unique,machine_name,m_cell_m1name,case when m_cell_m1name IN ('TWIN','MILL3','MILL1','MILL5') then '5' when m_cell_m1name IN ('DECO','PLAS','MILL2','MILL4') THEN '4' ELSE '' END  AS totalmachine FROM machines LEFT OUTER JOIN machine_cells ON m_cell_id=machine_cell_id where m_cell_pit_show =1 and m_cell_m1name='".$cell."'");		
		
		$ret = array();
		$rows = (object)array();
		
		foreach($query->result() as $rows)
		{
			$query = $this->m1db->query("select  case when jmoworkcenterid='MILL3' then CAST(SUM(((omddeliveryquantity-omdquantityshipped)*jmoProductionStandard)/60) as DECIMAL(10,2)) 
				when jmoworkcenterid In ('MILL1','DECO','TWIN','MILL2','MILL4','MILL5') then CAST(SUM((((omddeliveryquantity-omdquantityshipped)*jmoProductionStandard)/60)+1) AS DECIMAL(10,2)) END as cycletime from Jobs 
									LEFT OUTER JOIN JobOperations ON jmpjobid=jmojobid
									LEFT OUTER JOIN Organizations ON cmoorganizationid=jmpCustomerOrganizationID
									LEFT OUTER JOIN workcentermachines ON jmoworkcenterid = xaqworkcenterid AND jmoworkcentermachineid = xaqworkcentermachineid
									LEFT OUTER JOIN SalesOrderJobLinks ON omjJobID = jmpjobid
									LEFT OUTER JOIN SalesOrderDeliveries ON omjSalesOrderID =omdSalesOrderID AND omjSalesOrderLineID = omdSalesOrderLineID AND omjSalesOrderDeliveryID = omdSalesOrderDeliveryID  
									where ujmpCurrentProdWeek <>'' and not exists (SELECT TOP 1 * FROM TimecardLines
									WHERE lmljobid = jmpjobid) 
									AND jmoworkcenterid='".$cell."' and xaquniqueid = '{".$rows->machine_unique."}'  and ujmpcurrentprodweek < 22
									and uomdCustomerDeliveryDate < DATEADD(wk,12,DATEADD(dd, 7-(DATEPART(dw, GETDATE())), GETDATE()) )  group by jmoworkcenterid");
									
			if(isset(($query->row())->cycletime))
				$rows->backloghrs = ($query->row())->cycletime;
			else
				$rows->backloghrs = 0;
			
			array_push($ret,$rows);
		}
		
		return $ret;
		
	}
	function unplanned_jobs($cell)
	{
		/* $query = $this->m1db->query("SELECT DISTINCT xaquniqueid,rtrim(cmoname) as Customer, jmoJobOperationID,
									rtrim(omlpartid)as PartID,CASE WHEN jmpjobid is not NULL THEN rtrim(jmpjobid) WHEN omdDeliveryType = 1 THEN 'Make To Order' WHEN omdDeliveryType = 2 THEN 'Pull From Stock' WHEN
									 omdDeliveryType = 4 THEN 'Kit Part' WHEN omdDeliveryType = 5 THEN 'Purchase to Order' ELSE '' END as jmpJobID ,rtrim(omlpartshortdescription) as PartDescription ,
									 (omddeliveryquantity-omdquantityshipped) AS 'remaining_Quantity',
									jmpscheduledstartdate ,uomdcustomerdeliverydate,
									workcenter, RTRIM(machine) AS MACHINE, 
									case when workcenter='MILL3' then ((omddeliveryquantity-omdquantityshipped)*cycletime)/60 when workcenter In ('MILL1','DECO','TWIN','MILL2','MILL4','MILL5') then (((omddeliveryquantity-omdquantityshipped)*cycletime)/60)+1 end as
cycletime,ujmporiginalprodweek,ujmpcurrentprodweek,
									jmmpartshortdescription,jmmpartid,
									CASE
									WHEN rmlreceiptid IS NOT NULL THEN 'Material recieved on ' + rmlreceiptdate
									WHEN pmlpurchaseorderid IS NOT NULL THEN 'Material PO:' + pmlpurchaseorderid + ' due on ' + pmlduedate
									WHEN jmmpurchaseorderid IS NOT NULL
									AND jmmpurchaseorderid <> '' THEN jmmpurchaseorderid
									ELSE 'No material ordered'
									END AS [MatStatus],
									rtrim(ujmpNestingJobID) as ujmpNestingJobID,
									omporderdate,
								

									CASE
									WHEN ujmpprogrammingrequired <> -1
									OR ujmpprogramcomplete = -1 THEN 'Program OK'
									ELSE 'Program Required'
									END AS program
									FROM salesorders
									LEFT OUTER JOIN salesorderlines
									ON omlsalesorderid = ompsalesorderid
									LEFT OUTER JOIN salesorderdeliveries
									ON omdsalesorderid = omlsalesorderid
									AND omdsalesorderlineid = omlsalesorderlineid
									LEFT OUTER JOIN parts
									ON imppartid = omlpartid
									LEFT OUTER JOIN
									(
									SELECT omjsalesorderid,
									omjsalesorderlineid,
									omjsalesorderdeliveryid,
									Max( omjsalesorderjoblinkid) AS thelinkid
									FROM salesorderjoblinks
									GROUP BY omjsalesorderid,
									omjsalesorderlineid,
									omjsalesorderdeliveryid) AS tcl
									ON omjsalesorderid = ompsalesorderid
									AND omjsalesorderlineid = omlsalesorderlineid
									AND omjsalesorderdeliveryid = omdsalesorderdeliveryid
									LEFT OUTER JOIN salesorderjoblinks AS omjx
									ON omjx.omjsalesorderid = omdsalesorderid
									AND omjx.omjsalesorderlineid = omdsalesorderlineid
									AND omjx.omjsalesorderdeliveryid = omdsalesorderdeliveryid
									AND omjx.omjsalesorderjoblinkid = thelinkid
									LEFT OUTER JOIN jobs
									ON jmpjobid = omjjobid
									LEFT OUTER JOIN jobmaterials
									ON jmmuniqueid =
									(
									SELECT TOP 1
									jmmuniqueid
									FROM jobmaterials
									WHERE jmmjobid = jmpjobid
									AND jmmjobassemblyid = 0
									ORDER BY jmmjobmaterialid DESC )
									LEFT OUTER JOIN
									(
									SELECT smljobid,
									Max( smlcreateddate) AS smpshipdate
									FROM shipmentlines
									GROUP BY smljobid) AS a
									ON smljobid = jmpjobid
									
									LEFT OUTER JOIN
									(
									SELECT pmljobid,
									Rtrim(Max( pmlpurchaseorderid )) AS pmlpurchaseorderid ,
									Format(Max( pmlduedate ),'dd/MM/yyyy') AS pmlduedate ,
									Max(rmlreceiptid) AS rmlreceiptid ,
									Format(Max(rmlreceiptdate),'dd/MM/yyyy') AS rmlreceiptdate
									FROM purchaseorderlines
									LEFT OUTER JOIN receiptlines
									ON rmlpurchaseorderid = pmlpurchaseorderid
									AND rmlpurchaseorderlineid = pmlpurchaseorderlineid
									WHERE pmljobtype = 1
									GROUP BY pmljobid ) AS c
									ON pmljobid = jmpjobid
									
									LEFT OUTER JOIN
									(
									SELECT jmojobid,xaquniqueid,jmoJobOperationID,
									Max( xaqdescription ) AS machine,
									MIN( xaqWorkCenterID ) AS workcenter,
									( jmoProductionStandard) as cycletime
									FROM joboperations
									LEFT OUTER JOIN workcentermachines
									ON jmoworkcenterid = xaqworkcenterid
									AND jmoworkcentermachineid = xaqworkcentermachineid
									WHERE jmojobassemblyid = 0
									AND jmoworkcenterid in ('MILL1','DECO','TWIN','MILL2','MILL3','MILL4','MILL5')
									GROUP BY jmojobid,xaquniqueid,jmoProductionStandard,jmoJobOperationID) AS e
									ON jmpjobid = jmojobid
									
									INNER JOIN organizations
									ON cmoorganizationid = ompcustomerorganizationid
								
									
									WHERE ( omdShippedcomplete <> -1
									)
									AND ( uomdCustomerDeliveryDate < DATEADD(wk,12,DATEADD(dd, 7-(DATEPART(dw, GETDATE())), GETDATE()) ))
									AND ompclosed <> -1
									AND omlclosed <> -1
									AND omdclosed <> -1and cmoName <> 'Takumi'
									and cmoName <> 'Takumi Treatment Line'
									AND ompcustomerpo NOT LIKE 'SH100%'
									AND (
									imppartgroupid <> 'GMRS'
									OR imppartgroupid IS NULL) and workcenter='".$cell."' AND NOT EXISTS(
									SELECT TOP 1
									*
									FROM timecardlines
									WHERE lmljobid IN
									(
									SELECT omjjobid
									FROM salesorderjoblinks
									WHERE omjsalesorderid = omdsalesorderid
									AND omjsalesorderlineid = omlsalesorderlineid)
									ORDER BY lmlcreateddate DESC )"); */
			
		$query = $this->m1db->query("SELECT DISTINCT xaquniqueid,rtrim(cmoname) as Customer, jmoJobOperationID,rtrim(omlpartid)as PartID,CASE WHEN jmpjobid is not NULL THEN rtrim(jmpjobid) WHEN omdDeliveryType = 1 THEN 'Make To Order' WHEN omdDeliveryType = 2 THEN 'Pull From Stock' WHEN omdDeliveryType = 4 THEN 'Kit Part' WHEN omdDeliveryType = 5 THEN 'Purchase to Order' ELSE '' END as jmpJobID ,
rtrim(omlpartshortdescription) as PartDescription,
(omddeliveryquantity-omdquantityshipped) AS 'remaining_Quantity',jmpscheduledstartdate ,uomdcustomerdeliverydate,workcenter,RTRIM(machine) AS MACHINE,case when workcenter='MILL3' THEN 
((omddeliveryquantity-omdquantityshipped)*cycletime)/60 when workcenter In ('MILL1','DECO','TWIN','MILL2','MILL4','MILL5') then (((omddeliveryquantity-omdquantityshipped)*cycletime)/60)+1 end as
cycletime,ujmporiginalprodweek,ujmpcurrentprodweek,jmmpartshortdescription,jmmpartid,
CASE	WHEN rmlreceiptid IS NOT NULL THEN 'Material recieved on ' + rmlreceiptdate
		WHEN pmlpurchaseorderid IS NOT NULL THEN 'Material PO:' + pmlpurchaseorderid + ' due on ' + pmlduedate
		WHEN jmmpurchaseorderid IS NOT NULL
		AND jmmpurchaseorderid <> '' THEN jmmpurchaseorderid
		ELSE 'No material ordered' END AS [MatStatus],
rtrim(ujmpNestingJobID) as ujmpNestingJobID,omporderdate,
CASE WHEN uajIssuedToJob IS NOT NULL OR imtPartTransactionID IS NOT NULL THEN 'GREEN' WHEN rmlReceiptID is not null OR unjProcessedComplete = -1 OR jmmReceivedComplete = -1 THEN 'ORANGE' ELSE 'RED' END AS 'material',
CASE WHEN ujmpprogrammingrequired <> -1 OR ujmpprogramcomplete = -1 THEN 'Program OK' ELSE 'Program Required' END AS program
FROM salesorders
LEFT OUTER JOIN salesorderlines ON omlsalesorderid = ompsalesorderid
LEFT OUTER JOIN salesorderdeliveries ON omdsalesorderid = omlsalesorderid AND omdsalesorderlineid = omlsalesorderlineid
LEFT OUTER JOIN parts ON imppartid = omlpartid
LEFT OUTER JOIN (SELECT omjsalesorderid,omjsalesorderlineid,omjsalesorderdeliveryid,Max( omjsalesorderjoblinkid) AS thelinkid FROM salesorderjoblinks GROUP BY omjsalesorderid, omjsalesorderlineid,omjsalesorderdeliveryid) AS tcl
					  ON omjsalesorderid = ompsalesorderid AND omjsalesorderlineid = omlsalesorderlineid AND omjsalesorderdeliveryid = omdsalesorderdeliveryid
LEFT OUTER JOIN salesorderjoblinks AS omjx ON omjx.omjsalesorderid = omdsalesorderid AND omjx.omjsalesorderlineid = omdsalesorderlineid AND omjx.omjsalesorderdeliveryid = omdsalesorderdeliveryid
					AND omjx.omjsalesorderjoblinkid = thelinkid	
LEFT OUTER JOIN jobs ON jmpjobid = omjjobid 
INNER JOIN organizations ON cmoorganizationid = ompcustomerorganizationid
left outer join 
			uNestingJob on ujmpNestingJobID = unjNestingJobID
outer apply (Select top 1 imttransactiondate,imtPartTransactionID from PartTransactions where imtTransactionType =2 and imtInventoryQuantityReceived > 0 and imtJobID = jmpJobID )A 
outer apply (SELECT TOP 1 max( uajPartBinID ) as stockBin, max(uajIssuedToJob) as uajIssuedToJob,uajLotNumberID from uLotNumJobs where uajJobID = jmpJobID GROUP BY uajLotNumberID)E
outer apply (Select top 1 Format((rmlreceiptdate),'dd/MM/yyyy') AS rmlreceiptdate,rmlReceiptID from ReceiptLines where rmlJobID = jmpJobID and rmlJobAssemblyID = 0 )B 
outer apply (Select top 1 pmlpurchaseorderid,jmmpartshortdescription,jmmReceivedComplete,jmmQuantityPerAssembly,jmmPartID, jmmPurchaseOrderID, Format(pmlduedate,'dd/MM/yyyy') as pmlDueDate from jobMaterials Left Outer Join PurchaseOrderLines on PMLJOBID = JMMJOBID and pmlPurchaseOrderID = jmmPurchaseOrderID where jmmJobID = jmpJobID and jmmJobAssemblyID = 0 )C 

OUTER apply (SELECT jmojobid,xaquniqueid,jmoJobOperationID,( xaqdescription ) AS machine,( xaqWorkCenterID ) AS workcenter,( jmoProductionStandard) as cycletime FROM joboperations
								LEFT OUTER JOIN workcentermachines ON jmoworkcenterid = xaqworkcenterid AND jmoworkcentermachineid = xaqworkcentermachineid WHERE jmojobassemblyid = 0 AND 
								jmoworkcenterid in ('MILL1','DECO','TWIN','MILL2','MILL3','MILL4','MILL5') and jmpjobid = jmojobid
								) AS u
WHERE ( omdShippedcomplete <> -1	) AND ( uomdCustomerDeliveryDate < DATEADD(wk,22,DATEADD(dd, 7-(DATEPART(dw, GETDATE())), GETDATE()) )) AND ompclosed <> -1 AND omlclosed <> -1
AND omdclosed <> -1   and cmoName <> 'Takumi' and cmoName <> 'Takumi Treatment Line' AND ompcustomerpo NOT LIKE 'SH100%' AND (imppartgroupid <> 'GMRS' OR imppartgroupid IS NULL) and workcenter='".$cell."' 
AND NOT EXISTS( SELECT TOP 1 * FROM timecardlines WHERE lmljobid IN(SELECT omjjobid	FROM salesorderjoblinks	WHERE omjsalesorderid = omdsalesorderid AND omjsalesorderlineid = omlsalesorderlineid)
					ORDER BY lmlcreateddate DESC ) ");
			return $query->result();
	}
	function machines_weekdata()
	{
		
		$data = array();
		$qry_res = $this->db->query('Call machine_actualhours()');
		$data['actual'] = $qry_res->result();
		
		$qry_res->next_result();
		$qry_res->free_result();
		
		$query = $this->db->query("select * from machine_idealhours");
		
		$data['idealhours'] = $query->result();
		
		$query = $this->m1db->query("SELECT * FROM (SELECT xaquniqueid,concat('w',ujmpCurrentProdWeek) AS week,
		sum(case when jmoworkcenterid='MILL3' then ((omddeliveryquantity-omdquantityshipped)*jmoProductionStandard)/60 when jmoworkcenterid In ('MILL1','DECO','TWIN','MILL2','MILL4','MILL5') then (((omddeliveryquantity-omdquantityshipped)*jmoProductionStandard)/60)+1 end) AS cycletime 
		FROM Jobs 
left outer join joboperations on jmpjobid=jmojobid 
LEFT OUTER JOIN WorkCenterMachines on xaqWorkCenterMachineID = jmoWorkCenterMachineID AND jmoworkcenterid=xaqWorkCenterID
LEFT OUTER JOIN SalesOrderJobLinks ON omjJobID = jmpjobid
LEFT OUTER JOIN SalesOrderDeliveries ON omjSalesOrderID =omdSalesOrderID AND omjSalesOrderLineID = omdSalesOrderLineID AND omjSalesOrderDeliveryID = omdSalesOrderDeliveryID  
WHERE ujmpCurrentProdWeek > '18' AND jmoworkcenterid in ('MILL1','DECO','TWIN','MILL2','MILL3','MILL4','MILL5') AND not exists (Select * from TimecardLines where lmlJobID = jmpJobID and lmlJobOperationID = jmoJobOperationID) and uomdCustomerDeliveryDate < DATEADD(wk,12,DATEADD(dd, 7-(DATEPART(dw, GETDATE())), GETDATE()) ) GROUP BY xaquniqueid,ujmpCurrentProdWeek)
x pivot(MAX(cycletime) FOR week IN ([w".date('W')."],[w".(date('W')+1)."],[w".(date('W')+2)."],[w".(date('W')+3)."],[w".(date('W')+4)."],[w".(date('W')+5)."],[w".(date('W')+6)."],[w".(date('W')+7)."]))as piv");
		

		$data['hourscommit'] = $query->result();	
		return $data;		
		
	}
	function machines($cell)
	{		
			
		$query = $this->db->query("SELECT substr(acthr_weekno,2) as week,machine_unique,machine_name,m_cell_m1name,case when m_cell_m1name IN ('TWIN','MILL3','MILL1','MILL5') then '5' when m_cell_m1name IN ('DECO','PLAS','MILL2','MILL4') THEN '4' ELSE '' END  AS totalmachine FROM machines 
LEFT OUTER JOIN machine_actualhours ON acthr_machineid = machine_unique
LEFT OUTER JOIN machine_cells ON m_cell_id=machine_cell_id where m_cell_pit_show =1 and m_cell_m1name='".$cell."'");		
				
		return $query->result();
		
		
	}
	function setactualvalue()
	{
		$data = array('acthr_weekno'=>'w'.$_POST['week'],
				'acthr_year'=>date('Y'),
				'acthr_value'=>$_POST['actualvalue'],
				'acthr_machineid'=>$_POST['machineid']);
		$query = $this->db->insert('machine_actualhours',$data);
		return true;
	}
	function planjobssubmit()
	{
		for($i=0;$i<=count($_POST['data'])-1;$i++)
		{	
			foreach($_POST['data'][$i] as $k=>$v)
			{
				$jobid = explode("/",$v); 
			}
			$query = $this->m1db->set('ujmpCurrentProdWeek',$_POST['week'])
								->where('jmpjobid',$jobid[0])
								->update('jobs');
		}
		
		if ($this->m1db->affected_rows()>0)
			return 1;
		else
			return 0;
	}
	function gethrscommitweekjob()
	{
		if($_POST['week']!=0)
			$week = "AND ujmpcurrentprodweek = ".$_POST['week']."";
		else
			$week = "AND ujmpcurrentprodweek < ".date('W')."";
		
		if($_POST['machine']!='')
			$machine = "and  xaquniqueid = '".$_POST['machine']."'";
		else
			$machine = '';
		
		
		$query = $this->m1db->query("select jmojobid  as jobid,jmppartid as partid,jmppartshortdescription as partdesc,cmoname as customer,ujmpCurrentProdWeek as week,xaqdescription as machine,
									case when jmoworkcenterid='MILL3' then CAST(((omddeliveryquantity-omdquantityshipped)*jmoProductionStandard)/60 as DECIMAL(10,2)) when jmoworkcenterid In ('MILL1','DECO','TWIN','MILL2','MILL4','MILL5') then CAST((((omddeliveryquantity-omdquantityshipped)*jmoProductionStandard)/60)+1 AS DECIMAL(10,2)) END AS cycletime, 
									jmmPartID as matid,rtrim(ujmpNestingJobID) as nestingid,
									CASE	WHEN rmlreceiptid IS NOT NULL THEN 'Material recieved on ' + rmlreceiptdate
											WHEN pmlpurchaseorderid IS NOT NULL THEN 'Material PO:' + pmlpurchaseorderid + ' due on ' + pmlduedate
											WHEN jmmpurchaseorderid IS NOT NULL
											AND jmmpurchaseorderid <> '' THEN jmmpurchaseorderid
											ELSE 'No material ordered' END AS [MatStatus] 
									from Jobs 
									LEFT OUTER JOIN JobOperations ON jmpjobid=jmojobid
									LEFT OUTER JOIN Organizations ON cmoorganizationid=jmpCustomerOrganizationID
									LEFT OUTER JOIN workcentermachines ON jmoworkcenterid = xaqworkcenterid AND jmoworkcentermachineid = xaqworkcentermachineid
									LEFT OUTER JOIN SalesOrderJobLinks ON omjJobID = jmpjobid
									LEFT OUTER JOIN SalesOrderDeliveries ON omjSalesOrderID =omdSalesOrderID AND omjSalesOrderLineID = omdSalesOrderLineID AND omjSalesOrderDeliveryID = omdSalesOrderDeliveryID  
									outer apply (Select top 1 Format((rmlreceiptdate),'dd/MM/yyyy') AS rmlreceiptdate,rmlReceiptID from ReceiptLines where rmlJobID = jmpJobID and rmlJobAssemblyID = 0 )B 
									outer apply (Select top 1 pmlpurchaseorderid,jmmpartshortdescription,jmmReceivedComplete,jmmQuantityPerAssembly,jmmPartID, jmmPurchaseOrderID, Format(pmlduedate,'dd/MM/yyyy') as pmlDueDate from jobMaterials 
									Left Outer Join PurchaseOrderLines on PMLJOBID = JMMJOBID and pmlPurchaseOrderID = jmmPurchaseOrderID where jmmJobID = jmpJobID and jmmJobAssemblyID = 0 )C 
									where ujmpCurrentProdWeek <>'' and not exists (SELECT TOP 1 * FROM TimecardLines
									WHERE lmljobid = jmpjobid) 
									AND jmoworkcenterid='".$_POST['cell']."' ".$week." ".$machine."
									and uomdCustomerDeliveryDate < DATEADD(wk,12,DATEADD(dd, 7-(DATEPART(dw, GETDATE())), GETDATE()) )  ORDER BY ujmpCurrentProdWeek desc");	
									
	$row =$query->result();
	
	
	return $row;
	
	}
	function jobs_changeweek()
	{
		$jobs = $_POST['jobs'];	
		
		foreach($jobs as $k=>$v)
		{
			$this->m1db->set('ujmpCurrentProdWeek',$_POST['week']);	
			$this->m1db->where('jmpjobid', explode(" ",$v)[0]); 			
			$this->m1db->update('jobs');
		}
		if($this->m1db->affected_rows())
			return true;
		else
			return false;
		
	}
	
	function getmachines()
	{
		$query = $this->m1db->select('xacShortDescription as processdesc,xawProcessID as processid,xaquniqueid,xaqWorkCenterMachineID,xaqDescription,xaqWorkCenterMachineID as machineid')
							->join('WorkCenters','xawWorkCenterID = xaqWorkCenterID','left')
							->join('Processes','xacProcessID=xawProcessID','left')
							->where('xaqWorkCenterID',$_POST['workcenterid'])
							->get('WorkCenterMachines');
		
		return $query->result();
	}
	function updatemachine()
	{
		$data = $_POST['ids'];
		
		foreach($data as $k=>$v)
		{
			foreach($v as $ks=>$m)
			{
				
				$a = explode("/",$m);			
				$jobid = $a[0];			
				$joboperationid =  $a[1];
				
				$this->m1db->set('jmoWorkCenterID', $_POST['workcenter']);	
				$this->m1db->set('jmoProcessID', $_POST['processid']);	
				$this->m1db->set('jmoProcessShortDescription',$_POST['processdesc']);	
				$this->m1db->set('jmoworkcentermachineid', $_POST['machine']);	
				$this->m1db->where('jmojobid', $jobid);	
				$this->m1db->where('jmoJobOperationID', $joboperationid);	
				$this->m1db->update('joboperations');
			}
		}			
		if($this->m1db->affected_rows())
			return true;
		else
			return false;
	}
}