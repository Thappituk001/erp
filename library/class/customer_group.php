<?php
class customer_group 
{
	public $id;
	public $code;
	public $name;
	public function __construct($id = '')
	{
		if( $id != '' )
		{
			$qs = dbQuery("SELECT * FROM tbl_customer_group WHERE id = ".$id);
			if( dbNumRows($qs) == 1 )
			{
				$rs 				= dbFetchObject($qs);
				$this->id 		= $rs->id;
				$this->code 	= $rs->code;
				$this->name	 	= $rs->name;	
			}
		}
	}
	
	public function add(array $ds)
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			if( $this->isExists($ds['code']) === FALSE )
			{
				$sc = dbQuery("INSERT INTO tbl_customer_group (code, name) VALUES ('".$ds['code']."', '".$ds['name']."')");
			}
		}
		
		return $sc;
	}
	
	
	public function update($code, array $ds)
	{
		return dbQuery("UPDATE tbl_customer_group SET name = '".$ds['name']."' WHERE code = '".$code."'");	
	}
	
	
	public function isExists($code)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT * FROM tbl_customer_group WHERE code = '".$code."'");
		if( dbNumRows($qs) > 0 )
		{
			$sc = TRUE;
		}
		return $sc;
	}
	
	public function delete($id)
	{
		return dbQuery("DELETE FROM tbl_customer_group WHERE id = ".$id);
	}
	
	public function hasMember($id)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id_customer FROM tbl_customers WHERE group_id = ".$id);
		if( dbNumRows($qs) > 0 )
		{
			$sc = TRUE;
		}
		return $sc;
	}
	
	public function countMember($id)
	{
		$qs = dbQuery("SELECT COUNT(*) FROM tbl_customers WHERE group_id = ".$id);
		list( $sc ) = dbFetchArray($qs);
		return  $sc;			
	}
	
	public function getGroupId($code)
	{
		$sc = 0;
		$qs = dbQuery("SELECT id FROM tbl_customer_group WHERE code = '".$code."'");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);	
		}
		return $sc;
	}
}

?>