<?php
require('inc/init.inc.php');
include('inc/header.inc.php');

$table = isset($_GET['table']) && trim($_GET['table']) != '' ? trim($_GET['table']) : '';
$table = isset($_POST['table']) && trim($_POST['table']) != '' ? trim($_POST['table']) : $table;

if($table=='')
{
	if($rs = $db->execute("show tables"))
	{
		while(!$rs->EOF)
		{
			$table = $rs->getColumn(0);
			echo '<br/><a href="tables.php?table='.$table.'">'.$table.'</a>';
			$rs->nextRow();
		}
	}
}
else
{
	if($rs = $db->execute("describe $table"))
	{
		while(!$rs->EOF)
		{
			$table = $rs->getColumn(0);
			echo '<br/><a href="tables.php?table='.$table.'">'.$table.'</a>';
			$rs->nextRow();
		}
	}
}

include('inc/footer.inc.php');

?>