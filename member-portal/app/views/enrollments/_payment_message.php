<?php extract($this->view_vars['transaction']); ?>
<div class="row">
	<h2><?= $description; ?></h2><hr/>
	<?php if($success): ?>
		<p>
	        Congratulations, Your payment was successful!
	    </p>

	    <p>Your payment of $<strong><?= $amount ?></strong> has been processed successfully.</p>

	    <p>Your receipt number is <strong><?= $trxid ?></span></strong>
	        <br/>This can be used as your personal reference should you require any further assistance with this transaction.</p>

	    <p>Thank you for your purchase </p>

	    <p><a href="<?= home_url('members'); ?>" target="_self">Click here to continue.</p>
	<?php else: ?>
		<p><?= $message ?></p>
	<?php endif; ?>
</div>        

        	
