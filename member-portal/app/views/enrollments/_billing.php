<?php
	$img_url = amac_mp_home('/app/public/img/');
	$b = $this->view_vars['object'];

	$ctypes = array('visa'=>'', 'mastercard'=>'', 'amex'=>'');

	$cctype = [];
	if(isset($b['card_type']))
	{
		$ctypes[$b['card_type']] = 'checked';
	}


    // build drop-down items for months
    $optMonths = '';
    foreach (array('01','02','03','04','05','06','07','08','09','10','11','12') as $option) {
    		$selected = $b['expiry_month'] === $option ? 'selected' : '';
            $optMonths .= "<option value='$option' $selected>$option</option>\n";
    }
                        
    // build drop-down items for years
    $thisYear = (int) date('Y');
    $optYears = '';
    foreach (range($thisYear, $thisYear + 15) as $year) {
    		$selected = $b['expiry_year'] == $year ? 'selected' : '';
            $optYears .= "<option value='$year' $selected>$year</option>\n";
    }

    $ccno = isset($b['card_number']) ? $b['card_number'] : '';
    $ccname = isset($b['card_name']) ? $b['card_name'] : '';
    $ccv = isset($b['ccv']) ? $b['ccv'] : '';

?>
<div id="mp_billing_details">
<form action="<?php echo $this->view_vars['action']; ?>" method="POST">
<input type="hidden" name="registration" value="1">
<div class="small-12 large-12 columns">
	<?php 
		if(isset($_SESSION['mvc_flash']))
		{
			echo "<div class='alert-box alert'>";
			$this->display_flash();
			echo "</div>";
		} 
	?>
</div>
<div class="row">
	<input type="radio" name="data[card_type]" value="visa" <?php echo $ctypes['visa']; ?>>&nbsp;<img src="<?php echo $img_url;?>visa_small.png">
	<input type="radio" name="data[card_type]" value="mastercard" <?php echo $ctypes['mastercard']; ?>>&nbsp;<img src="<?php echo $img_url;?>mastercard_small.png">
	<input type="radio" name="data[card_type]" value="amex" <?php echo $ctypes['amex']; ?>>&nbsp;<img src="<?php echo $img_url;?>amex_small.png">
</div>
<div class="row">
	<div class="small-12 large-12 columns">
		<div>
			<input type="text" value="<?php echo $ccno; ?>" pattern="[0-9]*" id="card_number" name="data[card_number]" autocomplete="off" tabindex=24 placeholder="Card Number">
		</div>
		<div>
			<input type="text" value="<?php echo $ccname; ?>" id="card_name" name="data[card_name]" autocomplete="off" tabindex=25 placeholder="Name on Card">
		</div>
		<div>
			<select style="width: 180px; margin-right: 20px; float: left" id="expiry_month" name="data[expiry_month]" tabindex=26>
				<option value="">Month</option>
                <?php echo $optMonths; ?>
			</select>
		</div>
		<div>
			<select style="width: 180px; margin-right: 20px; float: left" id="expiry_year" name="data[expiry_year]" tabindex=27>
				<option value="">Year</option>
	            <?php echo $optYears; ?>
			</select>
		</div>
		<div>
			<input style="width: 180px; margin-right: 20px; float: left" type="text" size="4" maxlength="4" value="<?php echo $ccv; ?>" pattern="[0-9]*" id="ccv" name="data[ccv]" tabindex=28 placeholder="CCV">
		</div>
	</div>
</div>
<div class="row">
    <div class="small-4 large-4 columns">
		<input type="submit" id="register" value="Confirm" class="button button-blue">
	</div>
	<div class="small-8 large-8 columns">&nbsp;</div>
</div>
</form>
</div>