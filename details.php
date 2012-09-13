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
* @file      ./details.php
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

define('IN_DPM', true);
require_once('./includes/global.php');

// Valid ID?
$domainid = (is_string($_GET['d'])) ? sanitize($_GET['d']) : intval($_GET['d']);

if (!is('domain', $domainid))
{
	redirect('index.php');
}

// ################################################################
// Pull the domain and it's status.
$domain = get_value('details', $domainid);

if (count($domain) == 0 OR $domain['status'] == 'Sold' OR $domain['hidden'] == 1)
{
	redirect('index.php');
}

// ################################################################
// Setup the info for the template
if (in_array($domain['status'], array('Make Offer', 'Not For Sale', 'Pending Sale')))
{
	$domain['price'] = 'n/a';
}
else
{
	$domain['price'] = $config->get('currency') . "&nbsp;$domain[price]";
}

$domain['description'] = ($domain['description'] != '' ? wordwrap($domain['description'], 70, '<br />') : 'N/A');
$domain['keywords']    = ($domain['keywords'] == '' ? strtolower($domain['domain']) : $domain['keywords']);
$domain['added']       = date('m/d/Y', $domain['added']);
$domain['expiry']      = dpm_date('M jS, Y', $domain['expiry']);
$domain['lower']       = strtolower($domain['domain']);
$domain['upper']       = strtoupper($domain['domain']);
$domain['ext']         = substr($domain['domain'], strpos($domain['domain'], strchr($domain['domain'], '.')));
$domain['noext']       = str_replace($domain['ext'], '', $domain['domain']);
$domain['chars']       = strlen($domain['noext']);

// ################################################################
// Output page
$keywords   .= ',' . ($domain['keywords'] == '') ? strtolower($domain['domain']) : $domain['keywords'];
$description = ($domain['description'] != '') ? $domain['domain'] . ' - ' . ($domain['description']) : $description;

$pagetitle = "Details for $domain[domain]";

include("$template/details.php");

?>