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
* @file      ./index.php
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

// ################################################################
// Ordering
$orderby = is('orderby', sanitize($_GET['sort']));

// Browsing a category?
$catid = (isset($_GET['cat'])) ? intval($_GET['cat']) : '';
$catid = is('catid', $catid);

// SQL to pull all the domains from the database
$getdomainsql = "
	SELECT domains.*, IF(categories.title IS NULL, 'None', GROUP_CONCAT(categories.title SEPARATOR ', ')) AS category
	FROM " . TABLE_PREFIX . "domains AS domains
	LEFT JOIN " . TABLE_PREFIX . "dom2cat AS d2c ON (domains.domainid = d2c.domainid)
	LEFT JOIN " . TABLE_PREFIX . "categories AS categories ON (d2c.catid = categories.catid)
	WHERE domains.hidden != 1
		AND domains.status != 'Sold'
		" . (!is_null($catid) ? "AND d2c.catid = $catid" : '') . "
	GROUP BY COALESCE(d2c.domainid, RAND())
	ORDER BY " . ($orderby == 'category' ? 'categories.catid' : "domains.$orderby") . " ASC
";

// Will hold all domain data to pass to the template file.
$domains = array();

// This is used both in pagination and to determine if there were any results.
$numdomains = $db->num_rows($db->query($getdomainsql));

// Pagination variables
$page = (isset($_GET['page'])) ? intval($_GET['page']) : 0;
$pagination = paginate($numdomains, $page, $orderby, $catid);

// Execute the query, and if there are any results, build the domain table.
$getdomains = $db->query("
	$getdomainsql
	LIMIT $pagination[limit], " . $config->get('maxperpage') . "
") or $db->raise_error();

// This will process all domains (if any) to sort out their price, expiration, etc.
if ($numdomains > 0)
{
	$row = 0;

	while ($domain = $db->fetch_array($getdomains))
	{
		if (in_array($domain['status'], array('Make Offer', 'Not For Sale', 'Pending Sale')))
		{
			$domain['price'] = 'n/a';
		}
		else
		{
			$domain['price'] = $config->get('currency') . "&nbsp;$domain[price]";
		}

		$domain['description'] = (empty($domain['description'])) ? $domain['domain'] : $domain['description'];
		$domain['expiry'] = dpm_date('M jS, Y', $domain['expiry']);
		$domain['class'] = ($row & 1);
		$domains[] = $domain;

		$row++;
	}
	$db->free_result($getdomains);
}

// ################################################################
// Output Page
$currentcat = '';
$catkeywords = '';

if (!is_null($catid) AND $catid > 0)
{
	$currentcat = get_value('catedit', $catid);
	$catkeywords = ($currentcat['keywords'] != '') ? $currentcat['keywords'] : strtolower($currentcat['title']);
	$currentcat = " in '$currentcat[title]'";
}

$pagetitle = ($currentcat != '') ? "Domains $currentcat - Home" : 'Home';

$catidsort = (!is_null($catid) ? "&amp;cat=$catid" : '');

include("$template/index.php");

?>