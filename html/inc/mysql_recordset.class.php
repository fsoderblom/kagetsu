<?php
/** 
 * MySQL database wrapper - a simple wrapper to make developement easier.
 * This file is used by mysql_dbw.class.php
 *
 * @author Peter Nolin 2005
 * @since 2005-05
 */
class mysql_recordset 
{

	// public
	var $BOF = true;
	var $EOF = true;	

	// private
	var $_connection=-1;
	var $_rowCount=-1;
	var $_columnCount=-1;
	var $_currentRow=-1;

	var $_queryId=-1;
	var $_records=array();	
	

	/**
	 * Constructor
	 */
	function mysql_recordset($connection, $queryid, $sdu)
	{
		$this->_connection=$connection;
		$this->_queryId=$queryid;

		if($queryid)
		{
			if ($sdu==1) 
			{
		 		// whatever you'd like to do if the query is insert, update or delete
				$this->_rowCount=0;
				$this->_columnCount=0;
			} 
			else 
			{
			 // and this for select statements
				$this->_rowCount=mysql_num_rows($this->_queryId);
				$this->_columnCount=mysql_num_fields($this->_queryId);
			}

		} 
		else 
		{
			$this->_rowCount=0;
			$this->_columnCount=0;
		}
		if($this->_rowCount>0 && $this->_currentRow==-1)
		{
			$this->_currentRow=0;
			$this->_records=mysql_fetch_array($this->_queryId);
			$this->EOF=false;
			$this->BOF=true;
		}
		
		return $this->_queryId;
	}


	// ===================================================================
	// PUBLIC METHODS
	// ===================================================================
	

	/**
	 * getColumn - Returns the value of a specific column using a stringname.
	 *
	 * Example: $name = $rs->getColumn["FIRST_NAME"]; // $name = "kalle"
	 */
	function getColumn($columnname)
	{
		return $this->_records[$columnname];
	}


	/**
	 * getColumnName - Returns the COLUMNNAME from a position. You might need this
	 * to use getColumn() if you don't know the column name, only the order.
	 *
	 * Exampe: $columnname = $rs->getColumnName(0); // $columnname = "ID";
	 */
	function getColumnName($columnnumber)
	{
		return mysql_field_name($this->_queryId, $columnnumber);
	}


	/**
    * firstRow - sets the recordpointer to the first record (default action on query)
	 */
	function firstRow()
	{
		if($this->getRowCount() > 0)
		{
			$this->_records = array();
			$this->_currentRow=0;
			
			if(mysql_data_seek($this->_queryId,$this->_currentRow)) 
			{
				$this->_records=mysql_fetch_array($this->_queryId);
				$this->EOF=false;
				if($this->_records)
				{
					return true;
				}
			}
		}
		
		$this->EOF=true;
		return false;
	}

	
	/**
	 * lastRow - sets the recordpointer to the last record.
	 */
	function lastRow()
	{
		if($this->getRowCount() > 0)
		{
			$this->_records=array();
			$temp=$this->getRowCount()-1;			
			$tempResult=mysql_data_seek($this->_queryId,$temp);
			
			if($tempResult) 
			{
				$this->_currentRow=$temp;
				$this->_records=mysql_fetch_array($this->_queryId);
				
				if($this->_records)
				{
					$this->EOF = false;
					return true;
				}
			}
		}
		
		$this->EOF=true;
		return false;
	}


	/**
	 * nextRow - sets the recordpointer one step forward. If already on the last row EOF is set true.
	 */
	function nextRow()
	{
		if($this->getRowCount()>0)
		{
			$this->_records=array();
			$this->_currentRow++;
			$this->_records=mysql_fetch_array($this->_queryId);
			if($this->_records)
			{
				$this->_checkEOF($this->_currentRow-1);
				return true;
			}
		}
		
		$this->EOF=true;
		return false;
	}
	

	/**
	 * prevRow - sets the recordpointer one step back. If already on the first row BOF is set true.
	 */
	function prevRow()
	{
		if($this->getRowCount()>0)
		{
			$this->_currentRow--;
			if($this->_currentRow<0)
			{
				$this->BOF=true;
				return false;
			}
			return $this->moveRow($this->_currentRow);
		}

		$this->EOF=false;

		return false;		
	}
	

	function moveRow($row=0)
	{
		if($row==0)
		{
			return $this->firstRow();			
		} 
		else if($row==($this->getRowCount() -1))
		{
			return $this->lastRow();			
		}

		if($this->getRowCount()>0 && $row<$this->getRowCount())
		{
			$this->_currentRow=$row;
			if(mysql_data_seek($this->_queryId,$this->_currentRow))
			{
				$this->_records=mysql_fetch_array($this->_queryId);

				if($this->_records)
				{
					$this->EOF=false;

					return true;
				}
			}
		}

		$this->EOF=true;

		return false;
	}


	/**
	 * getRowCount - returns the number of rows in this recordset
	 */
	function getRowCount()
	{
		return $this->_rowCount;
	}
	

	/**
	 * getColumnCount - returns the number of columns in this recordset
	 */
	function getColumnCount()
	{
		return $this->_columnCount;
	}

	
	/**
	 * getLastInsertId - returns the last unique primary key (insert id)
	 */
	function getLastInsertId()
	{
		return mysql_insert_id($this->_connection);
	}


	/**
	 *
	 */
	function getLastError()
	{
		$this->_lastError=mysql_error($this->_connection);
		return $this->_lastError;
	}


	/**
	 * dump - display a nice tree using print_r() to dump all data in recordset
	 * Should be used for debug only.
	 */
	function dump()
	{
	}

	// ===================================================================
	// PRIVATE METHODS
	// ===================================================================

	/**
	 *
	 */
	function _checkEOF($currentrow)
	{
		if($currentrow>=($this->_rowCount-1)){
			$this->EOF=true;
		}
		else {
			$this->EOF=false;
		}
	}

}

?>