<?php 
include('inc/init.inc.php');
$page = 6;
include('inc/header.inc.php');
?>
<form name="legendform" action="" method="post">
	<h2>Visual object legend</h2>
	<input type="image" src="imagetest_image.php" id="legendimg" name="legendimg" onmousemove="testimg('legendimg','objinfo')"/>
	<div name="objinfo" id="objinfo">Please hold mouse<br/>over legend and wait!</div>
</form>
<?php

include('inc/footer.inc.php');
?>