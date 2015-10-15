<?php
	$img_url = amac_mp_home('/app/public/img/');
	$b = $this->view_vars['object']->billing;
	
	$ccno = $b->card_number;

	if(!empty($ccno))
	{
	    $ccno = substr($ccno, 0, 4) . str_repeat("X", strlen($ccno) - 8) . substr($ccno, -4);
	}

    $expdate = !empty($b->expiry_date) ? date('F Y', strtotime($b->expiry_date)) : '';

    // build drop-down items for months
    $optMonths = '';
    foreach (array('01','02','03','04','05','06','07','08','09','10','11','12') as $option) {
            $optMonths .= "<option value='$option'>$option</option>\n";
    }
                        
    // build drop-down items for years
    $thisYear = (int) date('Y');
    $optYears = '';
    foreach (range($thisYear, $thisYear + 15) as $year) {
            $optYears .= "<option value='$year'>$year</option>\n";
    }
?>
<div id="mp_billing_details">
<div class="row" style="background: transparent url(<?php echo $img_url; ?>verisign_small.png) no-repeat right center;">
	<h2>Billing Details</h2><hr/>
	<div class="small-6 large-8 columns">
		<div><p><strong>Card Type:</strong>&nbsp;&nbsp;<span id="p_cc_type"><?php echo strtoupper($b->card_type); ?></span></p></div>
		<div><p><strong>Card Number:</strong>&nbsp;&nbsp;<span id="p_cc_no"><?php echo $ccno; ?></span></p></div>
		<div><p><strong>Expiry Date:</strong>&nbsp;&nbsp;<span id="p_exp_date"><?php echo $expdate; ?></span></p></div>
		<div><p><strong>Auto Renew:</strong>&nbsp;&nbsp;<input id="auto_renew" value="1" tabindex=23 type="checkbox"></p></div>
	</div>
    <div class="small-6 large-4 columns">
		&nbsp;
	</div>
</div>
<form id="cc-update">
<div class="row">
	<input type="radio" name="data[Billing][card_type]" value="visa">&nbsp;<img src="<?php echo $img_url;?>visa_small.png">
	<input type="radio" name="data[Billing][card_type]" value="mastercard">&nbsp;<img src="<?php echo $img_url;?>mastercard_small.png">
	<input type="radio" name="data[Billing][card_type]" value="amex">&nbsp;<img src="<?php echo $img_url;?>amex_small.png">
</div>
<input type="hidden" name="udid" value="<?php echo $this->view_vars['object']->id; ?>">
<div class="row">
	<div class="small-12 large-12 columns">
		<div>
			<input type="text" value="" pattern="[0-9]*" id="card_number" name="data[Billing][card_number]" autocomplete="off" tabindex=24 placeholder="Card Number">
		</div>
		<div>
			<input type="text" id="card_name" name="data[Billing][card_name]" autocomplete="off" tabindex=25 placeholder="Name on Card">
		</div>
		<div>
			<select id="expiry_month" name="expiry_month" tabindex=26>
				<option value="">Month</option>
                <?php echo $optMonths; ?>
			</select>
		</div>
		<div>
			<select id="expiry_year" name="expiry_year" tabindex=27>
				<option value="">Year</option>
	            <?php echo $optYears; ?>
			</select>
		</div>
		<div>
			<input type="text" size="4" maxlength="4" value="" pattern="[0-9]*" id="ccv" name="data[Billing][ccv]" tabindex=28 placeholder="CCV">
		</div>
	</div>
</div>
<div class="row">
	<div class="small-6 large-6 columns">&nbsp;</div>
    <div class="small-6 large-6 columns">
		<input type="button" id="update_cc_btn" value="Update" class="button button-blue">
	</div>
</div>
</form>
</div>