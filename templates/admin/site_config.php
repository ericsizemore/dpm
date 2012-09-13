<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>Use the following form to edit your portfolio title, keywords, description, etc.<br /><br /></p>
<?php

if ($result != ''):
?>
					<div id="result"><?php echo $result; ?></div>
					<br />
<?php
endif;
?>
					<div id="table">
						<form method="post" action="site_config.php">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td colspan="2"><h1>Site Configuration</h1></td>
						</tr>
						<tr>
							<td><label for="title">Title:</label></td>
							<td><input type="text" name="title" id="title" value="<?php echo $config->get('title'); ?>" /></td>
						</tr>
						<tr>
							<td><label for="description">Description:</label></td>
							<td><input type="text" name="description" id="description" value="<?php echo $config->get('description'); ?>" /></td>
						</tr>
						<tr>
							<td><label for="keywords">Keywords</label>:</td>
							<td><input type="text" name="keywords" id="keywords" value="<?php echo $config->get('keywords'); ?>" /></td>
						</tr>
						<tr>
							<td><label for="email">E-mail:</label></td>
							<td><input type="text" name="email" id="email" value="<?php echo $config->get('contactemail'); ?>" /></td>
						</tr>
						<tr>
							<td><label for="perpage">PerPage*:</label></td>
							<td><input type="text" name="perpage" id="perpage" value="<?php echo $config->get('maxperpage'); ?>" /></td>
						</tr>
						<tr>
							<td><label for="currency">Currency*:</label></td>
							<td><input type="text" name="currency" id="currency" value="<?php echo $config->get('currency'); ?>" /></td>
						</tr>
						<tr>
							<td colspan="2"><h1>Paypal Information</h1></td>
						</tr>
						<tr>
							<td><label for="paypal_sandbox">Use Sandbox Server? (to test system):</label></td>
							<td>
								<select name="paypal_sandbox" id="paypal_sandbox">
									<?php echo $pp_sandbox_select; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><label for="paypal_log">Paypal Log (log all transactions? recommended):</label></td>
							<td>
								<select name="paypal_log" id="paypal_log">
									<?php echo $pp_log_select; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><label for="paypal_email">Paypal E-mail:</label></td>
							<td><input type="text" name="paypal_email" id="paypal_email" value="<?php echo $config->get('paypal_email'); ?>" /></td>
						</tr>
						<tr>
							<td colspan="2"><h1>Change Password</h1></td>
						</tr>
						<tr>
							<td><label for="current_pass">Current Password:</label></td>
							<td><input type="password" name="current_pass" id="current_pass" /></td>
						</tr>
						<tr>
							<td><label for="new_pass">New Password:</label></td>
							<td><input type="password" name="new_pass" id="new_pass" /></td>
						</tr>
						<tr>
							<td><label for="cnew_pass">Confirm New Password:</label></td>
							<td><input type="password" name="cnew_pass" id="cnew_pass" /></td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="submit" value="Update" class="submit" /></td>
						</tr>
						</tbody>
						</table>
						</form>
						<br />
					</div>
					<div id="pages">
						<p>* Per Page: refers to pagination. How many domains per page?</p>
						<p>* Currency: only enter the symbol, e.g: '$'</p>
					</div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>