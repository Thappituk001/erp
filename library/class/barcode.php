<?php
class barcode {

public function __construct(){}

public function add(array $ds)
{
	$sc = FALSE;
	if( count($ds) > 0 )
	{
		$fields = "";
		$values = "";
		$i = 1;
		foreach($ds as $field => $value )
		{
			$fields .= $i == 1 ? $field : ", ".$field;
			$values .= $i == 1 ? "'".$value."'" : ", '".$value."'";
			$i++;
		}
		$sc = dbQuery("INSERT INTO tbl_barcode (".$fields.") VALUES (".$values.")");
	}

	return $sc;
}


public function update($id, array $ds)
{
	$sc = FALSE;
	if( count( $ds ) > 0 )
	{
		$set = "";
		$i = 1;
		foreach( $ds as $field => $value )
		{
			$set .= $i== 1 ? $field ." = '".$value."'" : ", ".$field . " = '".$value."'";
			$i++;
		}
		$sc = dbQuery("UPDATE tbl_barcode SET ".$set." WHERE id = '".$id."'");
	}

	return $sc;
}


public function delete($id)
{
	return dbQuery("DELETE FROM tbl_barcode WHERE id = '" .$id."'");
}




public function isExists($id)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT barcode FROM tbl_barcode WHERE id = '".$id."'");
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}




public function getBarcode($id_pd)
{
	$sc = "";
	$qs = dbQuery("SELECT barcode FROM tbl_barcode WHERE id_product = '".$id_pd."' AND unit_qty = 1 LIMIT 1")	;
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}



public function getBarcodes($id_pd)
{
	return dbQuery("SELECT * FROM tbl_barcode WHERE id_product = '".$id_pd."'");
}


public function getDetail($barcode)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT * FROM tbl_barcode WHERE barcode = '".$barcode."'");
	if( dbNumRows($qs) == 1 )
	{
		$sc = dbFetchObject($qs);
	}
	return $sc;
}

}//// end class



?>
