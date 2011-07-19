<?php

function testing($db, $ip, $fromdate, $days)
{
	$result = array();

	$start = "$fromdate 00:00:00";
	$todate = date("Y-m-d", strtotime("$start +$days days"));
	$stop = "$todate 23:59:59";
	
	// Create a network->SLA look array for faster access
	$SLA = array();
	if($rs = $db->execute("select * from ".TBL_SUBNET." ORDER BY prefix DESC"))
	{
	    while(!$rs->EOF)
	    {
	    	$SLA[$rs->getColumn('network')] = array("START"=>$rs->getColumn("start"), "STOP"=>$rs->getColumn("end"));
			$rs->nextRow();
	    }
	}
		
	$sql = "select * from ".TBL_LOG." where ip='".$ip."' and event in(".EVT_NODE_DOWN.",".EVT_NODE_RECOVERED.") and datum>='".$start."' and datum<='".$stop."' order by id";

	if($rs = $db->execute($sql))
	{
		$arr = array();
		$date_mem = "";
		$evt_mem = "";
		$status = 0; // 0=unknown, 1=up, 2=down
		$isdown = false;

		// Get the SLA times the hard way!
		if($rs->getRowCount()==0)
		{
			foreach($SLA as $k=>$v)
			{
				if(ip_network($ip, $k)==true)
				{
					$sla_start = $SLA[$k]["START"];
					$sla_stop = $SLA[$k]["STOP"];
				}
			}
		}

		while(!$rs->EOF)
		{
			$dt = $rs->getColumn('datum');
			$date = substr($dt, 0, 10);
			$event = $rs->getColumn('event');
			$network = $rs->getColumn('network');

			$sla_start = $SLA[$network]["START"];
			$sla_stop = $SLA[$network]["STOP"];
						
			if($event==EVT_NODE_DOWN)
			{
				if($status==0 || $status==1)
				{
					if($status==0)
					{
						$isdown=false;
					}
				$arr[] = array("DATE"=>$date, "TIMESTAMP"=>$dt, "STATUS"=>"DOWN");
				$status = 2;
    	    			}
    			}
    		else if($event==EVT_NODE_RECOVERED)
    		{
			if($status==0 || $status==2)
			{
				if($status==0) // recovered found but never a down! Force down on first day.
				{
		    			$arr[] = array("DATE"=>$fromdate, "TIMESTAMP"=>"$fromdate $sla_start", "STATUS"=>"DOWN");
						$isdown = false;
				}
	    			$arr[] = array("DATE"=>$date, "TIMESTAMP"=>$dt, "STATUS"=>"UP");
	    			$status=1;
			}
    		}

//			$res = getUptimeDay($date_mem, $sla_start_mem, $sla_stop_mem, $ip, $arr);
			$rs->nextRow();
		}
	
		for($i=0; $i<$days; $i++)
		{
			$currdate = date("Y-m-d", strtotime("$fromdate +$i days"));

			unset($events);
			$events = array();
			
			$arrlen = count($arr);

			for($x=0; $x<$arrlen; $x++)
			{
				if($arr[$x]["DATE"]==$currdate)
				{	
					if(count($events)==0 && $arr[$x]["STATUS"]=="UP")
					{
						$events[] = "$currdate $sla_start";
					}
					
					$events[] = $arr[$x]["TIMESTAMP"];
				}
			}
			
			if(count($events)>0)
			{
				$res = getUptimeDay($currdate, $sla_start, $sla_stop, $ip, $events);
				$isdown = $res["STILL_DOWN"] == "Y" ? true : false;
				$result[] = $res;
			}
			else
			{
				if($isdown)
				{
					$events[] = "$currdate $sla_start";
//					$events[] = "$currdate $sla_stop";
				}
				else
				{
					$events[] = "$currdate $sla_start";
					$events[] = "$currdate $sla_start";
				}
				
				$res = getUptimeDay($currdate, $sla_start, $sla_stop, $ip, $events);
				$isdown = $res["STILL_DOWN"] == "Y" ? true : false;
				$result[] = $res;
			}
		}
	}
	
//	return array("$ip"=>$result);
	return $result;
	
}

/**
 * Returns downtimeinfo for one IP at one particular day.
 * The method only counts if within SLA time. FINALLY!!!
 * The events MUST start with a down event.
 */
function getUptimeDay($date, $sla_start, $sla_stop, $ip, $events)
{
	$totaltime = 0;
	$downtime = 0;
	$slatime = 0;
	$eventcount = 0;
		
//	echo "<pre>";
//	print_r($events);
//	echo "</pre>";
		
	$start = strtotime("$date 00:00:00");
	$stop = strtotime("$date 23:59:59");

	$slastart = strtotime("$date $sla_start");
	$slastop = strtotime("$date $sla_stop");
	
	$down = strtotime($events[$eventcount++]);

	if(count($events) > 1)
	{
	    $up = strtotime($events[$eventcount++]);
	}
	else
	{
		$up = $stop;
	}

	while($start<=$stop)
	{
		$totaltime+=60;

		if($start>=$slastart && $start<$slastop)
		{
			$slatime+=60;

			if($start>=$down && $start<$up)
			{
				$downtime+=60;
			}
			
			if($start>=$up)
	    	{
	    		$down = $eventcount<count($events) ? strtotime($events[$eventcount++]) : $stop;
        		$up = $eventcount<count($events) ? strtotime($events[$eventcount++]) : $stop; 
	    	}
		}

		$start+=60;
	}
	
	$percent = sprintf("%01.2f", ($slatime-$downtime)/$slatime*100);
	$res = array(	"DATE"=>"$date",
					"IP"=>"$ip",
					"SLA_FROM"=>"$sla_start",
					"SLA_TO"=>"$sla_stop",
					"TOTALTIME_SEC"=>"$totaltime", 
					"SLATIME_SEC"=>"$slatime", 
					"DOWNTIME_SEC"=>"$downtime", 
					"STILL_DOWN"=>count($events)%2!=0?"Y":"N", 
					"UPTIME_PERCENT"=>"$percent",
					"EVENTS"=>$events);
	
	return $res;
}

?>
