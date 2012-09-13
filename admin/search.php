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

// Logged in?
if (!$adm->verify_auth())
{
	redirect('index.php');
}

// ################################################################
$error = '';

// Performing a search?
if (isset($_REQUEST['query']) AND !empty($_REQUEST['query']))
{
	$search = urldecode($_REQUEST['query']);
	$search = preg_replace('#([^a-z0-9\- \.]+)#i', '', sanitize($search));
	$search = (strpos($search, ' ') !== false) ? array_unique(explode(' ', $search)) : $search;

	// If multiple keywords, check to see they meet the min. length requirement
	$tooshort = false;

	if (is_array($search))
	{
		foreach ($search AS $searchpart)
		{
			$searchpart = trim($searchpart);

			if (strlen($searchpart) < 2)
			{
				$tooshort = true;
				break;
			}
		}
	}

	if ((is_string($search) AND strlen($search) < 2) OR (is_array($search) AND $tooshort))
	{
		$error = 'Please enter a search term that is atleast 2 characters in length.<br />If you entered multiple keywords, one of them was too short.';
	}
	else
	{
		// Ordering
		$orderby = is('orderby', sanitize($_GET['sort']));

		// Multiple keywords?
		if (is_array($search))
		{
			$search = array_map('trim', $search);

			$searchsql = '';

			foreach ($search AS $searchpart)
			{
				$searchpart = $db->prepare($searchpart, true);
				$searchsql .= ($searchsql == '' ? 'WHERE' : 'OR') . " domains.domain LIKE '%$searchpart%' ";
			}
		}
		else
		{
			$searchsql = "WHERE domains.domain LIKE '%" . $db->prepare($search, true) . "%'";
		}

		// ################################################################
		$getdomainsql = "
			SELECT domains.*, IF(categories.title IS NULL, 'None', GROUP_CONCAT(categories.title SEPARATOR ', ')) AS category
			FROM " . TABLE_PREFIX . "domains AS domains
			LEFT JOIN " . TABLE_PREFIX . "dom2cat AS d2c ON (domains.domainid = d2c.domainid)
			LEFT JOIN " . TABLE_PREFIX . "categories AS categories ON (d2c.catid = categories.catid)
			$searchsql
			GROUP BY COALESCE(d2c.domainid, RAND())
			ORDER BY " . ($orderby == 'category' ? 'categories.catid' : "domains.$orderby") . " ASC
		";

		// Will hold all domain data to pass to the template file.
		$domains = array();

		// This is used both in pagination and to determine if there were any results.
		$numdomains = $db->num_rows($db->query($getdomainsql));

		// Pagination
		$pagination = paginate($numdomains, (isset($_GET['page']) ? intval($_GET['page']) : 0), $orderby, NULL, (is_array($search) ? implode(' ', $search) : $search));

		// Execute the query, and if there are any results, build the domain table.
		$getdomains = $db->query("
			$getdomainsql
			LIMIT $pagination[limit], " . $config->get('maxperpage') . "
		") or $db->raise_error();

		if ($numdomains > 0)
		{
			$row++;

			while ($domain = $db->fetch_array($getdomains))
			{
				$domain['description'] = (empty($domain['description'])) ? $domain['domain'] : $domain['description'];
				$domain['expiry'] = dpm_date('M jS, Y', $domain['expiry']);
				$domain['class'] = ($row & 1);
				$domains[] = $domain;

				$row++;
			}
			$db->free_result($getdomains);
		}
	}

	// ################################################################
	$searchquery = ($search != '' ? '&amp;query=' . urlencode((is_array($search) ? implode(' ', $search) : $search)) : '');
}

// ################################################################
// Output Page

$pagetitle = 'Search' . ($searchquery != '' ? ' Results' : '');

include("$template_admin/search.php");

?>