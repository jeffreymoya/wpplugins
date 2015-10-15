<div id="mp_enroll" class="row full-width">
	<hr/>
	<div class="small-2 large-2 columns">
	    &nbsp;
	</div>
    <div class="small-8 large-8 columns">
	    <div class="row">
		  <div class="small-12 columns">
		  	<?php
		  		if(isset($transaction))
		  		{
			    	$this->render_view('_payment_message', ['transaction'=>$transaction]);
		  		}
		  		else
		  		{
			    	$this->render_view('_membership', ['object'=>$membership, 'action_type'=>$action_type]);
			    	
			    	if(is_user_logged_in())
			    	{
				    	$this->render_view('_billing_info', ['billing_info'=>$billing_info, 'action_type'=>$action_type]);
			    	}
			    	else
			    	{
				    	$this->render_view('_billing', ['object'=>$object]);
			    	}
		  		}
			?>
		  </div>
		</div>
    </div>
    <div class="small-2 large-2 columns">
		&nbsp;
	</div>
</div>