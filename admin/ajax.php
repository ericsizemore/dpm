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