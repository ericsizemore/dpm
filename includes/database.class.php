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
* @file      ./includes/database.class.php
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
* Class to interact with a mysql database.
*/
class db_mysql
{
	/**
	* Class instance.
	*
	* @var object
	*/
	private static $instance;

	/**
	* Connection to MySQL.
	*
	* @var string
	*/
	protected $link;

	/**
	* Holds the most recent connection.
	*
	* @var string
	*/
	protected $recent_link = NULL;

	/**
	* Holds the contents of the most recent SQL query.
	*
	* @var string
	*/
	protected $sql = '';

	/**
	* The text of the most recent database error message.
	*
	* @var string
	*/
	protected $error = '';

	/**
	* The error number of the most recent database error message.
	*
	* @var integer
	*/
	public $errno = '';

	/**
	* We set this outside of the class. If set to true, the error message/sql is displayed.
	*
	* @var boolean
	*/
	public $is_admin = false;

	/**
	* Database host.
	*/
	protected static $db_host;

	/**
	* Database username.
	*/
	protected static $db_user;

	/**
	* Database password.
	*/
	protected static $db_pass;

	/**
	* Database name.
	*/
	protected static $db_name;

	/**
	* Constructor. Initializes a database connection and selects our database.
	*
	* @param  string   $db_host  Database host
	* @param  string   $db_user  Database username
	* @param  string   $db_pass  Database password
	* @param  string   $db_name  Database name
	* @return boolean            Connection resource, if database connection is established.
	*/
	private function __construct()
	{
		self::set_params();

		$this->link = @mysql_connect(self::$db_host, self::$db_user, self::$db_pass);

		if (is_resource($this->link) AND @mysql_select_db(self::$db_name, $this->link))
		{
			$this->recent_link =& $this->link;
			return $this->link;
		}
		else
		{
			// If we couldn't connect or select the db...
			$this->raise_error('db_mysql::__construct() - Could not select and/or connect to database: ' . self::$db_name);
		}
	}

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
	*/
	private function __clone() {}

	/**
	* Sets connection/database parameters.
	*
	* @param  void
	* @return void
	*/
	protected static function set_params()
	{
		global $config;

		self::$db_host = $config->get('dbhost');
		self::$db_user = $config->get('dbuser');
		self::$db_pass = $config->get('dbpass');
		self::$db_name = $config->get('dbname');
	}

	/**
	* Executes a sql query. If optional $only_first is set to true, it will
	* return the first row of the result as an array.
	*
	* @param  string   $sql         Query to run
	* @param  boolean  $only_first  Return only the first row, as an array?
	* @return mixed                 Result resource, or first row array if $only_first is true
	*/
	public function query($sql, $only_first = false)
	{
		$this->recent_link =& $this->link;
		$this->sql =& $sql;
		$result = @mysql_query($sql, $this->link);

		if ($only_first)
		{
			$return = $this->fetch_array($result);
			$this->free_result($result);
			return $return;
		}
		return $result;
	}

	/**
	* Fetches a row from a query result and returns the values from that row as an array.
	*
	* @param  string  $result  The query result we are dealing with.
	* @return array            Fetched row as array.
	*/
	public function fetch_array($result)
	{
		return @mysql_fetch_assoc($result);
	}

	/**
	* Will fetch all records from the database, and will optionally return the
	* value of a single field from all records.
	*
	* @param  string  $sql    SQL Query string
	* @param  string  $field  Field/column
	* @return array           Will return array of all db records.
	*/
	public function fetch_all($sql, $field = '')
	{
		$return = array();

		if (($result = $this->query($sql)))
		{
			while ($row = $this->fetch_array($result))
			{
				$return[] = ($field) ? $row[$field] : $row;
			}
			$this->free_result($result);
		}
		return $return;
	}

	/**
	* Returns the number of rows in a result set.
	*
	* @param  string   $result  The query result we are dealing with.
	* @return integer           Number of rows.
	*/
	public function num_rows($result)
	{
		return @mysql_num_rows($result);
	}

	/**
	* Escapes a value to make it safe for using in queries.
	*
	* @param  string   $value    Value to be escaped
	* @param  boolean  $do_like  Do we need to escape this string for a LIKE statement?
	* @return string             Escaped value.
	*/
	public function prepare($value, $do_like = false)
	{
		$value = stripslashes($value);

		if ($do_like)
		{
			$value = str_replace(array('%', '_'), array('\%', '\_'), $value);
		}
		return @mysql_real_escape_string($value, $this->link);
	}

	/**
	* Frees memory associated with a query result.
	*
	* @param  string   $result  The query result we are dealing with.
	* @return boolean
	*/
	public function free_result($result)
	{
		return @mysql_free_result($result);
	}

	/**
	* Returns the auto generated id used in the last query
	*
	* @param  void
	* @return integer
	*/
	public function insert_id()
	{
		return @mysql_insert_id($this->link);
	}

	/**
	* Closes our connection to MySQL.
	*
	* @param  void
	* @return boolean
	*/
	public function close()
	{
		$this->sql = '';
		return @mysql_close($this->link);
	}

	/**
	* Returns the MySQL error message.
	*
	* @param  void
	* @return string  Error message.
	*/
	public function error()
	{
		$this->error = (is_null($this->recent_link)) ? '' : @mysql_error($this->recent_link);
		return $this->error;
	}

	/**
	* Returns the MySQL error number.
	*
	* @param  void
	* @return integer  Error number.
	*/
	public function errno()
	{
		$this->errno = (is_null($this->recent_link)) ? 0 : @mysql_errno($this->recent_link);
		return $this->errno;
	}

	/**
	* Gets the url/path of where we are when a MySQL error occurs.
	*
	* @param  void
	* @return string  URL/path to the file which had the error.
	*/
	final protected function get_error_path()
	{
		if (dpm_getenv('REQUEST_URI'))
		{
			$errorpath = dpm_getenv('REQUEST_URI');
		}
		else
		{
			if (dpm_getenv('PATH_INFO'))
			{
				$errorpath = dpm_getenv('PATH_INFO');
			}
			else
			{
				$errorpath = dpm_getenv('PHP_SELF');
			}

			if (dpm_getenv('QUERY_STRING'))
			{
				$errorpath .= '?' . dpm_getenv('QUERY_STRING');
			}
		}

		if (($pos = strpos($errorpath, '?')) !== false)
		{
			$errorpath = urldecode(substr($errorpath, 0, $pos)) . substr($errorpath, $pos);
		}
		else
		{
			$errorpath = urldecode($errorpath);
		}
		return 'http://' . HOST . sanitize($errorpath);
	}

	/**
	* If there is a database error, the script will be stopped and an error message displayed.
	*
	* @param  string  $errormsg  The error message. If empty, one will be built with $this->sql.
	* @return string             Fully formatted error message.
	*/
	final public function raise_error($errormsg = '')
	{
		global $config;

		if ($this->recent_link)
		{
			$this->error = $this->error($this->recent_link);
			$this->errno = $this->errno($this->recent_link);
		}
		else
		{
			$this->error = $this->error($this->link);
			$this->errno = $this->errno($this->link);
		}

		if ($errormsg == '')
		{
			$this->sql = "Error in SQL query:\n\n" . rtrim($this->sql) . ';';
			$errormsg =& $this->sql;
		}
		else
		{
			$errormsg = $errormsg . ($this->sql != '' ? "\n\nSQL:" . rtrim($this->sql) . ';' : '');
		}

		$message = htmlspecialchars("$errormsg\n\nError: {$this->error}\nError Number: {$this->errno}\nFilename: " . $this->get_error_path());
		$message = '<code>' . nl2br($message) . '</code>';

		if (!$this->is_admin)
		{
			$message = "<!--\n\n$message\n\n-->";
		}

		// Set the correct path to the error template
		$path = (defined('IN_ADMIN') OR defined('IN_INSTALL')) ? '..' : '.';

		$title = ($config->get('title') == '') ? 'Domain Portfolio Manager' : $config->get('title');
		$dbemail = encode_email($config->get('dbemail'));

		eval('$output = "' . addslashes(file_get_contents("$path/templates/dberror.tpl")) . '";');

		echo $output;
		exit;
	}
}

/**
* Class to interact with a mysql database, using MySQL Improved.
*/
class db_mysqli extends db_mysql
{
	/**
	* Class instance, start of implementing Singleton pattern.
	*
	* @var object
	*/
	private static $instance;

	/**
	* Constructor. Initializes a database connection and selects our database.
	*
	* @param  string    $db_host     Database host
	* @param  string    $db_user     Database username
	* @param  string    $db_pass     Database password
	* @param  string    $db_name     Database name
	* @param  string    $configfile  Config file (my.ini / my.cnf)
	* @return resource               Connection resource, if database connection is established.
	*/
	private function __construct()
	{
		parent::set_params();

		$this->link = @mysqli_init();

		$connect = @mysqli_real_connect($this->link, parent::$db_host, parent::$db_user, parent::$db_pass, parent::$db_name);

		if ($connect AND @mysqli_select_db($this->link, parent::$db_name))
		{
			$this->recent_link =& $this->link;
			return $this->link;
		}
		else
		{
			// If we couldn't connect or select the db...
			$this->raise_error('db_mysqli::__construct() - Could not select and/or connect to database: ' . parent::$db_name);
		}
	}

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
	*/
	private function __clone() {}

	/**
	* Executes a sql query. If optional $only_first is set to true, it will
	* return the first row of the result as an array.
	*
	* @param  string   $sql         Query to run
	* @param  boolean  $only_first  Return only the first row, as an array?
	* @return mixed                 Result resource, or first row array if $only_first is true
	*/
	public function query($sql, $only_first = false)
	{
		$this->recent_link =& $this->link;
		$this->sql =& $sql;
		$result = @mysqli_query($this->link, $sql);

		if ($only_first)
		{
			$return = $this->fetch_array($result);
			$this->free_result($result);
			return $return;
		}
		return $result;
	}

	/**
	* Fetches a row from a query result and returns the values from that row as an array.
	*
	* @param  string  $result  The query result we are dealing with.
	* @return array            Fetched row as array.
	*/
	public function fetch_array($result)
	{
		return @mysqli_fetch_assoc($result);
	}

	/**
	* Will fetch all records from the database, and will optionally return the
	* value of a single field from all records.
	*
	* @param  string  $sql    SQL Query string
	* @param  string  $field  Field/column
	* @return array           Will return array of all db records.
	*/
	public function fetch_all($sql, $field = '')
	{
		$return = array();

		if (($result = $this->query($sql)))
		{
			while ($row = $this->fetch_array($result))
			{
				$return[] = ($field) ? $row[$field] : $row;
			}
			$this->free_result($result);
		}
		return $return;
	}

	/**
	* Returns the number of rows in a result set.
	*
	* @param  string   $result  The query result we are dealing with.
	* @return integer           Number of rows.
	*/
	public function num_rows($result)
	{
		return @mysqli_num_rows($result);
	}

	/**
	* Escapes a value to make it safe for using in queries.
	*
	* @param  string   $value    Value to be escaped
	* @param  boolean  $do_like  Do we need to escape this string for a LIKE statement?
	* @return string             Escaped value.
	*/
	public function prepare($value, $do_like = false)
	{
		$value = stripslashes($value);

		if ($do_like)
		{
			$value = str_replace(array('%', '_'), array('\%', '\_'), $value);
		}
		return @mysqli_real_escape_string($this->link, $value);
	}

	/**
	* Frees memory associated with a query result.
	*
	* @param  string  $result  The query result we are dealing with.
	* @return void
	*/
	public function free_result($result)
	{
		return @mysqli_free_result($result);
	}

	/**
	* Returns the auto generated id used in the last query
	*
	* @param  void
	* @return integer
	*/
	public function insert_id()
	{
		return @mysqli_insert_id($this->link);
	}

	/**
	* Closes our connection to MySQLi.
	*
	* @param  void
	* @return boolean
	*/
	public function close()
	{
		$this->sql = '';
		return @mysqli_close($this->link);
	}

	/**
	* Returns the MySQLi error message.
	*
	* @param  void
	* @return string  Error message.
	*/
	public function error()
	{
		$this->error = (is_null($this->recent_link)) ? '' : @mysqli_error($this->recent_link);
		return $this->error;
	}

	/**
	* Returns the MySQLi error number.
	*
	* @param  void
	* @return integer  Error number.
	*/
	public function errno()
	{
		$this->errno = (is_null($this->recent_link)) ? 0 : @mysqli_errno($this->recent_link);
		return $this->errno;
	}
}

/**
* Class to backup the database.
*/
class db_backup
{
	/**
	* Class instance.
	*
	* @var object
	*/
	private static $instance;

	/**
	* An array of tables to backup.
	*
	* @var array
	*/
	private $tables;

	/**
	* Database (db_mysql/db_mysqli) object.
	*
	* @var object
	*/
	private $dbobj;

	/**
	* Path to the backup file.
	*
	* @var string
	*/
	public $file;

	/**
	* Contains the error message, if an error occurs.
	*
	* @var string
	*/
	public $error;

	/**
	* Constructor.
	*
	* @param  void
	* @return void
	*/
	private function __construct() {}

	/**
	*/
	private function __clone() {}

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
	* Sets db_mysql/db_mysqli object and $table array
	*
	* @param  object  &$db     Database (db_mysql/db_mysqli) object
	* @param  array   $tables  Array of database tables to backup
	* @return void
	*/
	public function set_params(&$db, $tables)
	{
		if (!is_array($tables))
		{
			die('db_backup::__construct() - $tables must be an array');
		}

		$this->tables = $tables;
		$this->dbobj =& $db;
	}

	/**
	* Gets the table structure.
	*
	* CREATE TABLE ...
	*
	* @param  string  $table  Table to get structure for.
	* @return string          Table structure.
	*/
	private function get_tbl_structure($table)
	{
		$struct = "\n--\n-- Table stucture for table `$table`\n--\n";
		$struct .= "DROP TABLE IF EXISTS `$table`;\n";
		$struct .= "CREATE TABLE `$table` (\n";

		// Fields
		$fields = $this->get_fields($table);

		foreach ($fields AS $key => $value)
		{
			$struct .= "\t`$value[Field]` $value[Type]";
			$struct .= ($value['Null'] != 'YES') ? ' NOT NULL' : '';
			$struct .= ($value['Default'] != '') ? " DEFAULT '$value[Default]'" : '';
			$struct .= ($value['Extra'] != '') ? " $value[Extra]" : '';
			$struct .= ",\n";
		}
		$struct = rtrim($struct, ",\n");

		// Keys
		$keys = $this->get_keys($table);

		foreach ($keys AS $key => $value)
		{
			$kname = $value['Key_name'];

			if (($kname != 'PRIMARY') AND ($value['Non_unique'] == 0))
			{
				$kname = "UNIQUE|$kname";
			}

			if (!isset($index[$kname]))
			{
				$index[$kname] = array();
			}
			$index[$kname][] = "`$value[Column_name]`";
		}

		while (list($x, $columns) = @each($index))
		{
			$struct .= ",\n";

			if ($x == 'PRIMARY')
			{
				$struct .= "\tPRIMARY KEY (" . implode($columns, ', ') . ')';
			}
			else if (substr($x, 0, 6) == 'UNIQUE')
			{
				$struct .= "\tUNIQUE `" . substr($x, 7) . '` (' . implode($columns, ', ') . ')';
			}
			else
			{
				$struct .= "\tKEY `$x` (" . implode($columns, ', ') . ')';
			}
		}

		$struct .= "\n) ENGINE=MyISAM;";

		return clean($struct);
	}

	/**
	* Gets the table data/content.
	*
	* INSERT INTO ... VALUES(...)
	*
	* @param  string  $table  Table to get data/content for.
	* @return string          Table data/content.
	*/
	private function get_tbl_content($table)
	{
		$data = $this->get_data($table);
		$content = "\n--\n-- Dumping data for table `$table`\n--\n";

		foreach ($data AS $key => $value)
		{
			$content .= "INSERT INTO `$table` VALUES (";

			foreach ($value AS $key2 => $value2)
			{
				if (!isset($value[$key2]))
				{
					$content .= 'NULL, ';
				}
				else if ($value[$key2] != '')
				{
					$content .= "'" . $this->dbobj->prepare($value[$key2]) . "', ";
				}
				else
				{
					$content .= "'', ";
				}
			}
			$content = rtrim($content, ', ');
			$content .= ");\n";
		}
		return $content;
	}

	/**
	* Helper function for get_table_content
	*
	* @param  string  $table  Table to get data/content for.
	* @return string          Tabel content.
	*/
	private function get_data($table)
	{
		$ret = array();
		$res = $this->dbobj->query("SELECT * FROM $table");

		while ($temp = $this->dbobj->fetch_array($res))
		{
			array_push($ret, $temp);
		}
		$this->dbobj->free_result($res);

		return $ret;
	}

	/**
	* Helper function for get_table_structure
	*
	* Gets table keys.
	*
	* @param  string  $table  Table to get keys for.
	* @return string          Table keys.
	*/
	private function get_keys($table)
	{
		$ret = array();
		$res = $this->dbobj->query("SHOW KEYS FROM $table");

		while ($temp = $this->dbobj->fetch_array($res))
		{
			array_push($ret, $temp);
		}
		$this->dbobj->free_result($res);

		return $ret;
	}

	/**
	* Helper function for _get_table_structure
	*
	* Gets table fields.
	*
	* @param  string  $table  Table to get fields for.
	* @return string          Table fields.
	*/
	private function get_fields($table)
	{
		$ret = array();
		$res = $this->dbobj->query("SHOW FIELDS FROM $table");

		while ($temp = $this->dbobj->fetch_array($res))
		{
			array_push($ret, $temp);
		}
		$this->dbobj->free_result($res);

		return $ret;
	}

	/**
	* Performs the actual backup of the database.
	*
	* @param  void
	* @return integer  Number > 0 if successful, 0 otherwise. 
	*/
	public function do_backup()
	{
		global $version;

		if (!is_writable('backups'))
		{
			$this->error = './admin/backups not writable';
			return false;
		}

		$this->file = "backups/dpm_backup_" . time() . ".sql";

		$backup = "--\n-- Domain Portfolio Manager Backup\n";
		$backup .= "-- DPM Version: $version\n";
		$backup .= "--\n-- DATE : " .  gmdate('D, d M Y H:i:s') . " GMT\n";
		$backup .= "--\n-- -------------------------------------------\n\n";

		foreach ($this->tables AS $table)
		{
			$backup .= $this->get_tbl_structure($table) . "\n\n" . $this->get_tbl_content($table) . "\n\n";
		}

		@touch($this->file);

		return (file_put_contents($this->file, $backup));
	}
}

/**
* Class to restore a database backup.
*/
class db_restore
{
	/**
	* Class instance.
	*
	* @var object
	*/
	private static $instance;

	/**
	* If an error occurs, this will tell us what it is.
	*
	* @var string
	*/
	public $error;

	/**
	* Database (db_mysql/db_mysqli) object.
	*
	* @var object
	*/
	private $dbobj;

	/**
	* Constructor.
	*
	* @param  void
	* @return void
	*/
	private function __construct() {}

	/**
	*/
	private function __clone() {}

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
	* Sets database ojbect.
	*
	* @param  object  &$db  Database (db_mysql/db_mysqli) object
	* @return void
	*/
	public function set_params(&$db)
	{
		$this->dbobj = &$db;
	}

	/**
	* Strips SQL comments out of an uploaded SQL file.
	*
	* Based on phpBB2's "remove_remarks" function.
	*
	* @param  string  $sql  SQL content
	* @return string        Uncommented SQL
	*/
	private function remove_comments($sql)
	{
		$lines = preg_split("#\n#", $sql, -1, PREG_SPLIT_NO_EMPTY);

		$sql = '';

		$linecount = count($lines);

		$output = '';

		for ($i = 0; $i < $linecount; $i++)
		{
			if (($i != ($linecount - 1)) OR (strlen($lines[$i]) > 0))
			{
				$output .= ($lines[$i][0] != '#') ? $lines[$i] . "\n" : "\n";

				// Trading a bit of speed for lower mem. use here.
				$lines[$i] = '';
			}
		}
		return $output;
	}

	/**
	* Splits SQL file into single SQL statements.
	*
	* Based on phpBB2's "split_sql_file" function.
	*
	* @param  string  $sql        SQL content
	* @param  string  $delimiter  Delimter the SQL commands are separated by.
	* @return string              Split SQL
	*/
	private function split_sql($sql, $delimiter)
	{
		$tokens = preg_split("#$delimiter#", $sql, -1, PREG_SPLIT_NO_EMPTY);

		$sql = '';

		$output = array();
		$matches = array();

		$token_count = count($tokens);

		for ($i = 0; $i < $token_count; $i++)
		{
			if (($i != ($token_count - 1)) OR (strlen($tokens[$i] > 0)))
			{
				$total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
				$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
				$unescaped_quotes = $total_quotes - $escaped_quotes;

				if (($unescaped_quotes % 2) == 0)
				{
					array_push($output, $tokens[$i]);

					// save memory.
					$tokens[$i] = '';
				}
				else
				{
					$temp = $tokens[$i] . $delimiter;

					// save memory.
					$tokens[$i] = '';

					// Do we have a complete statement yet?
					$complete_stmt = false;

					for ($j = $i + 1; (!$complete_stmt AND ($j < $token_count)); $j++)
					{
						$total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
						$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
						$unescaped_quotes = $total_quotes - $escaped_quotes;

						if (($unescaped_quotes % 2) == 1)
						{
							array_push($output, $temp . $tokens[$j]);

							// save memory.
							$tokens[$j] = '';
							$temp = '';

							$complete_stmt = true;
							$i = $j;
						}
						else
						{
							$temp .= $tokens[$j] . $delimiter;

							// save memory.
							$tokens[$j] = '';
						}
					}
				}
			}
		}
		return $output;
	}

	/**
	* Performs the actual restoration.
	*
	* @param  string   $file  Path to the backup file on the server.
	* @return boolean         true if restore is successful, false if not.
	*/
	public function do_restore($file)
	{
		$this->error = '';

		if (!file_exists($file))
		{
			$this->error = "The SQL backup file doesn't appear to exist at this location:<br /><code>$file</code>\n";
			return false;
		}

		$sql = file_get_contents($file);

		if (empty($sql))
		{
			$this->error = "The SQL backup file appears to be empty? Please double check the file at:<br /><code>$file</code>\n";
			return false;
		}

		$sql = $this->remove_comments($sql);
		$sql = $this->split_sql($sql, ';');

		$count = count($sql);

		for ($i = 0; $i < $count; $i++)
		{
			$tmp_sql = trim($sql[$i]);

			if (!empty($tmp_sql) AND $tmp_sql[0] != '#')
			{
				if (!$this->dbobj->query($tmp_sql))
				{
					$this->error .= $this->dbobj->error;
				}
			}
		}

		if (empty($this->error))
		{
			return true;
		}
		return false;
	}
}
