<?php

// YES I am aware of that the code is unpleasant to watch and hard to follow.
// But this is a result of misunderstandings in how the report should be done.
// Will be fixed in kagetsu 2.0
// With regards, Peter

require('inc/init.inc.php');
require('inc/reportfunctions.php');


$headersent=false;

$ip = isset($_GET['ip']) && trim($_GET['ip']) != '' ? trim($_GET['ip']) : '10.6.56.1';
$ip = isset($_POST['ip']) && trim($_POST['ip']) != '' ? trim($_POST['ip']) : $ip;
$fromdate = isset($_GET['fromdate']) && trim($_GET['fromdate']) != '' ? trim($_GET['fromdate']) : '';
$fromdate = isset($_POST['fromdate']) && trim($_POST['fromdate']) != '' ? trim($_POST['fromdate']) : $fromdate;
$days = isset($_GET['days']) && trim($_GET['days']) != '' ? trim($_GET['days']) : '';
$days = isset($_POST['days']) && trim($_POST['days']) != '' ? trim($_POST['days']) : $days;

$sep = isset($_POST['sep']) && trim($_POST['sep'])!='' ? trim($_POST['sep']) : '0';
$sep = str_replace("0",",", $sep);
$sep = str_replace("1",";", $sep);
$sep = str_replace("2","\t", $sep);

$ofcObj = new office($db);
$vrfObj = new vrf($db);

$names = array();
$vrfs = array();
$offices = array();
$scanres = array();

if (isset($_REQUEST['id'])) {
    $ids = implode(',', $_REQUEST['id']);
}

if($rs = $db->execute("select ip,name,office,vrf from ".TBL_OBJECT." where id IN($ids) AND status<>'".STATUS_DISABLED."'"))
{
	while(!$rs->EOF)
	{
		$ip = $rs->getColumn("ip");
		$scanres[$ip] = testing($db, $ip, $fromdate, $days); 

		$names[$ip] = $rs->getColumn("name");
		$vrfs[$ip] = $vrfObj->getName($rs->getColumn("vrf"));
		$offices[$ip] = $ofcObj->getName($rs->getColumn("office"));
		
		$rs->nextRow();
	}
	
	foreach($scanres as $ip=>$dayarr)
	{
		$total_sla = 0;
		$total_down = 0;
		
		foreach($dayarr as $k2=>$v)
		{
			$total_sla += $v["SLATIME_SEC"];
			$total_down += $v["DOWNTIME_SEC"];
			$total_uptime = sprintf("%01.2f", ($total_sla-$total_down)/$total_sla*100);
			
			for($i=0; $i<count($v["EVENTS"]); $i++)
			{
				$dn = $v["EVENTS"][$i];
				$up = $v["EVENTS"][$i+1];

				if($up!=$dn)
				{
					$dn = substr($v["EVENTS"][$i],0,16);
					$up = substr($v["EVENTS"][$i+1],0,16);

					if($headersent==false)
					{
						$filename=rand().'.csv';
						header('Pragma: private');
						header('Cache-Control: private, must-revalidate');
						header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
						header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
						header("Cache-Control: no-store, no-cache, must-revalidate");
						header("Content-Transfer-Encoding: binary");
						header('Content-Disposition: attachment; filename='.$filename.'');
						header('Content-Type: application/vnd.ms-excel');
						echo "Object".$sep."Name".$sep."Office".$sep."VRF".$sep."Down".$sep."Up".$sep."% Uptime".$sep."% Total".$sep."SLA from".$sep."SLA to";
						$headersent=true;
					}

					echo "\r\n".$ip.$sep.$names[$ip].$sep.$offices[$ip].$sep.$vrfs[$ip].$sep.$dn.$sep.$up.$sep.$v["UPTIME_PERCENT"]."%".$sep.$total_uptime."%".$sep.$v["SLA_FROM"].$sep.$v["SLA_TO"];
				}
				$i++;
			}
		}
	}

	if($headersent==false)
	{
		include('inc/header.inc.php');
		echo "No downtimes found. Everything OK.";
		echo '<br/><a href="report_uptime_object.php">back</a>';
		include('inc/footer.inc.php');
	}

}
else
{
	include('inc/header.inc.php');
	echo "Sorry, no data!";
	echo '<br/><a href="report_uptime_object.php">back</a>';
	include('inc/footer.inc.php');
}


//=================================================================
// FUNCTIONS BELOW
//=================================================================


?>