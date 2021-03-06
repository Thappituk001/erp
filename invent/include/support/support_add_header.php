<?php $user = isset( $_GET['id_order']) ? employee_name($order->getOrderUser($order->id)) : employee_name(getCookie('user_id')); ?>

<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
        <label class="form-control input-sm text-center" style="margin-bottom:0px;" <?php echo $disabled; ?>><?php echo $order->reference; ?></label>
    </div>

    <div class="col-sm-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm text-center input-header" id="dateAdd" value="<?php echo thaiDate($order->date_add); ?>" <?php echo $disabled; ?> />
    </div>

    <div class="col-sm-3">
    	<label>ผู้เบิก[พนักงาน]</label>
        <input type="text" class="form-control input-sm text-center input-header" id="customer" value="<?php echo customerName($order->id_customer); ?>"  <?php echo $disabled; ?>/>
    </div>

		<div class="col-sm-2">
    	<label>งบประมาณคงเหลือ</label>
      <input type="text" class="form-control input-sm text-center input-header" id="balance" value="<?php echo number($bd->getBalance($order->id_budget),2); ?>" disabled/>
    </div>

		<div class="col-sm-3">
    	<label>ผู้ทำรายการ[พนักงาน]</label>
        <input type="text" class="form-control input-sm text-center input-header" id="user" value="<?php echo $user; ?>"  disabled/>
    </div>

    <div class="col-sm-10">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm input-header" id="remark" value="<?php echo $order->remark; ?>" <?php echo $disabled; ?> />
    </div>

    <div class="col-sm-2">
    <label class="display-block not-show">btn</label>
    <?php if( isset( $_GET['id_order'] ) && $order->state < 8): ?>
    	<button type="button" class="btn btn-sm btn-warning btn-block" id="btn-edit-order" onclick="getEdit()">แก้ไข</button>
        <button type="button" class="btn btn-sm btn-success btn-block hide" id="btn-update-order" onclick="validUpdate()">บันทึก</button>
    <?php else : ?>
    	<button type="button" class="btn btn-sm btn-success btn-block" onclick="addNew()">สร้างออเดอร์</button>
    <?php endif; ?>
    </div>
</div>
<input type="hidden" id="id_customer" value="<?php echo $order->id_customer; ?>" />
<input type="hidden" id="id_support" value="<?php echo $sp->getId($order->id_customer); ?>" />
<input type="hidden" id="id_budget" value="<?php echo $order->id_budget; ?>" />
<input type="hidden" id="role" value="3" />
<input type="hidden" id="isOnline" value="0" />
