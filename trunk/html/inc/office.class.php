<?php

if(!defined("_office")) {
define("_office",1);

class office 
{
	var $_dbm; // holds an instance of mysql_dbw. The only constructorparam.
	var $_names = array();
	
	/**
	 * constructor
	 */
	function office($dbm)
	{
		$this->_dbm = $dbm;
		if($rs = $dbm->execute("select * from ".TBL_OFFICE." order by name"))
		{
			while(!$rs->EOF)
			{
				$this->_names[$rs->getColumn("id")] = $rs->getColumn("name");
				$rs->nextRow();
			}
		}
	}

	// =========================================================================
	// PUBLIC
	// =========================================================================
	function getName($id)
	{
		return $this->_names[$id];
	}

	function getNames()
	{
		return $this->_names;
	}

	// =========================================================================
	// PRIVATE (not meant for direct use)
	// =========================================================================


} // end class
} // end if defined
?>