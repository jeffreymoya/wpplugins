<?php
	$b = $this->view_vars['billing_info'];
	$action_type = $this->view_vars['action_type'];
	$expdate = !empty($b->expiry_date) ? date('F Y', strtotime($b->expiry_date)) : '';

	$ccno = $b->card_number;

	if(!empty($ccno))
	{
	    $ccno = substr($ccno, 0, 4) . str_repeat("X", strlen($ccno) - 8) . substr($ccno, -4);
	}

?>
<div class="row">
<h2>Billing Info</h2><hr/>
	<div class="small-12 large-12 columns">
		<div class="row">
			<div class="small-6 large-4 columns">
				<p><strong>Card Type: </strong></p>
			</div>
			<div class="small-6 large-4 columns">
				<p><?php echo strtoupper($b->card_type); ?></p>
			</div>
			<div class="small-6 large-4 columns">&nbsp;</div>
		</div>
		<div class="row">
			<div class="small-6 large-4 columns">
				<p><strong>Card Number: </strong></p>
			</div>
			<div class="small-6 large-4 columns">
				<p><?php echo $ccno; ?></p>
			</div>
			<div cl ass="small-6 large-4 columns">&nbsp;</div>
		</div>
		<div class="row">
			<div class="small-6 large-4 columns">
				<p><strong>Expiry Date: </strong></p>
			</div>
			<div class="small-6 large-4 columns">
				<p><?php echo $expdate; ?></p>
			</div>
			<div class="small-6 large-4 columns">&nbsp;</div>
		</div>
	</div>
</div>
<div class="row">
    <div class="small-4 large-4 columns mtop10">
    	<form method="POST">
    		<input name="<?=$action_type;?>" type="hidden" value="1">
			<input type="submit" id="register" value="Confirm" class="button button-blue">
    	</form>
	</div>
	<div class="small-8 large-8 columns">&nbsp;</div>
</div>
