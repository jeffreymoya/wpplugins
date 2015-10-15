<?php
	$obj = $this->view_vars['object'];
	$nrm = (isset($obj->notify_renew_membership ) && $obj->notify_renew_membership != 0) ? 'checked' : '';
	$nnc = (isset($obj->notify_new_courses ) && $obj->notify_new_courses != 0) ? 'checked' : '';
	$searchable = (isset($obj->searchable ) && $obj->searchable != 0) ? 'checked' : '';
?>
<div class="row">
	<h2>Settings</h2><hr/>
	<div class="small-12 large-12 columns">
		<form>
		<input type="hidden" id="UserDetailHiddenId" name="data[UserDetail][id]" value="<?php echo $this->view_vars['object']->id; ?>">
    	<div class="row">
			<div>
				<input <?php echo $nrm; ?> id="notify_renew_membership" value="1" name="data[UserDetail][notify_renew_membership]" tabindex=22 type="checkbox">
				<span>Remind me to renew membership</span>
			</div>
			<div>
				<input <?php echo $nnc; ?> id="notify_new_courses" value="1" name="data[UserDetail][notify_new_courses]" tabindex=23 type="checkbox">
				<span>Notify me of new courses</span>
			</div>
			<div>
				<input <?php echo $searchable; ?> id="searchable" value="1" name="data[UserDetail][searchable]" tabindex=24 type="checkbox">
				<span>List me on the member search facility of the AMAC website</span>
			</div>
    	</div>
		<div class="row">
			<div class="small-6 large-6 columns">&nbsp;</div>
			<div class="small-6 large-6 columns">
				<input type="button" id="update_settings_btn" value="Save" class="button button-blue"/>
			</div>
		</div>
		</form>
	</div>
</div>