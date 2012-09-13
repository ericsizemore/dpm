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

// for javascript in header file
define('IN_CONTACT', true);

define('IN_DPM', true);
require_once('./includes/global.php');

// ################################################################
$mode = sanitize($_GET['mode']);
$mode = (!in_array($mode, array('general', 'domain'))) ? 'general' : $mode;

// Are we contacting about a certain domain?
if ($mode == 'domain')
{
	// Valid ID?
	$domainid = (is_string($_GET['d'])) ? sanitize($_GET['d']) : intval($_GET['d']);

	if (!is('domain', $domainid))
	{
		redirect('index.php');
	}

	// Pull the domain and it's status.
	$getdomain = get_value('contact', $domainid);

	if (count($getdomain) == 0)
	{
		redirect('index.php');
	}

	$domain = $getdomain['domain'];
	$status = $getdomain['status'];

	unset($getdomain);

	// Has the domain been sold, or is it not for sale?
	if (in_array($status, array('Sold', 'Not For Sale', 'Pending Sale')))
	{
		redirect('index.php');
	}
}

$result = '';

// ################################################################
// Process the form and send the email..
require_once('./includes/recaptcha.class.php');

$recaptcha = recaptcha::getInstance();
$recaptcha_error = NULL;

if (!empty($_POST['submit']))
{
	$name = sanitize($_POST['sender_name']);
	$email = sanitize($_POST['sender_email']);
	$phone = sanitize($_POST['sender_phone']);
	$phone = preg_replace('#([^0-9\.]+)#', '', $phone);

	// meh, if they try it...
	$phone = ($phone == '5555555555' OR $phone == '1111111111') ? '' : $phone;

	// If this is concerning a domain, we'll check for an offer.
	if ($mode == 'domain')
	{
		$offer = sanitize(str_replace($config->get('currency'), '', $_POST['sender_offer']));
		$offer = preg_replace('#\.[0-9]{2}$#', '', $offer);
		$offer = preg_replace('#([^0-9,]+)#', '', $offer);
	}
	else
	{
		// Otherwise, we need an email subject.
		$subject = sanitize($_POST['sender_subject']);
	}

	// Email body/message...
	$message = str_replace("\r\n", "\n", $_POST['sender_message']);
	$message = wordwrap(sanitize($message, false), 75);

	// reCAPTCHA - Just say no, to spam. :)
	$recaptcha_challenge = sanitize($_POST['recaptcha_challenge_field']);
	$recaptcha_response = sanitize($_POST['recaptcha_response_field']);

	/**
	* Create a session value for name, email, and message.
	* This way, if there's an error, a user won't lose what they've entered.
	*/
	$_SESSION['form'] = array(
		'name'    => $name,
		'email'   => $email,
		'phone'   => $phone,
		'message' => $message
	);

	// Holds any errors that may happen with entered data
	$errors = array();

	// We need to make sure all data is there/valid...
	if (empty($name) OR is('injection', $name))
	{
		$errors[] = 'Your name is required.';
	}

	if (empty($email))
	{
		$errors[] = 'Your email is required.';
	}

	if (empty($phone))
	{
		$errors[] = 'Your phone number is required.';
	}

	if (empty($message))
	{
		$errors[] = 'A message is required.';
	}

	if (!is('email', $email) OR is('injection', $email))
	{
		$errors[] = 'Email is invalid.';
	}

	if (strlen($phone) < 10)
	{
		$errors[] = 'Your phone number must be at least 10 characters in length.';
	}

	if ($mode == 'general' AND empty($subject))
	{
		$errors[] = 'A subject is required.';
	}

	if (is('spam', $message))
	{
		$errors[] = 'Sorry, but your message seemed a bit like spam.';
	}

	if (count($errors) > 0)
	{
		$result .= 'The following errors occurred:<br /><ul>';

		foreach ($errors AS $error)
		{
			$result .= "<li>$error</li>\n";
		}

		$result .= '</ul>';

		unset($errors);
	}
	else
	{
        $resp = $recaptcha->check_answer(get_ip(), $recaptcha_challenge, $recaptcha_response);

		if (!$resp->is_valid)
		{
			$recaptcha_error = $resp->error;
			$result .= 'reCAPTCHA: Incorrect. Try again.';
		}
		else
		{
			$recaptcha_error = NULL;

			$params = array(
				'name'    => $name,
				'email'   => $email,
				'phone'   => $phone,
				'ip'      => get_ip(),
				'message' => $message
			);

			if ($mode == 'domain')
			{
				$params['domain'] = $domain;
				$params['offer'] = (empty($offer) ? 'n/a' : $offer);
			}
			else
			{
				$params['subject'] = $subject;
			}

			require_once('./includes/emailer.class.php');

			$emailer = emailer::getInstance();
			$emailer->set_params($config->get('contactemail'), $email, ($mode == 'domain' ? "Domain Inquiry: $domain" : "Inquiry: $subject"));
			$emailer->use_template($params, ($mode == 'domain' ? 'email.tpl' : 'email_general.tpl'));

			if ($emailer->send())
			{
				$result .= "Thank you, $name, your inquiry was sent.";

				// Reset the session array
				$_SESSION['form'] = array(
					'name'    => '',
					'email'   => '',
					'phone'   => '',
					'message' => ''
				);
			}
			else
			{
				$result .= 'Seems to have been a problem sending the email. Please try again.';
			}
		}
	}
}
else
{
	$_SESSION['form'] = array(
		'name'    => '',
		'email'   => '',
		'phone'   => '',
		'message' => ''
	);
}

// ################################################################
// Output page
$pagetitle = ($mode == 'domain') ? "Inquiring about $domain" : 'General Inquiry';

if ($mode == 'domain')
{
	include("$template/contact.php");
}
else
{
	include("$template/contact_general.php");
}

?>