<?php

include 'report-pagination.php';

$reports = '';
foreach ($this->report_types as $key => $value) {
	$selected = ($key == $this->type) ? 'selected' : ''; 
    $reports .= "<option value='$key' $selected>$value</option>";
}
	
$months = '';
foreach ($this->months as $key => $val) {
	$selected = ($key == $this->month_param) ? 'selected' : ''; 
    $months .= "<option value='$key' $selected>$val</option>";
}
                    
$years = '';
foreach ($this->years as $year) {
	$selected = ($year == $this->year_param) ? 'selected' : ''; 
    $years .= "<option value='$year' $selected>$year</option>";
}

?>
<script type="text/javascript">
	(function($){
		$(document).ready(function() {
			$('#reports-form select, input').on('change', function() {
				$('#reports-form').submit();
			});
		});
	}(jQuery));
</script>
<?php if(isset($this->export)): ?>
	<style type="text/css">
		body {
			font-family: 'Open Sans', sans-serif;
		}
		body, table {
			font-size: 14px;
		}
	</style>
<?php endif; ?>
<link rel="stylesheet" type="text/css" href="<?=hc_reports_home('/css/hcreports.css');?>">
<div class="row">
	<div class="small-12 large-12 columns">
	<h3><?php echo $this->report_types[$this->type]; ?></h3><hr/>
	<?php if(empty($this->export)): ?>
		<form id="reports-form">
		<input type="hidden" name="page" value="amac-reports">
		<input type="hidden" name="paginate" value="1">
		<div class="row">
				<div class="input">
					<label>Report Type:</label>
					<select name="type">
						<?php echo $reports; ?>
					</select>
				</div>
				<div class="input">
					<label>Month:</label>
					<select name="month">
						<?php echo $months; ?>
					</select>
				</div>
				<div class="input">
					<label>Year:</label>
					<select name="year">
						<?php echo $years; ?>
					</select>
				</div>
		</div>
		</form>
	<?php endif; ?>
	<div style="clear: both;"></div>
	<?php if(empty($this->error) && isset($this->data['result'])): ?>
	<div style="float: left;">
		<?php if(empty($this->export)): ?>
			<div>
				<div class="report-info">
					<p><?php echo $this->data['count'] . ' record'. ($this->data['count'] > 1 ? 's' : '') .' found.'; ?></p>
				</div>
				<?php if($this->data['count'] > 0): ?>
					<div class="export_opts">
						<a href="<?php echo admin_url('?' . $_SERVER['QUERY_STRING'] . '&export=xls'); ?>"><img src="<?php echo hc_reports_home('/images/excel.png'); ?>"></a>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if($this->data['count'] > 0): ?>
			<table id="data-table" cellpadding=0 cellmargin=0>
				<thead>
					<tr>
						<?php
							foreach ($this->data['headers'] as $header) 
							{
								echo "<th>$header</th>";
							}
						?>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($this->data['result'] as $datum) 
						{
							echo '<tr>';
							foreach ($datum as $key => $value) 
							{
								echo "<td>$value</td>";
							}
							echo '</tr>';
						}
					?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php elseif(!empty($this->error)): echo "<div class='error'>$this->error</div>"; endif;?>
	<?php
		if($this->data['count'] > 0)
		{
			ReportPagination::generate([
                'paginate' => $this->paginate,
                'limit'  => $this->limit,
                'count'  => $this->data['count']
            ]);
		}
	?>
</div>