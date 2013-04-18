<?php

//Include database connection details
require_once('db_config.php');
require_once('errormgr.php');
error_reporting(E_ALL);
//ini_set ('display_errors', '1'); //PHP 5 and >

define("DEBUG", 1);

class mysql_db
{
	private $_connection;
	private $_xml = "";
	private $_html = "";
	private	$last_query = "";
	private	$last_error_text = "";
	private	$mysql_error_text = "";
	private $magic_quotes_active = false;
	private $real_escape_string_exists = false;
	
	public function __construct()
	{
		$this->open_connection();
		/* Returns the current configuration setting of magic_quotes_gpc.
		Returns 0 if magic_quotes_gpc is off, 1 otherwise. */
		$this->magic_quotes_active = get_magic_quotes_gpc();
		$this->real_escape_string_exists = function_exists("mysql_real_escape_string");
	}

	public function open_connection()
	{
		$this->_connection = mysql_connect(DB_SERVER,DB_USER,DB_PASS);
		if(!$this->_connection)
		{
			$this->last_error_text = "Database connection failed";
			$this->mysql_error_text = mysql_error();
			return ErrorMgr::ERR_DB_CONNECT;
			//die('Database connection failed: '.mysql_error());
		}
		else
		{
			$db_select = mysql_select_db(DB_NAME,$this->_connection);
			if(!$db_select)
			{
				$this->last_error_text = "Database selection failed";
				$this->mysql_error_text = mysql_error();
				close_connection();
				return ErrorMgr::ERR_DB_SELECT;
				//die('Database selection failed: '.mysql_error());
			}
		}
	}

	public function close_connection()
	{
		if(isset($this->_connection))
		{
			mysql_close($this->_connection);
			unset($this->_connection);
		}
	}

	/* For SELECT returns resultset on success, false on error.
	* For INSERT, UPDATE, DELETE etc returns true on success, false on error */
	public function query($sql, &$result)
	{
		$this->last_query = $sql;

		$result = mysql_query($sql,$this->_connection);
		return $this->confirm_query($result);
	}

	/* Make sure query executed successfully.
	* Only for debugging purposes. */
	private function confirm_query($result)
	{

		if(!$result)
		{
			$this->last_error_text = "Database query failed";
			$this->mysql_error_text = mysql_error();
			return ErrorMgr::ERR_DB_QUERY;
		}
		
		return 0;
	}
	
	public function get_mysql_error()
	{
		return  $this->mysql_error_text;
	}

	public function get_last_error_code()
	{
		return $this->last_error_code;
	}

	public function get_last_error_text()
	{
		return  $this->last_error_text;
	}

	/* Escape special characters in the string before sending to the database
	* Consider PHP Version*/
	public function escape_value($value)
	{
		if($this->real_escape_string_exists)// PHP version 4.3.0 or higher
		{

			if($this->magic_quotes_active)
			{
				/* Automatically add back slashes when magic quotes are active in php.ini
				* First remove them. */
				$value = stripslashes($value);
			}
			//Escapes special characters in a string
			$value = mysql_real_escape_string($value);
		}
		else //before PHP version 4.3.0
		{
			if(!$this->magic_quotes_active)
			{
				// Add slashes manually
				$value = addslashes($value);
			}
		}

		return $value;
	}

	/* Returns an array of strings that corresponds to the fetched row.
	* Returns FALSE if there are no more rows.*/
	public function fetch_array($result_set)
	{
		return mysql_fetch_array($result_set);
	}
	
	public function fetch_assoc($result_set)
	{
		return mysql_fetch_assoc($result_set);
	}

	/* Retrives the number of rows from the result set.
	* Returns FALSE on failure. */
	public function num_rows($result_set)
	{
		return mysql_num_rows($result_set);
	}

	/* Get the ID generated in the last query
	* 0 if the previous query does not generate an AUTO_INCREMENT value.
	* FALSE if no MySQL connection was established.&nbsp; */
	public function insert_id()
	{
		return mysql_insert_id($this->_connection);
	}

	/* Get number of affected rows in previous MySQL operation.
	* Return -1 if the last query failed.&nbsp; */
	public function affected_rows()
	{
		return mysql_affected_rows($this->_connection);
	}
	
	public function MYSQL2HTML(&$result, $containerName="container", $elemTableName="element", $encoding="UTF-8")
	{
		//this functions creates HTML output from the SQL result.
	   
		$fieldCount = mysql_num_fields($result);
		
		$_html =<<<EOT
<TABLE id="{$containerName}" border="1" cellpadding="12">\n<TR>
EOT;
		for($i = 0; $i < $fieldCount; $i++) 
		{
			$field_info = mysql_fetch_field($result, $i);
			$_html .=<<<EOT
\n\t<TH>{$field_info->name}</TH>
EOT;
		}

		$_html .=<<<EOT
\n</TR>
EOT;
		while ($row = mysql_fetch_assoc($result)) 
		{
			$i = 0;
			$_html .=<<<EOT
\n<TR>
EOT;
			foreach($row as $value) 
			{
				$value = htmlspecialchars($value);
				if(is_null($value) or $value == "")
				{
					$value = "&nbsp;";
				}
				$_html .=<<<EOT
\n\t<TD>{$value}</TD>
EOT;
				$i++;
			}
			$_html .= <<<EOT
\n\t</TR>
EOT;
		}
		
		$_html .= <<<EOT
</TABLE>\n
EOT;

		//print $_xml;
		return $_html;
	}
	
	public function MYSQL2XML(&$result, $containerName="container", $elementName="element", $encoding="UTF-8")
	{
		//this function creates XML output from the SQL result.
	   
		$fieldCount = mysql_num_fields($result);
		
		for($i = 0; $i < $fieldCount; $i++) 
		{
			$field_info = mysql_fetch_field($result, $i);
			$fieldNames[$i] = $field_info->name;
			//print "{$fieldNames[$i]} ";
		}
		$_xml =<<<EOT
<?xml version="1.0" encoding="utf-8"?>\n<{$containerName} xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
EOT;
		while ($row = mysql_fetch_assoc($result)) 
		{
			$i = 0;
			$_xml .=<<<EOT
\n<{$elementName}>
EOT;
			foreach($row as $value) 
			{
				$value = htmlspecialchars($value);
				$_xml .=<<<EOT
\n\t<{$fieldNames[$i]}>{$value}</{$fieldNames[$i]}>
EOT;
				$i++;
			}
			$_xml .=<<<EOT
\n</{$elementName}>
EOT;
		}
		
		$_xml .=<<<EOT
\n</{$containerName}>
EOT;

		//print $_xml;
		return $_xml;
	}
	public function MYSQL2XML2(&$result, $containerName="container", $elementName="element", $encoding="UTF-8")
	{
		//this function creates XML output from the SQL result.
	    $doc = new DOMDocument();
		$doc->formatOutput = true;
		  
		$elemRoot = $doc->createElement( $containerName );
		$doc->appendChild( $elemRoot );
		  
		$fieldCount = mysql_num_fields($result);
		
		for($i = 0; $i < $fieldCount; $i++) 
		{
			$field_info = mysql_fetch_field($result, $i);
			$fieldNames[$i] = $field_info->name;
			//print "{$fieldNames[$i]} ";
		}

		while ($row = mysql_fetch_assoc($result)) 
		{
			$i = 0;
			$elemTable = $doc->createElement( $elementName );
			
			foreach($row as $value) 
			{
				$value = htmlspecialchars($value);
				$elemField = $doc->createElement($fieldNames[$i]);
				$elemField->appendChild( $doc->createTextNode($value) );
				$elemTable->appendChild($elemField);
				$i++;
			}
			$elemRoot->appendChild($elemTable);	
		}
		
		//print $doc->saveXML();
		return $doc->saveXML();
	}
	
	public function MYSQL2XML3($result, $tableName, &$dom)
	{

		$fieldCount = mysql_num_fields($result);
		
		if($fieldCount == 0) {return ErrorMgr::ERR_DB_XML_TRANSFORM;}
		
		for($i = 0; $i < $fieldCount; $i++) 
		{
			$field_info = mysql_fetch_field($result, $i);
			$fieldNames[$i] = $field_info->name;
			//print "{$fieldNames[$i]} ";
		}
		
		$nodes = $dom->getElementsByTagName('data');
		$nodeData = null;
		
		foreach ($nodes as $node) {
			$nodeData = $node;
		}
		while ($row = mysql_fetch_assoc($result)) 
		{
			$i = 0;
			$nodeTable = $dom->createElement( $tableName );
			
			foreach($row as $value) 
			{
				$value = htmlspecialchars($value);
				$nodeField = $dom->createElement($fieldNames[$i]);
				$nodeField->appendChild( $dom->createTextNode($value) );
				$nodeTable->appendChild($nodeField);
				$i++;
			}
			$nodeData->appendChild($nodeTable);	
		}

		//print $doc->saveXML();
		return ErrorMgr::ERR_SUCCESS ;
	}
	
	public function getTableList()
	{
	//$arr = $db->fetch_array($result);
	//$arr = (mysql_fetch_assoc($result));
	}

	public function getInvitationEmailbyInviter($idPlayer, &$firstName, &$lastName, &$idTeam, &$teamName, &$idLeague, &$leagueName, &$idInviteeRole, &$emailSubject, &$emailHTML, &$emailText) {
	
		$result = ErrorMgr::ERR_SUCCESS;
		$recordset = NULL;

		$query = "CALL getInvitationEmailbyInviter(" . $idPlayer . ");";
		
		$result = $this->query($query, $recordset);
		
		if ($result != ErrorMgr::ERR_SUCCESS) {
			$this->close_connection();
			return $result;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		while ($row = mysql_fetch_assoc($recordset)) 
		{
			$firstName = $row[firstName];
			$lastName = $row[lastName];
			$idTeam = $row[idTeam];
			$teamName = $row[teamName];
			$idLeague = $row[idLeague];
			$leagueName = $row[leagueName];
			$idInviteeRole = $row[idInviteeRole];
		}

		$this->close_connection();
		return $result;
	}
	
	public function getAccountReminderEmail($email, $securityCode, &$idPlayer, &$userName, &$firstName, &$lastName, &$emailSubject, &$emailHTML, &$emailText) {
	
		$result = ErrorMgr::ERR_SUCCESS;
		$recordset = NULL;

		$query = "CALL getAccountReminderByEmail('" . $email. "', '" . $securityCode . "');";
		//print $query;
		//die();
		
		$result = $this->query($query, $recordset);
		
		if ($result != ErrorMgr::ERR_SUCCESS) {
			$this->close_connection();
			return $result;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		while ($row = mysql_fetch_assoc($recordset)) 
		{
			$idPlayer = $row[idPlayer];
			$userName = $row[userName];
			$firstName = $row[firstName];
			$lastName = $row[lastName];
		}

		$this->close_connection();
		return $result;
	}
	
	public function getTeamManagerByPlayerId($idPlayer, &$idManager)
	{
		$result = ErrorMgr::ERR_SUCCESS;
		$recordset = NULL;

		$query = "CALL getTeamManagerByPlayerId(" . $idPlayer . ");";
		
		$result = $this->query($query, $recordset);
		
		if ($result != ErrorMgr::ERR_SUCCESS) {
			$this->close_connection();
			return $result;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		while ($row = mysql_fetch_assoc($recordset)) 
		{
			$idManager = $row[idTeamManager];
		}

		$this->close_connection();
		return $result;
	}
	
	public function getLeagueManagerByTeamManagerId($idTeamManager, &$idLeagueManager)
	{
		$result = ErrorMgr::ERR_SUCCESS;
		$recordset = NULL;

		$query = "CALL getTeamManagerByPlayerId(" . $idTeamManager . ");";
		
		$result = $this->query($query, $recordset);
		
		if ($result != ErrorMgr::ERR_SUCCESS) {
			$this->close_connection();
			return $result;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		while ($row = mysql_fetch_assoc($recordset)) 
		{
			$idManager = $row[idLeagueManager];
		}

		$this->close_connection();
		return $result;
	}
	
	public function logActivity ($IP, $API_VER, $API_LIBRARY, $API_METHOD, $text = NULL)
	{
		$result = ErrorMgr::ERR_SUCCESS;
		$recordset = NULL;

		$query = "INSERT INTO logactivity (IP, apiVersion, apiLibrary, apiMethod, ffText, dtLog) values ('" . $IP . "','" . $API_VER . "','" . $API_LIBRARY . "','" . $API_METHOD . "','" . $text . "','" . date("Y-m-d H:i:s") . "')"; 
		//		print $query;
		//		die();
		$result = $this->query($query, $recordset);
		$this->close_connection();
		return $result;	
	}

}
?>