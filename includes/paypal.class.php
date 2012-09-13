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

if (!defined('IN_DPM'))
{
	die('You\'re not supposed to be here.');
}

/**
* Class to handle Paypal IPN transactions.
*
* This file was not part of the original DNP, this is exclusive to DPM.
*/
class Paypal_IPN
{
	/**
	* Paypal's sandbox url 
	*/
	const SANDBOX_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

	/**
	* Paypal's main url 
	*/
	const STANDARD_URL = 'https://www.paypal.com/cgi-bin/webscr';

	/**
	* Class instance.
	* 
	* @var	object
	*/
	private static $instance;

	/**
	* Holds the most recent error, if any.
	* 
	* @var	string
	*/
	private $error = '';

	/**
	* Holds Paypal's IPN respnse
	* 
	* @var	string
	*/
	private $response = '';

	/**
	* POST data from Paypal's IPN
	* 
	* @var	array
	*/   
	public $data = array();

	/**
	* Fields to be submitted to Paypal
	* 
	* @var	array
	*/
	private $fields = array();

	/**
	*/
	public $config = array(
		'use_sandbox' => false,
		'use_log'     => true,
		'business'    => ''
	);

	/**
	*/
	private $dbobj; 

	/**
	* Constructor. Sets required/initial fields for the Paypal request.
	* 
	* @param	void
	* @return	void
	*/
	private function __construct()
	{
		$this->add_field('rm', '2');
		$this->add_field('cmd', '_xclick');
	}

	/**
	* Creates an instance of the class.
	* 
	* @param	void
	* @return	object
	*/
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	*/
	public function init($params, &$db)
	{
		$this->dbobj =& $db;

		if (is_array($params) AND !empty($params))
		{
			foreach ($params AS $key => $val)
			{
				$this->config["$key"] = $val;
			}
		}
	}

	/**
	* Builds the fields that will be sent to Paypal
	* 
	* @param	string	$field	Name of field, eg: business
	* @param	string	$value	Value of field, eg: email@example.com
	* @return	void	
	*/
	public function add_field($field, $value)
	{
		$this->fields["$field"] = $value;
	}

	/**
	* Submits the request to Paypal.
	* 
	* @param	void
	* @return	string
	*/
	public function submit_request()
	{
		$paypalurl = ($this->config['use_sandbox']) ? self::SANDBOX_URL : self::STANDARD_URL;

		echo <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Processing Payment...</title>
<script type="text/javascript">
function paypal_submit()
{
	document.paypal_form.submit();
}

window.onload = function()
{
	setInterval('paypal_submit()', 5000);
};
</script>
</head>

<body>

<center>
	<h2>Please wait, your payment is being processed and you will be redirected to the Paypal website.</h2>
</center>

<form method="post" id="paypal_form" name="paypal_form" action="$paypalurl">

HTML;

		foreach ($this->fields AS $name => $value)
		{
			echo "<input type=\"hidden\" name=\"$name\" value=\"$value\" />\n";
		}

		echo <<<HTML
<br /><br />
<center>
	If you are not automatically redirected to Paypal within 5 seconds...<br /><br />
	<input type="submit" name="submit" value="Click Here" />
</center>
</form>

</body>
</html>
HTML;
	}

	/**
	* Validate Paypal's IPN response.
	* 
	* @param	void
	* @return	boolean
	*/
	public function validate_ipn()
	{
		$query[] = 'cmd=_notify-validate';

		foreach($_POST AS $key => $val)
		{
			$this->data["$key"] = $val;

			$query[] = $key . '=' . urlencode($val);
		}

		$query = implode('&', $query);

		$used_curl = false;

		$result = '';

		if (function_exists('curl_init') AND $ch = curl_init())
		{
			curl_setopt($ch, CURLOPT_URL, ($this->config['use_sandbox'] ? self::SANDBOX_URL : self::STANDARD_URL));
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Via cURL/PHP');
			$result = curl_exec($ch);
			curl_close($ch);

			if ($result !== false)
			{
				$used_curl = true;
			}
		}

		if (!$used_curl)
		{
			$host = @parse_url(($this->config['use_sandbox'] ? self::SANDBOX_URL : self::STANDARD_URL));
			$host = $host['host'];
 
			$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
			$header .= "Host: $host\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen($query) . "\r\n\r\n";

			if ($fp = @fsockopen($host, 80, $errno, $errstr, 15))
			{
				@socket_set_timeout($fp, 15);
				@fwrite($fp, $header . $query);

				while (!@feof($fp))
				{
					$result = @fgets($fp, 1024);

					if (strcmp($result, 'VERIFIED') == 0)
					{
						break;
					}
				}
				@fclose($fp);
			}
		}

		$this->response =& $result;

		if (empty($result) or $result === false)
		{
			$this->error = 'Not able to post back to Paypal, or no response from Paypal';
			$this->log(false);
			return false;
		}

		if (
			$this->config['business'] != '' 
			AND $result == 'VERIFIED' 
			AND $this->data['payment_status'] == 'Completed' 
			AND (
				strtolower($this->data['business']) == strtolower($this->config['business']) 
				OR strtolower($this->data['receiver_email']) == strtolower($this->config['business'])
			)
		)
		{
			if (SAPI_NAME == 'cgi' OR SAPI_NAME == 'cgi-fcgi')
			{
				header('Status: 200 OK');
			}
			else
			{
				header('HTTP/1.1  200 OK');
			}
			$this->log(true);
			return true;
		}
		else
		{
			if (SAPI_NAME == 'cgi' OR SAPI_NAME == 'cgi-fcgi')
			{
				header('Status: 503 Service Unavailable');
			}
			else
			{
				header('HTTP/1.1 503 Service Unavailable');
			}
			$this->error = 'Invalid Paypal IPN transaction';
			$this->log(false);
			return false;
		}
	}

	/**
	* Logs IPN results.
	* 
	* @param	boolean	$success	true/false if succeeded or not. 
	*/
	private function log($success)
	{
		if (!$this->config['use_log'])
		{
			return;
		}

		$log  = '[' . date('m/d/Y g:i A') . '] - ';
		$log .= ($success) ? "SUCCESS!\n" : "FAIL: $this->error\n";
		$log .= "IPN POST Vars from Paypal:\n";

		foreach ($this->data AS $key => $val)
		{
			$log .= "$key=$val, ";
		}

		$log .= "\nIPN Response from Paypal Server:\n $this->response";

		$this->dbobj->query("
			INSERT INTO " . TABLE_PREFIX . "paypal_log 
				(
					first_name, last_name, payer_email, address_country, address_street, address_city, address_state, 
					address_zip, item_name, item_number, mc_gross, mc_fee, mc_currency, txn_id, rawdata, dateline
				)
			VALUES 
				(
					
					'" . $this->dbobj->prepare($this->data['first_name']) . "',
					'" . $this->dbobj->prepare($this->data['last_name']) . "',
					'" . $this->dbobj->prepare($this->data['payer_email']) . "',
					'" . $this->dbobj->prepare($this->data['address_country']) . "',
					'" . $this->dbobj->prepare($this->data['address_street']) . "',
					'" . $this->dbobj->prepare($this->data['address_city']) . "',
					'" . $this->dbobj->prepare($this->data['address_state']) . "',
					'" . $this->dbobj->prepare($this->data['address_zip']) . "',
					'" . $this->dbobj->prepare($this->data['item_name']) . "',
					'" . $this->dbobj->prepare($this->data['item_number']) . "',
					'" . $this->dbobj->prepare($this->data['mc_gross']) . "',
					'" . $this->dbobj->prepare($this->data['mc_fee']) . "',
					'" . $this->dbobj->prepare($this->data['mc_currency']) . "',
					'" . $this->dbobj->prepare($this->data['txn_id']) . "',
					'" . $this->dbobj->prepare($log) . "',
					'" . $this->dbobj->prepare($this->data['custom']) . "'
				)
		");
		return;
	}
}
