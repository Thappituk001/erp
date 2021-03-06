<?php
class lend
{

	public $id_order;
	public $id_zone;

	public function __construct($id = '')
	{
		if( $id != '')
		{
			$this->getData($id);
		}
	}



	public function getData($id)
	{
		$qs = dbQuery("SELECT * FROM tbl_order_lend WHERE id_order = '".$id."'");
		if( dbNumRows($qs) == 1)
		{
			$rs = dbFetchArray($qs);
			foreach ($rs as $key => $value)
			{
				$this->$key = $value;
			}
		}
	}



	public function add(array $ds = array())
	{
		$sc = FALSE;
		if( !empty($ds))
		{
			$fields = "";
			$values = "";
			$i      = 1;
			foreach($ds as $field => $value)
			{
				$fields .= $i == 1 ? $field : ", " .$field;
				$values .= $i == 1 ? "'".$value."'" : ", '".$value."'";
				$i++;
			}

			$sc = dbQuery("INSERT INTO tbl_order_lend (".$fields.") VALUES (".$values.")");
		}

		return $sc;
	}



	public function update($id, array $ds = array())
	{
		$sc = FALSE;
		if( !empty($ds))
		{
			$set = "";
			$i   = 1;
			foreach($ds as $field => $value)
			{
				$set .= $i == 1 ? $field . " = '".$value."'" : ", ".$field ." = '".$value."'";
				$i++;
			}

			$sc = dbQuery("UPDATE tbl_order_lend SET ".$set." WHERE id_order = '".$id."'");
		}

		return $sc;
	}



	public function addDetail($id_order, $id_product, array $ds = array())
	{
		$sc = FALSE;
		if( !empty($ds))
		{
			if( $this->isExists($id_order, $id_product) === TRUE)
			{
				$sc = $this->updateDetail($id_order, $id_product, $ds['qty']);
			}
			else
			{
				$sc = $this->insertDetail($ds);
			}
		}

		return $sc;
	}





	public function insertDetail(array $ds = array())
	{
		$sc = FALSE;
		if( !empty($ds))
		{
			$fields = "";
			$values = "";
			$i      = 1;
			foreach($ds as $field => $value)
			{
				$fields .= $i == 1 ? $field : ", " .$field;
				$values .= $i == 1 ? "'".$value."'" : ", '".$value."'";
				$i++;
			}

			$sc = dbQuery("INSERT INTO tbl_order_lend_detail (".$fields.") VALUES (".$values.")");
		}

		return $sc;
	}





	public function updateDetail($id_order, $id_product, $qty)
	{
	 	return dbQuery("UPDATE tbl_order_lend_detail SET qty = qty + ". $qty." WHERE id_order = '".$id_order."' AND id_product = '".$id_product."'");
	}






	public function isExists($id_order, $id_product)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id FROM tbl_order_lend_detail WHERE id_order = ".$id_order." AND id_product = '".$id_product."'");
		if( dbNumRows($qs) > 0)
		{
			$sc = TRUE;
		}

		return $sc;
	}





	public function getLendQty($id_order, $id_product)
	{
		$sc = 0;
		$qs = dbQuery("SELECT qty FROM tbl_order_lend_detail WHERE id_order = '".$id_order."' AND id_product = '".$id_product."'");
		if( dbNumRows($qs) == 1)
		{
			list( $sc ) = dbFetchArray($qs);
		}

		return $sc;
	}


	public function getReturnedQty($id_order, $id_product)
	{
		$sc = 0;
		$qs = dbQuery("SELECT received FROM tbl_order_lend_detail WHERE id_order = '".$id_order."' AND id_product = '".$id_product."'");
		if( dbNumRows($qs) == 1)
		{
			list( $sc ) = dbFetchArray($qs);
		}

		return $sc;
	}




}	//---	End class

 ?>
