<?php

class Scheduler_model extends CI_Model {
    
	
    function __construct(){
		parent::__construct();
		
		$this->m1db = $this->load->database('M1', TRUE);
	}	
	
	function unschedule_jobs($cell,$machineid,$material_type,$materialstatus)
	{
		if(($machineid=='all')|| ($machineid=='')){$machine_id='';}
		else{$machine_id="AND xaqUniqueID='".$machineid."'";}
		
		if($material_type=='all'){$materialtype='';}
		else{$materialtype="AND jmmPartID='".str_replace("'", '"', $material_type)."'";}
			
		$query = $this->m1db->query("Select rtrim(ujmpCurrentProdWeek) AS proweekno,jmpJobID as jobid,jmpPartID as partid,jmpPartShortDescription AS partdesc,cmoName AS customer,jmmPartID AS materialid,cast(( ujmmLength* ujmmWidth * jmmEstimatedQuantity )/3456 as decimal(10,3)) AS sheetrequired,xaqUniqueID,CASE WHEN uajIssuedToJob IS NOT NULL OR imtPartTransactionID IS NOT NULL THEN 'GREEN' WHEN rmlReceiptID is not null OR unjProcessedComplete = -1 OR jmmReceivedComplete = -1 THEN 'ORANGE' ELSE 'RED' END AS 'Material_status',format(jmpScheduledStartDate,'yyyy-MM-dd') as schedulestart, jmoJobOperationID as operationid, jmpProductionQuantity,cast((jmpProductionQuantity* jmoProductionStandard)/60 as decimal(10,3)) as Estimatedprodhrs, xaqDescription,CASE WHEN ujmpbucketweek IS NOT NULL THEN 'op2' ELSE '' END AS checkoperation,CASE WHEN ujmpnestingjobid <>'' THEN ujmpnestingjobid WHEN ujmpnestingjobid = '' THEN FORMAT(pmlduedate,'yyyy-MM-dd')   ELSE 'nonesting' END AS materialdue  from Jobs 
		LEFT outer join partrevisions on jmpPartID = imrPartID and jmpPartRevisionID = imrPartRevisionID 
		Left Outer Join JobOperations on JMOJOBID = JMPJOBID and JMOJOBASSEMBLYID = 0 
		left outer join workcentermachines on xaqWorkCenterID = jmoWorkCenterID and xaqWorkcenterMachineID = jmoWorkCenterMachineID  
		LEFT OUTER JOIN Organizations ON jmpCustomerOrganizationID=cmoOrganizationID 
		left outer join uNestingJob on ujmpNestingJobID = unjNestingJobID
		LEFT OUTER JOIN SalesOrderJobLinks ON omjJobID = jmpjobid
		LEFT OUTER JOIN SalesOrderDeliveries ON omjSalesOrderID =omdSalesOrderID AND omjSalesOrderLineID = omdSalesOrderLineID AND omjSalesOrderDeliveryID = omdSalesOrderDeliveryID 
		outer apply (Select top 1 imttransactiondate,imtPartTransactionID from PartTransactions where imtTransactionType =2 and imtInventoryQuantityReceived > 0 and imtJobID = jmpJobID )z 
		outer apply(Select top 1 jmmReceivedComplete ,jmmPartID , jmmPartShortDescription , rmlReceiptID, ujmmLength, ujmmWidth , jmmEstimatedQuantity from jobmaterials Left Outer Join ReceiptLines on RMLJOBID = JMMJOBID and RMLJOBASSEMBLYID = JMMJOBASSEMBLYID and RMLJOBMATERIALID = JMMJOBMATERIALID where jmmJobID = jmpJobID and jmmJobAssemblyID = 0 order by jmmJobMaterialID DESC  )A 
		outer apply (Select max(uajIssuedToJob) as uajIssuedToJob from uLotNumJobs where uajJobID = jmpJobID )C  
		OUTER apply(SELECT TOP 1 pmlduedate from PurchaseOrderLines WHERE pmlJobID = jmpJobID ORDER BY pmlcreateddate desc )D
		where ujmobucketweek is null and jmpProductionComplete <> -1 and jmoQuantityComplete = 0 and jmoProductionComplete <> -1 and jmpQuantityShipped = 0 AND 
		not EXISTS (Select pmlSalesOrderID from PurchaseOrderLines where pmlJobID = jmpJobID and pmlJobType = 2) AND 
		not exists (Select lmltimecardid from timecardlines where lmljobid = jmojobid AND lmlJobOperationID = jmoJobOperationID) AND 
		exists (Select omjjobid from SalesOrderJobLinks left outer join SalesOrderDeliveries on OMDSALESORDERID = OMJSALESORDERID and OMDSALESORDERLINEID = OMJSALESORDERLINEID Left Outer Join SalesOrderLines on OMDSALESORDERID = OMLSALESORDERID and OMDSALESORDERLINEID = OMLSALESORDERLINEID Left Outer Join SalesOrders on OMDSALESORDERID = OMPSALESORDERID 
		where omdShippedComplete <> -1 and omdClosed <> -1 and omlClosed <> -1 and ompClosed <> -1 and omjJobID = jmpjobid   ) 
		AND ( uomdCustomerDeliveryDate < DATEADD(wk,22,DATEADD(dd, 7-(DATEPART(dw, GETDATE())), GETDATE()) )) 
		and jmoWorkCenterID = '".$cell."' $machine_id  $materialtype");
		//AND ( uomdCustomerDeliveryDate < DATEADD(wk,12,DATEADD(dd, 7-(DATEPART(dw, GETDATE())), GETDATE()) ))
		//print $this->m1db->last_query();
		if($materialstatus!='all'){
		$issue = $materialstatus;
				$temparray = array_filter( $query->result(),function($resultvar) use($issue)  {
					return $resultvar->Material_status == $issue;					
				});
		$row = array_values($temparray);
		
		}
		else
		{
			$row = $query->result();
		}
		$ret = array();
		$rows = (object)array();
		foreach($row as $rows)
		{
			$query = $this->db->query("SELECT machine_name FROM machines WHERE machine_unique='".$rows->xaqUniqueID."'");
			$suggest = $query->row();			
			if(isset($suggest->machine_name))
			{
			$rows->machine = $suggest->machine_name;
			}
			else{
				$rows->machine = '';
			}
			
			array_push($ret,$rows);
		}	
		return $ret;		
	}

	/* function material_startweekdate()
	{
		
		$query = $this->m1db->query("Select imrPartID as material, CASE WHEN (SheetsOnHand - sheetsused) /CASE uimcUoM WHEN 'MM' THEN 87782 ELSE 3456 END <0 THEN 0 ELSE (SheetsOnHand - sheetsused) /CASE uimcUoM WHEN 'MM' THEN 87782 ELSE 3456 END END as SheetsOnHand, uimcUoM FROM (
Select imrPartID , sum(imrSheetSizeX * imrSheetSizeY * ablQuantityOnHand) as SheetsOnHand, IMPPARTCLASSID from Parts
Left Outer Join PartRevisions on IMRPARTID = IMPPARTID Left Outer Join LotNumbers on ABLPARTID = IMRPARTID AND
ABLPARTREVISIONID = IMRPARTREVISIONID
where uimrStockType = 'P' and ablPartBinID like 'S%'
Group By imrPartID ,IMPPARTCLASSID)a
Left Outer Join PartClasses on IMPPARTCLASSID = IMCPARTCLASSID
OUTER apply( Select CAST(SUM(ujmmLength * ujmmWidth * jmmEstimatedQuantity) as float) as sheetsused from jobs Left Outer Join JobMaterials on JMMJOBID = JMPJOBID and JMMJOBASSEMBLYID = 0 where jmmPartID = imrPartID and not exists (Select * from parttransactions where imtJobid = jmpJobID and imrPartID = jmpPartID ) and exists (Select * from JobOperations WHERE JMOJOBID = JMPJOBID and JMOJOBASSEMBLYID = 0 and ujmobucketweek <= DATEADD(dd, 0 - (1 + 5 + DATEPART(dw, GETDATE())) % 7, GETDATE()) ) )B
ORDER BY imrPartID");
		return $query->result();
		
	} */
	
	
	function add_jobsto_schedule()
	{
		
		$jobids=$_POST['jobids'];
		$job_id = explode(',', $jobids);		

		$machine_id=$_POST['machine_id'];
		$week=$_POST['week'];
				
		for($i=0;$i<=(count($job_id)-1);$i++)
		{
			$this->m1db->set('ujmpbucketweek', date('Y-m-d H:i:s'));
			
			$this->m1db->where('jmpJobID', $job_id[$i]);
			$this->m1db->update('Jobs');
			
			//print $this->m1db->last_query();
		}
		
		if ($this->m1db->affected_rows()>0)
			return 1;
		else
			return 0;
	}			
	
	function schedulejobs_total()
	{
		$machine_id = $_POST['machine_id'];
		$week = $_POST['week'];
		if($week==1){ $week=date("W"); }else if($week==2){ $week = date("W")+1; } else if($week==3){ $week = date("W")+2; } 
		$dto = new DateTime();
		$dto->setISODate(2021, $week);
		$weekstartdate = $dto->format('Y-m-d');
		$dto->modify('+6 days');
		$weekenddate = $dto->format('Y-m-d');
		
		$query = $this->m1db->query("select count(jmpjobid) as total,xaqUniqueID from Jobs
LEFT OUTER JOIN  JobOperations ON jmpjobid=jmojobid
LEFT OUTER JOIN WorkCenterMachines ON jmoWorkCenterMachineID=xaqWorkCenterMachineID and jmoWorkCenterID=xaqWorkCenterID
 where ujmpbucketweek>='".$weekstartdate."' and ujmpbucketweek<='".$weekenddate."' AND xaqUniqueID='{".$machine_id."}' GROUP BY xaqUniqueID");
		
		return $query->row();
	}
	
	public function getscheduledhours()
	{
		$query = $this->m1db->query("Select SUBSTRING(CONVERT(nvarchar(50),xaqUniqueID ),1,36) as machine_unique ,
SUM(CASE WHEN jmoStartDate < getdate() THEN jmoProductionStandard * (CASE WHEN (jmpProductionQuantity - COALESCE(qtycomplete,0))< 0 then 0 ELSE jmpProductionQuantity - COALESCE(qtycomplete,0) END ) ELSE 0 END)/60 as latehours,
SUM(CASE WHEN YEAR(jmoStartDate) = YEAR(getdate()) and DATEPART(ISO_WEEK, jmoStartDate) = DATEPART(ISO_WEEK, GETDATE()) THEN jmoProductionStandard * (CASE WHEN (jmpProductionQuantity - COALESCE(qtycomplete,0))< 0 then 0 ELSE jmpProductionQuantity - COALESCE(qtycomplete,0) END ) ELSE 0 END)/60 as cwhours,
SUM(CASE WHEN jmoStartDate > getdate() THEN jmoProductionStandard * (CASE WHEN (jmpProductionQuantity - COALESCE(qtycomplete,0))< 0 then 0 ELSE jmpProductionQuantity - COALESCE(qtycomplete,0) END ) ELSE 0 END)/60 as futurehours,
CASE WHEN ujmobucketweek < getdate()-7 THEN CONCAT(YEAR(GETDATE()), '-', RIGHT(STUFF(DATEPART(ISO_WEEK, GETDATE()),1,0,'0'),2)) ELSE CONCAT(YEAR(ujmobucketweek), '-', RIGHT(STUFF(DATEPART(ISO_WEEK, ujmobucketweek),1,0,'0'),2)) END AS bucketweek
from jobs
Left Outer Join JobOperations on JMOJOBID = JMPJOBID and JMOJOBASSEMBLYID = 0
Left Outer Join WorkCenterMachines on JMOWORKCENTERID = XAQWORKCENTERID and JMOWORKCENTERMACHINEID = XAQWORKCENTERMACHINEID
outer apply (Select SUM( lmlGoodQuantity ) as qtycomplete from TimecardLines where lmlJobID = jmpJobID and lmlJobAssemblyID = 0 and lmlJobOperationID = jmoJobOperationID )A
Where jmoProductionComplete <> -1 and jmoWorkCenterMachineID <> 0 and ujmobucketweek < DATEADD(wk,3,getdate()) GROUP BY xaqUniqueID, CASE WHEN ujmobucketweek < getdate()-7 THEN CONCAT(YEAR(GETDATE()), '-', RIGHT(STUFF(DATEPART(ISO_WEEK, GETDATE()),1,0,'0'),2)) ELSE CONCAT(YEAR(ujmobucketweek), '-', RIGHT(STUFF(DATEPART(ISO_WEEK, ujmobucketweek),1,0,'0'),2)) END");

	return $query->result();
	}
	
	function scheduledhours_cell()
	{
		$query = $this->m1db->query("Select jmoWorkCenterID as workcenter,
SUM(CASE WHEN ujmpbucketweek IS NULL THEN jmoProductionStandard * (CASE WHEN (jmpProductionQuantity - COALESCE(qtycomplete,0))< 0 then 0 ELSE (jmpProductionQuantity - COALESCE(qtycomplete,0)) END ) ELSE 0 END)/60 as unscheduledhrs,
SUM(CASE WHEN ujmpbucketweek IS NOT NULL THEN jmoProductionStandard * (CASE WHEN (jmpProductionQuantity - COALESCE(qtycomplete,0))< 0 then 0 ELSE (jmpProductionQuantity - COALESCE(qtycomplete,0)) END ) ELSE 0 END)/60 as scheduledhrs,
SUM( jmoProductionStandard * CASE WHEN (jmpProductionQuantity - COALESCE(qtycomplete,0))< 0 then 0 ELSE (jmpProductionQuantity - COALESCE(qtycomplete,0)) END)/60 as totalhours,
CASE WHEN jmoStartDate < getdate()-5 THEN 'BL' WHEN jmoStartDate BETWEEN getdate()-5 and getdate() THEN CONCAT(YEAR(GETDATE()), '-', RIGHT(STUFF(DATEPART(ISO_WEEK, GETDATE()),1,0,'0'),2)) ELSE CONCAT(YEAR( jmoStartDate ), '-', RIGHT(STUFF(DATEPART(ISO_WEEK, jmoStartDate),1,0,'0'),2)) END AS bucketweek
from jobs
Left Outer Join JobOperations on JMOJOBID = JMPJOBID and JMOJOBASSEMBLYID = 0
Left Outer Join WorkCenters on JMOWORKCENTERID = XAWWORKCENTERID
outer apply (Select SUM( lmlGoodQuantity ) as qtycomplete from TimecardLines where lmlJobID = jmpJobID and lmlJobAssemblyID = 0 and lmlJobOperationID = jmoJobOperationID )A
Where jmpProductionComplete <> -1 and jmpClosed <> -1 and jmoProductionComplete <> -1 and xawInactive <> -1 and jmoWorkCenterID in ('TWIN', 'DECO','WATER','MILL1','MILL2','MILL3','MILL4','MILL5','WIRES') and jmoStartDate < DATEADD(wk,4,getdate())
GROUP BY jmoWorkCenterID, CASE WHEN jmoStartDate < getdate()-5 THEN 'BL' WHEN jmoStartDate BETWEEN getdate()-5 and getdate() THEN CONCAT(YEAR(GETDATE()), '-', RIGHT(STUFF(DATEPART(ISO_WEEK, GETDATE()),1,0,'0'),2)) ELSE CONCAT(YEAR( jmoStartDate ), '-', RIGHT(STUFF(DATEPART(ISO_WEEK, jmoStartDate),1,0,'0'),2)) END");

	return $query->result();
	}
	function open_jobcard()
	{
		$data =array();
		$jobid = $_POST['jobid'];
		$jobdata = $this->m1db->query("SELECT jmpjobid AS jobid,cmoname AS customer,jmppartid AS partid,jmppartshortdescription AS partdesc,format(jmporderquantity,'#.#') AS orderqty,FORMAT( omdDeliveryDate ,'dd/MM/yyyy') AS deliverydate FROM Jobs  Left Outer Join SalesOrderJobLinks on JMPJOBID = OMJJOBID 
Left Outer Join SalesOrderDeliveries on OMJSALESORDERID = OMDSALESORDERID and OMJSALESORDERLINEID = OMDSALESORDERLINEID AND omdSalesOrderDeliveryID = CASE omjSalesOrderDeliveryID WHEN 0 THEN 1 ELSE 
omjSalesOrderDeliveryID END and OMJSALESORDERDELIVERYID = OMDSALESORDERDELIVERYID 
Left Outer Join SalesOrderLines on OMJSALESORDERID = OMLSALESORDERID and OMJSALESORDERLINEID = OMLSALESORDERLINEID 
Left Outer Join SalesOrders on OMJSALESORDERID = OMPSALESORDERID 
left outer join organizations on jmpCustomerOrganizationID=cmoOrganizationID WHERE jmpjobid='".$jobid."'");
		$data =  $jobdata->row_array(); 
		$data['jobdata'] = $jobdata->row_array(); 
		
		$joboperationdata = $this->m1db->query("SELECT jmpjobid AS jobid,jmojobassemblyid AS assemblyid,jmojoboperationid AS oprationid,jmoprocessshortdescription AS processdesc,format(jmoProductionStandard,'#.#') AS cycletime,jmoworkcentermachineid,jmoworkcenterid AS workcenterid, 
jmoestimatedproductionhours,omdsalesorderid,xaqUniqueID,xaqDescription,jmouniqueid,case when jmoworkcenterid IN ('MILL1','MILL2','MILL3','MILL4','MILL5','TWIN','DECO','PLAS') THEN '1' ELSE '' end as operation FROM Jobs left outer join joboperations on jmpjobid=jmojobid 
left outer join organizations on jmpCustomerOrganizationID=cmoOrganizationID 
Left Outer Join SalesOrderJobLinks on JMPJOBID = OMJJOBID 
Left Outer Join SalesOrderDeliveries on OMJSALESORDERID = OMDSALESORDERID and OMJSALESORDERLINEID = OMDSALESORDERLINEID AND omdSalesOrderDeliveryID = CASE omjSalesOrderDeliveryID WHEN 0 THEN 1 ELSE 
omjSalesOrderDeliveryID END and OMJSALESORDERDELIVERYID = OMDSALESORDERDELIVERYID 
Left Outer Join SalesOrderLines on OMJSALESORDERID = OMLSALESORDERID and OMJSALESORDERLINEID = OMLSALESORDERLINEID 
Left Outer Join SalesOrders on OMJSALESORDERID = OMPSALESORDERID
LEFT OUTER JOIN WorkCenterMachines ON xaqWorkCenterMachineID=jmoworkcentermachineid AND xaqWorkCenterID=jmoworkcenterid
WHERE jmpjobid='".$jobid."'");

		$ret = array();
		$row = (object)array();
		foreach($joboperationdata->result() as $row)
		{
			$query = $this->db->query("SELECT machine_name FROM machines WHERE machine_unique='".$row->xaqUniqueID."'");
			$suggest = $query->row();			
			if(isset($suggest->machine_name))
			{
			$row->machine = $suggest->machine_name;
			$row->uniqueid = $row->xaqUniqueID;
			}
			else{
				$row->machine = '';
			}
			
			array_push($ret,$row);
		}		

		$data['joboperationdata'] = $ret;
			
		$jobmaterialdata = $this->m1db->query("Select jmmPartID as materialid,jmmPartShortDescription,cast(( ujmmLength* ujmmWidth * jmmEstimatedQuantity )/3456 as decimal(10,3)) AS sheetrequired, jmmPartRevisionID, jmmEstimatedQuantity ,ujmpNestingJobID as nestingid, unjProcessedComplete, jmmPurchaseOrderID as POI , FORMAT(pmlDueDate,'dd/MM/yyyy') as duedate, FORMAT(rmlReceiptDate,'dd/MM/yyyy') as receiptdate, imtTransactionDate
from JobMaterials Left Outer Join Jobs on JMMJOBID = JMPJOBID and JMMJOBASSEMBLYID = 0 Left Outer Join uNestingJob on ujmpNestingJobID = unjNestingJobID
OUTER APPLY(Select TOP 1 * FROM PurchaseOrderLines Left Outer Join PurchaseOrders on PMLPURCHASEORDERID = PMPPURCHASEORDERID Left Outer Join ReceiptLines on RMLPURCHASEORDERID = PMLPURCHASEORDERID and RMLPURCHASEORDERLINEID = PMLPURCHASEORDERLINEID Left Outer Join Receipts on RMLRECEIPTID = RMPRECEIPTID WHERE PMLJOBID = JMMJOBID and PMLJOBASSEMBLYID = JMMJOBASSEMBLYID and PMLJOBMATERIALID = JMMJOBMATERIALID ORDER BY rmlReceiptDate DESC, pmpOrderDate DESC)A
OUTER APPLY( Select TOP 1 * FROM PartTransactions WHERE IMTJOBID = JMMJOBID and IMTJOBASSEMBLYID = 0 AND imtJobMaterialID = jmmJobMaterialID and imtIssueType = 1 )B
Where jmmJobID = '".$jobid."'");		
		$data['jobmaterialdata'] = $jobmaterialdata->row_array(); 
		
		
		$returnobject = (object)$data;
			
		return $returnobject;
	}
	function scheduledjob()
	{
		$machine_id = $_POST['machineid'];
		$query = $this->m1db->query("Select rtrim(ujmpCurrentProdWeek) as proweekno,CONCAT('week',DATEPART(ISO_WEEK, ujmobucketweek)) AS Week,ujmobucketweek,cmoOrganizationID as customerid,cmoName as customername,xaqUniqueID,rtrim(jmpJobID) as jmpjobid,rtrim(jmpPartID) as jmppartid ,jmpPartShortDescription, FORMAT(uomdCustomerDeliveryDate, 'dd/MM/yyyy') as customerdeliverydate,FORMAT(jmpProductionDueDate,'dd/MM/yyyy') as prodduedate,  jmoJobOperationID as operationid, FORMAT(jmpProductionQuantity,'#') as quantity, FORMAT(jmoProductionStandard,'#') as prodstandard, xaqDescription from Jobs 
		LEFT outer join partrevisions on jmpPartID = imrPartID and jmpPartRevisionID = imrPartRevisionID 
		Left Outer Join JobOperations on JMOJOBID = JMPJOBID and JMOJOBASSEMBLYID = 0 
		left outer join workcentermachines on xaqWorkCenterID = jmoWorkCenterID and xaqWorkcenterMachineID = jmoWorkCenterMachineID  
		LEFT OUTER JOIN Organizations ON jmpCustomerOrganizationID=cmoOrganizationID 
	LEFT OUTER JOIN SalesOrderJobLinks ON omjJobID = jmpjobid
LEFT OUTER JOIN SalesOrderDeliveries ON omjSalesOrderID =omdSalesOrderID AND omjSalesOrderLineID = omdSalesOrderLineID AND omjSalesOrderDeliveryID = omdSalesOrderDeliveryID  
		where ujmpbucketweek IS not null and jmpProductionComplete <> -1 and jmoQuantityComplete = 0 and jmoProductionComplete <> -1 and jmpQuantityShipped = 0 AND 
		not EXISTS (Select pmlSalesOrderID from PurchaseOrderLines where pmlJobID = jmpJobID and pmlJobType = 2)  AND 
		NOT exists (Select lmltimecardid from timecardlines where lmljobid = jmojobid AND lmlJobOperationID = jmoJobOperationID ) AND 
		exists (Select omjjobid from SalesOrderJobLinks left outer join SalesOrderDeliveries on OMDSALESORDERID = OMJSALESORDERID and OMDSALESORDERLINEID = OMJSALESORDERLINEID Left Outer Join SalesOrderLines on OMDSALESORDERID = OMLSALESORDERID and OMDSALESORDERLINEID = OMLSALESORDERLINEID Left Outer Join SalesOrders on OMDSALESORDERID = OMPSALESORDERID where omdShippedComplete <> -1 and omdClosed <> -1 and omlClosed <> -1 and ompClosed <> -1 and omjJobID = jmpjobid   ) 
		 AND xaquniqueid='".$machine_id."' ");

		return $query->result();
	}

	
function schedule_job_bucket()
{
	$data = $_POST['ids'];
	
	foreach($data as $k=>$v)
	{
		
		foreach($v as $ks=>$m)
		{
			
			$a = preg_split("~\s+~",$m);	
			
			$year = date("Y");
			$date = new DateTime();
			$date->setISODate($year,$a[2]);			
			 
			
			$this->m1db->set('ujmpbucketweek', $date->format('Y-m-d'));	
			$this->m1db->where('jmpjobid',$a[0]);
			
			$this->m1db->update('jobs');
			
			$this->m1db->set('ujmobucketweek', $date->format('Y-m-d'));	
			$this->m1db->where('jmojobid', $a[0]);
			$this->m1db->where('jmoJobOperationID', $a[1]);
			$this->m1db->update('joboperations');
		}
	}	
	return true;
}
function unschedule_jobs_update()
{
	$job = $_POST['jobs'];	
	
	foreach($job as $k=>$v)
	{
	$this->m1db->set('ujmpbucketweek', null);	
	$this->m1db->where('jmpjobid', explode(" ",$v)[0]); 
	
	$this->m1db->update('jobs');
	
	$this->m1db->set('ujmobucketweek', null);	
	$this->m1db->where('jmojobid', explode(" ",$v)[0]);
	$this->m1db->where('jmoJobOperationID', explode(" ",$v)[1]);
	$this->m1db->update('joboperations'); 
	}
	
	return true;
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
			
			$a = preg_split("~\s+~",$m);			
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
	return true;
}
}