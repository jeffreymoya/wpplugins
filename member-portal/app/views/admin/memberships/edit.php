<?php
	$delete_img = amac_mp_home('/app/public/img/delete.png');
	echo "<input type='hidden' id='del_img_url' value='$delete_img'>";
?>
<h3>Edit Membership</h3><hr/>
<div class="mp-admin-form">
	<?php echo $this->form->create($model->name); ?>
	<?php echo $this->form->input('code'); ?>
	<?php echo "<input type='hidden' name='orig_code' value='$object->code'>"; ?>
	<?php echo $this->form->input('description'); ?>
	<?php echo "<input type='hidden' name='orig_description' value='$object->description'>"; ?>
	<?php echo $this->form->input('fee'); ?>
	<?php echo $this->form->belongs_to_dropdown('MembershipAddon', $addons, array('style'=>'width:170px;','label'=>'Available add-ons','empty' => 'Select Add-on')); ?>
	<h4>Assigned add-ons</h4>
	<div id="m_assigned_addons">
		<?php
		$orig_addons = [];
		$m_addons = $object->memberships_addons;
		if(isset($m_addons))
		{
			foreach ($m_addons as $ma) 
			{
				$ao = $ma->membership_addon;
				echo "<p><a id='$ao->id' href='#'><img src='$delete_img'></a><span class='addon-desc'>$ao->description</span>";
				echo "<input type='hidden' name='addons[]' value='$ao->id'></p>";
				$orig_addons[] = $ao->id;
			}
			
		}
		$orig_addons = implode("|", $orig_addons);
		?>
	</div>
	<?php echo "<input type='hidden' name='orig_addons' value='$orig_addons'>"; ?>
	<?php echo $this->form->end('Update'); ?>
</div>
