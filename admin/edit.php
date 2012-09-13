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
define('CALENDAR', true);
require_once('../includes/global.php');

// Logged in?
if (!$adm->verify_auth())
{
	redirect('index.php');
}

// ################################################################
$mode = sanitize($_GET['mode']);

switch ($mode)
{
	case 'domain':
		$domainid = intval($_GET['d']);

		if (!is('domain', $domainid))
		{
			redirect('admin.php');
		}

		$domaininfo = get_value('domainedit', $domainid);

		if (count($domaininfo) == 0)
		{
			redirect('admin.php');
		}

		// Start building the page..
		$result = '';

		// Build selects...
		$hide_select   = build_select('hidden', $domaininfo['hidden']);
		$status_select = build_select('status', $domaininfo['status']);
		$cat_select    = build_select('category', $domaininfo['domainid']);
		$issite_select = build_select('issite', $domaininfo['issite']);

		// ################################################################
		// Update the domain
		if (!empty($_POST['submit']))
		{
			$domain      = sanitize($_POST['domain']);
			$domain      = str_replace(array('http://', 'https://'), '', $domain);
			$domain      = preg_replace('#^www\.#i', '', $domain);
			$description = sanitize(str_replace(array('"', "\r\n"), array('\'', "\n"), $_POST['description']), false);
			$keywords    = sanitize(strtolower($_POST['keywords']));
			$registrar   = sanitize($_POST['registrar']);
			$expiry      = sanitize($_POST['expiry']);
			$price       = sanitize(preg_replace('#\.[0-9]{2}$#', '', $_POST['price']));
			$price       = preg_replace('#([^0-9,\.]+)#', '', $price);
			$status      = sanitize($_POST['status']);
			$issite      = intval($_POST['issite']);
			$hidden      = intval($_POST['hidden']);

			// If they do not enter anything for price, or status is one of the below, just set to 0.00
			if (empty($price) OR $status == 'Not For Sale' OR $status == 'Make Offer')
			{
				$price = '0.00';
			}

			if (empty($domain) OR empty($registrar) OR empty($expiry))
			{
				$result .= 'Required fields left empty. You must provide a valid domain, registrar, and expiration date.';
			}
			else if (!is('expdate', $expiry))
			{
				$result .= 'The expiration date seems to be invalid. Correct format: mm/dd/yyyy. Eg: ' . date('m/d/Y');
			}
			else
			{
				$sql = $db->query("
					UPDATE " . TABLE_PREFIX . "domains
					SET
						domain = '" . $db->prepare($domain) . "',
						description = '" . $db->prepare($description) . "',
						keywords = '" . $db->prepare($keywords) . "',
						registrar = '" . $db->prepare($registrar) . "',
						expiry = " . strtotime($expiry) . ",
						price = '" . $db->prepare($price) . "',
						status = '" . $db->prepare($status) . "',
						issite = $issite,
						hidden = $hidden
					WHERE domainid = $domainid
					LIMIT 1
				");

				// if SQL update was successful, process categories, then give result
				if ($sql)
				{
					// Prepare categories
					$count = count($_POST['category']);

					if ($count > 0)
					{
						for ($i = 1; $i < $count; $i++)
						{
							$_POST['category'][$i] = intval($_POST['category'][$i]);
						}
					}

					$category = $_POST['category'];
					unset($_POST['category']);

					if (count($category) > 0)
					{
						$db->query("
							DELETE FROM " . TABLE_PREFIX . "dom2cat
							WHERE domainid = $domainid
						");

						foreach ($category AS $cat)
						{
							if ($cat == -1 OR !is('cat', $cat))
							{
								continue;
							}

							$db->query("
								INSERT INTO " . TABLE_PREFIX . "dom2cat (catid, domainid)
								VALUES($cat, $domainid)
							");
						}
					}

					// Done!
					$result .= "Domain <code>$domaininfo[domain]</code> updated.";
				}
				else
				{
					$db->raise_error();
				}
			}

			$result = "<div id=\"result\">$result &mdash; <a href=\"./admin.php\">Back</a></div>";

			// Give the form fresh values
			$domaininfo    = get_value('domainedit', $domainid);
			$hide_select   = build_select('hidden', $domaininfo['hidden']);
			$status_select = build_select('status', $domaininfo['status']);
			$cat_select    = build_select('category', $domaininfo['domainid']);
			$issite_select = build_select('issite', $domaininfo['issite']);
		}
		break;
	case 'category':
		$catid = intval($_GET['cat']);

		if (!is('category', $catid))
		{
			redirect('categories.php');
		}

		$catinfo = get_value('catedit', $catid);

		if (count($catinfo) == 0)
		{
			redirect('categories.php');
		}

		// Start building the page..
		$result = '';

		// ################################################################
		// Update the category
		if (!empty($_POST['submit']))
		{
			$category    = sanitize($_POST['category']);
			$description = sanitize(str_replace(array('"', "\r\n"), array('\'', "\n"), $_POST['description']));
			$keywords    = sanitize(strtolower($_POST['keywords']));

			if (empty($category))
			{
				$result .= 'Category title cannot be left blank!';
			}
			else
			{
				$sql = $db->query("
					UPDATE " . TABLE_PREFIX . "categories
					SET
						title = '" . $db->prepare($category) . "',
						description = '" . $db->prepare($description) . "',
						keywords = '" . $db->prepare($keywords) . "'
					WHERE catid = $catid
					LIMIT 1
				");

				if ($sql)
				{
					$result .= 'Category successfully updated.';
				}
				else
				{
					$db->raise_error();
				}
			}
			$catinfo = get_value('catedit', $catid);

			$result = "<div id=\"result\">$result &mdash; <a href=\"./categories.php\">Back</a></div>";
		}
		break;
	default:
		redirect('admin.php');
		break;
}

// ################################################################
$pagetitle = 'Edit ' . ucfirst($mode);

include("$template_admin/edit.php");

?>