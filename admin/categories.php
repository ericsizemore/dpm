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
$getcatsql = "
	SELECT catid, title, description
	FROM " . TABLE_PREFIX . "categories
	ORDER BY title ASC
";

// Will hold category data to be used in the template file
$cats = array();

// Used in pagination, and to determine if we even have any categories
$numcats = $db->num_rows($db->query($getcatsql));

// Pagination
$pagination = paginate($numcats, (isset($_GET['page']) ? intval($_GET['page']) : 0));

// Execute the query, and if there are any results, build the category table.
$getcats = $db->query("
	$getcatsql
	LIMIT $pagination[limit], " . $config->get('maxperpage') . "
") or $db->raise_error();

if ($numcats > 0)
{
	$row = 0;

	while ($cat = $db->fetch_array($getcats))
	{
		$cat['numdomains'] = $db->query("
			SELECT COUNT(domainid) AS count
			FROM " . TABLE_PREFIX . "dom2cat
			WHERE catid = $cat[catid]
		", true);
		$cat['numdomains'] = $cat['numdomains']['count'];
		$cat['class'] = ($row & 1);
		$cats[] = $cat;

		$row++;
	}
	$db->free_result($getcats);
}

// ################################################################
// Output page
$pagetitle = 'Categories';

include("$template_admin/categories.php");

?>