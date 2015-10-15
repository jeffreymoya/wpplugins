<?php
	$m = $this->view_vars['object']->membership;
	$renew_date = $this->view_vars['object']->renewal_date;
	$action = isset($renew_date) ? 'upgrade' : 'renew';
	$mem_incl = '';
	$tick_icon = amac_mp_home('/app/public/img/tick.png');
	foreach ($m->memberships_addons as $memberships_addons) 
	{
		$ma = $memberships_addons->membership_addon;
		$mem_incl .= "<p><img src='$tick_icon'/>&nbsp;".ucfirst($ma->description)."</p>";
	}
?>
<div class="row">
	<h2><?=ucfirst($this->view_vars['action']) ?> Membership</h2><hr/>
	<div class="small-6 large-6 columns">
		<div><p><strong>Membership Type:</strong>&nbsp;&nbsp;<?php echo $m->description; ?></p></div>
		<div><p><strong>Membership Inclusions:</strong>&nbsp;&nbsp;</p></div>
	</div>
    <div class="small-6 large-6 columns">
		<div><p><strong>Membership Fee:</strong>&nbsp;&nbsp;$<?php echo $m->fee; ?></p></div>
		<div><?php echo $mem_incl; ?></div>
	</div>
</div>
<?php if($m->code !== 'full'): ?>
	<div class="row">
		<div class="small-6 large-6 columns">&nbsp;</div>
	    <div class="small-6 large-6 columns">
			<a href="<?php echo home_url('/'.$action); ?>"><button class="button button-blue"><?= ucfirst($action) ?></button></a>
		</div>
	</div>
<?php endif; ?>