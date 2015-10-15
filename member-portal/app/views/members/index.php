<div id="mp_profile" class="row full-width">
	<hr/>
	<div class="small-2 large-2 columns">
	    &nbsp;
	</div>
    <div class="small-8 large-8 columns">
	    <div class="row">
			<div class="row">
				<div class="row">
				  <div class="small-12 columns">
				    <ul class="tabs show-for-medium-up" data-tab>
				      <li class="tab-title active"><a href="#panel1">Account Details</a></li>
				      <?php
				      	if(isset($object->membership))
				      	{
				      		echo '<li class="tab-title"><a href="#panel2">Membership</a></li>';
				      	}
				      ?>
				    </ul>
			        	<dl class="accordion" data-accordion>
			        		<dd class="accordion-navigation">
			        			<a href="#panel1" class="show-for-small-only">Account Details</a>
			        			<div id="panel1" class="content active">
			        				<div class="content-box section-box">
				        				<div class="small-12 large-12 columns">
				        					<?php
										    	$this->render_view('_profile_details', ['object'=>$object]);
										    	$this->render_view('_profile_pwd');
										    	$this->render_view('_profile_settings', ['object'=>$object]);
				        					?>
									    </div>
				        			</div>
			        			</div>
			        			<?php
			        				if(isset($object->membership))
			        				{
			        					echo '<a href="#panel2" class="show-for-small-only">Membership</a>
						        			 <div id="panel2" class="content">
						        			 <div class="content-box section-box">
							        		 <div class="small-12 large-12 columns">';

							    		$this->render_view('_profile_membership', ['object'=>$object]);
							    		$this->render_view('_profile_billing');
							    		$this->render_view('_profile_payment_history', ['object'=>$object]);

						        		echo '</div></div></div>';
			        				}
			        			?>
			        		</dd>
			        	</dl>
				  </div>
				</div>
			</div>
	    </div>
    </div>
    <div class="small-2 large-2 columns">
		&nbsp;
	</div>
</div>