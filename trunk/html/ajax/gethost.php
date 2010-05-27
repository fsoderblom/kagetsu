<?php

	header("Content-type: text/html;charset=iso-8859-1");

	if(!is_object($db)) return;

	$dest = isset($_GET['prm1']) && $_GET['prm1']!='' ? $_GET['prm1'] : ''; // destination
	$ip = isset($_GET['prm2']) && $_GET['prm2']!='' ? $_GET['prm2'] : ''; // search

	if(strlen($ip)>=8)
	{
		echo $dest."||";
		echo gethostbyaddr($ip);
	}
	
?>
