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
* @file      ./rss.php
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
$path = str_replace(dpm_getenv('DOCUMENT_ROOT'), '', realpath('.')) . '/';
$path = str_replace('//', '/', $path);
$host = HOST;

$feed = sanitize($_GET['feed']);

if (empty($feed))
{
	$feed = 'latest';
}

// ################################################################
switch ($feed)
{
	case 'category':
		$catid = (isset($_GET['catid'])) ? intval($_GET['catid']) : '';
		$catid = is('catid', $catid);

		if (!is_null($catid))
		{
			$query = $db->query("
				SELECT domains.*, IF(categories.title IS NULL, 'None', GROUP_CONCAT(categories.title SEPARATOR ', ')) AS category
				FROM " . TABLE_PREFIX . "domains AS domains
				LEFT JOIN " . TABLE_PREFIX . "dom2cat AS d2c ON (domains.domainid = d2c.domainid)
				LEFT JOIN " . TABLE_PREFIX . "categories AS categories ON (d2c.catid = categories.catid)
				WHERE domains.hidden != 1
					AND domains.status != 'Sold'
					AND d2c.catid = $catid
				GROUP BY COALESCE(d2c.domainid, RAND())
				ORDER BY domains.domain ASC
				LIMIT 10
			") or $db->raise_error();

			if ($db->num_rows($query) > 0)
			{
				while ($category = $db->fetch_array($query))
				{
					$category['description'] = (empty($category['description'])) ? $category['domain'] : $category['description'];

					if (strpos($feed, ' - ' . $category['category']) === false)
					{
						$feed .= ' - ' . $category['category'];
					}

					$items .= "
		<item>
			<title>$category[domain]</title>
			<link>http://{$host}/{$path}details.php?d=$category[domainid]</link>
			<pubDate>" . gmdate('D, d M Y H:i:s T', $category['added']) . "</pubDate>
			<category>$category[category]</category>
			<description>$category[description]</description>
			<guid isPermaLink=\"true\">http://{$host}/{$path}details.php?d=$category[domainid]</guid>
		</item>
";
				}
			}
			$db->free_result($query);
		}

		if (empty($items))
		{
			$items .= "
		<item>
			<title />
			<link />
			<pubDate />
			<category />
			<description />
			<guid />
		</item>
";
		}
		break;
	case 'latest':
	default:
		$query = $db->query("
			SELECT domainid, domain, description, added
			FROM " . TABLE_PREFIX . "domains
			WHERE hidden != 1
				AND status != 'Sold'
			ORDER BY added DESC
			LIMIT 10
		") or $db->raise_error();

		if ($db->num_rows($query) > 0)
		{
			while ($latest = $db->fetch_array($query))
			{
				$latest['description'] = (empty($latest['description'])) ? $latest['domain'] : $latest['description'];

				$items .= "
		<item>
			<title>$latest[domain]</title>
			<link>http://{$host}/{$path}details.php?d=$latest[domainid]</link>
			<pubDate>" . gmdate('D, d M Y H:i:s T', $latest['added']) . "</pubDate>
			<description>$latest[description]</description>
			<guid isPermaLink=\"true\">http://{$host}/{$path}details.php?d=$latest[domainid]</guid>
		</item>
";
			}
		}
		else
		{
			$items .= "
		<item>
			<title />
			<link />
			<pubDate />
			<description />
			<guid />
		</item>
";
		}
		$db->free_result($query);
		break;
}

if (empty($items))
{
	exit;
}

// ################################################################
header('Content-type: text/xml; charset=UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" 
	xmlns:dc="http://purl.org/dc/elements/1.1/" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
>
	<channel>
		<title>' . $config->get('title') . ' - ' . ucfirst($feed) . '</title>
		<link>http://' . $host . $path . '</link>
		<description>' . $config->get('description') . '</description>
		<language>en</language>
		<lastBuildDate>' . gmdate('D, d M Y H:i:s T') . '</lastBuildDate>
		<generator>Domain Portfolio Manager v' . $version . '</generator>
';

echo $items;

echo '
	</channel>
</rss>';

exit;

?>