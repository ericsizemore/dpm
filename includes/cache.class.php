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

if (!defined('IN_DPM'))
{
	die('You\'re not supposed to be here.');
}

/*
$cache = dpm_cache::getInstance();
$cache->setPath('/tmp');

$data = $cache->get('key');

if ($data === false)
{
    $data = 'This will be cached';
    $cache->set('key', $data);
}
*/

/**
* Got the idea for this class from Jonathan Gales:
* http://www.jongales.com/blog/2009/02/18/simple-file-based-php-cache-class/
*
* The basic idea (and some code) came from him, although 
* obviously it's been modified.
*/
class dpm_cache
{
	/**
	* Class instance.
	*
	* @var object
	*/
	private static $instance;

	/**
	* This will be the directory where cache files are stored.
	*
	* @var array
	*/
	private $dir;

	/**
	* Constructor.
	*
	* @param  void
	* @return void
	*/
	private function __construct() {}

	/**
	* Creates an instance of the class.
	*
	* @param  void
	* @return object
	*/
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	* Sets the path where cache files will be saved.
	*
	* @param  string  Path to cache storage
	* @return void
	*/
	public function setPath($dir)
	{
		$this->dir = $dir;
	}

	/**
	* Sets the filename (and path) to be used for the cache file.
	*
	* @param  string  Value to be cached (ran through dpm_hash, which returns md5)
	* @return string
	*/
	private function setFile($key)
	{
		return sprintf('%s/%s', $this->dir, dpm_hash($key));
	}

	/**
	* Retrieves cached item.
	*
	* @param  string   Cache key
	* @param  integer  Expiration, in seconds
	* @return mixed
	*/
	public function get($key, $expiration = 3600)
	{
		if (!is_dir($this->dir) OR !is_writable($this->dir))
		{
			return false;
		}

		$cache_path = $this->setFile($key);

		if (!@file_exists($cache_path))
		{
			return false;
		}

		if (filemtime($cache_path) < (time() - $expiration))
		{
			$this->clear($key);
			return false;
		}

		if (!$fp = @fopen($cache_path, 'rb'))
		{
			return false;
		}

		flock($fp, LOCK_SH);
		$cache = (filesize($cache_path) > 0) ? unserialize(fread($fp, filesize($cache_path))) : NULL;
		flock($fp, LOCK_UN);

		fclose($fp);

		return $cache;
	}

	/**
	* Add an item to cache.
	*
	* @param  string   Cache key
	* @param  mixed    Cache data
	* @return boolean
	*/
	public function set($key, $data)
	{
		if (!is_dir($this->dir) OR !is_writable($this->dir))
		{
			return false;
		}

		$cache_path = $this->setFile($key);

		if (!$fp = @fopen($cache_path, 'wb'))
		{
			return false;
		}

		if (flock($fp, LOCK_EX))
		{
			fwrite($fp, serialize($data));
			flock($fp, LOCK_UN);
		}
		else
		{
			return false;
		}

		fclose($fp);
		@chmod($cache_path, 0777);

		return true;
	}

	/**
	* Clears/resets cached item. 
	*
	* @param  string   Cache key
	* @return boolean
	*/
	public function clear($key)
	{
		$cache_path = $this->setFile($key);

		if (file_exists($cache_path))
		{
			@unlink($cache_path);
			return true;
		}
		return false;
	}
}
