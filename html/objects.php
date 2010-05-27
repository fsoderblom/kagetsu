<?php 
require('inc/init.inc.php');
$page = 1;

$search = isset($_POST['search']) && trim($_POST['search'])!='' ? trim($_POST['search']) : '';
$vrf = isset($_POST['vrf']) && trim($_POST['vrf'])!='' ? trim($_POST['vrf']) : '';
$office = isset($_POST['office']) && trim($_POST['office'])!='' ? trim($_POST['office']) : '';

$ofcObj = new office($db);
$vrfObj = new vrf($db);
$typeObj = new type($db);
$parentsObj = new parents($db);

if (isset($_REQUEST['id'])) {
    $ids = implode(',', $_REQUEST['id']);
    if($id=='')
    {
    	$id = $_REQUEST['id'][0]; // for single save 
	}
}

if($act=='editnew')
{
	header("location: objects.php?act=edit");
}

require('inc/header.inc.php');
?>
<form name="form1" action="" method="post">
<!-- 	<input type="hidden" name="id" value="<?php echo $id?>" />  -->
	<input type="hidden" name="act" value="" />
	<input type="hidden" name="ids" value="<?php echo $ids?>" />
<?php
if($act=='edit')
{
	showEditform($db, $id, $ofcObj, $vrfObj, $typeObj, $parentsObj);
}
if($act=='editbatch')
{
	showBatcheditform($db, $id, $ofcObj, $vrfObj, $typeObj, $parentsObj);
}
if($act=='save')
{
	
	$tm = new tablemagic($db);
	$tm->setTable(TBL_OBJECT);
	$tm->load($id);
	$tm->setColumn("ip", $_POST["ip"]);
    $tm->setColumn("name", $_POST["name"]);
    $tm->setColumn("office", $_POST["office_edit"]);
    $tm->setColumn("parent", $_POST["parent"]);
    $tm->setColumn("vrf", $_POST["vrf_edit"]);
    $tm->setColumn("type", $_POST["type"]);
    $tm->setColumn("status", $_POST["status"]);
    $tm->setColumn("statuschanged", $_POST["statuschanged"]);
    $tm->setColumn("comment", $_POST["comment"]);

    if(!$tm->save())
    {
    	error("Could not save");
    }
}
if($act=='savebatch')
{
	$tm = new tablemagic($db);
	$idarr = explode(",", $_POST['ids']);
	foreach($idarr as $k=>$v)
	{
		if($tm->load($v, TBL_OBJECT, "id"))
		{
		    if($_POST["office_edit"]!='') $tm->setColumn("office", $_POST["office_edit"]);
		    if($_POST["parent"]!='') $tm->setColumn("parent", $_POST["parent"]);
		    if($_POST["vrf_edit"]!='') $tm->setColumn("vrf", $_POST["vrf_edit"]);
		    if($_POST["type"]!='') $tm->setColumn("type", $_POST["type"]);
		    if($_POST["comment"]!='') $tm->setColumn("comment", $_POST["comment"]);
	
			if(!$tm->save())
			{
				echo "<br/>Could not save id $v";
			}
		}
	}
}
else if($act=='del')
{
	$tm = new tablemagic($db);
	$tm->setTable(TBL_OBJECT);
	if($tm->load($id))
	{
		$tm->kill();
//		echo $tm->getSQL();
	}
}

?>
	<h2>Objects</h2>

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
    			<option value="">&nbsp;</option>
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
			<th><label for="id">Choose IP (dbl click)</label></th>
		</tr>
		<tr>
			<td><select name="id[]" id="id" multiple="multiple" size="10" style="width: 99%;" onchange="checkselected('id', 'editbtn')"  ondblclick="document.form1.act.value='edit'; document.form1.submit();" title="Result window / list of IPs"></select></td>			
		</tr>
	</table>

	<br/><input disabled="disabled" type="button" id="editbtn" class="icon_edit" value="Edit selected" onclick="document.form1.act.value='edit'; document.form1.submit();" title="Edit selected object above (or double click on ip)"/>
	<input type="button"  class="icon_edit" value="Batch edit" onclick="selectAll('id'); document.form1.act.value='editbatch'; document.form1.submit();" title="Edit multiple objects" />
	<input type="submit" class="icon_new" value="new" onclick="this.form.act.value='editnew'; this.form.ids.value=''">

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

function showBatcheditform($db, $id, $ofcObj, $vrfObj, $typeObj, $parentsObj)
{
	echo "<h2 class=\"edit\">Edit multiple objects</h2>";
	?>
    <div id="editform">
    	<table>
    		<tr>
    			<th>office</th>
    			<td>
    			<select name="office_edit">
    			<option value="">&nbsp;</option>
    			<?php foreach($ofcObj->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>"><?php echo $v?></option>
    			<?php }?>
	   			</select>
    			</td>
    		</tr>
    		<tr>
    			<th>vrf</th>
    			<td>
    			<select name="vrf_edit">
    			<option value="">&nbsp;</option>
    			<?php foreach($vrfObj->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>"><?php echo $v?></option>
    			<?php }?>
	   			</select>
    			</td>
    		</tr>
    		<tr>
    			<th>type</th>
    			<td>
    			<select name="type">
    			<option value="">&nbsp;</option>
    			<?php foreach($typeObj->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>"><?php echo $v?></option>
    			<?php }?>
	   			</select>
    			</td>
    		</tr>
    		<tr>
    			<th>parent</th>
    			<td>
    			<select name="parent">
    			<option value="">&nbsp;</option>
    			<?php foreach($parentsObj->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>"><?php echo $v?></option>
    			<?php }?>
	   			</select>
    			</td>
    		</tr>
    		<tr>
    			<th>comment</th>
    			<td><input type="text" size="80" name="comment" value="" /></td>
    		</tr>
    		<tr>
    			<td>&nbsp;</td>
    			<td>
    			<br/>
    			<input type="submit" class="icon_save" value="save" onclick="this.form.act.value='savebatch'" />&nbsp;
    			<input type="submit" class="icon_cancel" value="cancel" />&nbsp;
    			</td>
    			
    		</tr>
    	</table>
    </div>
    <hr noshade>
	<?php
}

function showEditform($db, $id, $ofcObj, $vrfObj, $typeObj, $parentsObj)
{
	$sql = "select ".TBL_OBJECT.".*, ".TBL_OFFICE.".name as officename ";
	$sql.= "from ".TBL_OBJECT." ";
	$sql.= "left join ".TBL_OFFICE." on ".TBL_OBJECT.".office=".TBL_OFFICE.".id ";
	$sql.= "where ".TBL_OBJECT.".id='".$id."'";
	
	if($rs = $db->execute($sql))
	{
		$loaded = true;
	}
	else
	{
		$loaded = false;
	}

	$tm = new tablemagic($db);
	$tm->setTable(TBL_OBJECT);
	if($loaded)
	{
		echo "<h2 class=\"edit\">Edit object</h2>";
	}
	else
	{
		echo "<h2 class=\"edit\">Create new object</h2>";
	}
	?>
    <div id="editform">
    	<table>
    	<tr>
    	<td>
    	<table>
    		<tr>
    			<th>id</th>
    			<td><input name="id" class="disabled" readonly="readonly" type="text" value="<?php echo $rs->getColumn("id")?>" /></td>
    		</tr>
    		<tr>
    			<th>ip</th>
    			<td><input type="text" name="ip" id="ip" onblur="getHost(this.form.ip.value);" value="<?php echo $rs->getColumn("ip")?>" /></td>
    		</tr>
    		<tr>
    			<th>name</th>
    			<td><input type="text" name="name" id="name" value="<?php echo $rs->getColumn("name")?>" /></td>
    		</tr>
    		<tr>
    			<th>office</th>
    			<td>
    			<select name="office_edit">
    			<option value="NULL">&nbsp;</option>
    			<?php foreach($ofcObj->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>" <?php if($k==$rs->getColumn("office")) echo 'selected="selected"'?>><?php echo $v?></option>
    			<?php }?>
	   			</select>
    			</td>
    		</tr>
    		<tr>
    			<th>vrf</th>
    			<td>
    			<select name="vrf_edit">
    			<option value="NULL">&nbsp;</option>
    			<?php foreach($vrfObj->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>" <?php if($k==$rs->getColumn("vrf")) echo 'selected="selected"'?>><?php echo $v?></option>
    			<?php }?>
	   			</select>
    			</td>
    		</tr>
    		<tr>
    			<th>type</th>
    			<td>
    			<select name="type" id="type">
    			<option value="NULL">&nbsp;</option>
    			<?php foreach($typeObj->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>" <?php if($k==$rs->getColumn("type")) echo 'selected="selected"'?>><?php echo $v?></option>
    			<?php }?>
	   			</select>
    			</td>
    		</tr>
    		<tr>
    			<th>parent</th>
    			<td>
    			<select name="parent">
    			<option value="">&nbsp;</option>
    			<?php foreach($parentsObj->_names as $k=>$v) {?>
    				<option value="<?php echo $k?>" <?php if($k==$rs->getColumn("parent")) echo 'selected="selected"'?>><?php echo $v?></option>
    			<?php }?>
	   			</select>
    			</td>
    		</tr>
    	</table>
    	</td>
    	<td>
	    	<table>
	    		<tr>
	    			<th>status</th>
	    			<td>
	    				<?php
	    					$style="";
	    					if($rs->getColumn("status")==STATUS_UP) $style="background-color:#aaffaa;"; 
	    					if($rs->getColumn("status")==STATUS_DISABLED) $style="background-color:#ffffaa;"; 
	    					if($rs->getColumn("status")==STATUS_DOWN) $style="background-color:#ffaaaa;"; 
	    				?>
		    			<select name="status" style="<?php echo $style?>">
		    			<option value="0" <?php if($rs->getColumn("status")==STATUS_UP) echo 'selected="selected"'?> style="background-color:#aaffaa;">UP</option>
		    			<option value="<?php echo STATUS_DISABLED?>" <?php if($rs->getColumn("status")==STATUS_DISABLED) echo 'selected="selected"'?> style="background-color:#ffffaa;">DISABLED</option>
		    			<option value="<?php echo STATUS_DOWN?>" <?php if($rs->getColumn("status")==STATUS_DOWN) echo 'selected="selected"'?> style="background-color:#ffaaaa;">DOWN</option>
			   			</select>
	    			</td>
	    		</tr>
	    		<tr>
	    			<th>changed</th>
	    			<td><input  type="text" name="statuschanged" value="<?php echo $rs->getColumn("statuschanged")?>" /></td>
	    		</tr>
	    		<tr>
	    			<th>accesstime</th>
	    			<td><input class="disabled" readonly="readonly" type="text" name="accesstime" value="<?php echo $rs->getColumn("accesstime")?>" /></td>
	    		</tr>
	    		<tr>
	    			<th>successful</th>
	    			<td><input  class="disabled" readonly="readonly" type="text" name="succesful" value="<?php echo $rs->getColumn("successful")?>" /></td>
	    		</tr>
	    		<tr>
	    			<th>failed</th>
	    			<td><input  class="disabled" readonly="readonly" type="text" name="failed" value="<?php echo $rs->getColumn("failed")?>" /></td>
	    		</tr>
	    		<tr>
	    			<th>comment</th>
	    			<td><input type="text" size="80" name="comment" value="<?php echo $rs->getColumn("comment")?>" /></td>
	    		</tr>
	    		<tr><td colspan="2">&nbsp;</td></tr>
	    		<tr>
	    			<td colspan="2" nowrap>
	    			<input type="submit" class="icon_save" value="save" onclick="this.form.act.value='save'" />&nbsp;
	    			<?php if($loaded){?>
	    			<input type="submit" class="icon_delete" value="delete" onclick="if(okDel('object')) {this.form.act.value='del'} else return false;" />&nbsp;
					<?php }?>
	    			<input type="submit" class="icon_cancel" value="cancel" />&nbsp;
	    			</td>
	    			
	    		</tr>
	    	</table>
    	</td>
    	</tr>
    	</table>
    </div>
    <hr noshade>
    <?php
	
}

?>
