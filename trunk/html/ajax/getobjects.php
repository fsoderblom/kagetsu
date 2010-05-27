<?php

	header("Content-type: text/html;charset=iso-8859-1");

	if(!is_object($db)) return;

	$dest = isset($_GET['prm1']) && $_GET['prm1']!='' ? $_GET['prm1'] : ''; // destination
	$ip = isset($_GET['prm2']) && $_GET['prm2']!='' ? $_GET['prm2'] : ''; // search
	if(strlen($ip)>=1)
	{
		echo $dest."||";

		$sql = "SELECT id,ip,name,status FROM ".TBL_OBJECT." WHERE ip LIKE '%$ip%' or name LIKE '%$ip%'";
		
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
	}
	
?>
