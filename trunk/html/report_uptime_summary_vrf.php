<?php 

require('inc/init.inc.php');
$page = 6;
require('inc/header.inc.php');

$sep = isset($_POST['sep']) && trim($_POST['sep'])!='' ? trim($_POST['sep']) : '0';
$defaultfrom = substr(date("Y-m-d", strtotime("".date("Y-m-d")."-1 months")), 0, 7)."-01";
$fromdate = isset($_POST['fromdate']) && trim($_POST['fromdate'])!='' ? trim($_POST['fromdate']) : $defaultfrom;
$days = isset($_POST['days']) && trim($_POST['days'])!='' ? trim($_POST['days']) : '30';

?>

<form name="form1" action="report_uptime_summary_vrf_generate.php" method="post">
	<input type="hidden" name="act" value="" />

	<h2>Report: Uptime per vrf summary</h2>

	<table>
		<tr>
			<th><label for="fromdate">From date</label></th>
			<td><input type="text" class="date" name="fromdate" id="fromdate" value="<?php echo $fromdate?>" /></td>
		</tr>
		<tr>
			<th><label for="days">Days</label></th>
			<td><input type="text" class="date" name="days" id="days" value="<?php echo $days?>"></td>
		</tr>
		<tr>
			<th><label for="days">.csv separator</label></th>
			<td>
				<select name="sep" id="sep">
					<option value="0"<?php if($sep=="0") echo 'selected="selected"'?>>Comma</option>
					<option value="1"<?php if($sep=="1") echo 'selected="selected"'?>>Semicolon</option>
					<option value="2"<?php if($sep=="2") echo 'selected="selected"'?>>Tab</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="days">VRF</label></th>
			<td>
				<select name="vrf[]" id="vrf" size="10" multiple="multiple" ondblclick="this.form.submit();" >
					<?php
					$vrf = new vrf($db);
					foreach($vrf->getNames() as $vrfid=>$vrfname)
					{
						echo "<option value=\"$vrfid\">$vrfname</option>";
					}
					?>
				</select>
			</td>
		</tr>
	</table>
	<br/><br/><input type="button" value="Generate report" class="icon_save" onclick="selectAll('vrf'); this.form.submit()" title="Generates report"/>

</form>
<?php
include('inc/footer.inc.php');

//=======================================================
// FUNCTIONS BELOW
//=======================================================
	

?>
