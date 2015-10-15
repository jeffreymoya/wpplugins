<?php
	$m = $this->view_vars['membership'];
	$action_type = $this->view_vars['action_type'];
	$mem_incl = '';
	$tick_icon = amac_mp_home('/app/public/img/tick.png');
	foreach ($m->memberships_addons as $memberships_addons) 
	{
		$ma = $memberships_addons->membership_addon;
		$mem_incl .= "<p><img src='$tick_icon'/>&nbsp;".ucfirst($ma->description)."</p>";
	}
?>
<div class="row">
	<h2><?=ucfirst($action_type);?> Membership</h2><hr/>
	<div class="small-6 large-4 columns">
		<div><p><strong>Membership Type:</strong>&nbsp;&nbsp;<?php echo $m->code; ?></p></div>
		<div><p><strong>Membership Inclusions:</strong>&nbsp;&nbsp;</p></div>
	</div>
    <div class="small-6 large-4 columns">
		<div><p><strong>Membership Fee:</strong>&nbsp;&nbsp;$<?php echo $m->fee; ?></p></div>
		<div><?php echo $mem_incl; ?></div>
	</div>
	 <div class="small-6 large-4 columns">&nbsp;</div>
</div>
