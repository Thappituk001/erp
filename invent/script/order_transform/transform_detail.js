// JavaScript Document
function updateDetailTable(){
	var id_order = $("#id_order").val();
	$.ajax({
		url:"controller/transformController.php?getDetailTable",
		type:"GET",
		cache:"false",
		data: { "id_order" : id_order },
		success: function(rs){
			if( isJson(rs) ){
				var source = $("#detail-table-template").html();
				var data = $.parseJSON(rs);
				var output = $("#detail-table");
				render(source, data, output);
			}
			else
			{
				var source = $("#nodata-template").html();
				var data = [];
				var output = $("#detail-table");
				render(source, data, output);
			}
		}
	});
}





//---	ลบรายการสินค้าที่สั่ง
function removeDetail(id, name){
	var id_order = $("#id_order").val();
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+name+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url:"controller/orderController.php?removeDetail",
				type:"POST", cache:"false", data:{ "id_order_detail" : id, "id_order" : id_order },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title: 'Deleted', type: 'success', timer: 1000 });
						updateDetailTable();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}





//---	ลบสินค้าที่เชื่อมโยงแล้วออกจากรายการ
function removeTransformProduct(id_order_detail, id_product, code){
	swal({
		title:'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+ code + ' หรือไม่ ?',
		type: 'warning',
		showCancelButton:true,
		cancelButtonText:'ยกเลิก',
		confirmButtonText: 'ลบรายการ',
		confirmButtonColor:'#FA5858',
		closeOnConfirm:false
	}, function(){
		$.ajax({
			url:'controller/transformController.php?removeTransformProduct',
			type:'POST',
			cache:'false',
			data:{
				'id_order_detail' : id_order_detail,
				'id_product' : id_product
			},
			success:function(rs){
				var rs = $.trim(rs);
				if( isJson(rs)){
					var ds = $.parseJSON(rs);
					$('#transform-box-'+id_order_detail).html(ds.data);
					swal({
						title:'success',
						type:'success',
						timer:1000
					});

				}else{
					swal('Error !', rs, 'error');
				}
			}
		});
	});
}





//---	ตรวจสอบสินค้าที่เชื่อมโยงว่าครบหรือไม่ก่อนบันทึกออเดอร์
function validateTransformProducts(){
	console.log('validate');
	var sc = true;
	$('.qty').each(function(index, el) {
		var arr = $(this).attr('id').split('-');
		var id = arr[1];
		var qty = parseInt( removeCommas($(this).text() ) );
		var trans_qty = parseInt($('#transform-qty-'+id).val());

		if( qty != trans_qty){
			sc = false;
		}
	});

	return sc;
}





function addToTransform(){
	var id_order = $('#id_order').val();
	var id_order_detail = $('#id_order_detail').val();
	var id_product = $('#id_product').val();
	var product_code = $('#trans-product').val();
	var qty = parseInt($('#trans-qty').val());
	var limit = parseInt($('#detail-qty').val());

	if( id_order_detail == ''){
		$('#transform-modal').modal('hide');
		swal('ไม่พบตัวแปร ID ORDER DETAIL');
		return false;
	}

	if( isNaN(qty) || qty < 1 || qty > limit){
		$('#qty-error').text('จำนวนไม่ถูกต้อง');
		$('#qty-error').removeClass('not-show');
		$('#trans-qty').focus();
		return false;
	}else{
		$('#qty-error').addClass('not-show');
	}

	if( id_product == '' || product_code == ''){
		$('#product-error').text('สินค้าไม่ถูกต้อง');
		$('#product-error').removeClass('not-show');
		$('#trans-product').focus();
		return false;
	}else{
		$('#product-error').addClass('not-show');
	}

	$.ajax({
		url:'controller/transformController.php?addTransformProduct',
		type:'POST',
		cache:'false',
		data:{
			'id_order' : id_order,
			'id_order_detail' : id_order_detail,
			'id_product' : id_product,
			'qty' : qty
		},
		success:function(rs){
			//--- ถ้าสำเร็จได้ JSON มาทำการ Update ตารางเลย
			var rs = $.trim(rs);
			if( isJson(rs)){
				var ds = $.parseJSON(rs);
				$('#transform-box-'+id_order_detail).html(ds.data);
				clearFields();
				$('#transform-modal').modal('hide');
			}else{
				swal('Error !', rs, 'error');
			}
		}
	});
}




function clearFields(){
	$('#id_order_detail').val('');
	$('#id_product').val('');
	$('#detail-qty').val('');
	$('#trans-qty').val('');
	$('#trans-product').val('');
}

//---- 	เปิดกล่องเชื่อมโยงสินค้า
function addTransformProduct(id){
	//---	id = id_order_detail
	//---	จำนวนที่สั่ง
	var qty = parseInt(removeCommas($('#qty-'+id).text()));

	//---	จำนวนที่เชื่อมโยงแล้ว
	var trans_qty = parseInt($('#transform-qty-'+id).val());

	//---	จำนวนคงเหลือที่จะเชื่อมโยงได้
	var available_qty = qty - trans_qty;

	$('#id_order_detail').val(id);

	$('#detail-qty').val(available_qty);

	$('#id_product').val('');

	$('#trans-qty').val(available_qty);

	$('#transform-modal').modal('show');
}


$('#transform-modal').on('shown.bs.modal', function(){
	$('#trans-product').focus();
});



$('#trans-product').autocomplete({
	source:'controller/transformController.php?getProduct',
	autoFocus:true,
	close:function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2){
			var name = arr[0];
			var id = arr[1];
			$('#id_product').val(id);
			$(this).val(name);
		}else{
			$('#id_product').val('');
			$(this).val('');
		}
	}
});



$(document).ready(function() {
	$('#trans-qty').numberOnly();
});


$('#trans-qty').focusout(function(){
	var input_qty = parseInt($(this).val());
	var limit = parseInt($('#detail-qty').val());
	if( isNaN(input_qty) || input_qty < 1 || input_qty > limit){
		$('#qty-error').text('ได้ไม่เกิน '+limit+' หน่วย');
		$('#qty-error').removeClass('not-show');
		$(this).val(limit);
	}else{
		$('#qty-error').addClass('not-show');
	}
});
