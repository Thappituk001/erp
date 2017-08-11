<?php
class brand
{
	public $id;
	public $code;
	public $name;
	public $error;
	public function __construct($id = "" )
	{
		if( $id != "" )
		{
			$qs = dbQuery("SELECT * FROM tbl_brand WHERE id = '".$id."'");
			if( dbNumRows($qs) == 1 )
			{
				$rs = dbFetchObject($qs);
				$this->id		= $rs->id;
				$this->code	= $rs->code;
				$this->name	= $rs->name;
			}
		}
	}

	public function add(array $ds)
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$fields = "";
			$values = "";
			$i = 1;
			foreach( $ds as $field => $value )
			{
				$fields .= $i == 1 ? $field : ", ".$field;
				$values .= $i == 1 ? "'".$value."'" : ", '".$value."'";
				$i++;
			}
			$sc = dbQuery("INSERT INTO tbl_brand (".$fields.") VALUES (".$values.")");
			if( $sc === FALSE)
			{
				$this->error = "Insert Fail";
			}
		}
		return $sc;
	}

	public function update($id, array $ds)
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$set 	= "";
			$i		= 1;
			foreach( $ds as $field => $value )
			{
				$set .= $i == 1 ? $field . " = '" . $value . "'" : ", ".$field . " = '" . $value . "'";
				$i++;
			}
			$sc = dbQuery("UPDATE tbl_brand SET " . $set . " WHERE id = '".$id."'");
			if( $sc === FALSE)
			{
				$this->error = "Update fail";
			}
		}
		return $sc;
	}


	public function delete($id)
	{
		$sc = dbQuery("DELETE FROM tbl_brand WHERE id = '".$id."'");
		if( $sc === FALSE )
		{
			$this->error = 'Delete fail';
		}
		return $sc;
	}





	public function isExists($id)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id FROM tbl_brand WHERE id = '".$id."'");
		if( dbNumRows($qs) > 0 )
		{
			$sc = TRUE;
		}
		return $sc;
	}




}////

?>