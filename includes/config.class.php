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
* This class will handle all of our configuration options.
*/
class dpm_config
{
	/**
	* Class instance.
	*
	* @var object
	*/
	private static $instance;

	/**
	* This will hold our config info.
	*
	* @var array
	*/
	private $info = array();

	/**
	* Constructor. Initiates the info array with default configuration options.
	*
	* The configuration options within this function have to be set before installation.
	*
	* @param  void
	* @return void
	*/
	private function __construct()
	{
		$this->info = array(
			/**
			* This is the hostname or IP address of your database server.
			*/
			'dbhost'    => 'localhost',

			/**
			* This is the username and password to conntect to the database server.
			*/
			'dbuser'    => 'username',
			'dbpass'    => 'password',

			/**
			* This is the name of the database you created.
			*/
			'dbname'    => 'database',

			/**
			* This determines which PHP extension we'll use, either mysql or mysqli
			* To use MySQLi PHP must be compiled with: --with-mysqli=/path/to/mysql_config
			*/
			'dbtype'    => (function_exists('mysqli_init') ? 'mysqli' : 'mysql'),

			/**
			* This is the prefix added to database tables, shouldn't need changed.
			*/
			'dbprefix'  => 'dpm_',

			/**
			* This is the email address used when there is a database error.
			*/
			'dbemail'   => 'webmaster@example.com',

			/**
			* This determines which reCAPTCHA theme we use. reCAPTCHA is used on the contact page.
			* Valid options are: red, white, blackglass, or clean
			*/
			'recaptcha' => 'clean',

			/**
			* This determines which template to use for the main site.
			* Valid options are: default or legacy
			*/
			'template'  => 'default',

			/**
			* This is your timezone. You need to enter the correct value from:
			*	http://www.php.net/manual/en/timezones.php
			*/
			'timezone'  => 'America/New_York',

			/**
			* If you'd like to show Google Adsesne ads on your portfolio, enter your publisher ID (without the pub-)
			* below (add it between the quotes ('') for `pubid`).
			*
			* Next, choose what type of ads to be shown. Only text, or, text and images.
			* For text, leave it as 'text' - otherwise, set to 'text_image'.
			*
			* Then, if you'd like to change the default size of the ad, edit 'size'.
			* It is {width}x{height}, so for example, if you wanted the header ad to be 468 x 60,
			* change 'size' to '468x60'
			*
			* Finally, you can enable/disable the ad for either the header or sidebar by setting 
			* 'show' to true (to enable) or false (to disable).
			*/
			'adsense'       => array(
				'pubid'     => '',
				'header'    => array(
					'type'  => 'text',
					'size'  => '728x90',
					'show'  => true
				),
				'sidebar'   => array(
					'type'  => 'text',
					'size'  => '180x150',
					'show'  => true
				)
			)
		);
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
	* Returns the value of a configuration option for use within the script.
	* It can also return all configuration items from the database.
	*
	* @param  mixed    $option  Name of the option to lookup
	* @param  boolean  $fromdb  If we're wanting to pull configuration options from the database.
	* @return string            Value of the given option, if any.
	*/
	public function get($option, $fromdb = false)
	{
		global $db;

		if ($fromdb AND !defined('IN_INSTALL') AND is_object($db))
		{
			// Get site config info
			$getconfig = $db->query("
				SELECT name, value
				FROM " . TABLE_PREFIX . "config
			") or $db->raise_error();

			while ($configrow = $db->fetch_array($getconfig))
			{
				$this->info["$configrow[name]"] = clean($configrow['value']);
			}
			$db->free_result($getconfig);
		}
		else
		{
			return (isset($this->info["$option"])) ? $this->info["$option"] : '';
		}
	}

	/**
	* Updates a config option in the database.
	*
	* @param  string   $option  Config option to update
	* @param  mixed    $value   Config value
	* @return boolean           true if update is successful, or false if not.
	*/
	public function update($option, $value)
	{
		global $db;

		$valid_options = array(
			'title',
			'description',
			'keywords',
			'maxperpage',
			'contactemail',
			'currency',
			'paypal_sandbox',
			'paypal_log',
			'paypal_email'
		);

		if (empty($option) OR !in_array($option, $valid_options))
		{
			die('dpm_config::update() - $option must be a valid configuration option.');
		}

		if ($db->query("UPDATE " . TABLE_PREFIX . "config SET value = '$value' WHERE name = '$option'"))
		{
			return true;
		}
		else
		{
			$db->raise_error();
		}
		return false;
	}
}
