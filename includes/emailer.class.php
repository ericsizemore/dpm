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
* @file      ./includes/emailer.class.php
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
* Class to send email. Based on a class by phpBB2.
*/
class emailer
{
	/**
	* Class instance, start of implementing Singleton pattern.
	*
	* @var object
	*/
	private static $instance;

	/**
	* The email recipient.
	*
	* @var string
	*/
	private $to;

	/**
	* The email subject.
	*
	* @var string
	*/
	private $subject;

	/**
	* The email body.
	*
	* @var string
	*/
	private $body;

	/**
	* Who the email is from.
	*
	* @var string
	*/
	private $from;

	/**
	* Host/domain.
	*
	* @var string
	*/
	private $host;

	/**
	* Extra email headers.
	*
	* @var string
	*/
	private $extra_headers;

	/**
	* Constructor. Sets host and initiates extra_headers.
	*
	* @param  void
	* @return void
	*/
	private function __construct()
	{
		$this->host = HOST;
		$this->extra_headers = '';
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
	* Sets email parameters (To, From, and Subject)
	*
	* @param  string  $to       Recipient email
	* @param  string  $from     Who the email is from
	* @param  string  $subject  Subject of the email
	* @return void
	*/
	public function set_params($to, $from, $subject)
	{
		$this->to = trim($to);
		$this->from = trim($from);
		$this->from = (is_null($from)) ? "noreply@{$this->host}" : $this->from;
		$this->subject = trim($subject);
	}

	/**
	* Allows us to set extra headers aside from the standard ones in the send() function.
	*
	* @param  string  $headers  Extra headers seperated by \n
	* @return void
	*/
	public function extra_headers($headers = '')
	{
		$this->extra_headers .= str_replace("\r\n", "\n", $headers);
	}

	/**
	* Allows us to use templates for the email body
	*
	* @param  array   $tpl_vars  An array of var => replacement
	* @param  string  $tpl_file  Template filename
	* @return void
	*/
	public function use_template($tpl_vars, $tpl_file)
	{
		$tpl_file = (defined('IN_ADMIN') ? '..' : '.') . "/templates/$tpl_file";

		if (!is_array($tpl_vars) OR count($tpl_vars) == 0)
		{
			trigger_error('emailer::use_template() - <code>$tpl_vars</code> must be an array, or is empty.', E_USER_ERROR);
		}

		if (!is_file($tpl_file))
		{
			trigger_error("emailer::use_template() - '<code>$tpl_file</code>' is not a file or does not exist.", E_USER_ERROR);
		}

		if (!($fp = @fopen($tpl_file, 'r')))
		{
			trigger_error("emailer::use_template() - Could not open template file: '<code>$tpl_file</code>'", E_USER_ERROR);
		}

		$this->body = fread($fp, filesize($tpl_file));

		foreach ($tpl_vars AS $var => $content)
		{
			$this->body = str_replace('{' . $var . '}', $content, $this->body);
		}
		fclose($fp);
	}

	/**
	* Wrapper of the mail() function, which also sets standard email headers,
	* plus any extra ones we may add in script via the extra_headers() function.
	*
	* @param  void
	* @return boolean  true if the email is sent, false if not.
	*/
	public function send()
	{
		$headers = "From: {$this->from}\n";
		$headers .= "Reply-To: {$this->from}\n";
		$headers .= "Return-Path: {$this->from}\n";
		$headers .= "Sender: {$this->from}\n";
		$headers .= "Message-ID: <" . dpm_hash(uniqid(time())) . "@{$this->host}>\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/plain; charset=UTF-8\n";
		$headers .= "Content-transfer-encoding: 8bit\n";
		$headers .= "Date: " . date('r', time()) . "\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: Normal\n";
		$headers .= "X-Mailer: Domain Portfolio Manager via PHP/" . PHP_VERSION . "\n";
		$headers .= "X-MimeOLE: Produced By Domain Portfolio Manager\n";

		if ($this->extra_headers != '')
		{
			$headers .= trim($this->extra_headers) . "\n";
		}

		if (@mail($this->to, $this->subject, $this->body, $headers))
		{
			return true;
		}
		return false;
	}
}
