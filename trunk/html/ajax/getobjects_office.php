<?php

	header("Content-type: text/html;charset=iso-8859-1");

	if(!is_object($db)) return;

	$dest = isset($_GET['prm1']) && $_GET['prm1']!='' ? $_GET['prm1'] : ''; // destination
	$office = isset($_GET['prm2']) && $_GET['prm2']!='' ? $_GET['prm2'] : ''; // search

	if($office=="all")
	{
		$sql = "SELECT id,ip,name,status FROM ".TBL_OBJECT;
	}
	else
	{
		$sql = "SELECT id,ip,name,status FROM ".TBL_OBJECT." WHERE office='".$office."'";
	}

	echo $dest."||";
    
    if($rs = $db->execute($sql))
    {
    	if(!$rs->EOF)
    	{
    		$c=0;		
    		while(!$rs->EOF)
    		{
    			if($c++>0) echo ";";
    			echo $rs->getColumn("id")."#".$rs->getColumn("ip")."#".$rs->getColumn("name")."#".$rs->getColumn("status");
    			$rs->nextRow();
    		}
    	}
    	else
    	{
    		echo "#NOTHING#FOUND";
    	}
    }
	
?>
