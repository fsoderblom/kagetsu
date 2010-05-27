<?php

if(!defined("_type")) {
define("_type",1);

class type 
{
	var $_dbm; // holds an instance of mysql_dbw. The only constructorparam.
	var $_names = array();
	
	/**
	 * constructor
	 */
	function type($dbm)
	{
		$this->_dbm = $dbm;
		if($rs = $dbm->execute("select * from ".TBL_TYPE." order by type"))
		{
			while(!$rs->EOF)
			{
				$this->_names[$rs->getColumn("id")] = $rs->getColumn("type");
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

	// =========================================================================
	// PRIVATE (not meant for direct use)
	// =========================================================================


} // end class
} // end if defined
?>