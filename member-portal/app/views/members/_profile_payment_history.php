<?php
	$payment_histories = $this->view_vars['object']->payment_histories;
	$data = '';
	$pdficon = amac_mp_home('/app/public/img/pdf_icon_mini.gif');
	foreach ($payment_histories as $ph) 
	{
		$invoice = DocHandler::request_url($ph->invoice);
		$receipt = DocHandler::request_url($ph->receipt);
		$data .= 
			"<tr>
				<td>$ph->date</td>
				<td>$ph->trx_id</td>
				<td>$ph->item</td>
				<td>$ph->card</td>
				<td>$ph->charge</td>
				<td>$ph->payment</td>
				<td>$ph->balance</td>
				<td><a target='_blank' href='{$invoice}'><img src='$pdficon'></a></td>
				<td><a target='_blank' href='{$receipt}'><img src='$pdficon'></a></td>
			</tr>";
	}
?>
<div class="row">
	<hr/>
	<h2>Payment History</h2>
	<table class="mp_payment_history">
		<thead>
			<tr>
				<th>Date</th>
				<th>Transaction No</th>
				<th>Item</th>
				<th>Card</th>
				<th>Charge</th>
				<th>Payment</th>
				<th>Balance</th>
				<th>Invoice</th>
				<th>Receipt</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $data; ?>
		</tbody>
	</table>
</div>