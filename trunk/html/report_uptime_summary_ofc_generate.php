<?php
require('inc/init.inc.php');
require('inc/reportfunctions.php');

$headersent=false;

$fromdate = isset($_POST['fromdate']) && trim($_POST['fromdate']) != '' ? trim($_POST['fromdate']) : '';
$days = isset($_POST['days']) && trim($_POST['days']) != '' ? trim($_POST['days']) : '';

$sep = isset($_POST['sep']) && trim($_POST['sep'])!='' ? trim($_POST['sep']) : '0';
$sep = str_replace("0",",", $sep);
$sep = str_replace("1",";", $sep);
$sep = str_replace("2","\t", $sep);

$ofcObj = new office($db);
$vrfObj = new vrf($db);

if (isset($_REQUEST['office'])) {
    $officearray = $_REQUEST['office'];
}
else
{
	die("Inget data kom till rapporten - avbryter");
}

$output = "#SUMMARY#"; // to be string replaced
$output .= "\r\n\r\nDate".$sep."Office".$sep."% Uptime";

for($ofc_count=0; $ofc_count < count($officearray); $ofc_count++)
{
	$ofckey = $officearray[$ofc_count];
	$ofcname = $ofcObj->getName($officearray[$ofc_count]);
	
	$summary_sla = 0;
    $summary_down = 0;
	$summmary_uptimes = array();
	
	$names = array();
	$vrfs = array();
	$offices = array();
	$scanres = array();
	$daysum = array();
	$idarr = array();

	if($rs = $db->execute("select id from ".TBL_OBJECT." where office='$ofckey' and status<>'".STATUS_DISABLED."'"))
	{
		while(!$rs->EOF)
		{
			$idarr[] = $rs->getColumn("id");
			$rs->nextRow();
		}
	}

	$ids = implode(",", $idarr);
	
	if($rs = $db->execute("select ip,name,office,vrf from ".TBL_OBJECT." where id IN($ids) and status<>'".STATUS_DISABLED."'"))
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
		
		$ipcount = 0;
		foreach($scanres as $ip=>$dayarr)
		{
			$total_sla = 0;
			$total_down = 0;
			$i = 0;
			foreach($dayarr as $k=>$v)
			{	
				$sla = $v["SLATIME_SEC"];
				$down = $v["DOWNTIME_SEC"];
				$uptime = sprintf("%01.2f", ($sla-$down)/$sla*100);

				$total_sla += $sla;
				$total_down += $down;
				$total_uptime = sprintf("%01.2f", ($total_sla-$total_down)/$total_sla*100);

				$summary_sla += $sla;
				$summary_down += $down;
				$summary_uptime = sprintf("%01.2f", ($summary_sla-$summary_down)/$summary_sla*100);

				$daysum[$i++] += doubleval($uptime);
			}
			
			$ipcount++; 
		}

		for($c=0; $c<$i; $c++)
		{
			$tmpdate = "$fromdate 00:00:00";
			$tmpdate = date("Y-m-d", strtotime("$tmpdate +$c days"));
			$ofuptime = sprintf("%01.1f", ($daysum[$c]/$ipcount));
			$output .= "\r\n".$tmpdate.$sep.$ofcname.$sep.$ofuptime."%";
		}		
	}

	$summary_uptimes[$ofcname] = $summary_uptime;
}


$output_sum = "From".$sep."To".$sep."Office".$sep."% Uptime";
foreach($summary_uptimes as $ofcname=>$uptime)
{
	$output_sum .= "\r\n".$fromdate.$sep.$tmpdate.$sep.$ofcname.$sep.$uptime."%";
}

$output = str_replace("#SUMMARY#", $output_sum, $output);

$filename=rand().'.csv';
header('Pragma: private');
header('Cache-Control: private, must-revalidate');
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Content-Transfer-Encoding: binary");
header('Content-Disposition: attachment; filename='.$filename.'');
header('Content-Type: application/vnd.ms-excel');

echo $output;
flush();

//=================================================================
// FUNCTIONS BELOW
//=================================================================




?>
