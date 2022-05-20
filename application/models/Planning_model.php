<?php

class Planning_model extends CI_Model {
    
	
    function __construct(){
		parent::__construct();
		
		$this->m1db = $this->load->database('M1', TRUE);
	}
	function unplanned_jobs($cell,$machineid)
	{
		$query = $this->m1db->query("select * from (SELECT DISTINCT rtrim(cmoname) as Customer, 
									rtrim(omlpartid)as PartID,CASE WHEN jmpjobid is not NULL THEN rtrim(jmpjobid) WHEN omdDeliveryType = 1 THEN 'Make To Order' WHEN omdDeliveryType = 2 THEN 'Pull From Stock' WHEN omdDeliveryType = 4 THEN 'Kit Part' WHEN omdDeliveryType = 5 THEN 'Purchase to Order' ELSE '' END as jmpJobID ,rtrim(omlpartshortdescription) as PartDescription ,(omddeliveryquantity-omdquantityshipped) AS 'remaining_Quantity',
									jmpscheduledstartdate ,uomdcustomerdeliverydate,CASE WHEN ((year(uomdcustomerdeliverydate) < year(getdate()) or year(uomdcustomerdeliverydate) <= year(getdate()) and DATEPART(week, uomdcustomerdeliverydate) < DATEPART(week, getdate()))) then 'B/L' WHEN year(uomdcustomerdeliverydate) = year(getdate()) and DATEPART(week, uomdcustomerdeliverydate) = DATEPART(week, getdate()) THEN 'Curr Week' WHEN DATEDIFF(year,getdate(),uomdcustomerdeliverydate)*52+ DATEPART(week, uomdcustomerdeliverydate) - DATEPART(week, getdate()) <=17 THEN 'Curr Week +'+ FORMAT(DATEDIFF(year,getdate(),uomdcustomerdeliverydate)*52 + DATEPART(week, uomdcustomerdeliverydate) - DATEPART(week, getdate()), '#') WHEN DATEDIFF(year,getdate(),uomdcustomerdeliverydate)*52+ DATEPART(week, uomdcustomerdeliverydate) - DATEPART(week, getdate()) >17 THEN 'Curr Week+18++' ELSE 'an Error' end as TheWeek,
									workcenter, RTRIM(machine) AS MACHINE, CASE
									WHEN lmljobid IS NOT NULL THEN 'Clocked in ' + lmlworkcenterid
									ELSE 'No Clock-in'
									END AS clockins,cycletime,ujmporiginalprodweek,ujmpcurrentprodweek,
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
									WHEN treatsreceiptid IS NOT NULL THEN 'Recieved from treats on ' + treatsreceiptdate
									WHEN treatspurchaseorderid IS NOT NULL THEN 'At treats PO:' + treatspurchaseorderid + ' due '+ treatsduedate
									WHEN plating_count IS NULL THEN 'No Plating Required'
									ELSE 'Not gone for plating yet'
									END AS treatsstatus,

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
									OUTER apply
									(
									SELECT TOP 1
									*
									FROM timecardlines
									WHERE lmljobid IN
									(
									SELECT omjjobid
									FROM salesorderjoblinks
									WHERE omjsalesorderid = omdsalesorderid
									AND omjsalesorderlineid = omlsalesorderlineid)
									AND lmljobassemblyid = 0
									ORDER BY lmlcreateddate DESC ) lml
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
									SELECT pmljobid AS treatsjobid,
									Rtrim(Max( pmlpurchaseorderid )) AS treatspurchaseorderid ,
									Format(Max( pmlduedate ),'dd/MM/yyyy') AS treatsduedate ,
									Max(rmlreceiptid) AS treatsreceiptid ,
									Format(Max(rmlreceiptdate),'dd/MM/yyyy') AS treatsreceiptdate,
									FORMAT(MAX(upmlSupplierCollectionDate),'dd/MM/yyyy') AS treatscollection
									FROM purchaseorderlines
									LEFT OUTER JOIN uSupplierAMFCollections
									on usfBatchID = pmlJobID
									and usfLoadID = (Select MAX(usfLoadID) from uSupplierAMFCollections )
									LEFT OUTER JOIN receiptlines
									ON rmlpurchaseorderid = pmlpurchaseorderid
									AND rmlpurchaseorderlineid = pmlpurchaseorderlineid
									WHERE pmljobtype = 2
									GROUP BY pmljobid ) AS d
									ON treatsjobid = jmpjobid
									LEFT OUTER JOIN
									(
									SELECT jmojobid,
									Max( xaqdescription ) AS machine,
									MIN( xaqWorkCenterID ) AS workcenter,
									max( jmoProductionStandard) as cycletime
									FROM joboperations
									LEFT OUTER JOIN workcentermachines
									ON jmoworkcenterid = xaqworkcenterid
									AND jmoworkcentermachineid = xaqworkcentermachineid
									WHERE jmojobassemblyid = 0
									AND jmoworkcenterid in ('MILL1','DECO','TWIN','MILL2','MILL3','MILL4','MILL5')
									GROUP BY jmojobid) AS e
									ON jmpjobid = jmojobid
									LEFT OUTER JOIN
									(
									SELECT jmojobid,
									Count(*) AS plating_count
									FROM joboperations
									WHERE jmooperationtype = 2
									AND jmoworkcenterid = 'OUTS'
									GROUP BY jmojobid ) AS theops
									ON theops.jmojobid = jmpjobid
									INNER JOIN organizations
									ON cmoorganizationid = ompcustomerorganizationid
									LEFT OUTER JOIN
									(
									SELECT smlpartid,
									Max( smlcreateddate ) AS smlcreateddate
									FROM shipmentlines
									GROUP BY smlpartid ) AS theshipment
									ON theshipment.smlpartid = jmppartid
									OUTER APPLY (
									Select top 1 * from uPartFaiStatus where uifPartID = omlPartID ORDER BY uifFairStatus DESC, uifPartFaiStatusID DESC
									)FAIRS
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
									OR imppartgroupid IS NULL) and workcenter='".$cell."')test WHERE clockins='No Clock-in'
						");
			return $query->result();
	}
	function machines_weekdata()
	{
		
		$data = array();
		$qry_res = $this->db->query('Call machine_actualhours()');
		$data['actual'] = $qry_res->result();
		
		$qry_res->next_result();
		$qry_res->free_result();
		
		$query = $this->m1db->query("SELECT * FROM (SELECT xaquniqueid,concat('w',ujmpCurrentProdWeek) AS week,sum(jmoProductionStandard * (omddeliveryquantity-omdquantityshipped))/60 AS cycletime FROM Jobs 
left outer join joboperations on jmpjobid=jmojobid 
LEFT OUTER JOIN WorkCenterMachines on xaqWorkCenterMachineID = jmoWorkCenterMachineID AND jmoworkcenterid=xaqWorkCenterID
LEFT OUTER JOIN SalesOrderJobLinks ON omjJobID = jmpjobid
LEFT OUTER JOIN SalesOrderDeliveries ON omjSalesOrderID =omdSalesOrderID AND omjSalesOrderLineID = omdSalesOrderLineID AND omjSalesOrderDeliveryID = omdSalesOrderDeliveryID  
WHERE ujmpCurrentProdWeek > '18' AND jmoworkcenterid in ('MILL1','DECO','TWIN','MILL2','MILL3','MILL4','MILL5') AND not exists (Select * from TimecardLines where lmlJobID = jmpJobID and lmlJobOperationID = jmoJobOperationID) GROUP BY xaquniqueid,ujmpCurrentProdWeek)
x pivot(MAX(cycletime) FOR week IN ([w19],[w20],[w21],[w22],[w23],[w24],[w25],[w26]))as piv");
		
		$data['hourscommit'] = $query->result();	
		return $data;
	}
}