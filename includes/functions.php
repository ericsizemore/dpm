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
* @file      ./includes/functions.php
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

if (!defined('IN_DPM'))
{
	die('You\'re not supposed to be here.');
}

/**
* Gets the host information the best way we can.
*
* @param  void
* @return string
*/
function get_host()
{
	$host = strtolower(strval(dpm_getenv('HTTP_HOST')));

	if (!preg_match('#^\[?(?:[a-zA-Z0-9-:\]_]+\.?)+$#', $host))
	{
		$host = 'localhost';
	}

	if (long2ip(ip2long($host)) === $host)
	{
		return $host;
	}
	return preg_replace('#^www\.#', '', $host);
}

/**
* Strip any unsafe tags/chars/attributes from input values.
*
* @param  string   $value       Value to be cleaned
* @param  boolean  $strip_crlf  Strip \r\n ?
* @param  boolean  $is_email    If the value is an email, pass it through the email sanitize filter.
* @return string                Sanitized value.
*/
function sanitize($value, $strip_crlf = true, $is_email = false)
{
	$value = preg_replace('@&(?!(#[0-9]+|[a-z]+);)@si', '', $value);

	if ($is_email)
	{
		/**
		* PHP versions older than 5.2.11 have bugs in FILTER_SANITIZE_EMAIL
		* It allows characters that shouldn't be allowed.
		*
		* We will only sanitize the email if they are using 5.2.11 and greater.
		* This shouldn't pose a problem on < 5.2.11 cause we validate the email
		* later on anyway.
		*/
		if (version_compare(PHP_VERSION, '5.2.11', '>='))
		{
			$value = filter_var($value, FILTER_SANITIZE_EMAIL);
		}
	}
	else
	{
		$value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	}

	// This will strip new line characters if $strip_crlf is set to true.
	if ($strip_crlf)
	{
		$value = preg_replace('@([\r\n])[\s]+@', '', $value);
	}

	$value = str_replace(array("\x0B", "\0"), '', $value);

	return clean($value);
}

/**
* Clean values pulled from the database, although could be used on anything.
*
* Cleans either a string, or can clean an entire array of values:
*	clean($array);
*
* @param  mixed  $value  Value to be cleaned
* @return mixed          Cleaned array or string.
*/
function clean($value)
{
	if (is_array($value))
	{
		foreach ($value AS $key => $val)
		{
			if (is_string($val))
			{
				$value["$key"] = trim(stripslashes($val));
			}
			else if (is_array($val))
			{
				$value["$key"] = clean($value["$key"]);
			}
		}
		return $value;
	}
	return trim(stripslashes($value));
}

/**
* Will make sure a variable is valid.
*
* @param  string  $option  What to check
* @param  mixed   $value   What to check's value.
* @return mixed            Depending on the $option, could return a string, NULL, or boolean.
*/
function is($option, $value)
{
	global $db;

	switch ($option)
	{
		case 'orderby':
			if (!in_array($value, array('domain', 'category', 'registrar', 'expiry', 'price', 'status')))
			{
				return 'domain';
			}
			break;
		case 'catid':
			if (is_string($value))
			{
				return NULL;
			}
			else if (is_integer($value) AND !is('category', $value))
			{
				return NULL;
			}
			break;
		case 'email':
			return (bool)(preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s\'"<>]+\.+[a-z]{2,6}))$#si', $value));
			break;
		case 'injection':
			return (bool)(preg_match('#(To:|Bcc:|Cc:|Content-type:|Mime-version:|Content-Transfer-Encoding:)#i', urldecode($value)));
			break;
		case 'spam':
			preg_match_all('#(<a href|\[url|http[s]?://)#i', $value, $matches, PREG_PATTERN_ORDER);
			return (bool)(count($matches[0]) > 2);
			break;
		case 'domain':
			$getdomain = $db->query("
				SELECT *
				FROM " . TABLE_PREFIX . "domains
				WHERE " . (is_numeric($value) ? "domainid = " . intval($value) : "domain = '$value'") . "
			");

			$numrows = $db->num_rows($getdomain);
			$db->free_result($getdomain);

			return (bool)($numrows > 0);
			break;
		case 'category':
			if (is_numeric($value) AND $value == 0)
			{
				return true;
			}

			$getcategory = $db->query("
				SELECT *
				FROM " . TABLE_PREFIX . "categories
				WHERE " . (is_numeric($value) ? "catid = " . intval($value) : "title = '$category'") . "
			");

			$numrows = $db->num_rows($getcategory);
			$db->free_result($getcategory);

			return (bool)($numrows > 0);
			break;
		case 'expdate':
			$value = str_replace('-', '/', $value);

			// Expects mm/dd/yyyy Example: 06/11/2011
			if (preg_match('#[0-9]{2}/[0-9]{2}/[0-9]{4}#', $value))
			{
				$value = explode('/', $value);

				if ($value[2] < date('Y'))
				{
					return false;
				}

				if (checkdate($value[0], $value[1], $value[2]))
				{
					return true;
				}
			}
			return false;
			break;
	}
	return $value;
}

/**
* Get the users ip address.
*
* @param  void
* @return string  IP Address
*/
function get_ip()
{
	$ip = dpm_getenv('REMOTE_ADDR');

	if (dpm_getenv('HTTP_X_FORWARDED_FOR'))
	{
		if (preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', dpm_getenv('HTTP_X_FORWARDED_FOR'), $matches))
		{
			foreach ($matches[0] AS $match)
			{
				if (!preg_match('#^(10|172\.16|192\.168)\.#', $match))
				{
					$ip = $match;
					break;
				}
			}
			unset($matches);
		}
	}
	else if (dpm_getenv('HTTP_CLIENT_IP'))
	{
		$ip = dpm_getenv('HTTP_CLIENT_IP');
	}
	else if (dpm_getenv('HTTP_FROM'))
	{
		$ip = dpm_getenv('HTTP_FROM');
	}

	if (!filter_var($ip, FILTER_VALIDATE_IP))
	{
		return '0.0.0.0';
	}
	return $ip;
}

/**
* Returns an environment variable.
*
* @param  string  $varname  Variable name, eg: PHP_SELF
* @return string            Variable's value.
*/
function dpm_getenv($varname)
{
	if (isset($_SERVER[$varname]))
	{
		return $_SERVER[$varname];
	}
	else if (isset($_ENV[$varname]))
	{
		return $_ENV[$varname];
	}
	return '';
}

/**
* A wrapper for date that will format dates using the UTC timezone.
* This is used for domain expiration dates as UTC is pretty much 
* the standard for domain expiration dates.
*
* @param  string   $format     Date format
* @param  integer  $timestamp  Unix timestamp
* @return string               Formatted date
*/
function dpm_date($format, $timestamp)
{
	if (empty($timestamp))
	{
		$timestamp = time();
	}

	// Get the current timezone
	$current_tz = date_default_timezone_get();

	// Set to UTC, as that's pretty much the standard for domain expiration
	date_default_timezone_set('UTC');

	// format the time
	$date = date($format, $timestamp);

	// Set back to old timezone
	date_default_timezone_set($current_tz);

	return $date;
}

/**
* Returns a md5'ed hash.
*
* @param  string  $string  String to hash
* @return string  Hash
*/
function dpm_hash($string)
{
	return hash('md5', $string);
}

/**
* Helper function for encode_email
*
* @param  string  $char  Character to encode.
* @return string         Encoded character.
*/
function _encode_email_helper($char = 0)
{
	return '&#' . ord($char) . ';';
}

/**
* Encodes an email address, if that email is valid.
*
* At this time, this will mainly be used on database error pages, that
* places the email address in HTML.
*
* @param  string  $email  Email address to encode.
* @return string          Encoded email address if valid, or plain email if not valid.
*/
function encode_email($email)
{
	if (is('email', $email))
	{
		$chars = str_split($email);

		$encoded = filter_var($chars, FILTER_CALLBACK, array('options' => '_encode_email_helper'));
		$encoded = implode('', $encoded);

		unset($chars);

		return $encoded;
	}
	return $email;
}

/**
* Redirects to another URL.
*
* @param  string  $url  Destination url
* @return void
*/
function redirect($url)
{
	if (count($_SESSION))
	{
		session_write_close();
	}

	$url = filter_var($url, FILTER_SANITIZE_URL);

	header("Location: $url", true, 302);
	exit;
}

/**
* Generates the pagination.
*
* @param  integer  $numresults  Total number of results.
* @param  integer  $page        Current page
* @param  string   $orderby     Sort order
* @param  integer  $catid       Category ID (if any)
* @param  string   $search      Search query (will only be used for search.php)
* @return array                 Array consisting of SQL limit, and pagination links.
*/
function paginate($numresults, $page, $orderby = NULL, $catid = NULL, $search = NULL)
{
	global $config;

	$usedefault = (bool)($config->get('template') != 'legacy' AND !defined('IN_ADMIN'));
	$link = '';
	$extra = array();

	if ($numresults > 0)
	{
		$perpage = ($config->get('maxperpage') <= 10) ? 10 : $config->get('maxperpage');
		$numpages = ceil($numresults / $perpage);
		$numpages = ($numpages == 0) ? 1 : $numpages;
		$page = ($page < 1) ? 1 : ($page > $numpages ? $numpages : $page);

		// Currently using sort?
		if (!is_null($orderby))
		{
			array_push($extra, "&amp;sort=$orderby");
		}

		// Browsing a category?
		if (!is_null($catid))
		{
			array_push($extra, "&amp;cat=$catid");
		}

		// Searching?
		if (!is_null($search))
		{
			array_push($extra, '&amp;query=' . urlencode($search));
		}

		$extra = implode('', $extra);

		// Generate Links - I know, it looks messy, but it works :P
		// It'll be something like: 1 ... 4 5 6 7 8 ... 15
		if ($page > 1)
		{
			$link .= " <a href=\"?page=1$extra\">First</a>";

			if ($page > 3 AND $page != 4)
			{
				$link .= ($usedefault) ? ' <span class="current">&hellip;</span>' : '&hellip;';
			}
		}

		for ($i = ($page - 2), $stop = ($page + 3); $i <= $stop; ++$i)
		{
			if ($i < 1 OR $i > $numpages)
			{
				continue;
			}

			if ($page == $i)
			{
				$link .= ($usedefault) ? " <span class=\"current\">$i</span> " : " <strong>[$i]</strong> ";
			}
			else
			{
				$link .= " <a href=\"?page=$i$extra\">$i</a> ";
			}
		}

		if ($page <= ($numpages - 3) AND $page != ($numpages - 3))
		{
			$link .= ($usedefault) ? ' <span class="current">&hellip;</span>' : '&hellip;';
		}

		if ($page < $numpages)
		{
			$link .= " <a href=\"?page=$numpages$extra\">Last</a>";
		}

		$lowerlimit = ($page - 1) * $perpage + 1;
		$upperlimit = $page * $perpage;

		if ($upperlimit > $numresults)
		{
			$upperlimit = $numresults;

			if ($lowerlimit > $numresults)
			{
				$lowerlimit = $numresults - $perpage;
			}
		}

		if ($lowerlimit <= 0)
		{
			$lowerlimit = 1;
		}
	}
	else
	{
		$lowerlimit = 1;
		$link = '';
	}

	return array(
		'limit' => $lowerlimit - 1,
		'link'  => $link
	);
}

/**
* Builds the Adsense HTML for header/sidebar.
*
* TODO: clean this up, little messy at the moment.
*
* @param  string  Is it for the header or sidebar?
* @return string
*/
function build_adsense($for)
{
	global $config, $adsense;

	$_tmp   = explode('x', $adsense["$for"]['size']);
	$width  = (!empty($_tmp[0])) ? $_tmp[0] : ($for == 'header' ? '728' : '180');
	$height = (!empty($_tmp[1])) ? $_tmp[1] : ($for == 'header' ? '90' : '150');
	$type   = (!empty($adsense["$for"]['type'])) ? $adsense["$for"]['type'] : 'text_image';

	$adsense['pubid'] = str_replace('pub-', '', $adsense['pubid']);

	$html = <<<AD
	<div id="adsense_{$for}">
		<script type="text/javascript">
		<!--
		google_ad_client = "pub-$adsense[pubid]";
		google_ad_width = $width;
		google_ad_height = $height;
		google_ad_format = "{$width}x{$height}_as";
		google_ad_type = "$type";
		google_ad_channel = "";

AD;

	unset($_tmp, $width, $height, $type);

	switch ($for)
	{
		case 'header':
			if ($config->get('template') == 'default')
			{
				$html .= <<<AD
		google_color_border = "FFFFFF";
		google_color_bg = "FFFFFF";
		google_color_link = "0b5e9d";
		google_color_text = "726B67";
		google_color_url = "0b5e9d";
AD;
			}
			else
			{
				$html .= <<<AD
		google_color_border = "FFFFFF";
		google_color_bg = "FFFFFF";
		google_color_link = "1082BE";
		google_color_text = "142E3A";
		google_color_url = "1082BE";
AD;
			}
			break;
		case 'sidebar':
			if ($config->get('template') == 'default')
			{
				$html .= <<<AD
		google_color_border = "9DBFD8";
		google_color_bg = "9DBFD8";
		google_color_link = "FFFFFF";
		google_color_text = "3962a0";
		google_color_url = "FFFFFF";
AD;
			}
			else
			{
				$html .= <<<AD
		google_color_border = "C5E3F1";
		google_color_bg = "C5E3F1";
		google_color_link = "1082BE";
		google_color_text = "FFFFFF";
		google_color_url = "1082BE";
AD;
			}
			break;
	}

	$html .= <<<AD

		//-->
		</script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
	</div>

AD;

	return $html;
}

/**
* Builds the HTML for categories and latest domains added.
*
* @param  string  $list  Which list to build.
* @return string         List HTML
*/
function build_list($list)
{
	global $db, $config;

	$uselegacy = (bool)($config->get('template') == 'legacy');

	switch ($list)
	{
		case 'cats':
		$rsshtml = <<<RSS
&nbsp; <span style="float: right;"><a href="rss.php?feed=category&amp;catid=%d" title="RSS Feed for '%s' domains."><img src="./templates/default/images/feed.png" alt="RSS Feed for '%s' domains." border="0" /></a></span>
RSS;
			$getcats = $db->query("
				SELECT catid, title, description
				FROM " . TABLE_PREFIX . "categories
				ORDER BY title ASC
			") or $db->raise_error();

			if ($db->num_rows($getcats) > 0)
			{
				$class = '';

				while ($cat = $db->fetch_array($getcats))
				{
					$class = ($class == '') ? ' class="odd"' : '';

					$cat['description'] = ($cat['description'] != '') ? $cat['description'] : $cat['title'];

					$cat['numdomains'] = $db->query("
						SELECT COUNT(d2c.domainid) AS count
						FROM " . TABLE_PREFIX . "dom2cat AS d2c
						LEFT JOIN " . TABLE_PREFIX . "domains AS dom ON(dom.domainid = d2c.domainid)
						WHERE d2c.catid = $cat[catid]
							AND dom.status != 'Sold'
							AND dom.hidden != 1
					", true);
					$cat['numdomains'] = $cat['numdomains']['count'];

					if ($uselegacy)
					{
						$listhtml .= "<a href=\"./?cat=$cat[catid]\" title=\"$cat[description]\">$cat[title] ($cat[numdomains])</a>\n";
					}
					else
					{
						$listhtml .= "<li$class><a href=\"./?cat=$cat[catid]\" title=\"$cat[description]\">$cat[title]</a> ($cat[numdomains])";
						$listhtml .= sprintf($rsshtml, $cat['catid'], $cat['title'], $cat['title']) . "</li>\n";
					}
				}
			}
			else
			{
				$listhtml = ($uselegacy) ? 'No Categories Yet' : '<li>No Categories Yet</li>';
			}
			$db->free_result($getcats);

			return trim($listhtml) . "\n";
			break;
		case 'latest':
			$getlatest = $db->query("
				SELECT domainid, domain, added
				FROM " . TABLE_PREFIX . "domains
				WHERE hidden != 1
					AND status != 'Sold'
				ORDER BY added DESC
				LIMIT 5
			") or $db->raise_error();

			if ($db->num_rows($getlatest) == 0)
			{
				$listhtml = ($uselegacy) ? '&nbsp;- None' : '<li>None</li>';
			}
			else
			{
				$class = '';

				while ($latest = $db->fetch_array($getlatest))
				{
					$date = date('m/d/Y', $latest['added']);

					if ($uselegacy)
					{
						$listhtml .= "<a href=\"details.php?d=$latest[domainid]\" title=\"Details for $latest[domain]\">$latest[domain] ($date)</a>\n";
					}
					else
					{
						$listhtml .= "<li$class><a href=\"details.php?d=$latest[domainid]\" title=\"Details for $latest[domain]\">$latest[domain]</a> ($date)</li>\n";
					}

					$class = ($class == '') ? ' class="odd"' : '';
				}
			}
			$db->free_result($getlatest);

			return trim($listhtml) . "\n";
			break;
		default:
			break;
	}
}

/**
* Gets current page name.
*
* @param  void
* @return string  Current page name.
*/
function dpm_page()
{
	$page = basename($_SERVER['SCRIPT_FILENAME'], '.php');

	if (empty($page) OR $page == 'index')
	{
		$page = 'home';
	}
	return $page;
}

/**
* Gets the latest version of Domain Portfolio Manager.
* Borrowed from phpBB and modified - phpBB.com
*
* @param  boolean  $check  Check domain-portfolio.secondversion.com for latest version?
* @return string           Latest version if $check is true, current script version if not.
*/
function dpm_version($check = false)
{
	if ($check)
	{
		if ($fsock = @fsockopen('domain-portfolio.secondversion.com', 80, $errno, $errstr, 10))
		{
			fputs($fsock, "GET /current.txt HTTP/1.1\r\nHOST: domain-portfolio.secondversion.com\r\nConnection: close\r\n\r\n");

			$latest = '';
			$getinfo = false;

			while (!feof($fsock))
			{
				if ($getinfo)
				{
					$latest .= fread($fsock, 1024);
				}
				else
				{
					if (fgets($fsock, 1024) == "\r\n")
					{
						$getinfo = true;
					}
				}
			}
			fclose($fsock);

			$latest = array_map('trim', explode("\n", $latest));

			return implode('.', $latest);
		}
		return 'n/a';
	}
	else
	{
		return '1.0.1';
	}
}

/**
* Will get values from the database for forms, etc.
*
* @param  string   $option  What do we need the value for?
* @param  integer  $id      If it's for a domain or category (by ID)
* @return array             Data for given $option from the database.
*/
function get_value($option, $id = 0)
{
	global $db;

	switch ($option)
	{
		case 'paypal':
			$domaininfo = $db->query("
				SELECT * 
				FROM " . TABLE_PREFIX . "domains
				WHERE " . (is_numeric($id) ? "domainid = '" . intval($id) . "'" : "domain = '$id'") . "
			", true);
			return $domaininfo;
			break;
		case 'contact':
			$domaininfo = $db->query("
				SELECT domain, status
				FROM " . TABLE_PREFIX . "domains
				WHERE " . (is_numeric($id) ? "domainid = '" . intval($id) . "'" : "domain = '$id'") . "
					AND hidden != 1
			", true) or $db->raise_error();
			return $domaininfo;
			break;
		case 'details':
		case 'domainedit':
			$domaininfo = $db->query("
				SELECT domains.*, d2c.*, IF(categories.title IS NULL, 'None', GROUP_CONCAT(categories.title SEPARATOR ', ')) AS category, domains.domainid AS domainid
				FROM " . TABLE_PREFIX . "domains AS domains
				LEFT JOIN " . TABLE_PREFIX . "dom2cat AS d2c ON (domains.domainid = d2c.domainid)
				LEFT JOIN " . TABLE_PREFIX . "categories AS categories ON (d2c.catid = categories.catid)
				WHERE " . (is_numeric($id) ? "domains.domainid = '" . intval($id) . "'" : "domains.domain = '$id'") . "
				GROUP BY COALESCE(d2c.domainid, RAND())
			", true) or $db->raise_error();
			return (count($domaininfo)) ? $domaininfo : array();
			break;
		case 'catedit':
			$catinfo = $db->query("
				SELECT title, description, keywords
				FROM " . TABLE_PREFIX . "categories
				WHERE catid = '$id'
			", true) or $db->raise_error();
			return (count($catinfo)) ? $catinfo : array();
			break;
		default:
			break;
	}
}

/**
* Builds the 'hide' dropdown menu in admin for the edit page.
*
* @param  string  $option  Which select to build.
* @param  string  $value   Select's value.
* @return string           The $option's HTML
*/
function build_select($option, $value = '')
{
	global $db;

	$values = array(
		'hidden'         => array('No', 'Yes'),
		'issite'         => array('No', 'Yes'),
		'paypal_sandbox' => array('No', 'Yes'),
		'paypal_log'     => array('No', 'Yes'),
		'status'         => array('For Sale', 'Not For Sale', 'Make Offer', 'Pending Sale', 'Sold')
	);

	$select = '';

	switch ($option)
	{
		case 'hidden':
		case 'issite':
		case 'paypal_sandbox':
		case 'paypal_log':
			foreach ($values[$option] AS $key => $val)
			{
				$select .= "<option label=\"$val\" value=\"$key\"" . ($value == $key ? ' selected="selected"' : '') . ">$val</option>\n";
			}
			break;
		case 'status':
			foreach ($values[$option] AS $val)
			{
				$select .= "<option label=\"$val\" value=\"$val\"" . ($value == $val ? ' selected="selected"' : '') . ">$val</option>\n";
			}
			break;
		case 'category':
			$select = '<option label="None" value="-1">None</option>' . "\n";

			$getcats = $db->query("
				SELECT catid, title
				FROM " . TABLE_PREFIX . "categories
				ORDER BY title ASC
			") or $db->raise_error();

			while ($cat = $db->fetch_array($getcats))
			{
				if ($value == '')
				{
					$select .= "<option label=\"$cat[title]\" value=\"$cat[catid]\">$cat[title]</option>\n";
				}
				else
				{
					$dom2cats = $db->fetch_all("
						SELECT domainid, catid
						FROM " . TABLE_PREFIX . "dom2cat
						WHERE domainid = $value
					", 'catid');

					$select .= "<option label=\"$cat[title]\" value=\"$cat[catid]\"";
					$select .= (in_array($cat['catid'], $dom2cats) ? ' selected="selected"' : '') . ">$cat[title]</option>\n";
				}
			}
			$db->free_result($getcats);
			break;
	}
	return $select;
}

/**
* Returns a 'nice', human-readable size - thanks to WordPress
*
* @param  integer  $bytes  Size to format
* @return string           Human-readable size.
*/
function size_format($bytes)
{
	$quant = array(
		'TB' => pow(1024, 4),
		'GB' => pow(1024, 3),
		'MB' => pow(1024, 2),
		'kB' => pow(1024, 1),
		'B'  => pow(1024, 0)
	);

	foreach ($quant AS $unit => $mag)
	{
		if (intval($bytes) >= $mag)
		{
			return number_format($bytes / $mag) . " $unit";
		}
	}
	return '-';
}

/**
* Functions for the Excel backup of database.
*/
function xlsStart()
{
	echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);  
	return;
}

function xlsEnd()
{
	echo pack("ss", 0x0A, 0x00);
	return;
}

function xlsWriteNumber($row, $col, $value)
{
	echo pack("sssss", 0x203, 14, $row, $col, 0x0);
	echo pack("d", $value);
	return;
}

function xlsWriteLabel($row, $col, $value)
{
	$length = strlen($value);

	echo pack("ssssss", 0x204, 8 + $length, $row, $col, 0x0, $length);
	echo $value;
	return;
}
