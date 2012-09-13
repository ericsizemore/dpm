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
$result = '';

$mode = sanitize($_GET['mode']);

switch ($mode)
{
	case 'domain':
		// Are we deleting multiple domains?
		if (isset($_GET['bulk']) AND $_GET['bulk'] == true AND count($_POST['domains']) > 0)
		{
			// yes, we are
			$count = count($_POST['domains']);
			$domains = array();

			for ($i = 0; $i <= $count; $i++)
			{
				// valid domain?
				if (is('domain', $_POST['domains'][$i]))
				{
					$domains[] = $_POST['domains'][$i];
				}
			}

			// memory saving
			unset($_POST['domains']);

			// If any domains passed our above check, delete them.
			if (count($domains) > 0)
			{
				if ($db->query("
					DELETE FROM " . TABLE_PREFIX . "domains
					WHERE domainid IN(" . implode(',', $domains) . ")
				"))
				{
					$db->query("
						DELETE FROM " . TABLE_PREFIX . "dom2cat
						WHERE domainid IN(" . implode(',', $domains) . ")
					");
					$result = '<div id="result">Domain(s) deleted. <a href="./admin.php">Back</a></div>';
				}
				else
				{
					$db->raise_error();
				}
				unset($domains);
			}
			else
			{
				redirect('admin.php');
			}
		}
		else
		{
			// no, we're not
			$domainid = intval($_GET['d']);

			// valid domain?
			if (!is('domain', $domainid))
			{
				redirect('admin.php');
			}

			// Delete..
			if ($db->query("
				DELETE FROM " . TABLE_PREFIX . "domains
				WHERE domainid = $domainid LIMIT 1
			"))
			{
				$db->query("
					DELETE FROM " . TABLE_PREFIX . "dom2cat
					WHERE domainid = $domainid
				");
				$result = '<div id="result">Domain deleted. <a href="./admin.php">Back</a></div>';
			}
			else
			{
				$db->raise_error();
			}
		}
		break;
	case 'category':
		// Are we deleting multiple categories?
		if (isset($_GET['bulk']) AND $_GET['bulk'] == true AND count($_POST['cats']) > 0)
		{
			// yes, we are
			$count = count($_POST['cats']);
			$cats = array();

			for ($i = 0; $i <= $count; $i++)
			{
				// valid category?
				if (is('category', $_POST['cats'][$i]))
				{
					$cats[] = $_POST['cats'][$i];
				}
			}

			// memory saving
			unset($_POST['cats']);

			// If any categories passed our above check, delete them.
			if (count($cats) > 0)
			{
				if ($db->query("
					DELETE FROM " . TABLE_PREFIX . "categories
					WHERE catid IN(" . implode(',', $cats) . ")
				"))
				{
					$db->query("
						DELETE FROM " . TABLE_PREFIX . "dom2cat
						WHERE catid IN(" . implode(',', $cats) . ")
					");
					$result = '<div id="result">Categories deleted. <a href="./categories.php">Back</a></div>';
				}
				else
				{
					$db->raise_error();
				}
				unset($cats);
			}
			else
			{
				redirect('categories.php');
			}
		}
		else
		{
			$catid = intval($_GET['cat']);

			if (!is('category', $catid))
			{
				redirect('categories.php');
			}

			// Delete..
			if ($db->query("
				DELETE FROM " . TABLE_PREFIX . "categories
				WHERE catid = $catid LIMIT 1
			"))
			{
				$db->query("
					DELETE FROM " . TABLE_PREFIX . "dom2cat
					WHERE catid = $catid
				");
				$result = '<div id="result">Category deleted. <a href="categories.php">Back</a></div>';
			}
			else
			{
				$db->raise_error();
			}
		}
		break;
	default:
		redirect('admin.php');
		break;
}

// ################################################################
// Output page
$pagetitle = 'Delete ' . ucfirst($mode);

include("$template_admin/delete.php");

?>