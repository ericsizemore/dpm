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
define('BULKADD', true);
define('CALENDAR', true);
require_once('../includes/global.php');

// Logged in?
if (!$adm->verify_auth())
{
	redirect('index.php');
}

// ################################################################
$result = '';
$mode = intval($_POST['legacy']);
$mode = (!$mode) ? 'new' : 'legacy';

/**
* Depending on the amount of domains entered,
* this could be quite an intensive process..
*/
if (!empty($_POST['submit']))
{
	$domains = trim(str_replace("\r\n", "\n", $_POST['domains']));

	unset($_POST['domains']);

	$domains = preg_split("#\n#", $domains, -1, PREG_SPLIT_NO_EMPTY);
	$domains = clean($domains);

	if (count($domains) == 0)
	{
		$result .= 'You didn\'t enter any domains!';
	}
	else
	{
		/**
		* The new way of doing bulk additions is to take the inputted domains
		* then have drop downs of status, etc.
		*
		* But we will leave a 'legacy' mode for those used to the old way.
		*
		* @since DPM 1.0.0
		*/
		switch ($mode)
		{
			// ################################################################
			case 'new':
				$registrar = sanitize($_POST['registrar']);
				$expiry    = sanitize(str_replace('-', '/', $_POST['expiry']));
				$price     = '0.00';
				$status    = sanitize($_POST['status']);
				$issite    = intval($_POST['issite']);
				$hidden    = intval($_POST['hidden']);

				// Stupid I have to check this.
				$_tmp = explode('/', $expiry);
				$_tmp[2] = ($_tmp[2] > 2037) ? 2037 : $_tmp[2];

				$expiry = implode('/', $_tmp);
				unset($_tmp);

				if (empty($registrar) OR empty($status) OR empty($expiry))
				{
					$result .= 'One of the required fields were left empty (regisrar, status, or expiry).';
				}
				else if (!is('expdate', $expiry))
				{
					$result .= 'The expiry date seems to be invalid. Please use the format: mm/dd/yyyy';
				}
				else
				{
					$numlines = count($domains);

					for ($i = 0; $i <= $numlines; $i++)
					{
						$domains[$i] = sanitize($domains[$i]);

						// Let's remove http://, https://, or www.
						$domains[$i] = str_replace(array('http://', 'https://'), '', $domains[$i]);
						$domains[$i] = preg_replace('#^www\.#i', '', $domains[$i]);

						// Moniker / GoDaddy make domains all CAPS. We don't want them that way.
						if ($domains[$i] == strtoupper($domains[$i]))
						{
							$domains[$i] = strtolower($domains[$i]);
						}

						if (trim($domains[$i]) == '')
						{
							continue;
						}

						if (is('domain', $db->prepare($domains[$i])))
						{
							continue;
						}

						// Is the domain valid?
						if (!preg_match('#^[a-z0-9]([-a-z0-9]+)?(\.[a-z]{2,3})?(\.[a-z]{2,4})$#i', $domains[$i]))
						{
							continue;
						}

						if ($domains[$i])
						{
							$sql = $db->query("
								INSERT INTO " . TABLE_PREFIX . "domains
									(domain, description, keywords, registrar, expiry, price, status, added, issite, hidden)
								VALUES
									(
										'" . $db->prepare($domains[$i]) . "',
										'',
										'',
										'" . $db->prepare($registrar) . "',
										" . strtotime($expiry) . ",
										'" . $db->prepare($price) . "',
										'" . $db->prepare($status) . "',
										" . time() . ",
										$issite,
										$hidden
									)
							");

							if ($sql)
							{
								// It's been added!
								$result .= "{$domains[$i]} added.<br />";
							}
							else
							{
								$result .= $db->raise_error();
							}
						}
					}
				}
				break;
			// ################################################################
			case 'legacy':
				$numlines = count($domains);
		
				for ($i = 0; $i <= $numlines; $i++)
				{
					$domains[$i] = sanitize($domains[$i]);
		
					if (trim($domains[$i]) == '')
					{
						continue;
					}

					$parts = explode(',', $domains[$i]);

					if (count($parts) != 5)
					{
						$result .= "You need to specifiy <code>Domain,Registrar,Expiry,Price,Status</code> you entered <em>{$domains[$i]}</em>";
						break;
					}
					else
					{
						// Let's remove http://, https://, or www.
						$parts[0] = str_replace(array('http://', 'https://'), '', $parts[0]);
						$parts[0] = preg_replace('#^www\.#i', '', $parts[0]);

						// Moniker / GoDaddy make domains all CAPS. We don't want them that way.
						if ($parts[0] == strtoupper($parts[0]))
						{
							$parts[0] = strtolower($parts[0]);
						}

						// Exp.
						$parts[2] = str_replace('-', '/', $parts[2]);

						$_tmp = explode('/', $parts[2]);
						$_tmp[2] = ($_tmp[2] > 2037) ? 2037 : $_tmp[2];

						$parts[2] = implode('/', $_tmp);
						unset($_tmp);

						// Status
						if (empty($parts[4]) OR !in_array($parts[4], array('For Sale', 'Not For Sale', 'Make Offer', 'Sold')))
						{
							$parts[4] = 'For Sale';
						}

						// Price
						$parts[3] = preg_replace('#([^0-9,\.]+)#', '', $parts[3]);

						if (empty($parts[3]) OR $parts[4] == 'Not For Sale' OR $parts[4] == 'Make Offer')
						{
							$parts[3] = '0.00';
						}

						// Is it a valid domain, does it not already exist in the db, and is the expiration date valid?
						if (!preg_match('#^[a-z0-9]([-a-z0-9]+)?(\.[a-z]{2,3})?(\.[a-z]{2,4})$#i', $parts[0]))
						{
							$result .= "$parts[0] appears to be an invalid domain.<br />";
						}
						else if (is('domain', $db->prepare($parts[0])))
						{
							$result .= "$parts[0] is already in the database.<br />";
						}
						else if (!is('expdate', $parts[2]))
						{
							$result .= "$parts[2] for $parts[0] is an invalid expiration date. Please use the format: mm/dd/yyyy<br />";
						}
						else
						{
							// Insert..
							$sql = $db->query("
								INSERT INTO " . TABLE_PREFIX . "domains
									(domain, description, keywords, registrar, expiry, price, status, added, issite, hidden)
								VALUES
									(
										'" . $db->prepare($parts[0]) . "',
										'',
										'',
										'" . $db->prepare($parts[1]) . "',
										'" . strtotime($parts[2]) . "',
										'" . $db->prepare($parts[3]) . "',
										'" . $db->prepare($parts[4]) . "',
										" . time() . ",
										0,
										0
									)
							");

							if ($sql)
							{
								$result .= "$parts[0] added.<br />\n";
							}
							else
							{
								$result .= $db->raise_error();
							}
						}
					}
				}
				break;
		}
	}
	unset($domains);

	$result = "<div id=\"result\" style=\"height: 125px; overflow: scroll;\">$result</div>";
}

// ################################################################
// Output page
$pagetitle = 'Add Domains Bulk';

include("$template_admin/add_domains_bulk.php");

?>