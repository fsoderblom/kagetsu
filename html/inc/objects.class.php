<?php

if(!defined("_objects")) {
define("_objects",1);

class objects 
{

	var $_dbm; // holds an instance of mysql_dbw. The only constructorparam.
	var $_names = array();
	var $_numOfObjects = 0;
	var $_pixelsize = 13;
	var $_imageHeight = 0;
	var $_imageWidth = 0;

	
	/**
	 * constructor
	 */
	function objects($dbm)
	{
		$this->_dbm = $dbm;
		
		if($rs = $dbm->execute("select count(id) as numOfObjects from ".TBL_OBJECT.""))
		{
			if(!$rs->EOF)
			{
				$this->_numOfObjects = $rs->getColumn("numOfObjects");
			}
		}
	}

	// =========================================================================
	// PUBLIC
	// =========================================================================
	function getObjectCount()
	{
		return $this->_numOfObjects;
	}
  
	function getPixelSize()
	{
		return $this->_pixelsize;
	}
	function getImageWidth()
	{
		if($this->_imageWidth==0)
		{
			$this->_imageWidth=ceil(sqrt($this->getObjectCount()))*$this->getPixelSize();
		}
		return $this->_imageWidth;
	}
	function getImageHeight()
	{
		if($this->_imageHeight==0)
		{
			$this->_imageHeight=ceil(sqrt($this->getObjectCount()))*$this->getPixelSize();
		}
		return $this->_imageHeight;
	}

	function getObjectStatuses()
	{
		if($rs = $this->_dbm->execute("select status from ".TBL_OBJECT." order by office,ip"))
		{
			if(!$rs->EOF)
			{
				return $rs;
			}
		}
		
		return false;
	}

	function getObject($nr)
	{
		if($rs = $this->_dbm->execute("select * from ".TBL_OBJECT." order by office,ip limit $nr,1"))
		{
			if(!$rs->EOF)
			{
				return $rs;
			}
		}
		
		return false;
	}
	
	function getObjectNumberAt($xpos,$ypos)
	{
		$objnr = 0;
		
		for($y=$this->getPixelSize(); $y<=$this->getImageHeight(); $y+=$this->getPixelSize())
		{
			for($x=$this->getPixelSize(); $x<=$this->getImageWidth(); $x+=$this->getPixelSize())
			{
				if($x>$xpos && $y>$ypos)
				{
					return $objnr;
				}

				$objnr++;
			}
		}

		return $objnr;
	}


	function getObjectXY($nr)
	{
		$objnr = 0;
		$x=0;
		
		for($y=2; $y<=$this->getImageHeight(); $y+=$this->getPixelSize())
		{
			for($x=2; $x<=$this->getImageWidth(); $x+=$this->getPixelSize())
			{
				if($objnr==$nr)
				{
					return array($x, $y);
				}

				$objnr++;
			}
			
		}
		
		return false;
	}

	// =========================================================================
	// PRIVATE (not meant for direct use)
	// =========================================================================


} // end class
} // end if defined
?>