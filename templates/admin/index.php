<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>
						Please use the following form to login to the admin area.<br /><br />
						If you have forgotten your password, please use the "Forgot your password?" link.<br /><br />
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
						<h1>Login</h1>
						<form method="post" action="index.php">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td><label for="user">Username:</label></td>
							<td><input type="text" name="user" id="user" /></td>
						</tr>
						<tr>
							<td><label for="pass">Password:</label></td>
							<td><input type="password" name="pass" id="pass" /></td>
						</tr>
						<tr>
							<td colspan="2"><a href="./?mode=lostpass" title="Forgot your password?">Forgot your password?</a></td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="submit" value="Login" class="submit" /></td>
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