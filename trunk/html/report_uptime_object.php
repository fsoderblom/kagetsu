<?php 
// YES I am aware of that the code is unpleasant to watch and hard to follow.
// But this is a result of misunderstandings in how the report should be done.
// Will be fixed in kagetsu 2.0
// With regards, Peter

require('inc/init.inc.php');
$page = 6;
require('inc/header.inc.php');


$search = isset($_POST['search']) && trim($_POST['search'])!='' ? trim($_POST['search']) : '';
$vrf = isset($_POST['vrf']) && trim($_POST['vrf'])!='' ? trim($_POST['vrf']) : '';
$office = isset($_POST['office']) && trim($_POST['office'])!='' ? trim($_POST['office']) : '';
$sep = isset($_POST['sep']) && trim($_POST['sep'])!='' ? trim($_POST['sep']) : '0';

$defaultfrom = substr(date("Y-m-d", strtotime("".date("Y-m-d")."-1 months")), 0, 7)."-01";
$fromdate = isset($_POST['fromdate']) && trim($_POST['fromdate'])!='' ? trim($_POST['fromdate']) : $defaultfrom;
$days = isset($_POST['days']) && trim($_POST['days'])!='' ? trim($_POST['days']) : '30';
$ofcObj = new office($db);
$vrfObj = new vrf($db);
$typeObj = new type($db);
$parentsObj = new parents($db);

//$search = "10.6.56.1"; // DEBUG

?>

<form name="form1" action="report_uptime_object_generate.php" method="post">
	<input type="hidden" name="act" value="" />

	<h2>Report: uptime per object</h2>

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
	</table>
	<br/>
	<table>
		<tr>
			<th><label for="search">Quickfind</label></th>
			<th><label for="vrf">vrf</label></th>
			<th><label for="office">office</label></th>
		</tr>
		<tr>
			<td><input type="text" id="search" name="search" value="<?php echo $search?>" onkeyup="getobjects(this.form.search.value)" title="Type IP or part of IP and wait" /></td>
			<td>
    			<select name="vrf" id="vrf" onchange="getobjects_vrf(this.form.vrf.value)">
    			<option value="">&nbsp;</option>
    			<?php foreach($vrfObj->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>" <?php if($vrf==$k) echo 'selected="selected"'?>><?php echo $v?></option>
    			<?php }?>
	   			</select>
			</td>
			<td>
    			<select name="office" id="office" onchange="getobjects_office(this.form.office.value)">
    			<option value="0">&nbsp;</option>
    			<option value="all">Show all</option>
    			<?php foreach($ofcObj->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>" <?php if($office==$k) echo 'selected="selected"'?>><?php echo $v?></option>
    			<?php }?>
	   			</select>
			</td>
		</tr>
	</table>
	<br/>
    <table style="width: 25em;">
		<tr>
			<th><label for="id">Choose one or more IPs</label></th>
		</tr>
		<tr>
			<td>
				<select ondblclick="this.form.submit();" multiple="multiple" name="id[]" id="id" size="10" style="width: 99%;" title="Result window / list of IPs"></select>
			</td>
		</tr>

	</table>
	<br/><input type="button" value="Generate report" class="icon_save" onclick="selectAll('id'); this.form.submit()" title="Generates report"/>

	<script type="text/javascript">
	<?php if($search!='') {?>
	    getobjects('<?php echo $search?>');
	<?php } else if($vrf!='') { ?>
	    getobjects_vrf('<?php echo $vrf?>');
	<?php } else if($office!='') { ?>
	    getobjects_office('<?php echo $office?>');
	<?php } ?>
	</script>

</form>
<?php
include('inc/footer.inc.php');

//=======================================================
// FUNCTIONS BELOW
//=======================================================
	

?>
