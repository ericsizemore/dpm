<?php

/**
* @author    Eric Sizemore <admin@secondversion.com>
* @package   Domain Portfolio Manager
* @link      http://domain-portfolio.secondversion.com/
* @version   1.0.1
* @copyright (C) 2010 - 2012 Eric Sizemore
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
*/

/**
* April 23rd, 2010: I sold Domain Name Portfolio 1.6.0 to Stephen Cox.
* June 21st, 2010 : Stephen sold it to Stu Buckingham. 
*
* As of June 21st, 2010 - Stu Buckingham technically is the copyright holder to DNP up to
* 1.6.x and he rebranded to DNS Portfolio.
*
* After more than a year of different people coming to me, wanting me to continue working on 
* the script, I finally decided to pick it back up again. Since it is under the GNU GPL, I am 
* able to do so. My continuation of the script will be called Domain Portfolio Manager.
*/

define('IN_DPM', true);
define('IN_ADMIN', true);
require_once('../includes/global.php');

// Logged in?
if (!$adm->verify_auth())
{
	redirect('index.php');
}

// ################################################################
$result = '';

// Update config
if (!empty($_POST['submit']))
{
	$new_title      = sanitize($_POST['title']);
	$new_descr      = sanitize($_POST['description']);
	$new_keys       = sanitize($_POST['keywords']);
	$new_email      = sanitize($_POST['email']);
	$new_ppage      = intval($_POST['perpage']);
	$new_currency   = sanitize($_POST['currency']);
	$new_pp_sandbox = sanitize($_POST['paypal_sandbox']);
	$new_pp_log     = sanitize($_POST['paypal_log']);
	$new_pp_email   = sanitize($_POST['paypal_email']);
	$current_pass   = sanitize($_POST['current_pass']);
	$new_pass       = sanitize($_POST['new_pass']);
	$cnew_pass      = sanitize($_POST['cnew_pass']);

	$updates = array();

	// Wanting to change password?
	if (!empty($new_pass))
	{
		if (empty($current_pass) OR dpm_hash(dpm_hash($current_pass)) != $_SESSION['dpm_admin_kp'])
		{
			$result .= 'Sorry, either you did not enter your current password, or it does not match your current password.';
		}
		else if ($new_pass != $cnew_pass)
		{
			$result .= 'Your new password and confirm new password did not match.';
		}
		else
		{
			$db->query("
				UPDATE " . TABLE_PREFIX . "admin
				SET password = '" . dpm_hash($new_pass) . "'
				WHERE adminid = " . intval($_SESSION['dpm_admin_id']) . "
			") or $db->raise_error();
		}
	}

	// Site options...
	if (empty($new_ppage))
	{
		$new_ppage = 10;
	}

	if (empty($new_currency))
	{
		$new_currency = '$';
	}

	if (empty($new_pp_sandbox))
	{
		$new_pp_sandbox = 0;
	}

	if (empty($new_pp_log))
	{
		$new_pp_log = 0;
	}

	if (empty($new_title) OR empty($new_descr) OR empty($new_keys) OR empty($new_email))
	{
		$result .= 'Required field(s) left empty. Required fields are:<br /><code>Title, Description, Keywords, Email</code>';
	}
	else if (!is('email', $new_email))
	{
		$result .= 'Sorry, but the email you entered is invalid.';
	}
	else if (mb_strlen($new_currency, 'utf-8') != 1)
	{
		$result .= 'Seems you entered an invalid currency. Only enter the symbol, eg: $';
	}
	else if (!empty($new_pp_email) AND !is('email', $new_pp_email))
	{
		$result .= 'Sorry, but it appears the paypal email you entered is invalid.';
	}
	else
	{
		$updates = array(
			'title'           => $db->prepare($new_title),
			'description'     => $db->prepare($new_descr),
			'keywords'        => $db->prepare($new_keys),
			'maxperpage'      => $db->prepare($new_ppage),
			'contactemail'    => $db->prepare($new_email),
			'currency'        => $db->prepare($new_currency),
			'paypal_sandbox'  => $db->prepare($new_pp_sandbox),
			'paypal_log'      => $db->prepare($new_pp_log),
			'paypal_email'    => $db->prepare($new_pp_email)
		);

		foreach ($updates AS $option => $value)
		{
			$config->update($option, $value);
		}
		unset($updates);

		$result .= 'Site configuration updated.';
	}
	$result = "<div id=\"result\">$result</div>";

	$config->get('', true);
}

// ################################################################
// Output page
$pagetitle = 'Site Configuration';

$pp_sandbox_select = build_select('paypal_sandbox', $config->get('paypal_sandbox'));
$pp_log_select = build_select('paypal_log', $config->get('paypal_log'));

include("$template_admin/site_config.php");

?>