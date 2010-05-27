<?php

if(!defined("_mysql_dbw")) {
define("_mysql_dbw",1);

require("mysql_recordset.class.php");

/** 
 * MySQL database wrapper - a simple wrapper to make developement easier.
 *
 * REQUIRES mysql_recordset.class.php in the same directory as this file.
 *
 * Usage example: 
 *
 *  $db = new mysql_dbw();
 *  $db->logon("localhost", "myuser", "mypassword", "mydb") or die("Cannot logon to database");
 *  if($rs = $db->execute("SELECT * FROM users"))
 *  {
 *     while(!$rs->EOF)
 *     {
 *        echo "<br>".$rs->getColumn('firstname');
 *        $rs->nextRow();
 *     }
 *  }
 *  else echo $db->getLastError();
 *  
 *
 * @author Peter Nolin 2005
 * @since 2005-05
 */
class mysql_dbw {

	// Public variables
	var $hostname="";
	var $username="";
	var $password="";
	var $database="";
	
	// "Private" - as if was possible :)
	var $_connection = -1;
	var $_lastError = "";
	var $_queryId = -1;
	var $_tempResultObj ="";

	var $_selectcount = 0;
	var $_updatecount = 0;

	/**
	 * Konstructor
	 *
	 */
	function mysql_dbw() 
	{
	}


	// ===================================================================
	// PUBLIC METHODS
	// ===================================================================


	/**
	 * logon
	 * Makes the connection to the database
	 *
	 * @param host	string	"hostname"
	 * @param user	string	"Username for database"
	 * @param password	string "The database password"
	 * @param database	string "The database name"
	 * @return boolean
	 */
	function logon($host="", $user="", $password="", $database="")
	{
		if ($host!="") {
			$this->hostname=$host;
		}
		if ($user!="") {
			$this->username=$user;
		}
		if ($password!="") {
			$this->password=$password;
		}
		if ($database!="") {
			$this->database=$database;
		}


		if( $this->_connection=@mysql_connect($this->hostname,$this->username,$this->password))
		{
			if($this->database && $this->_connection)
			{
				$temp=@mysql_select_db($this->database);
				if(!$temp)
				{
					@mysql_close($this->_connection);
					return false;
				}
			}

			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 *
	 */
	function execute($query)
	{
		if(!@mysql_select_db($this->database, $this->_connection))
		{
			return false;
		}
		
		if($this->_queryId = @mysql_query($query, $this->_connection))
		{
			//$tempid = mysql_insert_id($this->_queryId);
			if (eregi("^(insert|update|delete|commit|create table|create temporary table|drop table|load|truncate)",$query)) 
			{
				$this->_updatecount++;
				 // whatever you'd like to do if the query is insert, update or delete
				 $sdu=1;
			} else {
				 // and this for select statements
				 $sdu=0;
				$this->_selectcount++;
			}
	
			return new mysql_recordset($this->_connection, $this->_queryId, $sdu);
		} 
		else 
		{
			return false;
		}
	}


	/**
	 *
	 */
	function getLastError()
	{
		if($this->_connection!='')
		{
			$this->_lastError=@mysql_error($this->_connection);
		}
		else 
		{
			$this->_lastError=@mysql_error();
		}

		return $this->_lastError;
	}
	
	/**
	 *
	 */
	function logoff()
	{
		return @mysql_close($this->_connection);
	}


	/**
	 *
	 */
	function getConnection()
	{
		$this->_connection;
	}


	/**
	 *
	 */
	function getQueryCount($type='')
	{
		switch($type)
		{
			case 'select':
				$value = $this->_selectcount;
				break;

			case 'update':
				$value = $this->_updatecount;
				break;

			default: 
				$value = $this->_selectcount + $this->_updatecount;
				break;
		}

		return $value;
	}

	
	
	/**
	 * selectDB
	 * Possibility to choose database incase you didnt do it with logon()
	 *
	 * @param string	namn	"The database name"
	 * @returns boolean	"True if succeed"
	 */
	function selectDB($namn)
	{
		$this->database=$namn;
		if($this->_connection)
		{
			return @mysql_select_db($this->database);
		} 
		else 
		{
			return false;
		}
	}


	// ===================================================================
	// PRIVATE METHODS
	// ===================================================================


}
}
?>