<?php 
require('inc/init.inc.php');
$page = 5;
include('inc/header.inc.php');

$object = isset($_GET['object']) && trim($_GET['object']) != '' ? trim($_GET['object']) : '';
$object = isset($_POST['object']) && trim($_POST['object']) != '' ? trim($_POST['object']) : $object;

$event = isset($_GET['event']) && trim($_GET['event']) != '' ? trim($_GET['event']) : '';
$event = isset($_POST['event']) && trim($_POST['event']) != '' ? trim($_POST['event']) : $event;

$ofc = new office($db);

?>
<form name="userform" method="post" action="slatimes.php">
<input type="hidden" name="id" value="<?php echo $id?>" /> 
<input type="hidden" name="act" value="" />

<?php
if($act=='edit')
{
	showEditform($db, $id, $ofc);
}
else if($act=='save')
{
	$tm = new tablemagic($db);
	$tm->setTable(TBL_SUBNET);
	$tm->load($id);
	$tm->setColumn("network", $_POST["network"]);
    $tm->setColumn("prefix", $_POST["prefix"]);
    $tm->setColumn("office", $_POST["office"]);
    $tm->setColumn("start", $_POST["start"]);
    $tm->setColumn("end", $_POST["end"]);
    if(!$tm->save())
    {
    	error("Could not save");
    }
}
else if($act=='del')
{
	$tm = new tablemagic($db);
	$tm->setTable(TBL_SUBNET);
	if($tm->load($id))
	{
		$tm->kill();
	}
}

?>
<h2>SLA times</h2>
<table class="tables">
<tr>
	<th>id</th>
	<th>network</th>
	<th>prefix</th>
	<th>office</th>
	<th>start</th>
	<th>stop</th>
	<td>&nbsp;</td>
</tr>
<?php
if($rs = $db->execute("select * from ".TBL_SUBNET.""))
{
	while(!$rs->EOF)
	{
		?>
		<tr>
			<td><?php echo $rs->getColumn("id")?></td>
			<td><?php echo $rs->getColumn("network")?></td>
			<td><?php echo $rs->getColumn("prefix")?></td>
			<td><?php echo $ofc->getName($rs->getColumn("office"))?></td>
			<td><?php echo $rs->getColumn("start")?></td>
			<td><?php echo $rs->getColumn("end")?></td>
			<td><input type="submit" class="icon_edit" value="edit" onclick="this.form.act.value='edit'; this.form.id.value='<?php echo $rs->getColumn("id")?>'"></td>
		</tr>
		<?php
		$rs->nextRow();
	}
}

?>
<tr>
<td colspan="6">&nbsp;</td>
<td><input type="submit" class="icon_new" value="new" onclick="this.form.act.value='edit'; this.form.id.value=''"></td>
</tr>
</table>
</form>
<?php 
include('inc/footer.inc.php');

//=======================================================
// FUNCTIONS BELOW
//=======================================================

function showEditform($db, $id, $ofc)
{
	
	$tm = new tablemagic($db);
	$tm->setTable(TBL_SUBNET);
	if($tm->load($id))
	{
		$loaded = true;
		echo "<h2 class=\"edit\">Edit SLA</h2>";
	}
	else
	{
		$loaded = false;
		echo "<h2 class=\"edit\">Create new SLA</h2>";
	}
	?>
    <div id="editform">
    	<table>
    		<tr>
    			<th>id</th>
    			<td><input class="disabled" readonly="readonly" type="text" value="<?php echo $tm->getColumn("id")?>" /></td>
    		</tr>
    		<tr>
    			<th>network</th>
    			<td><input type="text" name="network" value="<?php echo $tm->getColumn("network")?>" /></td>
    		</tr>
    		<tr>
    			<th>prefix</th>
    			<td><input type="text" name="prefix" value="<?php echo $tm->getColumn("prefix")?>" /></td>
    		</tr>
    		<tr>
    			<th>office</th>
    			<td>
    			<select name="office">
    			<option value="">&nbsp;</option>
    			<?php foreach($ofc->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>" <?php if($k==$tm->getColumn("office")) echo 'selected="selected"'?>><?php echo $v?></option>
    			<?php }?>
	   			</select>
    			</td>
    		</tr>
    		<tr>
    			<th>start</th>
    			<td><input type="text" name="start" value="<?php echo $tm->getColumn("start")?>" /></td>
    		</tr>
    		<tr>
    			<th>end</th>
    			<td><input type="text" name="end" value="<?php echo $tm->getColumn("end")?>" /></td>
    		</tr>
    		<tr>
    			<td>&nbsp;</td>
    			<td>
    			<br/>
    			<input type="submit" class="icon_save" value="save" onclick="this.form.act.value='save'" />&nbsp;
    			<?php if($loaded){?>
    			<input type="submit" class="icon_delete" value="delete" onclick="if(okDel('SLA time')) {this.form.act.value='del'} else return false;" />&nbsp;
				<?php }?>
				<input type="submit" class="icon_cancel" value="cancel" />
    			</td>
    			
    		</tr>
    	</table>
    </div>
	<label style="color:red">Att ändra SLA tider kan påverka tidigare loggar. Ändras med varsamhet!</label>
    <br/><hr/>
    <?php
	
}

?>
