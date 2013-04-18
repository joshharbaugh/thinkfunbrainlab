<?php

require_once('db.php');
$navigator_user_agent = ' ' . strtolower($_SERVER['HTTP_USER_AGENT']);
 
class db_api_player
{	
	private	$last_query = "";
	private $last_error_text = "";
	private	$mysql_error_text = "";
	
	public $idPlayer;
	public $firstName = "";
	public $lastName = "";
	public $last_id = 0;
	
	//Function to sanitize values received from the form. Prevents SQL injection
	public function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	
	public function loginUser(&$dom)
	{
		$userName = "";
		$pwd = "";
		
		if(isset($_POST['userName']) and $_POST['userName'] != "") {$userName = $_POST['userName'];} 
		if(isset($_POST['pwd']) and $_POST['pwd'] != "") {$pwd = $_POST['pwd'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();

		$_username = $this->clean($userName);
		$_pwd = $this->clean($pwd);
		
		$result = $db->query("SELECT firstName, lastName FROM user where userName = '" . $_username . "' and password = '" . $_pwd . "'");
		
		if($db->num_rows($result) == 0)
		{
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		else
		{
			while ($row = mysql_fetch_assoc($result)) 
			{
				$this->idPlayer = $row['idPlayer'];
				$this->firstName = $row['firstName'];
				$this->lastName = $row['lastName'];
			}

//			if($_SERVER['HTTP_USER_AGENT'] == "")
//			header ("Content-Type:text/xml");
//			$xml = $db->MYSQL2XML($result, "ThinkFunBrainLab", "User");
//			print $xml;
		}
		
		$db->close_connection();
		return $result;
	}	
	
	public function getPlayerById(&$dom)
	{
		$_idPlayer = 0;
			
		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} 
	
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getPlayerById(" . $_idPlayer . ");";
		$result = $db->query($query, $recordset);

		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return 200;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "player", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getPlayerList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getPlayerList(" . $_start . ", " . $_count . ");";
		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			$db->close_connection();
			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return 200;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "player", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function addPlayer(&$dom)
	{
		$_userName = "";
		$_pwd = "";
		$_email = "NULL";
		$_idTeam = 0;
			
		if(isset($_POST['userName']) and $_POST['userName'] != "") {$_userName = $_POST['userName'];} 
		if(isset($_POST['pwd']) and $_POST['pwd'] != "") {$_pwd = $_POST['pwd'];} 
		if(isset($_POST['email']) and $_POST['email'] != "") {$_email = $_POST['email'];} 
		if(isset($_POST['idTeam']) and $_POST['idTeam'] != "") {$_idTeam = $_POST['idTeam'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL addPlayer('" . $_userName . "', '" . $_pwd . "', '" . $_email . "', " . $_idTeam . ");";
		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			$db->close_connection();
			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return 200;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "player", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getTeamById(&$dom)
	{
		$_idTeam = 0;
			
		if(isset($_POST['idTeam']) and $_POST['idTeam'] != "") {$_idTeam = $_POST['idTeam'];} 
	
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getTeamById(" . $_idTeam . ");";
		$result = $db->query($query, $recordset);

		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return 200;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "team", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getTeamList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getTeamList(" . $_start . ", " . $_count . ");";
		$result = $db->query($query, $recordset);

		if(	$result != 0)
		{
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			$db->close_connection();
			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return 200;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "team", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getTeamByPlayer(&$dom)
	{
		$_idPlayer = 0;
		$_start = 0;
		$_count = -1;
			
		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} 
		//if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		//if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getTeamByPlayer(" . $_idPlayer . ");";
		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			$db->close_connection();
			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return 200;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "team", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function addTeam(&$dom)
	{
		$_teamName = "";
		$_idLeague = 0;
		$_idCountry = 0;
			
		if(isset($_POST['teamName']) and $_POST['teamName'] != "") {$_teamName = $_POST['teamName'];} 
		if(isset($_POST['idLeague']) and $_POST['idLeague'] != "") {$_idLeague = $_POST['idLeague'];} 
		if(isset($_POST['idCountry']) and $_POST['idCountry'] != "") {$_idCountry = $_POST['idCountry'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL addTeam('" . $_teamName . "', " . $_idCountry . ", " . $_idLeague . ");";
		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			$db->close_connection();
			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return 200;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "team", $dom);
		$db->close_connection();
		return $result;
	}

	public function getLeagueById(&$dom)
	{
		$_idLeague = 0;
			
		if(isset($_POST['idLeague']) and $_POST['idLeague'] != "") {$_idLeague = $_POST['idLeague'];} 
	
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getLeagueById(" . $_idLeague . ");";
		$result = $db->query($query, $recordset);

		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return 200;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "league", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getLeagueList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getLeagueList(" . $_start . ", " . $_count . ");";
		$result = $db->query($query, $recordset);

		if(	$result != 0)
		{
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			$db->close_connection();
			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return 200;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "league", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function addLeague(&$dom)
	{
		$_leagueName = "";
		$_idLeague = 0;
		$_idCountry = 0;
			
		if(isset($_POST['leagueName']) and $_POST['leagueName'] != "") {$_leagueName = $_POST['leagueName'];} 
		if(isset($_POST['idCountry']) and $_POST['idCountry'] != "") {$_idCountry = $_POST['idCountry'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL addLeague('" . $_leagueName . "', " . $_idCountry . ");";
		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			$db->close_connection();
			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return 200;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "league", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function displayTableData($table, $format="html")
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();

		$result = $db->query("SELECT * FROM {$table}");
		
		if($db->num_rows($result) == 0)
		{
			header ("Content-Type:text/html");
			print "No records found.";
		}
		elseif($format == "xml")
		{
			header ("Content-Type:text/xml");
			$xml = $db->MYSQL2XML2($result, "ThinkFunBrainLab", $table);
			print $xml;
		}
		elseif($format == "html")
		{
			header ("Content-Type:text/html");
			print "<HTML>\n";
			print "<BODY>\n";
				$html = $db->MYSQL2HTML($result, "ThinkFunBrainLab", $table);
				print $html;
			print "</BODY>\n";
			print "</HTML>\n";
		}
		
		$db->close_connection();
	}
	
	public function getLastDatabaseError()
	{
		return $this->last_error_text;
	}
	
	public function getLastMysqlError()
	{
		return $this->mysql_error_text;
	}
	
	public function getTableList()
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();

		$result = $db->query("show tables");
		
		$db->fetch_array($result);
		$db->close_connection();
		return $arr;
	}
}
?>