<?php

	class warehouse
	{
		public $id;
		public $code;
		public $name;
		public $role;
		public $allowUnderZero;
		public $isDefault;
		public $active;
		
		public function __construct($id='')
		{
			if( $id != '' )
			{
				$qs = dbQuery("SELECT * FROM tbl_warehouse WHERE id = '".$id)."'";
				if( dbNumRows($qs) == 1 )
				{
					$rs = dbFetchObject($qs);
					$this->id		= 	$rs->id;
					$this->code	= $rs->code;
					$this->name	= $rs->name;
					$this->role	= $rs->role;
					$this->allowUnderZero	= $this->allow_under_zero == 1 ? TRUE : FALSE;
					$this->isDefault	= $rs->is_default == 1 ? TRUE : FALSE;
					$this->active	= $rs->active == 1 ? TRUE : FALSE;
				}
			}
		}
		
		
		public function add(array $ds)
		{
			$fields 	= '';
			$values 	= '';
			$n 		= count($ds);
			$i 			= 1;
			foreach( $ds as $key => $val )
			{
				$fields .=	 $key;
				if( $i < $n ){ $fields .= ', '; }
				$values .= "'".$val."'";
				if( $i < $n ){ $values .= ', '; }
				$i++;
			}
			$qs = dbQuery("INSERT INTO tbl_warehouse (".$fields.") VALUES (".$values.")");
			if( $qs )
			{
				return dbInsertId();
			}
			else
			{
				return FALSE;
			}
		}
		
		
		public function update($id, array $ds)
		{
			$set = '';
			$n = count($ds);
			$i = 1;
			foreach( $ds as $key => $val )
			{
				$set .= $key." = '".$val."'";
				if( $i < $n ){ $set .= ", "; }
				$i++;
			}
			return dbQuery("UPDATE tbl_warehouse SET ".$set." WHERE id = '".$id."'");
		}		
		
		public function deleteWarehouse($id)
		{
			if( $this->isWarehouseEmpty($id) === TRUE )
			{
				return $this->actionDelete($id);	
			}
			else
			{
				return FALSE;
			}
		}
		
		
		public function isWarehouseEmpty($id)
		{
			$sc = TRUE;
			$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE id = '".$id."'");
			if( dbNumRows($qs) > 0 )
			{
				$sc = FALSE;
			}
			return $sc;
		}
		
		
		private function actionDelete($id)
		{
			return dbQuery("DELETE FROM tbl_warehouse WHERE id = '".$id."'");	
		}
		
		public function isExists($id)
		{
			$sc = FALSE;
			$qs = dbQuery("SELECT code FROM tbl_warehouse WHERE id = '".$id."'");
			if( dbNumRows($qs) > 0 )
			{
				$sc = TRUE;
			}
			return $sc;
		}
		
		
		
		public function getCode($id)
		{
			$sc = "";
			$qs = dbQuery("SELECT code FROM tbl_warehouse WHERE id = '".$id."'");
			if( dbNumRows($qs) == 1 )
			{
				list( $sc ) = dbFetchArray($qs);	
			}
			return $sc;
		}
		
		
		public function getName($id)
		{
			$sc = "";
			$qs = dbQuery("SELECT name FROM tbl_warehouse WHERE id = '".$id."'");
			if( dbNumRows($qs) == 1 )
			{
				list( $sc ) = dbFetchArray($qs);	
			}
			return $sc;
		}
		
		
		public function getId($code)
		{
			$sc = '0000';
			$qs = dbQuery("SELECT id FROM tbl_warehouse WHERE code = '".$code."'");
			if( dbNumRows($qs) == 1 )
			{
				list( $sc ) = dbFetchArray($qs);
			}
			return $sc;
		}
		
		
	} 	//----- End class


?>