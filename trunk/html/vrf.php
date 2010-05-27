<?php 
require('inc/init.inc.php');
$page = 3;

include('inc/header.inc.php');

$object = isset($_GET['object']) && trim($_GET['object']) != '' ? trim($_GET['object']) : '';
$object = isset($_POST['object']) && trim($_POST['object']) != '' ? trim($_POST['object']) : $object;

$event = isset($_GET['event']) && trim($_GET['event']) != '' ? trim($_GET['event']) : '';
$event = isset($_POST['event']) && trim($_POST['event']) != '' ? trim($_POST['event']) : $event;
?>
<form name="userform" method="post" action="vrf.php">
<input type="hidden" name="id" value="<?php echo $id?>" /> 
<input type="hidden" name="act" value="" />

<?php
if($act=='edit')
{
	showEditform($db, $id);
}
else if($act=='save')
{
	$tm = new tablemagic($db);
	$tm->setTable(TBL_VRF);
	$tm->load($id);
	$tm->setColumn("name", $_POST["name"]);
    if(!$tm->save())
    {
    	error("Could not save");
    }
}
else if($act=='del')
{
	$tm = new tablemagic($db);
	$tm->setTable(TBL_VRF);
	if($tm->load($id))
	{
		$tm->kill();
	}
}

?>
<h2>VRF</h2>
<table class="tables">
<tr>
	<th>id</th>
	<th>vrf</th>
	<td>&nbsp;</td>
</tr>
<?php
if($rs = $db->execute("select * from ".TBL_VRF." order by name"))
{
	while(!$rs->EOF)
	{
		?>
		<tr>
			<td><?php echo $rs->getColumn("id")?></td>
			<td><?php echo $rs->getColumn("name")?></td>
			<td><input type="submit" class="icon_edit" value="edit" onclick="this.form.act.value='edit'; this.form.id.value='<?php echo $rs->getColumn("id")?>'"></td>
		</tr>
		<?php
		$rs->nextRow();
	}
}

?>
<tr>
<td colspan="2">&nbsp;</td>
<td><input type="submit" class="icon_new" value="new" onclick="this.form.act.value='edit'; this.form.id.value=''"></td>
</tr>
</table>
</form>
<?php 
include('inc/footer.inc.php');

//=======================================================
// FUNCTIONS BELOW
//=======================================================

function showEditform($db, $id)
{
	$tm = new tablemagic($db);
	$tm->setTable(TBL_VRF);
	if($tm->load($id))
	{
		$loaded = true;
		echo "<h2 class=\"edit\">Edit VRF</h2>";
	}
	else
	{
		$loaded = false;
		echo "<h2 class=\"edit\">Create new VRF</h2>";
	}
	?>
    <div id="editform">
    	<table>
    		<tr>
    			<th>id</th>
    			<td><input class="disabled" readonly="readonly" type="text" value="<?php echo $tm->getColumn("id")?>" /></td>
    		</tr>
    		<tr>
    			<th>vrf</th>
    			<td><input type="text" name="name" value="<?php echo $tm->getColumn("name")?>" /></td>
    		</tr>
    		<tr>
    			<td>&nbsp;</td>
    			<td>
    			<br/>
    			<input type="submit" class="icon_save" value="save" onclick="this.form.act.value='save'" />&nbsp;
    			<?php if($loaded){?>
    			<input type="submit" class="icon_delete" value="delete" onclick="if(okDel('vrf')) {this.form.act.value='del'} else return false;" />&nbsp;
				<?php }?>
    			<input type="submit" class="icon_cancel" value="cancel" />&nbsp;
    			</td>
    			
    		</tr>
    	</table>
    </div>
    <hr noshade>
    <?php
	
}

?>
