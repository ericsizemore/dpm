<?php

/**
* @author    Eric Sizemore <admin@secondversion.com>
* @package   Domain Portfolio Manager
* @link      http://domain-portfolio.secondversion.com/
* @version   1.0.1
* @copyright (C) 2010 - 2011 Eric Sizemore
* @license   http://domain-portfolio.secondversion.com/docs/license.html GNU Public License
*
*            This program is free software: you can redistribute it and/or modify
*            it under the terms of the GNU General Public License as published by
*            the Free Software Foundation, either version 3 of the License, or
*            (at your option) any later version.
*
*            This program is distributed in the hope that it will be useful,
*            but WITHOUT ANY WARRANTY; without even the implied warranty of
*            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*            GNU General Public License for more details.
*
*            You should have received a copy of the GNU General Public License
*            along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @file      ./install/install.php
*/

/**
* April 23rd, 2010: I sold Domain Name Portfolio 1.6.0 to Stephen Cox.
* June 21st, 2010 : Stephen sold it to Stu Buckingham. 
*
* As of June 21st, 2010 - Stu Buckingham technically is the copyright holder to DNP up to
* 1.6.x and he rebranded to DNS Portfolio.
*
* The script was originally created by me, Eric Sizemore, and has always been under
* the GNU GPL. So, with that in mind, I decided to continue development as the rights
* to 1.7.0 that was in development was not sold. Even with my rights to the code sold
* for previous versions, with the GPL, I still have the right to fork or continue development.
*
* After more than a year of different people coming to me, wanting me to continue working on 
* Domain Portfolio Manager, I finally decided to pick it back up again.
*
* So, this code now is based on the unreleased 1.7.0 I had, and has been re-versioned to start
* at 1.0.0.
*
* Enjoy. :)
*/

// ########################################################################
ignore_user_abort(true);

define('IN_DPM', true);
define('IN_INSTALL', true);

// ########################################################################
function do_queries()
{
	global $db, $query, $explain;

	if (is_array($query))
	{
		echo '<ul>';

		foreach ($query AS $key => $val)
		{
			if (is_array($val))
			{
				$val = array_pop($val);
			}

			echo "<li>$explain[$key]</li>\n<!-- " . htmlspecialchars($val) . " -->\n\n";
			flush();

			if (!$db->query($val))
			{
				echo $db->error();
			}
		}

		echo '</ul>';
	}
	unset($GLOBALS['query'], $GLOBALS['explain']);
}

// ################################################################
require_once('../includes/global.php');

$_REQUEST['step'] = (empty($_REQUEST['step'])) ? 1 : intval($_REQUEST['step']);

// We're in install, we want to show db errors
$db->is_admin = true;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Domain Portfolio Manager Install - Step <?php echo $_REQUEST['step']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="./style/install.css" />
</head>

<body style="margin: 0px">

<div id="body">
	<table cellpadding="4" cellspacing="0" border="0" width="100%" align="center" class="table" style="border: outset 1px;">
	<tr>
		<td width="180" style="padding-left: 20px;"><img src="./style/logo.png" alt="Domain Portfolio Manager" border="0" /></td>
		<td style="padding-left: 30px; font-weight: bold;">
			Version <?php echo $version; ?> Install<br />
			Step <?php echo $_REQUEST['step']; ?>
		</td>
	</tr>
	</table> 
	<form action="install.php" method="post">
<?php

// ########################################################################
if ($_REQUEST['step'] == 1)
{
	echo '
	<blockquote>
		<p style="font-weight: bold;">Welcome to Domain Portfolio Manager version ' . $version . '</p>
		<p>You are about to perform an install. Need to <a href="./upgrade.php">Upgrade</a> instead?</p>
		<p>Clicking the <code>Next Step</code> button will begin the installation process.</p>
		<p style="font-weight: bold">By installing, you are agreeing to our license, the <a href="license.txt" target="_blank">GNU GPL v3</a></p>
	</blockquote>
';
}

if ($_REQUEST['step'] == 2)
{
	require_once('mysql-install.php');
	$query =& $schema['CREATE']['query'];
	$explain =& $schema['CREATE']['explain'];
	do_queries();
}

if ($_REQUEST['step'] == 3)
{
?>
	<table>
	<tbody>
	<tr>
		<td style="font-weight: bold;">Admin Username:* <small><em>Only a-z 0-9 _</em></small></td>
		<td style="text-align: left;"><input type="text" name="username" maxlength="32" /></td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Admin Password:*</td>
		<td style="text-align: left;"><input type="password" name="password" /></td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Admin Password (again):*</td>
		<td style="text-align: left;"><input type="password" name="password2" /></td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Admin Contact Email:*</td>
		<td style="text-align: left;"><input type="text" name="email" maxlength="255" value="you@domain.com" /></td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Portfolio Title:</td>
		<td style="text-align: left;"><input type="text" name="title" maxlength="255" value="My Domain Portfolio" /></td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Portfolio Description:</td>
		<td style="text-align: left;"><input type="text" name="description" maxlength="255" value="My domain name portfolio." /></td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Portfolio Keywords:</td>
		<td style="text-align: left;"><input type="text" name="keywords" maxlength="255" value="my, domain, portfolio" /></td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Domains Per Page:</td>
		<td style="text-align: left;"><input type="text" name="perpage" maxlength="4" value="25" /></td>
	</tr>
	<tr>
		<td style="font-weight: bold;">Your Currency:</td>
		<td style="text-align: left;"><input type="text" name="currency" maxlength="1" value="$" /></td>
	</tr>
	</tbody>
	</table>
<?php
}

if ($_REQUEST['step'] == 4)
{
	$username = preg_replace('#([^a-z0-9\_]+)#i', '', sanitize($_POST['username']));
	$password = str_replace(array('\'', '"'), '', sanitize($_POST['password']));
	$password2 = str_replace(array('\'', '"'), '', sanitize($_POST['password2']));
	$email = sanitize($_POST['email'], true, true);
	$title = sanitize($_POST['title']);
	$keywords = sanitize($_POST['keywords']);
	$description = sanitize($_POST['description']);
	$perpage = intval($_POST['perpage']);
	$currency = sanitize($_POST['currency']);

	$errors = array();

	// ################################################################
	// Check to see if the required fields are empty
	if (empty($username))
	{
		array_push($errors, 'You must supply a username');
	}

	if (empty($password))
	{
		array_push($errors, 'You must supply a password');
	}

	if (empty($password2))
	{
		array_push($errors, 'You must enter your password again');
	}

	if (empty($email) OR !is('email', $email))
	{
		array_push($errors, 'You must enter a valid email');
	}

	if ($password != $password2)
	{
		array_push($errors, 'The passwords you entered do not match');
	}

	// ################################################################
	// These aren't required. If they don't enter them, set to default
	$title = ($title == '') ? 'My Domain Portfolio' : $title;
	$description = ($description == '') ? 'My domain name portfolio.' : $description;
	$keywords = ($keywords == '') ? 'my, domain, portfolio' : $keywords;
	$perpage = ($perpage == 0) ? 25 : $perpage;
	$currency = (mb_strlen($currency, 'UTF-8') != 1) ? '$' : $currency;

	if (count($errors))
	{
		echo "The following errors occurred:<br />\n<ul>\n";

		foreach ($errors AS $error)
		{
			echo "<li>$error</li>\n";
		}

		echo "</ul>\n";
		unset($errors);

		$_REQUEST['step'] = 3;
	}
	else
	{
		require_once('mysql-install.php');
		$query =& $schema['INSERT']['query'];
		$explain =& $schema['INSERT']['explain'];
		do_queries();
	}
}

if ($_REQUEST['step'] == 5)
{
	echo '
	<blockquote>
		<p style="font-weight: bold;">Installation Complete!</p>
		<p>Thank you for installing Domain Portfolio Manager, you can now beging <a href="../admin/">adding</a> domains to your portfolio.</p>
		<p>Please delete the <code>./install/</code> directory before continuing.</p>
	</blockquote>
';
}

?>
	<input type="hidden" name="step" value="<?php echo $_REQUEST['step'] + 1; ?>" />
	<table cellpadding="4" cellspacing="0" border="0" width="100%" align="center" class="table" style="padding: 4px; border: outset 1px;">
	<tr align="center">
<?php

if ($_REQUEST['step'] < 5)
{
?>
		<td style="font-weight: bold;">Click the button on the right to proceed.</td>
		<td><input type="submit" class="button" value="Next Step" title="Next Step" /></td>
<?php
}
else
{
?>
		<td colspan="2">&nbsp;</td>
<?php
}

?>
	</tr>
	</table>
	</form>
</div>
<p align="center">
	<a href="http://domain-portfolio.secondversion.com/" target="_blank" class="copyright">Copyright &copy; 2010 - <?php echo date('Y'); ?> Eric Sizemore (SecondVersion)</a>
</p>

</body>
</html>