<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>
						Please use the following form to send us an e-mail. We will try to respond as soon as possible.<br /><br />
					</p>
<?php

if ($result != ''):
?>
					<div id="result"><?php echo $result; ?></div>
					<br />
<?php
endif;
?>
					<div id="table">
						<h1>Contact</h1>
						<form action="./contact.php?mode=general" method="post" style="display: inline;" onsubmit="return validate_form('general');">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td><label for="sender_name">Name:*</label></td>
							<td><input type="text" name="sender_name" id="sender_name" maxlength="100" value="<?php echo $_SESSION['form']['name']; ?>" /></td>
						</tr>
						<tr>
							<td><label for="sender_email">E-mail:*</label></td>
							<td><input type="text" name="sender_email" id="sender_email" maxlength="100" value="<?php echo $_SESSION['form']['email']; ?>" /></td>
						</tr>
						<tr>
							<td><label for="sender_phone">Phone Number:*</label></td>
							<td><input type="text" name="sender_phone" id="sender_phone" maxlength="15" value="<?php echo $_SESSION['form']['phone']; ?>" /> <small>(numbers only, at least 10 numbers, eg: 5555555555)</td>
						</tr>
						<tr>
							<td><label for="sender_subject">Subject:*</label></td>
							<td>
								<select name="sender_subject" id="sender_subject">
									<option label="Advertising" value="Advertising">Advertising</option>
									<option label="Site Problem" value="Site Problem">Site Problem</option>
									<option label="Suggestions" value="Suggestions">Suggestions</option>
									<option label="Other" value="Other">Other</option>
								</select>
							</td>
						</tr>
						<tr>
							<td valign="top"><label for="sender_message">Message:*</label></td>
							<td><textarea name="sender_message" id="sender_message" rows="4" cols="35"><?php echo $_SESSION['form']['message']; ?></textarea></td>
						</tr>
						<tr>
							<td valign="top" colspan="2" class="center"><?php echo $recaptcha->get_html($recaptcha_error); ?></td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="submit" value="Submit" class="submit" /></td>
						</tr>
						</tbody>
						</table>
						</form>
						<br />
					</div>
					<div id="pages">&nbsp;</div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>