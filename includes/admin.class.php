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
* @file      ./includes/admin.class.php
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

class dpm_admin
{
	/**
	* Class instance.
	*
	* @var object
	*/
	private static $instance;

	/**
	* This will hold all the needed admin data.
	*
	* @var array
	*/
	private $admininfo;

	/**
	* Constructor.
	*
	* Basically just sets up the admininfo array.
	*
	* @param  void
	* @return void
	*/
	private function __construct()
	{
		$this->admininfo = array();
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
	* This will generate a hash composed of useragent, ipaddress, and ID.
	* We can use this to verify an admin's login, and keep it rather secure.
	*
	* @param  integer  $adminid  Admin ID
	* @return string             Generated hash
	*/
	private function gen_hash($adminid)
	{
		$hash = "DomainPortfolioManager:admin_id=$adminid;ip_seg=";
		$hash .= implode('.', array_slice(explode('.', get_ip()), 0, 2)) . ';';
		$hash .= 'useragent=' . dpm_getenv('HTTP_USER_AGENT') . ';';
		return dpm_hash($hash);
	}

	/**
	* Handles the admin login.
	*
	* @param  string   $username    Admin username
	* @param  string   $password    dpm_hash'ed admin password
	* @param  boolean  $fromglobal  True if called within global.php, false if not.
	* @return boolean               True if able to login, false if not.
	*/
	public function do_login($username, $password, $fromglobal = false)
	{
		global $db;

		$getadmininfo = $db->query("
			SELECT adminid, username, password
			FROM " . TABLE_PREFIX . "admin
			WHERE username = '" . $db->prepare($username) . "'
		") or $db->raise_error('dpm_admin::do_login() - Failed getting admin information.');

		if ($admininfo = $db->fetch_array($getadmininfo))
		{
			if ($username == $admininfo['username'] AND $password == ($fromglobal ? dpm_hash($admininfo['password']) : $admininfo['password']))
			{
				// admininfo array
				$this->admininfo = array(
					'dpm_admin_id'   => $admininfo['adminid'],
					'dpm_admin_name' => $admininfo['username'],
					'dpm_admin_key'  => substr(dpm_hash($admininfo['password']), 0, 16),
					'dpm_admin_hash' => $this->gen_hash($admininfo['adminid'])
				);

				// sessions
				$_SESSION['dpm_admin_id'] = $this->admininfo['dpm_admin_id'];
				$_SESSION['dpm_admin_name'] = $this->admininfo['dpm_admin_name'];
				$_SESSION['dpm_admin_key'] = $this->admininfo['dpm_admin_key'];
				$_SESSION['dpm_admin_kp'] = dpm_hash($admininfo['password']);
				$_SESSION['dpm_admin_hash'] = $this->admininfo['dpm_admin_hash'];
				return true;
			}
		}
		return false;
	}

	/**
	* Checks if admin is logged in on each admin page load.
	*
	* @param  void
	* @return boolean True if logged in, false if not.
	*/
	public function verify_auth()
	{
		global $db;

		$adminid = intval($_SESSION['dpm_admin_id']);
		$username = sanitize($_SESSION['dpm_admin_name']);
		$passkey = sanitize($_SESSION['dpm_admin_key']);
		$hash = sanitize($_SESSION['dpm_admin_hash']);

		if (empty($adminid) OR empty($username) OR empty($passkey) OR empty($hash))
		{
			return false;
		}

		if ($hash != $this->gen_hash($adminid))
		{
			$this->do_logout();
			return false;
		}

		if (
			$adminid == $this->admininfo['dpm_admin_id'] AND $username == $this->admininfo['dpm_admin_name'] AND 
			$passkey == $this->admininfo['dpm_admin_key'] AND $hash == $this->admininfo['dpm_admin_hash']
		)
		{
			return true;
		}
		return false;
	}

	/**
	* Will allow an admin to logout of the system.
	*
	* @param  void
	* @return void
	*/
	public function do_logout()
	{
		$this->admininfo = array();

		unset(
			$_SESSION['dpm_admin_id'],
			$_SESSION['dpm_admin_name'],
			$_SESSION['dpm_admin_key'],
			$_SESSION['dpm_admin_kp'],
			$_SESSION['dpm_admin_hash']
		);

		if (isset($_COOKIE[session_name()]))
		{
			$expire = intval(ini_get('session.cookie_lifetime'));
			$expire = ($expire == 0) ? 1200 : $expire;
			setcookie(session_name(), '', time() - $expire, '/');
		}
		session_destroy();
	}
}
