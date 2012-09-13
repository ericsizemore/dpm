<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>
						Please use the following form to reset your password.<br /><br />
						Once requesting a password reset, you should receive an email with a confirmation link.<br /><br />
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
						<h1>Lost Password</h1>
						<form method="post" action="index.php?mode=lostpass">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td><label for="user">Username:</label></td>
							<td><input type="text" name="user" id="user" /></td>
						</tr>
						<tr>
							<td><label for="email">Email:</label></td>
							<td><input type="text" name="email" id="email" /></td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="lostpass_submit" value="Reset" class="submit" /></td>
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