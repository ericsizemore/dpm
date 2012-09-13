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

/**
* Process/handle Paypal IPN transaction.
*
* This file was not part of the original DNP, this is exclusive to DPM.
*/
define('IN_DPM', true);
define('IN_PAYPAL', true);
require_once('./includes/global.php');
require_once('./includes/paypal.class.php');

// ################################################################
$paypal = Paypal_IPN::getInstance();
$paypal->init(array(
	'use_sandbox' => $config->get('paypal_sandbox'),
	'use_log'     => $config->get('paypal_log'),
	'business'    => $config->get('paypal_email')
), $db);

$path = str_replace(dpm_getenv('DOCUMENT_ROOT'), '', realpath('.')) . '/';
$path = str_replace('//', '/', $path);
$host = HOST;

$this_url = "http://{$host}/{$path}paypal.php";

if (empty($_GET['action']))
{
	$_GET['action'] = 'process';
}

// ################################################################
switch ($_GET['action'])
{
	case 'process':
		if ($config->get('paypal_email') == '')
		{
			redirect('index.php');
		}

		// Valid ID?
		$domainid = (is_string($_GET['d'])) ? sanitize($_GET['d']) : intval($_GET['d']);

		if (!is('domain', $domainid))
		{
			redirect('index.php');
		}

		$domain = get_value('details', $domainid);

		if (count($domain) == 0 OR $domain['status'] == 'Sold' OR $domain['status'] == 'Pending Sale' OR $domain['hidden'] == 1)
		{
			redirect('index.php');
		}

		$paypal->add_field('business', $paypal->config['business']);
		$paypal->add_field('return', "$this_url?action=success");
		$paypal->add_field('cancel_return', "$this_url?action=cancel");
		$paypal->add_field('notify_url', "$this_url?action=ipn");
		$paypal->add_field('item_name', $domain['domain']);
		$paypal->add_field('item_number', $domain['domainid']);
		$paypal->add_field('custom', time());
		$paypal->add_field('amount', $domain['price']);
		$paypal->submit_request();
		break;
	case 'success':
		echo '<html><head><title>Success</title></head><body><h3>Thank you for your payment. We will try to contact you as soon as possible.</h3>';
		echo '</body></html>';
		break;
	case 'cancel':
		echo '<html><head><title>Canceled</title></head><body><h3>The payment was canceled.</h3></body></html>';
		break;
	case 'ipn':
		$paypal_response = '';

		foreach ($_POST AS $key => $value)
		{
			$paypal_response .= "\n$key: $value";
		}

		if ($paypal->validate_ipn())
		{
			if (is('domain', $paypal->data['item_number']))
			{
				$domain = get_value('paypal', $paypal->data['item_number']);

				if (count($domain) == 0 OR $domain['status'] == 'Sold' OR $domain['hidden'] == 1)
				{
					$subject = ' - Invalid';
					$message = "Paypal IPN response validated, but invalid domain id (item_number) detected in the Paypal transaction, potential tampering.\n\nPaypal sent:\n\n$paypal_response";
				}
				else
				{
					$db->query("
						UPDATE " . TABLE_PREFIX . "domains 
						SET status = 'Pending Sale' 
						WHERE domainid = " . intval($paypal->data['item_number']) . "
					");

					$subject = ' - Verified - ' . $domain['domain'];
					$message = "Paypal IPN response validated and domain set to Pending Sale. Once you verify the payment with the buyer and complete the transfer of ownership, do not forget to mark the domain as Sold.\n\nFor your information, Paypal sent:\n\n$paypal_response";
				}
			}
			else
			{
				$subject = ' - Invalid';
				$message = "Paypal IPN response validated, but invalid domain id (item_number) detected in the Paypal transaction, potential tampering.\n\nPaypal sent:\n\n$paypal_response";
			}
		}
		else
		{
			$subject = ' - Not Verified';
			$message = "Paypal IPN response could not be validated.\n\nPaypal sent:\n\n$paypal_response";
		}

		require_once('./includes/emailer.class.php');

		$params = array(
			'domain'          => $domain['domain'], 
			'message'         => $message,
			'first_name'      => $paypal->data['first_name'],
			'last_name'       => $paypal->data['last_name'],
			'payer_email'     => $paypal->data['payer_email'],
			'address_country' => $paypal->data['address_country'],
			'address_street'  => $paypal->data['address_street'],
			'address_city'    => $paypal->data['address_city'],
			'address_state'   => $paypal->data['address_state'],
			'address_zip'     => $paypal->data['address_zip'],
			'item_name'       => $paypal->data['item_name'],
			'item_number'     => $paypal->data['item_number'],
			'price'           => $domain['price'],
			'mc_gross'        => $paypal->data['mc_gross'],
			'mc_fee'          => $paypal->data['mc_fee'],
			'total'           => $paypal->data['mc_gross'] - $paypal->data['mc_fee'],
			'mc_currency'     => $paypal->data['mc_currency'],
			'txn_id'          => $paypal->data['txn_id']
		);

		$emailer = emailer::getInstance();
		$emailer->set_params($config->get('contactemail'), NULL, "DPM::Paypal Payment$subject");
		$emailer->use_template($params, 'email_paypal.tpl');
		$emailer->send();
		break;
}

?>