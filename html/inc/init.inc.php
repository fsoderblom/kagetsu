<?php 

if(!defined("_init")) 
{
	define("_init",1);

	// Trim all input
	foreach($_POST as $k=>$v)
	{
		$_POST[$k] = trim($v);
	}
	foreach($_GET as $k=>$v)
	{
		$_GET[$k] = trim($v);
	}

	// Commonly used input
	$id = isset($_GET['id']) && trim($_GET['id'])!='' ? trim($_GET['id']) : '';
	$id = isset($_POST['id']) && trim($_POST['id'])!='' ? trim($_POST['id']) : $id;
	$id = is_numeric($id) ? $id : '';

	$act = isset($_GET['act']) && trim($_GET['act'])!='' ? trim($_GET['act']) : '';
	$act = isset($_POST['act']) && trim($_POST['act'])!='' ? trim($_POST['act']) : $act;

	session_start();

	// Imports
	include('inc/mysql_dbw.class.php');
	include('inc/tablemagic.class.php');
	include('inc/office.class.php');
	include('inc/vrf.class.php');
	include('inc/type.class.php');
	include('inc/parents.class.php');

	define("KAGETSU", "Kagetsu v1.0.3");

	// Settings
	define("DB_HOST", "localhost");					// Databasens adress / IP nr (Prod)
	define("DB_USER", "<user>");					// Användarnamn för databasen
	define("DB_PASS", "<password>");				// Lösenord för databasen
	define("DB_NAME", "kagetsu");					// Databasen
	define("SITE_URL", "https://host.name.domain.cc/");  // VIKTIG!!!!

	define("TBL_OBJECT", "object");
	define("TBL_EVENT", "event");
	define("TBL_LOG", "log");
	define("TBL_OFFICE", "office");
	define("TBL_SUBNET", "subnet");
	define("TBL_TYPE", "type");
	define("TBL_VRF", "vrf");

	define("EVT_NODE_FAILURE", 101);
	define("EVT_NODE_DOWN", 102);
	define("EVT_NODE_RECOVERED", 103);
	define("EVT_NODE_RECOVERED_FROM_FAILURE", 105);
	define("STATUS_DOWN", 999);
	define("STATUS_DISABLED", 998);
	define("STATUS_UP", 0);

	// ----------------------------
	// FIRE UP THE DATABASE 
	// ----------------------------
	$db = new mysql_dbw();
	$db->logon(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('<p style="color:red">DB ERROR::'.$db->getLastError().'</p>');



	//==========================================================================
	// ENDAST FUNKTIONER NEDANFÖR DENNA RAD
	//==========================================================================


	function ip_network_mask($ip, $net_addr, $net_mask)
	{ 
		if($net_mask <= 0)
		{ 
		    return false; 
		}
		 
		$ip_binary_string = sprintf("%032b",ip2long($ip)); 
		$net_binary_string = sprintf("%032b",ip2long($net_addr)); 
		return (substr_compare($ip_binary_string,$net_binary_string,0,$net_mask) === 0); 
	} 

	function ip_network($ip, $network)
	{ 
		list($net_addr, $net_mask) = explode("/", $network);
		return ip_network_mask($ip, $net_addr, $net_mask);
	} 

	function datum($s)
	{
		echo strftime('%e %b %G', strtotime(substr($s,0,16)));	
	}
	
	function datumtid($s)
	{
		echo strftime('%R den %e %b %G', strtotime(substr($s,0,16)));
	}
	
	function forumtid($dat, $override=false)
	{
		if($override)
			return strftime('%R den %e %b %G', strtotime(substr($dat,0,16)));

		$y = substr($dat, 0, 4);
		$m = substr($dat, 5, 2);
		$d = substr($dat, 8, 2);
		$h = substr($dat, 11, 2);
		$i = substr($dat, 14, 2);

		$fd = mktime(0,0,0,$m,$d,$y);
		$fi = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
		$ff = mktime(0,0,0,date("m"),date("d")-2,date("Y"));
		if($fd>$fi)
			return "Idag $h:$i";
		else if($fd<=$ff)
			return date("d M", $fd);
		else 
			return htmlentities("Igår");
	}

	function txt($text)
	{
		return htmlspecialchars(stripslashes($text));
	}
}
?>
