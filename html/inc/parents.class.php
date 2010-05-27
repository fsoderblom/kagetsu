<?php

if(!defined("_parents")) {
define("_parents",1);

class parents 
{
	var $_dbm; // holds an instance of mysql_dbw. The only constructorparam.
	var $_names = array();
	
	/**
	 * constructor
	 */
	function parents($dbm)
	{
		$this->_dbm = $dbm;
		if($rs = $dbm->execute("select * from ".TBL_OBJECT." where name!='' order by name"))
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

	// =========================================================================
	// PRIVATE (not meant for direct use)
	// =========================================================================


} // end class
} // end if defined
?>