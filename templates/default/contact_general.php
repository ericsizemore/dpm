<?php include('header.php'); ?>
			<div id="right">
				<h2>Contact</h2>
				<p>
					Please use the following form to send us an e-mail. We will try to respond as soon as possible.
				</p>
				<br />
<?php

if ($result != ''):
?>
				<div id="result"><?php echo $result; ?></div>
				<br />
<?php
endif;
?>
				<form action="./contact.php?mode=general" method="post" style="display: inline;" onsubmit="return validate_form('general');">
				<table cellspacing="0">
				<tbody>
				<tr class="odd">
					<td class="one"><label for="sender_name">Name:*</label></td>
					<td class="two"><input type="text" name="sender_name" id="sender_name" maxlength="100" value="<?php echo $_SESSION['form']['name']; ?>" /></td>
				</tr>
				<tr class="even">
					<td class="one"><label for="sender_email">E-mail:*</label></td>
					<td class="two"><input type="text" name="sender_email" id="sender_email" maxlength="100" value="<?php echo $_SESSION['form']['email']; ?>" /></td>
				</tr>
				<tr class="odd">
					<td class="one"><label for="sender_phone">Phone Number:*</label></td>
					<td class="two"><input type="text" name="sender_phone" id="sender_phone" maxlength="15" value="<?php echo $_SESSION['form']['phone']; ?>" /> <small>(numbers only, at least 10 numbers, eg: 5555555555)</td>
				</tr>
				<tr class="even">
					<td class="one"><label for="sender_subject">Subject:*</label></td>
					<td class="two">
						<select name="sender_subject" id="sender_subject">
							<option label="Advertising" value="Advertising">Advertising</option>
							<option label="Site Problem" value="Site Problem">Site Problem</option>
							<option label="Suggestions" value="Suggestions">Suggestions</option>
							<option label="Other" value="Other">Other</option>
						</select>
					</td>
				</tr>
				<tr class="odd">
					<td valign="top" class="one"><label for="sender_message">Message:*</label></td>
					<td class="two"><textarea name="sender_message" id="sender_message" rows="4" cols="35"><?php echo $_SESSION['form']['message']; ?></textarea></td>
				</tr>
				<tr class="even">
					<td valign="top" colspan="2" class="one"><?php echo $recaptcha->get_html($recaptcha_error); ?></td>
				</tr>
				<tr class="odd">
					<td colspan="2" class="two"><input type="submit" name="submit" value="Submit" class="button" /></td>
				</tr>
				</tbody>
				</table>
				</form>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>