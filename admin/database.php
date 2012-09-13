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

$default_tables = array(
	TABLE_PREFIX . 'admin',
	TABLE_PREFIX . 'config',
	TABLE_PREFIX . 'domains',
	TABLE_PREFIX . 'dom2cat',
	TABLE_PREFIX . 'categories',
	TABLE_PREFIX . 'paypal_log'
);

// ################################################################
if (!empty($_POST['backup']))
{
	$format = sanitize($_POST['format']);

	switch ($format)
	{
		case 'xls':
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: no-cache');
			header('Content-Type: application/vnd.ms-excel name="dpm_excel_' . gmdate('d-m-Y') . '.xls"');
			header('Content-Disposition: attachment; filename="dpm_excel_' . gmdate('d-m-Y') . '.xls"');

			$exceldata = "Domain\tCategory\tRegistrar\tExpiration\tStatus\tDate Added\t\n";

			xlsStart();

			xlsWriteLabel(0, 0, 'Domain');
			xlsWriteLabel(0, 1, 'Category');
			xlsWriteLabel(0, 2, 'Registrar');
			xlsWriteLabel(0, 3, 'Expiration');
			xlsWriteLabel(0, 4, 'Status');
			xlsWriteLabel(0, 5, 'Date Added');

			$getdomains = $db->query("
				SELECT domains.*, IF(categories.title IS NULL, 'None', GROUP_CONCAT(categories.title SEPARATOR ', ')) AS category
				FROM " . TABLE_PREFIX . "domains AS domains
				LEFT JOIN " . TABLE_PREFIX . "dom2cat AS d2c ON (domains.domainid = d2c.domainid)
				LEFT JOIN " . TABLE_PREFIX . "categories AS categories ON (d2c.catid = categories.catid)
				GROUP BY COALESCE(d2c.domainid, RAND())
				ORDER BY domains.domainid ASC
			") or $db->raise_error();

			$i = 1;

			while ($domain = $db->fetch_array($getdomains))
			{
				/// Build excel data
				$domain['expiry'] = date('m/d/Y', $domain['expiry']);
				$domain['added'] = date('m/d/Y', $domain['added']);

				xlsWriteLabel($i, 0, $domain['domain']);
				xlsWriteLabel($i, 1, $domain['category']);
				xlsWriteLabel($i, 2, $domain['registrar']);
				xlsWriteLabel($i, 3, $domain['expiry']);
				xlsWriteLabel($i, 4, $domain['status']);
				xlsWriteLabel($i, 5, $domain['added']);

				$i++;
			}

			xlsEnd();

			$db->free_result($getdomains);
			break;
		case 'sql':
		default:
			$backup = db_backup::getInstance();
			$backup->set_params($db, $default_tables);

			if ($backup->do_backup())
			{
				$result = 'Your database has been successfully backed up to:<br /><code>' . $backup->file . '</code>';
			}
			else
			{
				$result = 'Your database could not be backed up:<br /><code>' . $backup->error . '</code>';
			}
			break;
	}

	if ($format == 'xls')
	{
		exit;
	}
}

// ################################################################
$restoreoptions = array();

foreach (new DirectoryIterator('backups') AS $file)
{
	if (strpos($file->getFilename(), '.sql') !== false)
	{
		$restoreoptions[] = $file->getFilename();
	}
}

// Restore?
if (!empty($_POST['restore']))
{
	if ($file != -1)
	{
		$file = 'backups/' . sanitize($_POST['file']);

		$restore = db_restore::getInstance();
		$restore->set_params($db);

		if ($restore->do_restore($file))
		{
			$result = 'Your database has been successfully restored.';
		}
		else
		{
			$result = $restore->error;
		}
	}
	else
	{
		redirect('database.php');
	}
}

// ################################################################
// Optimize?
if (isset($_GET['optimize'], $_GET['table']) AND in_array($_GET['table'], $default_tables))
{
	$_GET['table'] = $db->prepare($_GET['table']);

	$db->query("OPTIMIZE TABLE $_GET[table]");

	redirect('database.php');
}

// ################################################################
// Get tables and their stats
$tablestats = array();

if (($stats = $db->query("
	SHOW TABLE STATUS
	FROM " . $db->prepare($config->get('dbname')) . "
	LIKE '%" . TABLE_PREFIX . "%'
")))
{
	$row = 0;

	while ($stat = $db->fetch_array($stats))
	{
		$stat['class'] = ($row & 1);
		$tablestats[] = $stat;

		$row++;
	}
}

// ################################################################
$pagetitle = 'Database';

include("$template_admin/database.php");

?>