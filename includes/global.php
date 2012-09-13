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

// ################################################################
// We need at least PHP 5.2
if (!version_compare(PHP_VERSION, '5.2', '>='))
{
	die('Your server is running PHP ' . PHP_VERSION . ', but DPM needs at least PHP 5.2');
}

// ... and filter
if (!function_exists('filter_var'))
{
	die('Filter functions missing: PHP must be compiled with filter support (<code>--enable-filter</code>)');
}

// ... and hash
if (!function_exists('hash'))
{
	die('Hash functions missing: PHP must be compiled with hash support (<code>--enable-hash</code>)');
}

// ... and mbstring
if (!function_exists('mb_strlen'))
{
	die('Multibye String functions missing: PHP must be compiled with mbstring support (<code>--enable-mbstring</code>)');
}

// ################################################################
// Set important PHP config. options
ini_set('display_errors', 1);
ini_set('html_errors', 0);
ini_set('arg_separator.output', '&amp;');

// Error reporting
if (defined('IN_PAYPAL') AND IN_PAYPAL === true)
{
	error_reporting(0);
}
else
{
	error_reporting(E_ALL & ~E_NOTICE & ~8192);
}

// In PHP 5.3, these are deprecated
if (version_compare(PHP_VERSION, '5.3', '<'))
{
	set_magic_quotes_runtime(0);

	ini_set('magic_quotes_sybase', 0);
	ini_set('zend.ze1_compatibility_mode', 0);
	ini_set('register_long_arrays', 0);
}

// Session stuff..
ini_set('session.auto_start', 0);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);
ini_set('session.cookie_httponly', 1);

// ################################################################
// Better to be safe than sorry... :)
if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS']))
{
	die('Request tainting attempted.');
}

// Reverse the effects of register_globals if neccessary.
if (ini_get('register_globals') OR strtolower(ini_get('register_globals')) == 'on')
{
	$supers = array('_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_SESSION', '_ENV', '_FILES');

	if (!isset($_SESSION) OR !is_array($_SESSION))
	{
		$_SESSION = array();
	}

	foreach ($supers AS $arrayname)
	{
		foreach (array_keys($GLOBALS["$arrayname"]) AS $varname)
		{
			if (!in_array($varname, $supers))
			{
				$GLOBALS["$varname"] = NULL;
				unset($GLOBALS["$varname"]);
			}
		}
	}
}

// ################################################################
// Make sure POST's are from the domain DPM is installed on
if (count($_POST) > 0 AND !defined('IN_PAYPAL'))
{
	$httphost = preg_replace('#^www\.#i', '', $_SERVER['SERVER_NAME']);

	if (!empty($httphost) AND !empty($_SERVER['HTTP_REFERER']))
	{
		$refparts = parse_url($_SERVER['HTTP_REFERER']);
		$refhost = $refparts['host'] . ($refparts['port'] ? ':' . $refparts['port'] : '');

		if (strpos($refhost, $httphost) === false)
		{
			die('POST requests are not permitted from "foreign" domains.');
		}
	}
}

// ################################################################
// Include needed files
require_once('cache.class.php');
require_once('config.class.php');
require_once('functions.php');
require_once('database.class.php');
require_once('admin.class.php');

define('HOST', get_host());

// ################################################################
// Check to see if the install directory was deleted.
if (is_dir((defined('IN_ADMIN') ? '..' : '.') . '/install/') AND !defined('IN_INSTALL'))
{
	die('The <code>./install/</code> directory is still on your server. Either:<br /><ol><li>You\'ve installed the script but have yet to remove the install directory.</li><li>You\'ve not installed yet. If so, <a href="./install/">click here</a> to begin installation.</li><li>You\ve installed the script and you need to upgrade. If so, <a href="./install/upgrade.php">click here</a> to upgrade.</li></ol>If the first case, then please delete the install directory. If you\'ve not installed, install first, then delete.<br />Not deleting it poses a potential security risk.');
}

// ################################################################
/*
Not used yet...

$cache = dpm_cache::getInstance();
$cache->setPath('/tmp');
*/

$config = dpm_config::getInstance();

// Set default timezone
date_default_timezone_set($config->get('timezone'));

// Check to see that the database settings in config were changed.
if (
	$config->get('dbuser') == 'username' OR 
	$config->get('dbpass') == 'password' OR 
	$config->get('dbname') == 'database' OR 
	$config->get('dbemail') == 'webmaster@example.com'
)
{
	die('The <code>./includes/config.class.php</code> file still has the default value for one or more of the following:<br /><ul><li>dbuser</li><li>dbpass</li><li>dbname</li><li>dbemail</li></ul>Please open <code>./includes/config.class.php</code> in your favorite text editor and make sure all values are correct.');
}

// ################################################################
switch ($config->get('dbtype'))
{
	case 'mysql':
		$db = db_mysql::getInstance();
		break;
	case 'mysqli':
		$db = db_mysqli::getInstance();
		break;
	default:
		die('No valid database class found.');
		break;
}

// MySQL Table Prefix
define('TABLE_PREFIX', $config->get('dbprefix'));

// ################################################################
// Get config db options.
$config->get('', true);

// Instantiate admin class
$adm = dpm_admin::getInstance();

// Current DPM Version, do not change please.
$version = dpm_version();

// Some template stuff
$template = './templates/' . $config->get('template');
$template_admin = '../templates/admin';

/**
* Grab site config options (title, description, etc).
* We only want to do this, though, if we are not in install.
*/
if (!defined('IN_INSTALL'))
{
	$title = $config->get('title');
	$description = $config->get('description');
	$keywords = $config->get('keywords');

	// Start a session
	session_start();

	if (isset($_SESSION['dpm_admin_name'], $_SESSION['dpm_admin_kp']))
	{
		if (($adm->do_login($_SESSION['dpm_admin_name'], $_SESSION['dpm_admin_kp'], true)) AND ($adm->verify_auth()))
		{
			$db->is_admin = true;
		}
	}
}
