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

	$mid = empty($user->id) ? "<input type='hidden' name='data[UserDetail][membership_id]' value='{$user->membership->id}'>" : "";
?>
<div class="row">
	<h2>Personal Details</h2><hr/>
	<?php echo $this->form->create('UserDetail', array('controller' => 'members', 'action' => 'update_profile', 'public' => true));?>
	<div class="small-6 large-6 columns">
		<?php echo $mid;?>
		<?php echo $this->form->input('firstname', array('tabindex'=>10));?>
		<?php echo $this->form->input('address1', array('label' => 'Address 1', 'tabindex'=>12));?>
		<?php echo $this->form->input('suburb', array('tabindex'=>14));?>
		<?php echo $this->form->text_input('user_email', array('value' => $user->user_email, 'label' => 'User Email', 'tabindex'=>17));?>
	</div>
    <div class="small-6 large-6 columns">
		<?php echo $this->form->input('lastname', array('tabindex'=>11));?>
		<?php echo $this->form->input('address2', array('label' => 'Address 2', 'tabindex'=>13));?>
		<div style="float:left; margin-right: 20px;">
			<label for="UserDetailState">State</label>
			<select id="UserDetailState" name="data[UserDetail][state]" tabindex=15 style="width: 70px;">
				<?php echo $opts; ?>
			</select>
		</div>
		<?php echo $this->form->input('postcode', array('style' => 'width: 70px;', 'tabindex'=>16));?>
		<?php echo $this->form->input('phone', array('tabindex'=>18));?>
		<input type="button" id="update_details_btn" value="Save" class="button button-blue"/>
	</div>
	</form>
</div>