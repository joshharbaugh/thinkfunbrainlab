<?php

require_once('auth.php');
require_once('db.php');
require_once('errormgr.php');

$navigator_user_agent = ' ' . strtolower($_SERVER['HTTP_USER_AGENT']);
 
class db_api_game
{
	//public $last_id = 0;
	private	$last_query = "";
	private $last_error_text = "";
	private	$mysql_error_text = "";

	public function getTournamentList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getTournamentList(" . $_start . ", " . $_count . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "tournament", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getCurrentTournament(&$dom)
	{
		$_id = 0;
			
		// check that user is logged in.
		//$auth = new api_auth();
		//if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
	
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getCurrentTournament();";
		
		$result = $db->query($query, $recordset);

		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "tournament", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getGameById(&$dom)
	{
		$_id = 0;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['id']) and $_POST['id'] != "") {$_id = $_POST['id'];} else{
			$this->last_error_text = "id";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
	
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameById(" . $_id . ");";
		$result = $db->query($query, $recordset);

		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game", $dom);
		$db->close_connection();
		return $result;
	}

	public function getGameGroupList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameGroupList(" . $_start . ", " . $_count . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_group", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getGameList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameList(" . $_start . ", " . $_count . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getGameListByGameGroup(&$dom)
	{
		$_idGameGroup = 0;
		
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['idGameGroup']) and $_POST['idGameGroup'] != "") {$_idGameGroup = $_POST['idGameGroup'];} else{
			$this->last_error_text = "idGameGroup";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameListByGameGroup(" . $_idGameGroup . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getGameCategoryById(&$dom)
	{
		$_id = 0;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['id']) and $_POST['id'] != "") {$_id = $_POST['id'];} else{
			$this->last_error_text = "id";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
	
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameCategoryById(" . $_id . ");";
		$result = $db->query($query, $recordset);

		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_category", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getGameCategoryList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameCategoryList(" . $_start . ", " . $_count . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_category", $dom);
		$db->close_connection();
		return $result;
	}	
	
	public function getGameChallengeById(&$dom)
	{
		$_id = 0;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['id']) and $_POST['id'] != "") {$_id = $_POST['id'];} else{
			$this->last_error_text = "id";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
	
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameChallengeById(" . $_id . ");";
		$result = $db->query($query, $recordset);

		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_challenge", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getGameChallengeList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameChallengeList(" . $_start . ", " . $_count . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_challenge", $dom);
		$db->close_connection();
		return $result;
	}	
	
	public function getGameChallengeListByGame(&$dom)
	{
		$_start = 0;
		$_count = -1;
		$_idGame = 0;
		$_rand = 1; // per Rick set random as default behaviour 10/12/2012
		
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		if(isset($_POST['random']) and $_POST['random'] != "") {$_rand = $_POST['random'];} 
		
		if(isset($_POST['idGame']) and $_POST['idGame'] != "") {$_idGame = $_POST['idGame'];} else{
			$this->last_error_text = "idGame";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		$query = "CALL getGameChallengeListByGame(" . $_start . ", " . $_count . ", " . $_rand  . ", " . $_idGame . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
	
		$result = $db->MYSQL2XML3($recordset, "game_challenge", $dom);
		$db->close_connection();
		return $result;
	}	
	
	public function getGameSkillById(&$dom)
	{
		$_id = 0;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['id']) and $_POST['id'] != "") {$_id = $_POST['id'];} else{
			$this->last_error_text = "id";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
	
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameSkillById(" . $_id . ");";
		$result = $db->query($query, $recordset);

		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_skill", $dom);
		$db->close_connection();
		return $result;
	}
	
	public function getGameSkillList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

					if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameSkillList(" . $_start . ", " . $_count . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_skill", $dom);
		$db->close_connection();
		return $result;
	}	
	
	public function getGameLevelById(&$dom)
	{
		$_idGameLevel = 0;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['idGameLevel']) and $_POST['idGameLevel'] != "") {$_idGameLevel = $_POST['idGameLevel'];} else{
			$this->last_error_text = "idGameLevel";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
	
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameLevelById(" . $_idGameLevel . ");";
		$result = $db->query($query, $recordset);

		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_level", $dom);
		$db->close_connection();
		return $result;
	}

	public function getGameLevelList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameLevelList(" . $_start . ", " . $_count . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_level", $dom);
		$db->close_connection();
		return $result;
	}	
	
	public function getGameTypeList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameTypeList(" . $_start . ", " . $_count . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_type", $dom);
		$db->close_connection();
		return $result;
	}	
	
	public function getGameTypeById(&$dom)
	{
		$_idGameType = 0;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['idGameType']) and $_POST['idGameType'] != "") {$_idGameType = $_POST['idGameType'];} else{
			$this->last_error_text = "idGameType";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameTypeById(" . $_idGameType . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_type", $dom);
		$db->close_connection();
		return $result;
	}	

	public function getGameAgeGroupById(&$dom)
	{
		$_id = 0;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['id']) and $_POST['id'] != "") {$_id = $_POST['id'];} else{
			$this->last_error_text = "id";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
	
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameAgeGroupById(" . $_id . ");";
		$result = $db->query($query, $recordset);

		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_age_group", $dom);
		$db->close_connection();
		return $result;
	}

	public function getGameAgeGroupList(&$dom)
	{
		$_start = 0;
		$_count = -1;
			
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL getGameAgeGroupList(" . $_start . ", " . $_count . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_age_group", $dom);
		$db->close_connection();
		return $result;
	}	
	
	public function getLeaderBoardPlayersByTournamentId(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_start = 0;
		$_count = -1;
		$_idTournament = 0;

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} else{
			$this->last_error_text = "count";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idTournament']) and $_POST['idTournament'] != "") {$_idTournament = $_POST['idTournament'];} else{
			$this->last_error_text = "idTournament";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 

		$recordset = null;
		$result = 0;
		
		$query = "CALL getLeaderBoardPlayersByTournamentId(" . $_start . ", " . $_count . ", " . $_idTournament . ");";
//		$db->logActivity($_SERVER['REMOTE_ADDR'], $_POST['API_VER'], $_POST['API_LIBRARY'], $_POST['API_METHOD'], $query);

		$result = $db->query($query, $recordset);

		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db->MYSQL2XML3($recordset, "leader_board", $dom);
		$db->close_connection();
		return $result;
	}

	public function getLeaderBoardLeaguesByTournamentId(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_start = 0;
		$_count = -1;
		$_idTournament = 0;

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} else{
			$this->last_error_text = "count";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idTournament']) and $_POST['idTournament'] != "") {$_idTournament = $_POST['idTournament'];} else{
			$this->last_error_text = "idTournament";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 

		$recordset = null;
		$result = 0;
		
		$query = "CALL getLeaderBoardLeaguesByTournamentId(" . $_start . ", " . $_count . ", " . $_idTournament . ");";
//		$db->logActivity($_SERVER['REMOTE_ADDR'], $_POST['API_VER'], $_POST['API_LIBRARY'], $_POST['API_METHOD'], $query);

		$result = $db->query($query, $recordset);

		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db->MYSQL2XML3($recordset, "leader_board", $dom);
		$db->close_connection();
		return $result;
	}

	public function getLeaderBoardPlayersByGame(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_id = "";
		$_start = 0;
		$_count = -1;
		$_idGame = 0;
		$_idPlayer = 0;
		$_idTournament = 0;
		$_rank = 'TRUE'; // get player ranking
						
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['id']) and $_POST['id'] != "") {$_id = $_POST['id'];} 
		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		if(isset($_POST['idTournament']) and $_POST['idTournament'] != "") {$_idTournament = $_POST['idTournament'];}
		if(isset($_POST['idGame']) and $_POST['idGame'] != "") {$_idGame = $_POST['idGame'];} else{
			$this->last_error_text = "idGame";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 	
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} else{
			$this->last_error_text = "count";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 	
		
		$recordset = null;
		$result = 0;
		
		// first pass will get play record with players ranking in the sorted query resultset
		$query = "CALL getLeaderBoardPlayersByGame(" . $_start . ", " . $_count . ", " . $_idTournament . ", " .  $_idGame . ", " . $_idPlayer . "," . $_rank . ");";
		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		/*
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		*/
		// if there are no records for the player ranking then "ranked_player" node will not be inserted
		$result = $db->MYSQL2XML3($recordset, "ranked_player", $dom);
		$db->close_connection();
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		// now get the leaderboard placements
		$_rank = 'FALSE'; 
		$query = "CALL getLeaderBoardPlayersByGame(" . $_start . ", " . $_count . ", " . $_idTournament . ", " .  $_idGame . ", " . $_idPlayer . "," . $_rank . ");";
		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db->MYSQL2XML3($recordset, "leader_board", $dom);
		$db->close_connection();
		return $result;
	}	

	public function getLeaderBoardPlayersByGameByPlayerTeam(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_start = 0;
		$_count = -1;
		$_idGame = 0;
		$_idPlayer = 0;
		$_idTournament = 0;
		$_rank = 'TRUE'; // get player ranking
				
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		if(isset($_POST['idTournament']) and $_POST['idTournament'] != "") {$_idTournament = $_POST['idTournament'];}
		if(isset($_POST['idGame']) and $_POST['idGame'] != "") {$_idGame = $_POST['idGame'];} else{
			$this->last_error_text = "idGame";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}  

		$recordset = null;
		$result = 0;
		
		// first pass will get play record with players ranking in the sorted query resultset
		$query = "CALL getLeaderBoardPlayersByGameByPlayerTeam(" . $_start . ", " . $_count . ", " . $_idTournament . ", " .  $_idGame . ", " . $_idPlayer . "," . $_rank . ");";
		
//		$db->logActivity($_SERVER['REMOTE_ADDR'], $_POST['API_VER'], $_POST['API_LIBRARY'], $_POST['API_METHOD'], $query);

		$result = $db->query($query, $recordset);

		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		/*
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		*/
		// if there are no records for the player ranking then "ranked_player" node will not be inserted
		$result = $db->MYSQL2XML3($recordset, "ranked_player", $dom);
		$db->close_connection();
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		$_rank = 'FALSE'; // no player ranking
		
		// now get the leaderboard placements
		$query = "CALL getLeaderBoardPlayersByGameByPlayerTeam(" . $_start . ", " . $_count . ", " . $_idTournament . ", " .  $_idGame . ", " . $_idPlayer . "," . $_rank . ");";
		$result = $db->query($query, $recordset);

		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db->MYSQL2XML3($recordset, "leader_board", $dom);
		$db->close_connection();
		return $result;
	}

	public function getLeaderBoardPlayersByGameByPlayerLeague(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_start = 0;
		$_count = -1;
		$_idGame = 0;
		$_idPlayer = 0;
		$_idTournament = 0;
		$_rank = 'TRUE'; // get player ranking

		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		if(isset($_POST['idTournament']) and $_POST['idTournament'] != "") {$_idTournament = $_POST['idTournament'];}
		if(isset($_POST['idGame']) and $_POST['idGame'] != "") {$_idGame = $_POST['idGame'];} else{
			$this->last_error_text = "idGame";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}  
		
		$recordset = null;
		$result = 0;
		
		// first pass will get play record with players ranking in the sorted query resultset
		$query = "CALL getLeaderBoardPlayersByGameByPlayerLeague(" . $_start . ", " . $_count . ", " . $_idTournament . ", " .  $_idGame . ", " . $_idPlayer . ", " . $_rank . ");";
		//		$db->logActivity($_SERVER['REMOTE_ADDR'], $_POST['API_VER'], $_POST['API_LIBRARY'], $_POST['API_METHOD'], $query);

		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();
			return $result;
		}

		/*
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		*/
		// if there are no records for the player ranking then "ranked_player" node will not be inserted
		$result = $db->MYSQL2XML3($recordset, "ranked_player", $dom);
		$db->close_connection();
		
		$db2 = new mysql_db();
		$recordset2 = null;
		$result = 0;
		$_rank = 'FALSE'; // no player ranking
		
		// now get the leaderboard placements
		$query = "CALL getLeaderBoardPlayersByGameByPlayerLeague(" . $_start . ", " . $_count . ", " . $_idTournament . ", " .  $_idGame . ", " . $_idPlayer . ", " . $_rank . ");";
	
		$result = $db2->query($query, $recordset2);

		if(	$result != 0)
		{
			$db2->close_connection();
			$this->last_error_text = $db2->get_last_error_text();
			$this->mysql_error_text = $db2->get_mysql_error();

			return $result;
		}
		
	
		if($db2->num_rows($recordset2) == 0)
		{
			$db2->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}


		$result = $db2->MYSQL2XML3($recordset2, "leader_board", $dom);
		$db2->close_connection();		
		return $result;
	}
	
	public function getLeaderBoardTeamsByGame(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_start = 0;
		$_count = -1;
		$_idGame = 0;
		$_idPlayer = 0;
		$_idTournament = 0;
		$_rank = 'TRUE'; // get player ranking
				
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		if(isset($_POST['idTournament']) and $_POST['idTournament'] != "") {$_idTournament = $_POST['idTournament'];}
		if(isset($_POST['idGame']) and $_POST['idGame'] != "") {$_idGame = $_POST['idGame'];} else{
			$this->last_error_text = "idGame";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 		
		$recordset = null;
		$result = 0;
		
		// first pass will get play record with players ranking in the sorted query resultset
		$query = "CALL getLeaderBoardTeamsByGame(" . $_start . ", " . $_count . ", " . $_idTournament . ", " .  $_idGame . ", " . $_idPlayer . "," . $_rank . ");";
		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		/*
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		*/
		// if there are no records for the team ranking then "ranked_team" node will not be inserted
		$result = $db->MYSQL2XML3($recordset, "ranked_team", $dom);
		$db->close_connection();
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		$_rank = 'FALSE'; // no team ranking
		
		// now get the leaderboard placements
		$query = "CALL getLeaderBoardTeamsByGame(" . $_start . ", " . $_count . ", " . $_idTournament . ", " .  $_idGame . ", " . $_idPlayer . "," . $_rank . ");";
		$result = $db->query($query, $recordset);

		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db->MYSQL2XML3($recordset, "leader_board", $dom);
		$db->close_connection();	
		return $result;
	}	

	public function getLeaderBoardLeaguesByGame(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_start = 0;
		$_count = -1;
		$_idGame = 0;
		$_idPlayer = 0;
		$_idTournament = 0;
		$_rank = 'TRUE'; // get player ranking
				
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		if(isset($_POST['idTournament']) and $_POST['idTournament'] != "") {$_idTournament = $_POST['idTournament'];}
		if(isset($_POST['idGame']) and $_POST['idGame'] != "") {$_idGame = $_POST['idGame'];} else{
			$this->last_error_text = "idGame";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 		
		$recordset = null;
		$result = 0;
		
		// first pass will get play record with players ranking in the sorted query resultset
		$query = "CALL getLeaderBoardLeaguesByGame(" . $_start . ", " . $_count . ", " . $_idTournament . ", " .  $_idGame . ", " . $_idPlayer . "," . $_rank . ");";
		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		/*
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		*/
		// if there are no records for the league ranking then "ranked_league" node will not be inserted
		$result = $db->MYSQL2XML3($recordset, "ranked_league", $dom);
		$db->close_connection();
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		$_rank = 'FALSE'; // no team ranking
		
		// now get the leaderboard placements
		$query = "CALL getLeaderBoardLeaguesByGame(" . $_start . ", " . $_count . ", " . $_idTournament . ", " .  $_idGame . ", " . $_idPlayer . "," . $_rank . ");";
		$result = $db->query($query, $recordset);

		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db->MYSQL2XML3($recordset, "leader_board", $dom);
		$db->close_connection();	
		return $result;
	}	

	public function startGameSession(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_id = 0;
		$_idPlayer = 0;
		$_idGame = 0;
		$_idGameGroup = 0;
		$_idGameLevel = 0;
		$_idGameType = 0;
		$_dtStartTime = "";
		
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} 
		if(isset($_POST['idGame']) and $_POST['idGame'] != "") {$_idGame = $_POST['idGame'];} else{
			$this->last_error_text = "idGame";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idGameGroup']) and $_POST['idGameGroup'] != "") {$_idGameGroup = $_POST['idGameGroup'];} else{
			$this->last_error_text = "idGameGroup";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idGameLevel']) and $_POST['idGameLevel'] != "") {$_idGameLevel = $_POST['idGameLevel'];} else{
			$this->last_error_text = "idGameLevel";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idGameType']) and $_POST['idGameType'] != "") {$_idGameType = $_POST['idGameType'];} else{
			$this->last_error_text = "idGameType";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['dtStartTime']) and $_POST['dtStartTime'] != "") {$_dtStartTime = $_POST['dtStartTime'];} else{
			$this->last_error_text = "dtStartTime";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		$recordset = null;
		$result = 0;
		
		$query = "CALL startGameSession(" . $_idPlayer . ", " . $_idGame . ", " . $_idGameGroup .", " . $_idGameLevel . ", " . $_idGameType . ", '" . $_dtStartTime . "');";
		$result = $db->query($query, $recordset);	
	
		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		//$this->last_id = $_id;
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db->MYSQL2XML3($recordset, "game_session", $dom);
		$db->close_connection();
		return $result;
	}	
	
	public function endGameSession(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_id = 0;
		$_idGameSession = 0;
		$_dtEndTime = "";
		$_points = 0;
		$_awards = 0;
		$_solved = 0;
		$_incorrects = 0;
		$_skips = 0;
		$_resets = 0;
		$_time_penalty = 0;
		
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['idGameSession']) and $_POST['idGameSession'] != "") {$_idGameSession = $_POST['idGameSession'];} else{
			$this->last_error_text = "idGameSession";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['dtEndTime']) and $_POST['dtEndTime'] != "") {$_dtEndTime = $_POST['dtEndTime'];} else{
			$this->last_error_text = "dtEndTime";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['points']) and $_POST['points'] != "") {$_points = $_POST['points'];} else{
			$this->last_error_text = "points";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['solved']) and $_POST['solved'] != "") {$_solved = $_POST['solved'];} else{
			$this->last_error_text = "solved";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if(isset($_POST['skips']) and $_POST['skips'] != "") {$_skips = $_POST['skips'];} else{
			$this->last_error_text = "skips";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if(isset($_POST['resets']) and $_POST['resets'] != "") {$_resets = $_POST['resets'];} else{
			$this->last_error_text = "resets";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if(isset($_POST['incorrects']) and $_POST['incorrects'] != "") {$_incorrects = $_POST['incorrects'];} else{
			$this->last_error_text = "incorrects";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if(isset($_POST['timePenalty']) and $_POST['timePenalty'] != "") {$_time_penalty = $_POST['timePenalty'];} else{
			$this->last_error_text = "incorrects";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		
		if(isset($_POST['awards']) and $_POST['awards'] != "") {$_awards = $_POST['awards'];} 
		
		//change time penalty values from milliseconds to seconds
		if($_time_penalty > 999){$_time_penalty = $_time_penalty/1000;}
		
		$recordset = null;
		$result = 0;
		
		$query = "CALL endGameSession(" . $_idGameSession . ", '" . $_dtEndTime . "', " .  $_points . ", " . $_awards . ", " . $_solved . ", " . $_incorrects . ", " .  $_skips . ", " .  $_resets . ", " .  $_time_penalty . ");";
		$result = $db->query($query, $recordset);	
		
		//print $query; 
		//die();	
		
		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		//$this->last_id = $_id;
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
		
		$result = $db->MYSQL2XML3($recordset, "game_session", $dom);

		$db->close_connection();
		return $result;
	}	

	public function startChallengeSession(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_id = 0;
		$_idPlayer = 0;
		$_idGame = 0;
		$_idGameGroup = 0;
		$_idGameLevel = 0;
		$_idGameType = 0;
		$_idChallenge= 0;
		$_dtStartTime = "";
		
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 		
		if(isset($_POST['idGame']) and $_POST['idGame'] != "") {$_idGame = $_POST['idGame'];} else{
			$this->last_error_text = "idGame";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idChallenge']) and $_POST['idChallenge'] != "") {$_idChallenge = $_POST['idChallenge'];} else{
			$this->last_error_text = "idChallenge";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idGameLevel']) and $_POST['idGameLevel'] != "") {$_idGameLevel = $_POST['idGameLevel'];} else{
			$this->last_error_text = "idGameLevel";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idGameType']) and $_POST['idGameType'] != "") {$_idGameType = $_POST['idGameType'];} else{
			$this->last_error_text = "idGameType";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['dtStartTime']) and $_POST['dtStartTime'] != "") {$_dtStartTime = $_POST['dtStartTime'];} else{
			$this->last_error_text = "dtStartTime";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}  
		
		$recordset = null;
		$result = 0;
		
		$query = "CALL startChallengeSession(" . $_idPlayer . ", " . $_idChallenge . ", " .  $_idGame . ", " .  $_idGameLevel . ", " . $_idGameType . ", '" . $_dtStartTime . "');"; 
		$result = $db->query($query, $recordset);	
	
		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		//$this->last_id = $_id;
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db->MYSQL2XML3($recordset, "challenge_session", $dom);
		$db->close_connection();
		return $result;
	}	
	
	public function endChallengeSession(&$dom)
	{
		// Creates an object from mysql_db class
		$db = new mysql_db();
		$_id = 0;
		$_idChallengeSession = 0;
		$_dtEndTime = "";
		$_skipped = "false";
		$_resets = 0;
		$_solved = "false";
		$_incorrects = 0;
		
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['idChallengeSession']) and $_POST['idChallengeSession'] != "") {$_idChallengeSession = $_POST['idChallengeSession'];} else{
			$this->last_error_text = "idChallengeSession";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['dtEndTime']) and $_POST['dtEndTime'] != "") {$_dtEndTime = $_POST['dtEndTime'];} else{
			$this->last_error_text = "dtEndTime";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}  
		if(isset($_POST['solved']) and $_POST['solved'] != "") {$_solved = $_POST['solved'];} else{
			$this->last_error_text = "solved";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['skipped']) and $_POST['skipped'] != "") {$_skipped = $_POST['skipped'];} else{
			$this->last_error_text = "skipped";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['resets']) and $_POST['resets'] != "") {$_resets = $_POST['resets'];} else{
			$this->last_error_text = "resets";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['incorrects']) and $_POST['incorrects'] != "") {$_incorrects = $_POST['incorrects'];} else{
			$this->last_error_text = "incorrects";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		$recordset = null;
		$result = 0;
		
		$query = "CALL endChallengeSession(" . $_idChallengeSession . ", '" . $_dtEndTime . "', " .  $_skipped . ", " . $_incorrects . ", " . $_solved . ", " .  $_resets . ");";
		
		//$db->logActivity($_SERVER['REMOTE_ADDR'], $_POST['API_VER'], $_POST['API_LIBRARY'], $_POST['API_METHOD'], $query);
		//$db->close_connection();
		//$db = new mysql_db();
		//print $query;
		//die('');
		$result = $db->query($query, $recordset);	

		if(	$result != 0)
		{
			$db->close_connection();
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		//$this->last_id = $_id;
		if($db->num_rows($recordset) == 0)
		{
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db->MYSQL2XML3($recordset, "challenge_session", $dom);
		$db->close_connection();
		return $result;
	}	
	
	public function getTopScoresByPlayerByGameByLevelByType(&$dom)
	{
		$_start = 0;
		$_count = -1;
		$_idPlayer = 0;
		$_idGame = 0;
		$_idGameLevel = 0;
		$_idGameType = 0;
		
		// check that user is logged in.
		$auth = new api_auth();
		if($auth->isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}

		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} else{
			$this->last_error_text = "count";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}  
		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idPlayer']) and $_POST['idGame'] != "") {$_idGame= $_POST['idGame'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idGameLevel']) and $_POST['idGameLevel'] != "") {$_idGameLevel = $_POST['idGameLevel'];} else{
			$this->last_error_text = "idGameLevel";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idGameType']) and $_POST['idGameType'] != "") {$_idGameType = $_POST['idGameType'];} else{
			$this->last_error_text = "idGameType";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		// to do: evaluate and return parameter errors here
		

		$result = 0;
		
		// get the best score
		$result = $this->getBestScoreByGameByLevelByTypeByPlayer($dom);
		if(	$result != 0)
		{
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			return $result;
		}
		
		$db = new mysql_db();
		$recordset = null;
		$query = "CALL getTopScoresByPlayerByGameByLevelByType(" . $_start . ", " . $_count . ", " . $_idPlayer . ", " . $_idGame. ", " . $_idGameLevel . ", " . $_idGameType . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
	
		$result = $db->MYSQL2XML3($recordset, "game_score", $dom);
		$db->close_connection();
		return $result;
	}	

	public function getBestScoreByGameByLevelByTypeByPlayer(&$dom)
	{
		$_start = 0;
		$_count = -1;
		$_idPlayer = 0;
		$_idGame = 0;
		$_idGameLevel = 0;
		$_idGameType = 0;
		
		if(isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];} 
		if(isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];} 
		
		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idPlayer']) and $_POST['idGame'] != "") {$_idGame= $_POST['idGame'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idGameLevel']) and $_POST['idGameLevel'] != "") {$_idGameLevel = $_POST['idGameLevel'];} else{
			$this->last_error_text = "idGameLevel";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idGameType']) and $_POST['idGameType'] != "") {$_idGameType = $_POST['idGameType'];} else{
			$this->last_error_text = "idGameType";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		$query = "CALL getBestScoreByGameByLevelByTypeByPlayer(" . $_start . ", " . $_count . ", " . $_idPlayer . ", " . $_idGame. ", " . $_idGameLevel . ", " . $_idGameType . ");";
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
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}
	
		$result = $db->MYSQL2XML3($recordset, "best_score", $dom);
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
			$db->close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
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
	
	public function getLastError()
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
		
		$arr = $db->fetch_array($result);
		$db->close_connection();
			
		return $arr;
	}
}
?>