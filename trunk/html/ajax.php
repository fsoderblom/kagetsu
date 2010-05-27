<?php 

	include('inc/init.inc.php');

	$cmd = isset($_GET['cmd']) && $_GET['cmd']!='' ? $_GET['cmd'] : '';

	switch($cmd)
	{
		case "getobjects": include("ajax/getobjects.php"); break;
		case "getobjects_vrf": include("ajax/getobjects_vrf.php"); break;
		case "getobjects_office": include("ajax/getobjects_office.php"); break;
		case "gethost": include("ajax/gethost.php"); break;
		case "getobjinfo": include("ajax/getobjinfo.php"); break;
	}

?>