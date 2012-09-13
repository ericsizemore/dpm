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
* @file      ./includes/recaptcha.class.php
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

if (!defined('IN_DPM'))
{
	die('You\'re not supposed to be here.');
}

/**
* A recaptcha_response is returned from recaptcha::check_answer()
*/
class recaptcha_response
{
	/**
	* Class instance.
	*
	* @var object
	*/
	private static $instance;

	/**
	* Valid response?
	*
	* @var  boolean
	*/
	public $is_valid;

	/**
	* Error message(s)
	*
	* @var string
	*/
	public $error;

	/**
	* Constructor.
	*
	*/
	private function __construct() {}

	/**
	* Creates an instance of the class.
	*
	* @param  void
	* @return object
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
	private function __clone() {}
}

/**
* ReCAPTCHA Class
*/
class recaptcha
{
	/**
	* @const API server
	*/
	const RECAPTCHA_API_SERVER = 'http://www.google.com/recaptcha/api';
	
	/**
	* @const API server (secure)
	*/
	const RECAPTCHA_API_SECURE_SERVER = 'https://www.google.com/recaptcha/api';

	/**
	* @const API verify server
	*/
	const RECAPTCHA_VERIFY_SERVER = 'www.google.com';

	/**
	* @const GLOBAL key (public)
	*/
	const RECAPTCHA_PUBLIC_KEY = '6Ld6FcUSAAAAAN8vMJEsBR1GpD3dPF_GhXpxmoU5';
	
	/**
	* @const GLOBAL key (private)
	*/
	const RECAPTCHA_PRIVATE_KEY = '6Ld6FcUSAAAAAHfkbZSphAtDPp7_HaZ5-7jpvEpB';

	/**
	* Class instance.
	*
	* @var object
	*/
	private static $instance;

	/**
	* Theme.
	*
	* @var string
	*/
	public $theme;

	/**
	* Constructor. Makes sure we have a valid theme set.
	*
	* @param  void
	* @return void
	*/
	private function __construct()
	{
		global $config;

		if (!in_array($config->get('recaptcha'), array('red', 'white', 'blackglass', 'clean')))
		{
			$this->theme = 'red';
		}
		else
		{
			$this->theme = $config->get('recaptcha');
		}
	}

	/**
	* Creates an instance of the class.
	*
	* @param  void
	* @return object
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
	private function __clone() {}

	/**
	* Encodes the given data into a query string format.
	*
	* @param  array   $data  Data to encode.
	* @return string         Encoded data.
	*/
	private function qsencode($data)
	{
		$req = '';

		foreach ($data AS $key => $value)
		{
			$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
		}

		$req = substr($req, 0, strlen($req) - 1);

		return $req;
	}

	/**
	* Submits an HTTP POST to a reCAPTCHA server.
	*
	* @param  string   $host  reCAPTCHA server host
	* @param  string   $path  reCAPTCHA server path
	* @param  array    $data  Data to verify response.
	* @param  integer  $port  reCAPTCHA server port
	* @return array           reCAPTCHA response.
	*/
	private function http_post($host, $path, $data, $port = 80)
	{
		$req = $this->qsencode($data);

		$http_request  = "POST $path HTTP/1.0\r\nHost: $host\r\nContent-Type: application/x-www-form-urlencoded;\r\n";
		$http_request .= "Content-Length: " . strlen($req) . "\r\nUser-Agent: reCAPTCHA/PHP\r\n\r\n$req";

		$response = '';

		if (false == ($fs = @fsockopen($host, $port, $errno, $errstr, 10)))
		{
			die('recaptcha::http_post() - Could not open socket');
		}
		fwrite($fs, $http_request);

		while (!feof($fs))
		{
			$response .= fgets($fs, 1160);
		}
		fclose($fs);

		$response = explode("\r\n\r\n", $response, 2);
		return $response;
	}

	/**
	* Gets the challenge HTML (javascript and non-javascript version).
	* This is called from the browser, and the resulting reCAPTCHA HTML widget
	* is embedded within the HTML form it was called from.
	*
	* @param  string   $error    The error given by reCAPTCHA (optional, default is null)
	* @param  boolean  $use_ssl  Should the request be made over ssl? (optional, default is false)
	* @return string             The HTML to be embedded in the user's form.
	*/
	public function get_html($error = NULL, $use_ssl = false)
	{
		if (self::RECAPTCHA_PUBLIC_KEY == '')
		{
			die('recaptcha::get_html() - To use reCAPTCHA you must get an API key from http://recaptcha.net/api/getkey');
		}

		$server = ($use_ssl) ? self::RECAPTCHA_API_SECURE_SERVER : self::RECAPTCHA_API_SERVER;

		$errorpart = '';

		if ($error)
		{
			$errorpart = "&amp;error=$error";
		}
		return '<script type="text/javascript">var RecaptchaOptions = { theme : \'' . $this->theme . '\' };</script>
		<script type="text/javascript" src="'. $server . '/challenge?k=' . self::RECAPTCHA_PUBLIC_KEY . $errorpart . '"></script>
		<noscript>
	  		<iframe src="'. $server . '/noscript?k=' . self::RECAPTCHA_PUBLIC_KEY . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br />
	  		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
	  		<input type="hidden" name="recaptcha_response_field" value="manual_challenge" />
		</noscript>';
	}

	/**
	* Calls an HTTP POST function to verify if the user's guess was correct
	*
	* @param  string  $remoteip      Users IP address
	* @param  string  $challenge     Challenge string
	* @param  string  $response      User response.
	* @param  array   $extra_params  Any extra parameters.
	* @return object                 Resonpse class object.
	*/
	public function check_answer($remoteip, $challenge, $response, $extra_params = array())
	{
		if (self::RECAPTCHA_PRIVATE_KEY == '')
		{
			die('recaptcha::check_answer() - To use reCAPTCHA you must get an API key from http://recaptcha.net/api/getkey');
		}

		if ($remoteip == '')
		{
			die('recaptcha::check_answer() - For security reasons, you must pass the remote ip to reCAPTCHA');
		}

		if (empty($challenge) OR strlen($challenge) == 0 OR empty($response) OR strlen($response) == 0)
		{
			$recaptcha_response = recaptcha_response::getInstance();
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error = 'incorrect-captcha-sol';
			return $recaptcha_response;
		}

		$response = $this->http_post(self::RECAPTCHA_VERIFY_SERVER, '/recaptcha/api/verify', array(
			'privatekey' => self::RECAPTCHA_PRIVATE_KEY,
			'remoteip'   => $remoteip,
			'challenge'  => $challenge,
			'response'   => $response
		) + $extra_params);

		$answers = explode("\n", $response[1]);
		$recaptcha_response = recaptcha_response::getInstance();

		if (trim($answers[0]) == 'true')
		{
			$recaptcha_response->is_valid = true;
		}
		else
		{
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error = $answers[1];
		}
		return $recaptcha_response;
	}
}
