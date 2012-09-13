<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>Here you can view your DPM database statistics, and, you can download a backup of your database.<br /><br /></p>
<?php

if ($result != ''):
?>
					<div id="result"><?php echo $result; ?></div>
					<br />
<?php
endif;
?>
					<div id="table">
						<h1>Database Backup</h1>
						<form method="post" action="database.php">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td>Format:</td>
							<td>
								<select name="format">
									<option label="MySQL" value="sql">MySQL</option>
									<option label="Excel" value="xls">Excel</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="center">Please note, the Excel format will only include:<br /><code>Domain, Category, Registrar, Expiration, Status, Date Added</code></td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="backup" value="Do Backup" /></td>
						</tr>
						</tbody>
						</table>
						</form>
						<br />
						<h1>Database Restore</h1>
						<form method="post" action="database.php">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td>Backup to restore:</td>
							<td>
								<select name="file">
<?php

if (count($restoreoptions)):
	foreach ($restoreoptions AS $restoreoption):

		preg_match('#dpm_backup_([0-9]+)\.sql#i', $restoreoption, $match);
		$restoreoption_prepared = date('F jS, Y g:i:s A', $match[1]);
		unset($match);

?>
									<option label="<?php echo $restoreoption_prepared; ?>" value="<?php echo $restoreoption; ?>"><?php echo $restoreoption_prepared; ?></option>
<?php
	endforeach;
else:
?>
									<option value="-1">No backups available.</option>
<?php
endif;
?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="restore" value="Do Restore" /></td>
						</tr>
						</tbody>
						</table>
						</form>
						<br />
						<h1>Database Statistics</h1>
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<thead>
						<tr class="header">
							<th>Table Name</th>
							<th>Records</th>
							<th>Data Size</th>
							<th>Overhead</th>
							<th>Effective Size</th>
							<th>Index Size</th>
							<th>Total Size</th>
						</tr>
						</thead>
						<tbody>
<?php

if (count($tablestats)):
	foreach ($tablestats AS $tablestat):
?>
						<tr class="r<?php echo $tablestat['class']; ?>">
							<td><?php echo $tablestat['Name']; ?></td>
							<td><?php echo $tablestat['Rows']; ?></td>
							<td><?php echo size_format($tablestat['Data_length'] + $tablestat['Data_free']); ?></td>
							<td><?php if ($tablestat['Data_free'] > 0): ?><a href="./database.php?optimize&amp;table=<?php echo $tablestat['Name']; ?>" title="Optimize Table"><?php echo size_format($tablestat['Data_free']); ?></a><?php else: ?><?php echo size_format($tablestat['Data_free']); ?><?php endif; ?></td>
							<td><?php echo size_format($tablestat['Data_length'] - $tablestat['Data_free']); ?></td>
							<td><?php echo size_format($tablestat['Index_length']); ?></td>
							<td><?php echo size_format($tablestat['Index_length'] + $tablestat['Data_length'] + $tablestat['Data_free']); ?></td>
						</tr>
<?php
	endforeach;
else:
?>
						<tr>
							<td colspan="7" class="center">Could not gather database statistics.</td>
						</tr>
<?php
endif;
?>
						<tr>
							<td colspan="7" class="header" style="color: #fff;">Overhead is unused space reserved by MySQL. To free up this space, click on the table's overhead figure above.</td>
						</tr>
						</tbody>
						</table>
						<br />
					</div>
					<div id="pages">&nbsp;</div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>