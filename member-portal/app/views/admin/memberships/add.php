<?php
	$delete_img = amac_mp_home('/app/public/img/delete.png');
	echo "<input type='hidden' id='del_img_url' value='$delete_img'>";
?>
<h3>Add Membership</h3><hr/>
<div class="mp-admin-form">
	<?php echo $this->form->create($model->name); ?>
	<?php echo $this->form->input('code'); ?>
	<?php echo $this->form->input('description'); ?>
	<?php echo $this->form->input('fee'); ?>
	<?php echo $this->form->belongs_to_dropdown('MembershipAddon', $addons, array('style'=>'width:170px;','label'=>'Addons','empty' => 'Select Addon')); ?>
	<h4>Assigned add-ons</h4>
	<div id="m_assigned_addons" style="display:none;"></div>
	<?php echo $this->form->end('Add'); ?>
</div>