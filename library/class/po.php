<?php
class po
{
	public $bookcode;
	public $code;
	public $reference;
	public $id_supplier;
	public $id_warehouse;
	public $credit_term;
	public $vat_type;
	public $vat_is_out;
	public $vat_amount;
	public $amount_ex;
	public $bill_discount;
	public $date_add;
	public $date_need;
	public $due_date;
	public $isCancle;
	public $status;
	
	public function __construct($reference = '')
	{
		if( $reference != '' )
		{
			$qs = dbQuery("SELECT * FROM tbl_po WHERE reference = '".$reference."' LIMIT 1");
			if( dbNumRows($qs) > 0 )
			{
				$rs = dbFetchObject($qs);
				$this->bookcode	= $rs->bookcode;
				$this->code 	= $rs->code;
				$this->reference	= $rs->reference;
				$this->id_supplier	= $rs->id_supplier;
				$this->id_warehouse	= $rs->id_warehouse;
				$this->credit_term		= $rs->credit_term;
				$this->vat_type		= $rs->vat_type; //ประเภทภาษี 1=7% , 2=1.5% , 3=0% , 4=ไม่มีVAT,5=10% 
				$this->vat_is_out	= $rs->vat_is_out; // Y = yes N = no
				$this->vat_amount	= $rs->vat_amount;
				$this->amount_ex	= $rs->amount_ex;
				$this->bill_discount	= $rs->bill_discount;
				$this->date_add	= $rs->date_add;
				$this->date_need	= $rs->date_need;
				$this->due_date	= $rs->due_date;
				$this->isCancle		= $rs->isCancle;
				$this->status		= $rs->status;
			}
		}
	}
	
	
	
	public function add( array $ds)
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$fields	= "";
			$values	= "";
			$i			= 1;
			foreach( $ds as $field => $value )
			{
				$fields 	.= $i == 1 ? $field : ", ".$field ;
				$values	.= $i == 1 ? "'".$value."'" : ", '".$value."'" ;
				$i++;
			}
			
			$sc = dbQuery("INSERT INTO tbl_po (" . $fields.") VALUES (".$values.")");
		}
		return $sc;
	}
	
	
	
	public function update($bookcode, $reference, $product_code, array $ds)
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$set 	= "";
			$i 		= 1;
			foreach( $ds as $field => $value )
			{
				$set .= $i == 1 ? $field . " = '".$value."'" : ", ". $field ." = '".$value."'";
				$i++;	
			}
			$sc = dbQuery("UPDATE tbl_po SET ". $set ." WHERE bookcode = '".$bookcode."' AND reference = '".$reference."' AND product_code = '".$product_code."'");
		}
		return $sc;
	}
	
	
	
	public function isExists($bookcode, $reference, $id_pd)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT bookcode FROM tbl_po WHERE bookcode = '".$bookcode."' AND reference = '".$reference."' AND id_product = '".$id_pd."'");
		if( dbNumRows($qs) > 0 )
		{
			$sc = TRUE;	
		}
		return $sc;
	}
	
	
	
	public function close($bookcode, $reference)
	{
		//--- status 1 = not receive yet,  2 = received some,  3 = closed;
		return dbQuery("UPDATE tbl_po SET status = 3 WHERE bookcode = '".$bookcode."' AND reference = '".$reference."'");
	}
	
	
	public function unClose($bookcode, $reference)
	{
		$status = $this->makeStatus($bookcode, $reference);
		return dbQuery("UPDATE tbl_po SET status = ".$status." WHERE bookcode = '".$bookcode."' AND reference = '".$reference."'");
	}
	
	
	
	public function makeStatus($bookcode, $reference)
	{
		$sc = 1;
		$qs = dbQuery("SELECT received FROM tbl_po WHERE bookcode = '".$bookcode."' AND reference = '".$reference."' AND received > 0");	
		if( dbNumRows($qs) > 0 )
		{
			$sc = 2;	
		}
		return $sc;
	}
	
	public function setStatus($reference, $status = "")
	{
		$sc = FALSE;
		if( $reference != "" )
		{
			if( $status == "" )
			{
				$qs = dbQuery("SELECT received FROM tbl_po WHERE reference = '".$reference."' AND received > 0");
				if( dbNumRows($qs) > 0 )
				{
					$status = 2;
				}
			}
			
			$sc = dbQuery("UPDATE tbl_po SET status = ".$status." WHERE reference = '".$reference."'");
			
		}
		
		return $sc;
	}
	
	
	public function getDetail($reference)
	{
		return dbQuery("SELECT * FROM tbl_po WHERE reference = '".$reference."' AND isCancle = 0 AND status != 3");	
	}
	
	
	public function getSupplierId($reference)
	{
		$sc = "";
		$qs = dbQuery("SELECT DISTINCT id_supplier FROM tbl_po WHERE reference = '".$reference."'");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}
	
	
	public function hasPO($reference)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id FROM tbl_po WHERE reference = '".$reference."' AND status != 3 AND isCancle = 0");
		if( dbNumRows($qs) > 0 )
		{
			$sc = TRUE;
		}
		return $sc;
	}
	
	
	public function search($txt)
	{
		return dbQuery("SELECT DISTINCT reference FROM tbl_po WHERE reference LIKE '%".$txt."%' AND status != 3 AND isCancle = 0");
	}
	
	public function received($reference, $id_pd, $qty)
	{
		return dbQuery("UPDATE tbl_po SET received = received + ".$qty.", status = 2 WHERE reference = '".$reference."' AND id_product = '".$id_pd."'");	
	}
	
	public function unReceived($reference, $id_pd, $qty)
	{
		$cQty = $this->getReceived($reference, $id_pd);
		$received = $cQty - $qty < 0 ? 0 : $cQty - $qty;
		return dbQuery("UPDATE tbl_po SET received = '".$received."' WHERE reference = '".$reference."' AND id_product = '".$id_pd."'");
	}
	
	
	public function getReceived($reference, $id_pd)
	{
		$sc = 0;
		$qs = dbQuery("SELECT received FROM tbl_po WHERE reference = '".$reference."' AND id_product = '".$id_pd."'");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}
	
	//--- ราคาสินค้า
	public function getPrice($reference, $id_pd)
	{
		$sc = 0;
		$qs = dbQuery("SELECT price FROM tbl_po WHERE reference = '".$reference."' AND id_product = '".$id_pd."'");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}
	
	
	//--- ส่วนลดระดับรายการสินค้า เป็นเงินบาท เช่น 10% หรือ 100  
	public function getDiscount($reference, $id_pd)
	{
		$sc = 0;
		$qs = dbQuery("SELECT discount FROM tbl_po WHERE reference = '".$reference."' AND id_product = '".$id_pd."'");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);	
		}
		return $sc;
	}
	
	
}///---- end class


?>