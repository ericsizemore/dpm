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

// Insert category
if (!empty($_POST['submit']))
{
	$category    = sanitize($_POST['category']);
	$description = sanitize(str_replace(array('"', "\r\n"), array('\'', "\n"), $_POST['description']), false);
	$keywords    = sanitize(strtolower($_POST['keywords']));

	/**
	* Create a session value for category, description, and keywords.
	* This way, if there's an error, we won't lost what has been entered.
	*/
	$_SESSION['form'] = array(
		'category'     => $category,
		'description'  => $description,
		'cat_keywords' => $keywords
	);

	// Was the category left empty or does it already exist in the database?
	if (empty($category))
	{
		$result .= 'You must enter a title for the category.';
	}
	else if (is('category', $db->prepare($category)))
	{
		$result .= "The category '$category' already exists in the database.";
	}
	else
	{
		if ($db->query("
			INSERT INTO " . TABLE_PREFIX . "categories (title, description, keywords)
			VALUES ('" . $db->prepare($category) . "', '" . $db->prepare($description) . "', '" . $db->prepare($keywords) . "')
		"))
		{
			$result .= "Category '$category' added.";
		}
		else
		{
			$db->raise_error();
		}

		// Reset the session array
		$_SESSION['form'] = array(
			'category'     => '',
			'description'  => '',
			'cat_keywords' => ''
		);
	}
	$result = "<div id=\"result\">$result</div>";
}
else
{
	$_SESSION['form'] = array(
		'category'     => '',
		'description'  => '',
		'cat_keywords' => ''
	);
}

// ################################################################
// Output page
$pagetitle = 'Add Category';

include("$template_admin/add_category.php");

?>