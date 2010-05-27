<?php

if(!defined("_tablemagic")) {
define("_tablemagic",1);

/**
 * Class tablemagic - a class for extremely easy access to a single row in a table.
 * 
 * This class is still "proof of concept" and should not be considered ready for 
 * large scale applications or advanced tables. 
 *
 * @requires mysql_dbw.class.php (C) Peter Nolin
 * @requires mysql_recordset.class.php (C) Peter Nolin
 * @author Peter Nolin 2007
 * @version 1.3 2007-10
 *
 * TODO:
 *
 *  - KLAR fixa att endast kolumner man satt/rört faktiskt är med i UPDATE satsen.
 *
 *  - KLAR Autodetect på primary key eller autoinc kolumnen.
 *
 *  - KLAR id kolumnen tas för givet ligga på plats 0 tydligen i vissa lägen. Fixa!
 *
 *  - KLAR Gör add- och stripslashes optional
 *
 * PHP4+ SAFE
 */
class tablemagic 
{
	var $_dbm; // holds an instance of mysql_dbw. The only constructorparam.

	// settings
	var $_idcol = ""; // default name for the id column / autoinc / primary key / unique col
	var $_slashed = true; // use addslashes and stripslashes in the querys?

	// flat vars
	var $_id; // holds the current id (id for loaded row)
	var $_table;
	var $_lastError;

	// arrayed vars
	var $_fields; // holds the value of each field (column) in the table for a row...
	var $_fieldnames; // .. holds a list of current fieldname (columnname)
	var $_fieldtypes; // .. and the types. All 3 have the same positions ofcourse!
	var $_ignorefields; // possibility to ignore some columns in the UPDATE query


	/**
	 * Construct
	 *
	 * @param $dbm - an object of mysql_dbw()
	 */
	function tablemagic($dbm)
	{
		$this->_dbm = $dbm;
		$this->_reset(); // clears and init the arrays.
	}

	// =========================================================================
	// PUBLIC
	// =========================================================================

	/**
	 * Load data for one entire database post. Requires that you have used setTable()
	 *
	 * @param id - the id you want to load
	 * @returns boolean (success or not)
	 */
	function load($id, $tablename="", $idcol="")
	{
		if($tablename!="")
		{
			$this->setTable($tablename, $idcol);
		}

		$this->_fields = array();

		if($this->_table!="" && $this->_idcol!="")
		{
			$sql = "SELECT * FROM ".$this->_table." WHERE ".$this->_idcol."='$id'";
			$this->_sql = $sql;

			if($rs = $this->_dbm->execute($sql))
			{
				if(!$rs->EOF)
				{
					$this->_id = $id;

					for($i=0; $i<count($this->_fieldnames); $i++)
					{
						if($this->_fieldtypes[$i] == 'text')
						{
							if($this->_slashed)
							{
								$this->_fields[$this->_fieldnames[$i]] = stripslashes("".$rs->getColumn($this->_fieldnames[$i]));
							}
							else
							{
								$this->_fields[$this->_fieldnames[$i]] = "".$rs->getColumn($this->_fieldnames[$i]);
							}
						}
						else
						{
							$this->_fields[$this->_fieldnames[$i]] = $rs->getColumn("".$this->_fieldnames[$i]);
						}

						// default ignore all fields for update. Remove from array when use of setColumn()
						$this->_ignorefields[] = $this->_fieldnames[$i];

					} // end for()

					return true;
				} // end EOF
			} // end execute()
		} // end main if()
		else
		{
			$this->_lastError = $this->_table != "" ? "IDCOL is not defined" : "TABLENAME is not defined";
		}

		$this->_id = "";		
		return false;
	} // end load()


	/**
	 * Saves a new or updates a post.
	 *
	 * @param boolean - false = does not do it for real. Just creates the SQL
	 * @returns boolean (success or not)
	 */
	function save($really=true)
	{
		$this->_sql = "";

		if($this->_id!="")
		{
			return $this->_update($really);
		}
		else
		{
			return $this->_save($really);
		}
	} // end save()


	/**
	 * Removes a post from the table (DELETE FROM)
	 */
	function kill()
	{
		if($this->_id!="")
		{
			$sql = "DELETE FROM ".$this->_table." WHERE ".$this->_idcol."='".$this->_id."'";
			$this->_sql = $sql;

			if($rs = $this->_dbm->execute($sql))
			{
				return true;
			}
		}

		return false;
	} // end kill()


	/**
	 * Sets table. Figures out dynamicly all fieldnames and types.
	 *
	 * @returns boolean (success or not)
	 */
	function setTable($tablename, $idcol="", $id="")
	{
		$this->_fields = array();
		$this->_fieldnames = array();
		$this->_fieldtypes = array();
		$this->_ignorefields = array();

		$sql = "DESCRIBE $tablename";
		$this->_sql = $sql;

		if($rs = $this->_dbm->execute($sql))
		{
			while(!$rs->EOF)
			{
				$this->_table = $tablename;
				$this->_fieldnames[] = $rs->getColumn('Field');

				// wheter or not to use add- and stripslashes. Setable with $this->_slashed
				$type = strtolower($rs->getColumn('Type'));
				if( strpos($type, "varchar")===false && strpos($type, "text")===false && strpos($type, "blob")===false)
				{
					$this->_fieldtypes[] = 'number';
				}
				else
				{
					$this->_fieldtypes[] = 'text';
				}

				// try to automaticly find out which column is the "id col"
				if($idcol == "" && (strtolower($rs->getColumn('Extra')) == "auto_increment" || strtolower($rs->getColumn('Key')) == "pri"))
				{
					$idcol = $rs->getColumn('Field');
				}

				$rs->nextRow();
			}

			if($idcol!="")
			{
				$this->setIdColumn($idcol);

				if($id!="")
				{
					return $this->load($id);
				}
			}

			return true;
		}

		return false;
	} // end setTable()


	/**
	 * Lets you have another name on the autoinc id column than the standard "id".
	 */
	function setIdColumn($s)
	{
		$this->_idcol = $s;
	}


	/**
	 * If you have a picture in a blob this is a good way to remove it from
	 * the update sql if you re-save a post.
	 */
	function dontUpdateField($fieldname)
	{
		$this->_ignorefields[] = $fieldname;
	}

	/**
	 * @returns - current loaded it or last insertid if we saved a new post.
	 */
	function getId()
	{
		return $this->_id;
	}


	/**
	 * @returns String - last database error that occured. Use this if you
	 * get false back from save() or other method that uses the database.
	 */
	function getLastError()
	{
		return $this->_lastError.$this->_dbm->getLastError();
	}


	/**
	 *
	 */
	function getColumnCount()
	{
		return count($this->_fields);
	}


	/**
	 *
	 */
	function getColumn($col)
	{
		if(is_numeric($col))
		{
			return $this->_fields[$this->_fieldnames[$col]];
		}
		else
		{
			return $this->_fields[$col];
		}
	} // end getColumn()


	/**
	 * alias for getColumn()
	 */
	function getField($col)
	{
		return $this->getColumn($col);
	}


	/**
	 *
	 */
	function setColumn($col, $val)
	{
		if(is_numeric($col))
		{
			$this->_fields[$this->_fieldnames[$col]] = $val;
			$this->_unignoreField($this->_fieldnames[$col]);
		}
		else
		{
			$this->_fields[$col] = $val;
			$this->_unignoreField($col);
		}
	} // end setColumn()


	/**
	 * alias for setColumn()
	 */
	function setField($col, $val)
	{
		$this->setColumn($col, $val);
	}


	/**
	 * Returns the last SQL string used to speak with the DB
	 */
	function getSQL()
	{
		return $this->_sql;
	}

	// =========================================================================
	// PRIVATE (not meant for direct use)
	// =========================================================================

	/**
	 * Creates an INSERT sql string and runs it.
	 *
	 * @returns boolean (success or not)
	 */
	function _save($really)
	{
		$sql = "INSERT INTO ".$this->_table." (".$this->_fieldnames[0];

		for($i=1; $i<count($this->_fieldnames); $i++)
		{
			$sql.= ",".$this->_fieldnames[$i];
		}
		$sql.= ") VALUES ('".$this->_fields[$this->_fieldnames[0]]."'";

		for($i=1; $i<count($this->_fieldnames); $i++)
		{
			if($this->_fieldtypes[$i] == 'text')
			{
				if($this->_slashed)
				{
					$sql.= ", '".addslashes($this->_fields[$this->_fieldnames[$i]])."'";
				}
				else
				{
					$sql.= ", '".$this->_fields[$this->_fieldnames[$i]]."'";
				}
			}
			else
			{
				$sql.= ", '".$this->_fields[$this->_fieldnames[$i]]."'";
			}
		}
		$sql.= ")";

		$this->_sql = $sql;

		if($really === true)
		{
			if($rs = $this->_dbm->execute($sql))
			{
				$this->_id = $rs->getLastInsertId();
				return true;
			}
		}

		return false;
	} // end PRIVATE _save()


	/**
	 * Creates an UPDATE sql string and runs it.
	 *
	 * @returns boolean (success or not)
	 */
	function _update($really)
	{
		if( count($this->_fieldnames) > count($this->_ignorefields))
		{
			$sql = "UPDATE ".$this->_table." SET ";
			for($i=0; $i<count($this->_fieldnames); $i++)
			{
				if(!in_array($this->_fieldnames[$i], $this->_ignorefields))
				{
					if($this->_fieldtypes[$i] == 'text')
					{
						if($this->_slashed)
						{
							$sql .= $this->_fieldnames[$i]."='".addslashes($this->_fields[$this->_fieldnames[$i]])."', ";
						}
						else
						{
							$sql .= $this->_fieldnames[$i]."='".$this->_fields[$this->_fieldnames[$i]]."', ";
						}
					}
					else
					{
						$sql .= $this->_fieldnames[$i]."='".$this->_fields[$this->_fieldnames[$i]]."', ";
					}
				}
			}
			$sql = substr($sql,0, strlen($sql)-2); // remove last comma
			$sql.= " WHERE ".$this->_idcol."='".$this->_id."'";

			$this->_sql = $sql;

			if($really === true)
			{
				if($this->_dbm->execute($sql))
				{
					return true;
				}
			}
		}
		else
		{
			$this->_lastError = "No field for update";
		}


		return false;
	} // end PRIVATE _update()


	/**
	 *
	 */
	function _reset()
	{
		unset($this->_id);
		unset($this->_idcol);
		unset($this->_table);

		$this->_fields = array();
		$this->_fieldnames = array();
		$this->_fieldtypes = array();
		$this->_ignorefields = array();
	} // end PRIVATE _reset()


	/**
	 * Removes a fieldname from the ignorelist. Called from setColumn()
	 */
	function _unignoreField($fieldname)
	{
		$tmparr = array();

		foreach($this->_ignorefields as $k=>$v)
		{
			if($v != $fieldname)
				$tmparr[] = $v;
		}

		$this->_ignorefields = $tmparr;
		// kolla på array_splice() metoden php.net

	} // end PRIVATE _unignoreField()

} // end class
} // end if defined
?>