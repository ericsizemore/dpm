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
* @file      ./install/upgrade.php
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

// Not even needed yet.
die('Not yet needed.');

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

// We're in upgrade, we want to show db errors
$db->is_admin = true;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Domain Portfolio Manager Upgrade - Step <?php echo $_REQUEST['step']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="./style/install.css" />
</head>

<body style="margin: 0px">

<div id="body">
	<table cellpadding="4" cellspacing="0" border="0" width="100%" align="center" class="table" style="border: outset 1px;">
	<tr>
		<td width="180" style="padding-left: 20px;"><img src="./style/logo.png" alt="Domain Portfolio Manager" border="0" /></td>
		<td style="padding-left: 30px; font-weight: bold;">
			Version <?php echo $version; ?> Upgrade<br />
			Step <?php echo $_REQUEST['step']; ?>
		</td>
	</tr>
	</table>
	<form action="upgrade.php" method="post">
<?php

// ########################################################################
if ($_REQUEST['step'] == 1)
{
	echo '
	<blockquote>
		<p style="font-weight: bold;">Welcome to Domain Portfolio Manager version ' . $version . '</p>
		<p>You are about to perform an upgrade.</p>
		<p>Clicking the <code>Next Step</code> button will begin the upgrade process.</p>
	</blockquote>
';
}

if ($_REQUEST['step'] == 2)
{
?>
	<table>
	<tbody>
	<tr>
		<td style="font-weight: bold;">Please choose the version you are upgrading from:</td>
		<td style="text-align: left;">
			<select name="from">
				<option label="1.0.x" value="1.0">1.0.x</option>
			</select>
		</td>
	</tr>
	<tr>
		<td style="font-weight: bold;" colspan="2">
			By upgrading, you are agreeing to our license, the <a href="license.txt" target="_blank">GNU GPL v3</a>
		</td>
	</tr>
	</tbody>
	</table>
<?php
}

if ($_REQUEST['step'] == 3)
{
	$from = sanitize($_POST['from']);

	$errors = array();

	// ################################################################
	// Check to see if the required fields are empty
	if (empty($from))
	{
		$errors[] = 'You must choose a version to upgrade from';
	}

	if (!in_array($from, array('1.0')))
	{
		$errors[] = 'Invalid version to upgrade from';
	}

	if (count($errors))
	{
		echo "The following errors occurred:<br />\n<ul>\n";

		foreach ($errors AS $error)
		{
			echo "<li>$error</li>\n";
		}

		echo "</ul>\n";
		unset($errors);

		$_REQUEST['step'] = 2;
	}
	else
	{
		require_once('mysql-upgrade.php');

		/**
		* Upgrade not needed yet, this is here as a placeholder if it comes to be needed.
		*
		* $query =& $schema['ALTER_{versioncode}']['query'];
		* $explain =& $schema['ALTER_{versioncode}']['explain'];
		* do_queries();
		* 
		* $query =& $schema['CREATE_{versioncode}']['query'];
		* $explain =& $schema['CREATE_{versioncode}']['explain'];
		* do_queries();
		*/
	}
}

if ($_REQUEST['step'] == 4)
{
	echo '
	<blockquote>
		<p style="font-weight: bold;">Upgrade Complete!</p>
		<p>Thank you for upgrading Domain Portfolio Manager.</p>
		<p>Please delete the <code>./install/</code> directory before continuing.</p>
	</blockquote>
';
}

?>
	<input type="hidden" name="step" value="<?php echo $_REQUEST['step'] + 1; ?>" />
	<table cellpadding="4" cellspacing="0" border="0" width="100%" align="center" class="table" style="padding: 4px; border: outset 1px;">
	<tr align="center">
<?php

if ($_REQUEST['step'] < 4)
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