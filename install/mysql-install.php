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

// ################################################################################
if (!is_object($db))
{
	exit;
}

// ################################################################################
$schema['CREATE']['query']['admin'] = "
CREATE TABLE IF NOT EXISTS " . TABLE_PREFIX . "admin (
	adminid  INT UNSIGNED NOT NULL AUTO_INCREMENT,
	username VARCHAR(32) NOT NULL,
	password CHAR(32) NOT NULL,
	PRIMARY KEY (adminid),
	KEY username (username)
) ENGINE=MyISAM
";
$schema['CREATE']['explain']['admin'] = 'Table <strong>' . TABLE_PREFIX . 'admin</strong> created';

$schema['CREATE']['query']['config'] = "
CREATE TABLE IF NOT EXISTS " . TABLE_PREFIX . "config (
	id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name  VARCHAR(25) NOT NULL,
	value VARCHAR(255) NOT NULL,
	PRIMARY KEY (id),
	KEY name (name)
) ENGINE=MyISAM
";
$schema['CREATE']['explain']['config'] = 'Table <strong>' . TABLE_PREFIX . 'config</strong> created';

$schema['CREATE']['query']['categories'] = "
CREATE TABLE IF NOT EXISTS " . TABLE_PREFIX . "categories (
	catid       INT UNSIGNED NOT NULL AUTO_INCREMENT,
	title       VARCHAR(100) NOT NULL,
	description TEXT NOT NULL,
	keywords    TEXT NOT NULL,
	PRIMARY KEY (catid)
) ENGINE=MyISAM
";
$schema['CREATE']['explain']['categories'] = 'Table <strong>' . TABLE_PREFIX . 'categories</strong> created';

$schema['CREATE']['query']['dom2cat'] = "
CREATE TABLE IF NOT EXISTS " . TABLE_PREFIX . "dom2cat (
	id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
	catid    INT UNSIGNED NOT NULL,
	domainid INT UNSIGNED NOT NULL,
	PRIMARY KEY (id)
) ENGINE=MyISAM
";
$schema['CREATE']['explain']['dom2cat'] = 'Table <strong>' . TABLE_PREFIX . 'dom2cat</strong> created';

$schema['CREATE']['query']['domains'] = "
CREATE TABLE IF NOT EXISTS " . TABLE_PREFIX . "domains (
	domainid    INT UNSIGNED NOT NULL AUTO_INCREMENT,
	domain      VARCHAR(100) NOT NULL,
	description TEXT NOT NULL,
	keywords    TEXT NOT NULL,
	registrar   VARCHAR(100) NOT NULL,
	expiry      INT UNSIGNED NOT NULL DEFAULT '0',
	price       DECIMAL(10,2) NOT NULL DEFAULT '0.00',
	status      ENUM('For Sale', 'Not For Sale', 'Make Offer', 'Pending Sale', 'Sold') NOT NULL,
	added       INT UNSIGNED NOT NULL DEFAULT '0',
	issite      TINYINT UNSIGNED NOT NULL DEFAULT '0',
	hidden      TINYINT UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (domainid),
	KEY hidden (hidden)
) ENGINE=MyISAM
";
$schema['CREATE']['explain']['domains'] = 'Table <strong>' . TABLE_PREFIX . 'domains</strong> created';

$schema['CREATE']['query']['paypal_log'] = "
CREATE TABLE IF NOT EXISTS " . TABLE_PREFIX . "paypal_log (
	logid           INT UNSIGNED NOT NULL AUTO_INCREMENT,
	first_name      VARCHAR(100) NOT NULL DEFAULT '',
	last_name       VARCHAR(100) NOT NULL DEFAULT '',
	payer_email     VARCHAR(255) NOT NULL DEFAULT '',
	address_country VARCHAR(200) NOT NULL DEFAULT '',
	address_street  VARCHAR(255) NOT NULL DEFAULT '',
	address_city    VARCHAR(200) NOT NULL DEFAULT '',
	address_state   VARCHAR(100) NOT NULL DEFAULT '',
	address_zip     VARCHAR(20) NOT NULL DEFAULT '',
	item_name       VARCHAR(100) NOT NULL DEFAULT '',
	item_number     INT UNSIGNED NOT NULL DEFAULT '0',
	mc_gross        DECIMAL(10,2) NOT NULL DEFAULT '0.00',
	mc_fee          DECIMAL(10,2) NOT NULL DEFAULT '0.00',
	mc_currency     VARCHAR(5) NOT NULL DEFAULT '',
	txn_id          VARCHAR(100) NOT NULL DEFAULT '',
	rawdata         TEXT NOT NULL,
	dateline        INT UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (logid)
) ENGINE=MyISAM
";
$schema['CREATE']['explain']['paypal_log'] = 'Table <strong>' . TABLE_PREFIX . 'paypal_log</strong> created';

// ################################################################################

$schema['INSERT']['query']['config'] = "
	INSERT INTO " . TABLE_PREFIX . "config 
		(name, value)
	VALUES 
		('title'         , '" . $db->prepare($title) . "'),
		('description'   , '" . $db->prepare($description) . "'),
		('keywords'      , '" . $db->prepare($keywords) . "'),
		('maxperpage'    , $perpage),
		('contactemail'  , '" . $db->prepare($email) . "'),
		('currency'      , '" . $db->prepare($currency) . "'),
		('paypal_sandbox', 0),
		('paypal_log'    , 1),
		('paypal_email'  , '')
";
$schema['INSERT']['explain']['config'] = 'Data for <strong>config</strong> entered';

$schema['INSERT']['query']['admin'] = "
	INSERT INTO " . TABLE_PREFIX . "admin 
		(username, password)
	VALUES 
		('" . $db->prepare($username) . "', '" . dpm_hash($password) . "')
";
$schema['INSERT']['explain']['admin'] = 'Data for <strong>admin</strong> entered';

?>