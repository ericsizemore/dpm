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
* @file      ./admin/index.php
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
* at 1.0.0. It's new name under me is: Domain Portfolio Manager.
*
* Enjoy. :)
*/

define('IN_DPM', true);
define('IN_ADMIN', true);
require_once('../includes/global.php');

// Already Logged in?
if ($adm->verify_auth())
{
	redirect('admin.php');
}

// ################################################################
$result = '';

// Reset Password
if (isset($_GET['mode']) AND $_GET['mode'] == 'lostpass')
{
	if (!empty($_POST['lostpass_submit']))
	{
		$username = sanitize(preg_replace('#([^a-z0-9\_]+)#i', '', $_POST['user']));
		$email    = sanitize($_POST['email'], true, true);

		if (!is('email', $email))
		{
			$result = 'Sorry, the email you entered appears to be invalid.';
		}
		else
		{
			$admin = $db->query("
				SELECT username
				FROM " . TABLE_PREFIX . "admin
				WHERE username = '" . $db->prepare($username) . "'
			") or $db->raise_error();

			if ($db->num_rows($admin) AND $email == $config->get('contactemail'))
			{
				$admin = $db->fetch_array($admin);
				$pass  = substr(dpm_hash(uniqid(time(), true)), 0, 8);

				$db->query("
					UPDATE " . TABLE_PREFIX . "admin
					SET password = '" . dpm_hash($pass) . "'
					WHERE username = '" . $db->prepare($username) . "'
				") or $db->raise_error();

				require_once('../includes/emailer.class.php');

				$emailer = emailer::getInstance();
				$emailer->set_params($config->get('contactemail'), NULL, 'DPM - Password reset');
				$emailer->use_template(array(
					'name' => $admin['username'],
					'pass' => $pass,
					'ip'   => get_ip()
				), 'admin/lostpass_email.tpl');
				$emailer->send();

				$result = 'Password reset, please check your email.';

				// Naughty me...
				$result .= '<script language="Javascript">function redirect(){location.href = \'./\';} setTimeout(\'redirect()\', 1000);</script>';
			}
			else
			{
				$result = 'The email and/or username you entered does not match the one we have on file.';
			}
		}
		$result = "<div id=\"result\">$result</div>";
	}

	// Output page
	$pagetitle = 'Lost Password';

	include("$template_admin/lostpass.php");
	exit;
}

// ################################################################
// Process login
if (!empty($_POST['submit']))
{
	$username = sanitize(preg_replace('#([^a-z0-9\_]+)#i', '', $_POST['user']));
	$password = sanitize(str_replace(array('\'', '"'), '', $_POST['pass']));

	if (empty($username) OR empty($password))
	{
		$result .= 'Please enter both username and password.';
	}
	else
	{
		if ($adm->do_login($username, dpm_hash($password)))
		{
			redirect('admin.php');
		}
		else
		{
			$result .= 'Invalid username and/or password. Please try again.';
		}
	}
	$result = "<div id=\"result\">$result</div>";
}

// ################################################################
// Output page
$pagetitle = 'Login';

include("$template_admin/index.php");

?>