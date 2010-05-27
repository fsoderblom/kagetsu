<?php 
require('inc/init.inc.php');
$page = 6;
include('inc/header.inc.php');

$object = isset($_GET['object']) && trim($_GET['object']) != '' ? trim($_GET['object']) : '';
$object = isset($_POST['object']) && trim($_POST['object']) != '' ? trim($_POST['object']) : $object;

$event = isset($_GET['event']) && trim($_GET['event']) != '' ? trim($_GET['event']) : '';
$event = isset($_POST['event']) && trim($_POST['event']) != '' ? trim($_POST['event']) : $event;

?>

<h2>Reports</h2>
<ul><h3 class="icon_excel">Spreadsheet</h3>
<li><a href="report_uptime_object.php">Uptimes per object</a></li>
<li><a href="report_uptime_summary_ofc.php">Uptime per office summary</a></li>
<li><a href="report_uptime_summary_vrf.php">Uptime per vrf summary</a></li>
</ul>
<ul><h3 class="icon_live">Live</h3>
<li><a href="imagetest.php">Visual object legend</a></li>
<li>Objects with recent ping difficulties</li>
<li>Objects without office, vrf or type</li>
</ul>
<?php 
include('inc/footer.inc.php');
?>
