<?php
    $data = $invoice->row();
    $session_id = $data->invoice_session; //get by invoice table

    $this->db->where('user_session',$session_id);
    $item_query = $this->db->get('items'); //get all items

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Invoice no <?php echo $data->invoice_no; ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/style.css">
    <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet"> 
</head>
<body>


	<div class="header">

		<div class="left-header">
			
			<h2>Invoice</h2>
			<ul>
				<li><span class="left">Invoice Num:</span> <span class="right"><?php echo $data->invoice_no; ?></span></li>
				<li><span class="left">Date:</span> <span class="right"><?php echo $data->dated; ?></span></li>
				<?php if ($data->due_dated!=='0000-00-00') { ?>
				<li><span class="left">Due Date:</span> <span class="right"><?php echo $data->due_dated; ?></span></li>
				<?php } ?>
			</ul>

			<p><strong>Bill to:</strong></p>
			<ul>
				<li class="bill-to"><?php echo $data->bill_to; ?></li>
			</ul>

		</div>
		<div class="right-header">
			<?php if (!empty($data->userfile)) { ?>
			<img src="file:///D:/xampp/htdocs/in/assets/images/<?php echo $data->userfile; ?>" />
			<?php } ?>
		</div>

	</div>

	<div class="container">
		<div class="item-table">
			<table class="table">
				<thead>
					<tr>
						<th style="text-align: left;padding-left: 15px" width="30%">Item Description</th>
						<th width="10%">Qty</th>
						<th width="20%">Price</th>
						<th width="20%">Discount %</th>
						<th width="20%">Subtotal</th>
					</tr>
				</thead>
				<tbody>
					<?php
	                foreach ($item_query->result_array() as $value) {
	                    $item_id = $value['id'];
	                    $user_sesison = $value['user_session'];
	                    $price = $value['price'];
	                    $item_name = $value['item_name'];
	                    $qty = $value['qty'];
	                    $discount = $value['discount'];
	                    $subtotal = $value['subtotal'];

	                    if (!empty($subtotal)) {
	                ?>
					<tr>
						<td style="text-align: left;padding-left: 15px"><?php echo $item_name; ?></td>
						<td><?php echo $qty; ?></td>
						<td><?php echo $price; ?></td>
						<td><?php echo $discount; ?></td>
						<td><?php echo $subtotal; ?></td>
					</tr>
					<?php }} ?>
				</tbody>
			</table>
		</div>
		<div class="subtotal">
			<table class="Subtotal-table">
				<tbody>
					<tr>
						<th>Subtotal</th>
						<th><?php echo $data->sub_total; ?></th>
					</tr>

					<?php if (!empty($data->tax)) { ?>
					<tr>
						<th>Tax %</th>
						<th><?php echo $data->tax; ?></th>						
					</tr>
					<?php } ?>
					
					<?php if (!empty($data->total_krw)) { ?>
					<tr>
						<th>Total KRW</th>
						<th><?php echo $data->total_krw; ?></th>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<div class="notes">
			<p><strong>Notes:</strong></p>
			<?php if (!empty($data->notes)) { ?>
			<p><?php echo $data->notes; ?></p>
			<?php } ?>
		</div>
		<span class="powered">ThirdHandBD</span>
	</div>


	
</body>
</html>