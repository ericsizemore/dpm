<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>These are logs for all of your Paypal transactions.<br /><span style="color: #ff0000;">You can click on the email, transaction id, or address to pop up a window so you can copy/see the whole value if needed.</span><br /><br /></p>
					<div id="table">
						<h1>Logs</h1>
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<thead>
						<tr class="header">
							<th>Domain</th>
							<th>Date</th>
							<th>Name</th>
							<th>Email</th>
							<th>Address</th>
							<th>Gross</th>
							<th>Fee</th>
							<th>Total</th>
							<th>ID</th>
						</tr>
						</thead>
						<tbody>
<?php

if (count($logs) == 0):
?>
						<tr>
							<td colspan="9" class="center">No logs are in the database.</td>
						</tr>
<?php
else:
	foreach ($logs AS $log):
?>
						<tr class="r<?php echo $log['class']; ?>">
							<td><a href="edit.php?mode=domain&amp;d=<?php echo $log['domainid']; ?>"><?php echo $log['domain']; ?></a></td>
							<td><?php echo $log['dateline']; ?></td>
							<td><?php echo $log['first_name'], ' ', $log['last_name']; ?></td>
							<td><input type="text" value="<?php echo $log['payer_email']; ?>" size="10" onclick="alert(this.value);" /></td>
							<td><textarea rows="4" cols="15" onclick="alert(this.value);"><?php echo $log['address_street'], "\n", $log['address_city'], ', ', $log['address_state'], ' ', $log['address_zip'], "\n", $log['address_country']; ?></textarea></td>
							<td><?php echo $log['mc_gross'], '<br />', $log['mc_currency']; ?></td>
							<td><?php echo $log['mc_fee'], '<br />', $log['mc_currency']; ?></td>
							<td><?php echo ($log['mc_gross'] - $log['mc_fee']), '<br />', $log['mc_currency']; ?></td>
							<td><input type="text" value="<?php echo $log['txn_id']; ?>" size="10" onclick="alert(this.value);" /></td>
						</tr>
<?php
	endforeach;
endif;
?>
						</tbody>
						</table>
						</form>
						<br />
					</div>
					<div id="pages"><?php echo $pagination['link']; ?></div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>