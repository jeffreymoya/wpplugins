<?php
	$user = $this->view_vars['object'];
	$states = array(''=>'-Select-','QLD'=>'QLD','NSW'=>'NSW','VIC'=>'VIC','SA'=>'SA','NT'=>'NT','WA'=>'WA','TAS'=>'TAS');
	$opts = '';
	$state = isset($user->state) ? $user->state : '';
	foreach ($states as $key => $value) 
	{
		$selected = $value === $state ? 'selected' : '';
		$opts .= "<option value='$key' $selected>$value</option>";
	}

?>
<div class="row">
	<h2>Membership Application</h2><hr/>
	<?php echo $this->form->create('UserDetail', array('controller' => 'enrollments', 'action' => 'enroll', 'public' => true));?>
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
	<div class="small-6 large-6 columns">
		<?php echo $mid;?>
		<?php echo $this->form->input('firstname', array('tabindex'=>10));?>
		<?php echo $this->form->input('address1', array('label' => 'Address 1', 'tabindex'=>12));?>
		<?php echo $this->form->input('suburb', array('tabindex'=>14));?>
		<?php echo $this->form->text_input('user_email', array('value' => $user->user_email, 'label' => 'User Email', 'tabindex'=>17));?>
		<input type="submit" id="register" value="Apply Now" class="button button-blue">
	</div>
    <div class="small-6 large-6 columns">
		<?php echo $this->form->input('lastname', array('tabindex'=>11));?>
		<?php echo $this->form->input('address2', array('label' => 'Address 2', 'tabindex'=>13));?>
		<div style="float:left; margin-right: 20px;">
			<label for="UserDetailState">State</label>
			<select id="UserDetailState" name="data[UserDetail][state]" tabindex=15 style="width: 150px;">
				<?php echo $opts; ?>
			</select>
		</div>
		<?php echo $this->form->input('postcode', array('style' => 'width: 150px;', 'tabindex'=>16));?>
		<?php echo $this->form->input('phone', array('tabindex'=>18));?>
	</div>
	</form>
</div>
