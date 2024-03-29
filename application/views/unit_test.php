<!DOCTYPE html>
<html>
<head>
<title>Unit Testing &rsaquo; <?=ucfirst($type)?></title>
<link rel="stylesheet" type="text/css" href="http://localhost/net.bulsam.oyunpaneliyeni/public/css/unit_test.css" charset="utf-8">
</head>
<body>
<div id="header"> 
	<h2>
		Unit Testing &rsaquo; <?=ucfirst($type)?>
	</h2>
</div>
<div id="nav">
	<input type="button" value="All" onclick="window.location='<?=site_url($this->uri->rsegment(1)."/all")?>'" />
	<input type="button" value="Models" onclick="window.location='<?=site_url($this->uri->rsegment(1)."/models")?>'" />
	<input type="button" value="Views" onclick="window.location='<?=site_url($this->uri->rsegment(1)."/views")?>'" />
	<input type="button" value="Helpers" onclick="window.location='<?=site_url($this->uri->rsegment(1)."/helpers")?>'" />
	<input type="button" value="Libraries" onclick="window.location='<?=site_url($this->uri->rsegment(1)."/libraries")?>'" />
	<select id="jump">
		<optgroup label="Test groups">
			<option value="<?=site_url($this->uri->rsegment(1)."/all")?>"<? if ($type == 'all') echo ' selected="selected"'?>>All</option>
		<? foreach($tests as $test_type => $testgroup): ?>
			<option value="<?=site_url($this->uri->rsegment(1)."/$test_type")?>"<? if ($type == $test_type) echo ' selected="selected"'?>><?=ucfirst($test_type)?></option>
		<? endforeach; ?>
		</optgroup>
		<? foreach($tests as $test_type => $testgroup): ?>
		<optgroup label="<?=ucfirst($test_type)?>">
			<? foreach($testgroup as $test): ?>
			<option value="<?=site_url($this->uri->rsegment(1)."/$test")?>"<? if ($type == $test) echo ' selected="selected"'?>><?=$test?></option>
			<? endforeach; ?>
		</optgroup>
		<? endforeach; ?>
	</select>
	<input type="button" value="Run" onclick="window.location=document.getElementById('jump').value" />
</div>
<? if (isset($msg) && strlen($msg) > 0): ?>
<div id="message">
	<?=$msg?>
</div>
<? endif; ?>
<? if ($totals['all'] > 0): ?>
<div id="report">
	<div class="summary <?=($totals['failed'] > 0) ? 'fail' : 'pass' ?>">
		<?=$totals['passed']?> / <?=$totals['all']?> tests passed in <?=$total_time?> seconds
	</div>
	<? foreach($report as $key => $test):
	if (array_key_exists($key, $headings['types']))
		echo "<h1>{$headings['types'][$key]}</h1>\n";
	if (array_key_exists($key, $headings['tests']))
		echo "<h2>{$headings['tests'][$key]}</h2>\n";
	?>
	<div class="test <?=($test['Result'] == 'Passed') ? 'pass' : 'fail' ?>">
		<div class="result"><?=strtoupper($test['Result'])?></div>
		<h3><?=$test['Test Name']?></h3>
		<div class="details">
			<div class="time"><?=$timings[$key]?></div>
			Expected
				<strong><?=var_export($test['Expected Value'])?></strong> (<?=strtolower($test['Expected Datatype'])?>),
			returned
				<? if (is_string($test['Test Value'])): ?>
				<?=highlight_code($test['Test Value'])?>
				<? else: ?>
				<strong><?=var_export($test['Test Value'])?></strong> (<?=strtolower($test['Test Datatype'])?>)<br />
				<? endif; ?>
				<? if ( ! empty($test['SQL Error'])): ?>
				<code><?=$test['SQL Error']?></code>
				<? endif; ?>
				<? if ( ! empty($test['SQL Query'])): ?>
				<?=highlight_code($test['SQL Query'])?>
				<? endif; ?>
			<em><?=substr($test['File Name'], strlen(FCPATH))?></em> on line <?=$test['Line Number']?>
		</div>
	</div>
	<? endforeach; ?>
</div>
<? endif; ?>
</body>
</html>