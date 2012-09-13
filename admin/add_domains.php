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
require_once('../includes/idna/idna_convert.class.php');

// Logged in?
if (!$adm->verify_auth())
{
	redirect('index.php');
}

// ################################################################
$result = '';

// Insert domain
if (!empty($_POST['submit']))
{
	$idna = new idna_convert();

	$domain      = sanitize($idna->encode($_POST['domain']));
	$description = sanitize(str_replace(array('"', "\r\n"), array('\'', "\n"), $_POST['description']), false);
	$keywords    = sanitize(strtolower($_POST['keywords']));
	$registrar   = sanitize($_POST['registrar']);
	$expiry      = sanitize(str_replace('-', '/', $_POST['expiry']));
	$price       = sanitize(preg_replace('#([^0-9,\.]+)#', '', $_POST['price']));
	$status      = sanitize($_POST['status']);
	$issite      = intval($_POST['issite']);
	$hidden      = intval($_POST['hidden']);

	// Sanitize categories
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

	// Let's remove http://, https://, or www.
	$domain = str_replace(array('http://', 'https://'), '', $domain);
	$domain = preg_replace('#^www\.#i', '', $domain);

	// If they do not enter anything for price, or status is one of the below, just set to 0.00
	if (empty($price) OR $status == 'Not For Sale' OR $status == 'Make Offer')
	{
		$price = '0.00';
	}

	// Moniker / GoDaddy make domains all CAPS. We don't want them that way.
	if ($domain == strtoupper($domain))
	{
		$domain = strtolower($domain);
	}

	// Stupid I have to check this.
	$_tmp = explode('/', $expiry);
	$_tmp[2] = ($_tmp[2] > 2037) ? 2037 : $_tmp[2];

	$expiry = implode('/', $_tmp);
	unset($_tmp);

	/**
	* Create a session value for domain, description, etc.
	* This way, if there's an error, we won't lose what's been entered.
	*/
	$_SESSION['form'] = array(
		'domain'      => $domain,
		'description' => $description,
		'keywords'    => $keywords,
		'registrar'   => $registrar,
		'expiry'      => $expiry,
		'price'       => $price
	);

	// Is the domain valid?
	if (!preg_match('#^[a-z0-9]([-a-z0-9]+)?(\.[a-z]{2,3})?(\.[a-z]{2,4})$#i', $domain))
	{
		$result .= 'The domain appears to be invalid.';
	}
	else
	{
		$domain = $idna->decode($domain);

		// Does it already exist, was it left empty, or is the expiration date valid?
		if (is('domain', $db->prepare($domain)))
		{
			$result .= 'The domain is already in the database.';
		}
		else if (empty($domain) OR empty($registrar) OR empty($expiry))
		{
			$result .= 'One of the required fields were left empty (domain, regisrar, or expiry).';
		}
		else if (!is('expdate', $expiry))
		{
			$result .= 'The expiry date seems to be invalid. Please use the format: mm/dd/yyyy';
		}
		else
		{
			$sql = $db->query("
				INSERT INTO " . TABLE_PREFIX . "domains
					(domain, description, keywords, registrar, expiry, price, status, added, issite, hidden)
				VALUES
					(
						'" . $db->prepare($domain) . "',
						'" . $db->prepare($description) . "',
						'" . $db->prepare($keywords) . "',
						'" . $db->prepare($registrar) . "',
						" . strtotime($expiry) . ",
						'" . $db->prepare($price) . "',
						'" . $db->prepare($status) . "',
						" . time() . ",
						$issite,
						$hidden
					)
			");

			$id = $db->insert_id();

			if ($sql AND $id)
			{
				// If any categories are chosen, add the domain to that category
				if (count($category) > 0)
				{
					foreach ($category AS $cat)
					{
						if (is('category', $cat))
						{
							$db->query("
								INSERT INTO " . TABLE_PREFIX . "dom2cat (catid, domainid)
								VALUES ($cat, $id)
							");
						}
					}
				}
				unset($category);

				// It's been added!
				$result .= "Domain '$domain' added.";

				// Reset the array
				$_SESSION['form'] = array(
					'domain'      => '',
					'description' => '',
					'keywords'    => '',
					'registrar'   => '',
					'expiry'      => '',
					'price'       => ''
				);
			}
			else
			{
				$db->raise_error();
			}
		}
	}
	$result = "<div id=\"result\">$result</div>";
}
else
{
	$_SESSION['form'] = array(
		'domain'      => '',
		'description' => '',
		'keywords'    => '',
		'registrar'   => '',
		'expiry'      => '',
		'price'       => ''
	);
}

// ################################################################
// Output page
$pagetitle = 'Add Domains';

include("$template_admin/add_domains.php");

?>