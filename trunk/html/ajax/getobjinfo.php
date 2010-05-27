<?php

	include('inc/objects.class.php');
	header("Content-type: text/html;charset=iso-8859-1");

	if(!is_object($db)) return;

	$dest = isset($_GET['prm1']) && $_GET['prm1']!='' ? $_GET['prm1'] : ''; // destination
	$xypos = isset($_GET['prm2']) && $_GET['prm2']!='' ? $_GET['prm2'] : ''; // search

	$ofc = new office($db);
	
	$o = new objects($db);
	$pos = explode(",",$xypos);

	echo $dest."||";
	
	$objnr = $o->getObjectNumberAt($pos[0], $pos[1]);
	if($rs = $o->getObject($objnr))
	{
		echo "<b>IP:</b> ".$rs->getColumn("ip")." <b>Office:</b> ".$ofc->getName($rs->getColumn("office"));
		echo "<br/><b>Name:</b> ".$rs->getColumn("name");
		echo "<br/><b>Last changed:</b> ".$rs->getColumn("statuschanged");
		echo "<br/><b>Successful:</b> ".$rs->getColumn("successful")." <b>Failed:</b> ".$rs->getColumn("failed");
	}
	    	
?>
