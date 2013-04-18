<?php

require_once('db.php');
$navigator_user_agent = ' ' . strtolower($_SERVER['HTTP_USER_AGENT']);
 
class db_api
{
	private $xml = "";
	private $html = "";
	
	public function getUserList($format="html")
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();

		$result = $db->query("SELECT * FROM user");
		
		if($db->num_rows($result) == 0)
		{
			header ("Content-Type:text/html");
			print "No records found.";
		}
		elseif($format == "xml")
		{
			if($_SERVER['HTTP_USER_AGENT'] == "")
			header ("Content-Type:text/xml");
			$xml = $db->MYSQL2XML($result, "ThinkFunProject", "User");
			print $xml;
		}
		elseif($format == "html")
		{
			header ("Content-Type:text/html");
			print "<HTML>\n";
			print "<BODY>\n";
				$html = $db->MYSQL2HTML($result, "ThinkFunProject", "User");
				print $html;
			print "</BODY>\n";
			print "</HTML>\n";
		}
		
		$db->close_connection();
	}	
	
	public function getGameList($format="html")
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();

		$result = $db->query("SELECT * FROM game");
		
		if($db->num_rows($result) == 0)
		{
			header ("Content-Type:text/html");
			print "No records found.";
		}
		elseif($format == "xml")
		{
			header ("Content-Type:text/xml");
			$xml = $db->MYSQL2XML($result, "ThinkFunProject", "Game");
			print $xml;
		}
		elseif($format == "html")
		{
			header ("Content-Type:text/html");
			print "<HTML>\n";
			print "<BODY>\n";
				$html = $db->MYSQL2HTML($result, "ThinkFunProject", "Game");
				print $html;
			print "</BODY>\n";
			print "</HTML>\n";
		}
	}

	public function getGameSkillList($format="html")
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();

		$result = $db->query("SELECT * FROM game_skill");
		
		if($db->num_rows($result) == 0)
		{
			header ("Content-Type:text/html");
			print "No records found.";
		}
		elseif($format == "xml")
		{
			header ("Content-Type:text/xml");
			$xml = $db->MYSQL2XML($result, "ThinkFunProject", "game_skill");
			print $xml;
		}
		elseif($format == "html")
		{
			header ("Content-Type:text/html");
			print "<HTML>\n";
			print "<BODY>\n";
				$html = $db->MYSQL2HTML($result, "ThinkFunProject", "game_skill");
				print $html;
			print "</BODY>\n";
			print "</HTML>\n";
		}
	}
	
	public function getGameAgeGroupList($format="html")
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();

		$result = $db->query("SELECT * FROM game_age_group");
		
		if($db->num_rows($result) == 0)
		{
			header ("Content-Type:text/html");
			print "No records found.";
		}
		elseif($format == "xml")
		{
			header ("Content-Type:text/xml");
			$xml = $db->MYSQL2XML($result, "ThinkFunProject", "game_age_group");
			print $xml;
		}
		elseif($format == "html")
		{
			header ("Content-Type:text/html");
			print "<HTML>\n";
			print "<BODY>\n";
				$html = $db->MYSQL2HTML($result, "ThinkFunProject", "game_age_group");
				print $html;
			print "</BODY>\n";
			print "</HTML>\n";
		}
	}
	
	public function getGameChallengeList($format="html")
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();

		$result = $db->query("CALL GetGameChallengeList(1, 0,-1);");
		
		if($db->num_rows($result) == 0)
		{
			header ("Content-Type:text/html");
			print "No records found.";
		}
		elseif($format == "xml")
		{
			header ("Content-Type:text/xml");
			$xml = $db->MYSQL2XML($result, "ThinkFunProject", "game_challenge");
			print $xml;
		}
		elseif($format == "html")
		{
			header ("Content-Type:text/html");
			print "<HTML>\n";
			print "<BODY>\n";
				$html = $db->MYSQL2HTML($result, "ThinkFunProject", "game_challenge");
				print $html;
			print "</BODY>\n";
			print "</HTML>\n";
		}
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
			$xml = $db->MYSQL2XML2($result, "ThinkFunProject", $table);
			print $xml;
		}
		elseif($format == "html")
		{
			header ("Content-Type:text/html");
			print "<HTML>\n";
			print "<BODY>\n";
				$html = $db->MYSQL2HTML($result, "ThinkFunProject", $table);
				print $html;
			print "</BODY>\n";
			print "</HTML>\n";
		}
		
		$db->close_connection();
	}
	
	public function getTableList()
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();

		$result = $db->query("show tables");
		
		$db->fetch_array($result);
		
		return $arr;
	}
}
?>