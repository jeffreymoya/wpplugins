<?php
	$defs = array(
		'username' => '',
		'useraddr' => '',
		'itemname' => '',
		'itemdetails' => array(''),
		'invoicedate' => '',
		'invoiceno' => '',
		'subtotal' => '',
		'tax' => '',
		'total' => '',
		'amtdue' => '',
		'quantity' => '',
		'rate' => '',
		'amount' => '',
		'duedate' => ''
	);

	if(isset($params) && sizeof($params) > 0)
	{
		$defs = array_merge($defs, $params);
	}

	extract($defs);
?>
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<style type="text/css">
	body, table {
		font: 12px 'Open Sans', sans-serif;
	}	
	p {
		margin: 5px 0;
		color: #4d4d4d;
	}
	.container {
		width: 850px;
	}
	.row {
		width: 100%;
		clear: both;
	}
	.logo {
		float: right;
		width: 20%;
	}
	.heading, .left-footer {
		float: left;
		margin: 10px 0 40px 0;
	}
	thead {
		font-weight: bold;
	}
	table {
		width: 100%;
		margin-bottom: 80px;
		border-spacing: 0;
	}
	table th {
		border-bottom: solid 2px #444;
	    line-height: 40px;
	}
	table td {
		vertical-align: top;
	}
	table, table th, .last p {
		text-align: right;
	}
	strong, table th {
		color: #0C195D;
	}
	.text-left {
		text-align: left;
	}
	.heading:first-child .row {
		width: 70%;
	}
</style>
<div class="container">
	<div class="row">
		<img class="logo" src="<?=DocHandler::home('/images/logo.png');?>">
	</div>
	<div class="row">
		<div class="heading" style="width:50%;">
			<p style="font-size: 25px; margin-top: 0">INVOICE</p>
			<div class="row">
				<p>Attention: <?=$username;?></p>
				<p><?=$useraddr;?></p>
			</div>
		</div>
		<div class="heading" style="width:30%;">
			<div class="row">
				<p><strong>Date</strong></p>
				<p><?=$invoicedate;?></p>
			</div>
			<div class="row">
				<p><strong>Invoice Number</strong></p>
				<p><?=$invoiceno;?></p>
			</div>
		</div>
		<div class="heading last" style="width:20%;">
			<p><strong>The Australian Medical Acupuncture College</strong></p>
			<p><strong>ABN 49 006 101 613</strong></p>
			<p>PO Box 7930 Bundall Qld 4217</p>
			<p>T: 07 5592 6699</p>
		</div>
	</div>
	<div class="row">
		<table>
			<thead>
				<tr>
					<th style="text-align: left;"><?=ucwords($itemname);?></th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td>&nbsp;</td><td>&nbsp;</td>
					<td><p>Subtotal</p></td>
					<td><p><?php echo empty($subtotal) ? '' : '$'.$subtotal;?></p></td>
				</tr>
				<tr>
					<td>&nbsp;</td><td>&nbsp;</td>
					<td><p>Tax</p></td>
					<td><p><?php echo empty($tax) ? '' : '$'.$tax;?></p></td>
				</tr>
				<tr>
					<td>&nbsp;</td><td>&nbsp;</td>
					<td><p>Total</p></td>
					<td><p><?php echo empty($total) ? '' : '$'.$total;?></p></td>
				</tr>
				<tr>
					<td>&nbsp;</td><td>&nbsp;</td>
					<td><p><strong>Amount Due</strong></p></td>
					<td><p><strong><?php echo empty($amtdue) ? '' : '$'.$amtdue;?></strong></p></td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td class="text-left">
						<?php
							foreach ($itemdetails as $item) {
								echo '<p>'.ucwords($item).'</p>';
							}
						?>
					</td>
					<td>
						<p><?=$quantity;?></p>
					</td>
					<td>
						<p><?=$rate;?></p>
					</td>
					<td>
						<p><?=$amount;?></p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="row">
		<div class="left-footer" style="width: 30%;">
		<p><strong>Due Date: <?=$duedate;?></strong></p>
		<p style="margin: 20px 0;">Please deposit payment to</p>
		<p><strong>Account Name: AMAC</strong></p>
		<p><strong>BSB:</strong></p>
		<p><strong>Account Number: 123456789</strong></p>
		</div>
	</div>
</div>