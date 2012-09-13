<?php

/**
* @author    Eric Sizemore <admin@secondversion.com>
* @package   Domain Portfolio Manager
* @link      http://domain-portfolio.secondversion.com/
* @version   1.0.1
* @copyright (C) 2010 - 2011 Eric Sizemore
* @license   http://domain-portfolio.secondversion.com/docs/license.html GNU Public License
* @file      ./admin/ajax.php
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

/**
* This file was not part of the original DNP, this is exclusive to DPM.
*/

define('IN_DPM', true);
define('IN_ADMIN', true);
require_once('../includes/global.php');
require_once('../includes/idna/idna_convert.class.php');
require_once('../includes/whois/whois.main.php');

@set_time_limit(0);

// Logged in?
if (!$adm->verify_auth())
{
	// since this is accessed via javascript, better to just exit
	exit;
}

// ################################################################
// Without an action or value to check, we can't proceed.
if (!isset($_GET['action'], $_GET['which']))
{
	die('Missing parameters.');
}

$result = '';

// There is only one action right now.
$action = sanitize($_GET['action']);
$which  = sanitize($_GET['which']);

switch ($action)
{
	case 'whois':
		$whois = new Whois();
		$whois->deep_whois = false;
		$result = $whois->Lookup($which);

		if (
			isset($result['regrinfo']['domain'], $result['regrinfo']['domain']['expires']) AND 
			!empty($result['regrinfo']['domain']['expires'])
		)
		{
			$tmpresult = strtotime($result['regrinfo']['domain']['expires']);
			$tmpresult = date('m/d/Y', $tmpresult);

			if (isset($result['regrinfo']['domain']['sponsor']) AND !empty($result['regrinfo']['domain']['sponsor']))
			{
				$result['regyinfo']['registrar'] = $result['regrinfo']['domain']['sponsor'];
			}

			if (isset($result['regyinfo']['registrar']) AND !empty($result['regyinfo']['registrar']))
			{
				$tmpresult .= '|' . $result['regyinfo']['registrar'];
			}
			$result = $tmpresult;
		}
		else
		{
			$result = '|';
		}
		break;
}

// ################################################################
echo $result;

?>