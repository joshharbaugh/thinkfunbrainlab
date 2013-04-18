<?php

require_once ('auth.php');
require_once ('db.php');
require_once ('errormgr.php');
require_once('lib_uuid.php');
require_once('api_queuemail.php');

$navigator_user_agent = ' ' . strtolower($_SERVER['HTTP_USER_AGENT']);
//error_reporting(E_ALL);
//ini_set ('display_errors', '1'); //PHP 5 and >

class db_api_account {
	private $last_query = "";
	private $last_error_text = "";
	private $mysql_error_text = "";

	public $idPlayer = 0;
	public $firstName = "";
	public $lastName = "";
	public $last_id = 0;

	//Function to sanitize values received from the form. Prevents SQL injection
	public function clean($str) {
		$str = @trim($str);
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}

	public function loginPlayer(&$dom) {
		$_userName = "";
		$_pwd = "";
		$userName= "";
		$pwd = "";
		$idPlayer = 0;

		if (isset($_POST['userName']) and $_POST['userName'] != "") {$_userName = $_POST['userName'];} else{
			$this->last_error_text = "userName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if (isset($_POST['pwd']) and $_POST['pwd'] != "") {$_pwd = $_POST['pwd'];} else{
			$this->last_error_text = "pwd";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		// to do: evaluate and return parameter errors here
		if ($_userName == "" || $_userName == "" ||
			$_pwd == "" || $_pwd == ""
			) 
			{
				return ErrorMgr::ERR_DB_LOGIN_CRED;
			} 
			
		// no point if already logged in.
		//$auth = new api_auth();
		//if(api_auth::isLoggedIn() ){return ErrorMgr::ERR_SUCCESS;}
		
		$this -> clean($_userName);
		//$this -> clean($_pwd);

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL loginPlayer ('" . $_userName . "', '" . $_pwd . "');";
		$result = $db -> query($query, $recordset);

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_LOGIN_CRED;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "player", $dom);
		$db -> close_connection();
		
		// update the authentication manager
		$this->getPlayerId($dom, $idPlayer);
	
		if($idPlayer > 0)
		{
			api_auth::setLoggedIn($idPlayer);
		}
		
		return $result;
	}

	public function logoutPlayer(&$dom) {
			
		$_idPlayer = 0;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_SUCCESS;}
		
		if (isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];} else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		//$auth = new api_auth();
		
		return api_auth::setLoggedOut($_idPlayer);	
	}
	
	public function resetPassword(&$dom) {
		$_token= "";
		$_newPwd = "";

		// check that user is logged in.
		//$auth = new api_auth();
		//if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['token']) and $_POST['token'] != "") {$_token = $_POST['token'];
		}else{
			$this->last_error_text = "token";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		
		if (isset($_POST['newPwd']) and $_POST['newPwd'] != "") {$_newPwd = $_POST['newPwd'];
		}else{
			$this->last_error_text = "newPwd";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		
		$query = "CALL resetPassword ('" . $_token  . "', '" . $_newPwd . "');";
		
		$result = $db -> query($query, $recordset);

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_PWD_RESET_TOKEN;
		}

		$result = $db -> MYSQL2XML3($recordset, "player", $dom);
		$db -> close_connection();
	}
	
	public function changePassword(&$dom) {
		$_userName = "";
		$_pwd = "";
		$_newPwd = "";

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['userName']) and $_POST['userName'] != "") {$_userName = $_POST['userName'];
		}else{
			$this->last_error_text = "userName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		
		if (isset($_POST['pwd']) and $_POST['pwd'] != "") {$_pwd = $_POST['pwd'];
		}else{
			$this->last_error_text = "pwd";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		
		if (isset($_POST['newPwd']) and $_POST['newPwd'] != "") {$_newPwd = $_POST['newPwd'];
		}else{
			$this->last_error_text = "newPwd";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		
		// TODO get the idPlayer from the session vars. 
		// this adds an extra level of protection to ensure that the client is referring to the logged in player. 
		
		// to do: evaluate and return parameter errors here
		if ($_userName == "" || $_userName == "" ||
			$_pwd == "" || $_pwd == "" ||
			$_newPwd == "" || $_newPwd == "") 
		{
			return ErrorMgr::ERR_DB_LOGIN_CRED;
		} 
			
		//if(api_auth::isLoggedIn() == FALSE){var_dump($_SESSION); die;}

		$this -> clean($_userName);
		//$this -> clean($_pwd);
		//$this -> clean($_newpwd);
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "SELECT idPlayer, firstName, lastName FROM player where userName = '" . $_userName . "' and password = '" . $_pwd . "'";
		$result = $db -> query($query, $recordset);

		if ($db -> num_rows($recordset) == 0) {
			return ErrorMgr::ERR_DB_LOGIN_CRED;
		} 

		while ($row = mysql_fetch_assoc($recordset)) {
			$this -> idPlayer = $row[idPlayer];
			$this -> firstName = $row[firstName];
			$this -> lastName = $row[lastName];
			break;
		}

		$db -> close_connection();
	
		$db = new mysql_db();
		$recordset = null;
		
		$query = "CALL changePassword(" . $this->idPlayer . ",'" . $_userName . "','"  . $_pwd . "','"  . $_newPwd ."');";
		$result = $db -> query($query, $recordset);

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
		}

		$result = $db -> MYSQL2XML3($recordset, "player", $dom);
		$db -> close_connection();

		// update the authentication manager
		api_auth::setLoggedIn($this->idPlayer);
		
		return $result;
	}

	public function getPlayerById(&$dom) {
		$_idPlayer = 0;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];
		}else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getPlayerById(" . $_idPlayer . ");";
		$result = $db -> query($query, $recordset);

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "player", $dom);
		$db -> close_connection();
		return $result;
	}

	public function setPlayerStatus(&$dom) {
		$_idPlayer = 0;
		$_idManager = 0;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];
		}else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if (isset($_POST['idTeamManager']) and $_POST['idTeamManager'] != "") {$_idTeamManager = $_POST['idTeamManager'];
		}else{
			$this->last_error_text = "idTeamManager";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if (isset($_POST['active']) and $_POST['active'] != "") {$_active = $_POST['active'];
		}else{
			$this->last_error_text = "active";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here
		
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		$idManager = 0;
		
		// make sure the manager id is actually the manager and therefore authorized to change the player status 
		$db->getTeamManagerByPlayerId($_idPlayer, $idTeamManager);
		
		if($idTeamManager == null){$idTeamManager = 0;}
				
		if($_idTeamManager != $idTeamManager)
		{
			$db -> close_connection();
			return ErrorMgr::ERR_API_UNAUTHORIZED;
		}
		
		$db -> close_connection();
		
		$db = new mysql_db();
		$query = "CALL setPlayerStatus(" . $_idPlayer . "," . $_active . ");";
		
		$result = $db -> query($query, $recordset);

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
		}

		$result = $db -> MYSQL2XML3($recordset, "status", $dom);
		$db -> close_connection();
		return $result;
	}

	public function setTeamManagerStatus(&$dom) {
		$_idTeamManager = 0;
		$_idLeagueManager = 0;
		
		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['idTeamManager']) and $_POST['idTeamManager'] != "") {$_idTeamManager = $_POST['idTeamManager'];
		}else{
			$this->last_error_text = "idTeamManager";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if (isset($_POST['idLeagueManager']) and $_POST['idLeagueManager'] != "") {$_idLeagueManager = $_POST['idLeagueManager'];
		}else{
			$this->last_error_text = "idLeagueManager";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if (isset($_POST['active']) and $_POST['active'] != "") {$_active = $_POST['active'];
		}else{
			$this->last_error_text = "active";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here
		
		$recordset = null;
		$result = 0;
		$idLeagueManager = 0;
		
		// make sure the manager id is actually the manager and therefore authorized to change the player status 
		/* TODO: well come back to this 
		//$db = new mysql_db();
		$db->getLeagueManagerByTeamManagerId($_idTeamManager, $idLeagueManager);
		
		if($idLeagueManager == null){$idLeagueManager = 0;}
				
		if($_idLeagueManager != $idLeagueManager)
		{
			$db -> close_connection();
			return ErrorMgr::ERR_API_UNAUTHORIZED;
		}
		
		
		//$db -> close_connection();
		*/
		
		$db = new mysql_db();
		$query = "CALL setPlayerStatus(" . $_idTeamManager . "," . $_active . ");";
		
		$result = $db -> query($query, $recordset);

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
		}

		$result = $db -> MYSQL2XML3($recordset, "status", $dom);
		$db -> close_connection();
		return $result;
	}

	public function getPlayerList(&$dom) 
	{
		$_start = 0;
		$_count = -1;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];
		}
		if (isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getPlayerList(" . $_start . ", " . $_count . ");";
		$result = $db -> query($query, $recordset);
		if ($result != 0) {
			$this -> last_error_text = $db -> get_last_error_text();
			$this -> mysql_error_text = $db -> get_mysql_error();

			$db -> close_connection();
			return $result;
		}

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "player", $dom);
		$db -> close_connection();
		return $result;
	}
	
	public function getTeamById(&$dom) {
		$_idTeam = 0;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['idTeam']) and $_POST['idTeam'] != "") {$_idTeam = $_POST['idTeam'];
		}else{
			$this->last_error_text = "idTeam";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getTeamById(" . $_idTeam . ");";
		$result = $db -> query($query, $recordset);

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "team", $dom);
		$db -> close_connection();
		return $result;
	}

	public function getTeamList(&$dom) {
		$_start = 0;
		$_count = -1;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];
		}
		if (isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getTeamList(" . $_start . ", " . $_count . ");";
		$result = $db -> query($query, $recordset);

		if ($result != 0) {
			$this -> last_error_text = $db -> get_last_error_text();
			$this -> mysql_error_text = $db -> get_mysql_error();

			$db -> close_connection();
			return $result;
		}

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "team", $dom);
		$db -> close_connection();
		return $result;
	}

	public function getTeamListByLeague(&$dom) {
		$_start = 0;
		$_count = -1;
		$_idLeague = 0;

		// check that user is logged in.
		//$auth = new api_auth();
		// need to be able to access when not logged in.
		//if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];
		}
		if (isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];
		}
		if (isset($_POST['idLeague']) and $_POST['idLeague'] != "") {$_idLeague = $_POST['idLeague'];
		}else{
			$this->last_error_text = "idLeague";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getTeamListByLeague(" . $_start . ", " . $_count . ", " . $_idLeague . ");";
		$result = $db -> query($query, $recordset);

		if ($result != 0) {
			$this -> last_error_text = $db -> get_last_error_text();
			$this -> mysql_error_text = $db -> get_mysql_error();

			$db -> close_connection();
			return $result;
		}

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "team", $dom);
		$db -> close_connection();
		return $result;
	}

	public function getPlayerListByTeamId(&$dom) {

		$_start = 0;
		$_count = -1;
		$_idTeam = 0;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		 
		if (isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];}
		if (isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];}
		if (isset($_POST['idTeam']) and $_POST['idTeam'] != "") {$_idTeam = $_POST['idTeam'];
		}else{
			$this->last_error_text = "idTeam";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getPlayerListByTeamId(" . $_start . ", " . $_count . ", " . $_idTeam . ");";
		$result = $db -> query($query, $recordset);

		if ($result != 0) {
			$this -> last_error_text = $db -> get_last_error_text();
			$this -> mysql_error_text = $db -> get_mysql_error();

			$db -> close_connection();
			return $result;
		}

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "team", $dom);
		$db -> close_connection();
		return $result;
	}

	public function getTeamManagerListByLeagueId(&$dom) {

		$_start = 0;
		$_count = -1;
		$_idTeam = 0;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
//				$auth = new api_auth();
	
		if (isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];}
		if (isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];}
		if (isset($_POST['idLeague']) and $_POST['idLeague'] != "") {$_idLeague = $_POST['idLeague'];
		}else{
			$this->last_error_text = "idLeague";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getTeamManagerListByLeagueId(" . $_start . ", " . $_count . ", " . $_idLeague . ");";
//$db->logActivity($_SERVER['REMOTE_ADDR'], $_POST['API_VER'], $_POST['API_LIBRARY'], $_POST['API_METHOD'], $query);

		$result = $db -> query($query, $recordset);

	 	if ($result != ErrorMgr::ERR_SUCCESS) {
			$this -> last_error_text = $db -> get_last_error_text();
			$this -> mysql_error_text = $db -> get_mysql_error();

			$db -> close_connection();
			//print "return 1: " . $result;
			return $result;
		}
		 
		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			//print "return: 2" . "ErrorMgr::ERR_DB_NO_RECORDS";
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "team", $dom);
		$db -> close_connection();
		//print "return: 3" . $result;
		
		return $result;
	}

	public function getTeamByPlayer(&$dom) {
		$_idPlayer = 0;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];
		}else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getTeamByPlayer(" . $_idPlayer . ");";
		$result = $db -> query($query, $recordset);
		if ($result != 0) {
			$this -> last_error_text = $db -> get_last_error_text();
			$this -> mysql_error_text = $db -> get_mysql_error();

			$db -> close_connection();
			return $result;
		}

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "team", $dom);
		$db -> close_connection();
		return $result;
	}

	public function addTeam(&$dom) {
		$_teamName = "";
		$_idLeague = 0;
		$_idCountry = 0;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['teamName']) and $_POST['teamName'] != "") {$_teamName = $_POST['teamName'];
		}else{
			$this->last_error_text = "teamName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if (isset($_POST['idLeague']) and $_POST['idLeague'] != "") {$_idLeague = $_POST['idLeague'];
		}else{
			$this->last_error_text = "idLeague";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if (isset($_POST['idCountry']) and $_POST['idCountry'] != "") {$_idCountry = $_POST['idCountry'];
		}else{
			$this->last_error_text = "idCountry";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL addTeam('" . $_teamName . "', " . $_idCountry . ", " . $_idLeague . ");";
		$result = $db -> query($query, $recordset);
		if ($result != 0) {
			$this -> last_error_text = $db -> get_last_error_text();
			$this -> mysql_error_text = $db -> get_mysql_error();

			$db -> close_connection();
			return $result;
		}

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "team", $dom);
		$db -> close_connection();
		return $result;
	}

	public function getLeagueById(&$dom) {
		$_idLeague = 0;

		// check that user is logged in.
		// need to be able to access when not logged in.
		//if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['idLeague']) and $_POST['idLeague'] != "") {$_idLeague = $_POST['idLeague'];
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getLeagueById(" . $_idLeague . ");";
		$result = $db -> query($query, $recordset);

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "league", $dom);
		$db -> close_connection();
		return $result;
	}

	public function getLeagueList(&$dom) {
		$_start = 0;
		$_count = -1;

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];
		}
		if (isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getLeagueList(" . $_start . ", " . $_count . ");";
		$result = $db -> query($query, $recordset);

		if ($result != 0) {
			$this -> last_error_text = $db -> get_last_error_text();
			$this -> mysql_error_text = $db -> get_mysql_error();

			$db -> close_connection();
			return $result;
		}

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "league", $dom);
		$db -> close_connection();
		return $result;
	}

	public function validateRegistrationCode(&$dom) {
		$_regCode = "";

		// check that user is logged in.
		////$auth = new api_auth();
		//if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['regCode']) and $_POST['regCode'] != "") {$_regCode = $_POST['regCode'];
		}else{
			$this->last_error_text = "regCode";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL validateRegistrationCode('" . $_regCode . "');";
		$result = $db -> query($query, $recordset);

		if ($result != 0) {
			$this -> last_error_text = $db -> get_last_error_text();
			$this -> mysql_error_text = $db -> get_mysql_error();

			$db -> close_connection();
			return $result;
		}

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "registration_code", $dom);
		$db -> close_connection();
		return $result;
	}
	
	public function getInvitationListByInviter(&$dom) {
			
		$_start = 0;
		$_count = -1;
		$_idPlayer = "";

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['start']) and $_POST['start'] != "") {$_start = $_POST['start'];
		}
		if (isset($_POST['count']) and $_POST['count'] != "") {$_count = $_POST['count'];
		}
		if (isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idPlayer = $_POST['idPlayer'];
		}else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}

		// to do: evaluate and return parameter errors here

		$db = new mysql_db();
		$recordset = null;
		$result = 0;

		$query = "CALL getInvitationListByInviter(" . $_start . ", " . $_count . ", " . $_idPlayer . ");";
		$result = $db -> query($query, $recordset);

		if ($result != 0) {
			$this -> last_error_text = $db -> get_last_error_text();
			$this -> mysql_error_text = $db -> get_mysql_error();

			$db -> close_connection();
			return $result;
		}

		if ($db -> num_rows($recordset) == 0) {
			$db -> close_connection();
			return ErrorMgr::ERR_DB_NO_RECORDS;
			//header ("Content-Type:text/html");
			//print "No records found.";
		}

		$result = $db -> MYSQL2XML3($recordset, "invitation", $dom);
		$db -> close_connection();
		return $result;
	}
		
	public function addPlayer(&$dom)
	{
		$_userName = "";
		$_firstName = "";
		$_lastName = "";
		$_pwd = "";
		$_email = "NULL";
		$_regCode = "NULL";
		$_idTeam = 0;
		$err = ErrorMgr::ERR_SUCCESS;
		$result = false;
			
		if(isset($_POST['regCode']) and $_POST['regCode'] != "") {$_regCode = $_POST['regCode'];} //optional
		
		if(isset($_POST['firstName']) and $_POST['firstName'] != "") {$_firstName = $_POST['firstName'];
		}else{
			$this->last_error_text = "firstName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}		
		if(isset($_POST['lastName']) and $_POST['lastName'] != "") {$_lastName = $_POST['lastName'];
		}else{
			$this->last_error_text = "lastName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['userName']) and $_POST['userName'] != "") {$_userName = $_POST['userName'];
		}else{
			$this->last_error_text = "userName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if(isset($_POST['email']) and $_POST['email'] != "") {$_email = $_POST['email'];
		}else{
			$this->last_error_text = "email";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['pwd']) and $_POST['pwd'] != "") {$_pwd = $_POST['pwd'];
		}else{
			$this->last_error_text = "pwd";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idTeam']) and $_POST['idTeam'] != "") {$_idTeam = $_POST['idTeam'];
		}else{
			$this->last_error_text = "idTeam";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
				
		// to do: evaluate and return parameter errors here
		//	print "err before: " . $err . "$result: " . $result;
			
		
		$result = $this->isPlayerRegistered($_userName, $_email, $err);
		//print "err after: " . $err . "$result: " . $result;
		//die();
		if($result == true)
		{
			//print "err after: " . $err;
			//die();
			return $err;
		}
		
		$db = new mysql_db();
		$recordset = null;
		$query = "CALL addPlayer('" . $_firstName . "','" . $_lastName . "','" . $_userName . "', '" . $_pwd . "', '" . $_email . "', " . $_idTeam . ", '" . $_regCode ."');";
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
		
		$result = $db->MYSQL2XML3($recordset, "player", $dom);
		$db->close_connection();
		
		$strTemplateHTML = "<!doctype html>";
		$strTemplateHTML .= "<html>";
		$strTemplateHTML .= "<head>";
		$strTemplateHTML .= "<meta charset=\"utf-8\">";
		$strTemplateHTML .= "<title>Welcome to Brain Lab</title>";
		$strTemplateHTML .= "</head>";
		$strTemplateHTML .= "<body>";
		$strTemplateHTML .= "<div style=\"font-family:Arial, Helvetica, sans-serif !important;width:600px;margin:0 auto;line-height:1.1\">";
		$strTemplateHTML .= "<img style=\"margin:5px 0 0 0\" src=\"https://www.thinkfunbrainlab.com/img/logo_email.png\">";
		$strTemplateHTML .= "<h4 style=\"margin:20px 0 0 0\">Dear --username--,</h4>";        
		$strTemplateHTML .= "<p>You have successfully registered as a --role-- on ThinkFun Brain Lab.</p>"; 
		$strTemplateHTML .= "<p>Click <a href=\"https://www.thinkfunbrainlab.com/home/login\">here</a> and enter your credentials to log in.</p>";   
		$strTemplateHTML .= "<p>We hope you have a great time on the site!</p>";
		$strTemplateHTML .= "<p>The Brain Lab Team</p>";
		$strTemplateHTML .= "<hr style=\"border:0;background-color:#333;height:2px;\">"; 
		$strTemplateHTML .= "</div>";
		$strTemplateHTML .= "</body>";
		$strTemplateHTML .= "</html>";
		
		$_userName = '';
		$_role = '';
		
		$this->getXmlValue($dom, 'userName', $_userName);
		$this->getXmlValue($dom, 'roleName', $_role);
		
		$strTemplateHTML = str_replace('--username--', $_userName, $strTemplateHTML);
		$strTemplateHTML = str_replace('--role--', $_role , $strTemplateHTML);
		$strHTML = $strTemplateHTML;
		
		$subject = 'Welcome to Brain Lab';
		
		// queue the email for sending
		$qm = new queuemail();
		$qm->sendmail($idInvitation, $_email, 'no-reply@thinkfunbrainlab.com', $subject, $strHTML, 'This is the email plain text');
		
		return $result;
	}

	public function addTeamAndManager(&$dom)
	{
		$_userName = "";
		$_firstName = "";
		$_userName = "";
		$_pwd = "";
		$_email = "NULL";
		$_regCode = "NULL";
		$_idTeam = 0;
		$_teamName = "";
		$_idLeague= 0;
			
		if(isset($_POST['regCode']) and $_POST['regCode'] != "") {$_regCode = $_POST['regCode'];
		}else{
			$this->last_error_text = "regCode";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if(isset($_POST['firstName']) and $_POST['firstName'] != "") {$_firstName = $_POST['firstName'];
		}else{
			$this->last_error_text = "firstName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['lastName']) and $_POST['lastName'] != "") {$_lastName = $_POST['lastName'];
		}else{
			$this->last_error_text = "lastName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['userName']) and $_POST['userName'] != "") {$_userName = $_POST['userName'];
		}else{
			$this->last_error_text = "userName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}		
		if(isset($_POST['pwd']) and $_POST['pwd'] != "") {$_pwd = $_POST['pwd'];
		}else{
			$this->last_error_text = "pwd";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['email']) and $_POST['email'] != "") {$_email = $_POST['email'];
		}else{
			$this->last_error_text = "email";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['teamName']) and $_POST['teamName'] != "") {$_teamName = $_POST['teamName'];
		}else{
			$this->last_error_text = "teamName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idLeague']) and $_POST['idLeague'] != "") {$_idLeague = $_POST['idLeague'];
		}else{
			$this->last_error_text = "idLeague";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		// to do: evaluate and return parameter errors here
		
		$result = $this->isPlayerRegistered($_userName, $_email, $err);
		if($result == true)
		{
			return $err;
		}
		
		$db = new mysql_db();
		$recordset = null;
		$query = "CALL addTeamAndManager('" . $_firstName . "','" . $_lastName . "','" . $_userName . "', '" . $_pwd . "', '" . $_email . "', " . $_idLeague . ", '" . $_teamName . "', '" . $_regCode ."');";
//print $query;
//die();
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
		
		$result = $db->MYSQL2XML3($recordset, "team_manager", $dom);
		$db->close_connection();
		
		$strTemplateHTML = "<!doctype html>";
		$strTemplateHTML .= "<html>";
		$strTemplateHTML .= "<head>";
		$strTemplateHTML .= "<meta charset=\"utf-8\">";
		$strTemplateHTML .= "<title>Welcome to Brain Lab</title>";
		$strTemplateHTML .= "</head>";
		$strTemplateHTML .= "<body>";
		$strTemplateHTML .= "<div style=\"font-family:Arial, Helvetica, sans-serif !important;width:600px;margin:0 auto;line-height:1.1\">";
		$strTemplateHTML .= "<img style=\"margin:5px 0 0 0\" src=\"https://www.thinkfunbrainlab.com/img/logo_email.png\">";
		$strTemplateHTML .= "<h4 style=\"margin:20px 0 0 0\">Dear --username--,</h4>";        
		$strTemplateHTML .= "<p>You have successfully registered as a --role-- on ThinkFun Brain Lab.</p>"; 
		$strTemplateHTML .= "<p>Click <a href=\"https://www.thinkfunbrainlab.com/home/login\">here</a> and enter your credentials to log in.</p>";   
		$strTemplateHTML .= "<p>We hope you have a great time on the site!</p>";
		$strTemplateHTML .= "<p>The Brain Lab Team</p>";
		$strTemplateHTML .= "<hr style=\"border:0;background-color:#333;height:2px;\">"; 
		$strTemplateHTML .= "</div>";
		$strTemplateHTML .= "</body>";
		$strTemplateHTML .= "</html>";
		
		$_userName = '';
		$_role = '';
		
		$this->getXmlValue($dom, 'userName', $_userName);
		$this->getXmlValue($dom, 'roleName', $_role);
		
		$strTemplateHTML = str_replace('--username--', $_userName, $strTemplateHTML);
		$strTemplateHTML = str_replace('--role--', $_role , $strTemplateHTML);
		$strHTML = $strTemplateHTML;
		
		$subject = 'Welcome to Brain Lab';
		
		// queue the email for sending
		$qm = new queuemail();
		$qm->sendmail($idInvitation, $_email, 'no-reply@thinkfunbrainlab.com', $subject, $strHTML, 'This is the email plain text');
		
		return $result;
	}

	public function addLeagueManager(&$dom)
	{
		$_userName = "";
		$_firstName = "";
		$_userName = "";
		$_pwd = "";
		$_email = "NULL";
		$_regCode = "NULL";
		$_idLeague= 0;
		$err = ErrorMgr::ERR_SUCCESS;
		
		if(isset($_POST['regCode']) and $_POST['regCode'] != "") {$_regCode = $_POST['regCode'];
		}else{
			$this->last_error_text = "regCode";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		if(isset($_POST['firstName']) and $_POST['firstName'] != "") {$_firstName = $_POST['firstName'];
		}else{
			$this->last_error_text = "firstName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['lastName']) and $_POST['lastName'] != "") {$_lastName = $_POST['lastName'];
		}else{
			$this->last_error_text = "lastName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['userName']) and $_POST['userName'] != "") {$_userName = $_POST['userName'];
		}else{
			$this->last_error_text = "userName";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['pwd']) and $_POST['pwd'] != "") {$_pwd = $_POST['pwd'];
		}else{
			$this->last_error_text = "pwd";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['email']) and $_POST['email'] != "") {$_email = $_POST['email'];
		}else{
			$this->last_error_text = "email";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['idLeague']) and $_POST['idLeague'] != "") {$_idLeague = $_POST['idLeague'];
		}else{
			$this->last_error_text = "idLeague";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		
		// to do: evaluate and return parameter errors here
		
		$result = $this->isPlayerRegistered($_userName, $_email, $err);
		if($result == true)
		{
			return $err;
		}
		
		$db = new mysql_db();
		$recordset = null;
		$query = "CALL addLeagueManager('" . $_firstName . "','" . $_lastName . "','" . $_userName . "', '" . $_pwd . "', '" . $_email . "', " . $_idLeague . ", '" . $_regCode ."');";
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
		
		
		$result = $db->MYSQL2XML3($recordset, "league_manager", $dom);
		$db->close_connection();
		
		
		$strTemplateHTML = "<!doctype html>";
		$strTemplateHTML .= "<html>";
		$strTemplateHTML .= "<head>";
		$strTemplateHTML .= "<meta charset=\"utf-8\">";
		$strTemplateHTML .= "<title>Welcome to Brain Lab</title>";
		$strTemplateHTML .= "</head>";
		$strTemplateHTML .= "<body>";
		$strTemplateHTML .= "<div style=\"font-family:Arial, Helvetica, sans-serif !important;width:600px;margin:0 auto;line-height:1.1\">";
		$strTemplateHTML .= "<img style=\"margin:5px 0 0 0\" src=\"https://www.thinkfunbrainlab.com/img/logo_email.png\">";
		$strTemplateHTML .= "<h4 style=\"margin:20px 0 0 0\">Dear --username--,</h4>";        
		$strTemplateHTML .= "<p>You have successfully registered as a --role-- on ThinkFun Brain Lab.</p>"; 
		$strTemplateHTML .= "<p>Click <a href=\"https://www.thinkfunbrainlab.com/home/login\">here</a> and enter your credentials to log in.</p>";   
		$strTemplateHTML .= "<p>We hope you have a great time on the site!</p>";
		$strTemplateHTML .= "<p>The Brain Lab Team</p>";
		$strTemplateHTML .= "<hr style=\"border:0;background-color:#333;height:2px;\">"; 
		$strTemplateHTML .= "</div>";
		$strTemplateHTML .= "</body>";
		$strTemplateHTML .= "</html>";
		
		$_userName = '';
		$_role = '';
		
		$this->getXmlValue($dom, 'userName', $_userName);
		$this->getXmlValue($dom, 'roleName', $_role);
		
		$strTemplateHTML = str_replace('--username--', $_userName, $strTemplateHTML);
		$strTemplateHTML = str_replace('--role--', $_role , $strTemplateHTML);
		$strHTML = $strTemplateHTML;
		
		$subject = 'Welcome to Brain Lab';
		
		// queue the email for sending
		$qm = new queuemail();
		$qm->sendmail($idInvitation, $_email, 'no-reply@thinkfunbrainlab.com', $subject, $strHTML, 'This is the email plain text');
			
		return $result;
	}
	
	
	public function sendAccountReminder(&$dom)
	{
		$_email = '';
		$_idPlayer = 0;
		$_userName = '';
		$_firstName = '';
		$_lastName = '';
		
		// check that user is logged in.
		//$auth = new api_auth();
		//if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
		
		if (isset($_POST['email']) and $_POST['email'] != "") {$_email = $_POST['email'];
		}else{
			$this->last_error_text = "email";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		}
		
		$uuid = UUID::mint();
		$_securitycode = $uuid->__get("string");
		
		$db = new mysql_db();
		$db->getAccountReminderEmail($_email, $_securitycode, $_idPlayer, $_userName, $_firstName, $_lastName, $_emailSubject, $_emailHTML, $_emailText);
		
		if($_idPlayer == null || $_idPlayer == 0 || $_userName == null)
		{
			return ErrorMgr::ERR_DB_EMAIL_NOT_EXISTS;
		}
		
		if($_firstName == null){$_firstName = '';}
		if($_lastName == null){$_lastName = '';}
		
		// $strHTML = "<!doctype html>";
		// $strHTML .= "<html>";
		// $strHTML .= "<head>";
		// $strHTML .= "<meta charset=\"utf-8\">";
		// $strHTML .= "<title>BrainLab Account Reminder</title>";
		// $strHTML .= "</head>";
		// $strHTML .= "<body>";
		// $strHTML .= "<h4>Hey Mate!</h4>";
		// $strHTML .= "<p>Forgot your user name [--username--] or password? .. you plonker!</p>";
		// $strHTML .= "<a style=\"color:#1881C2;text-decoration:underline\" href=\"https://www.thinkfunbrainlab.com/home/reset?code=--securitycode--&username=--username--\">click here</a>";
		// $strHTML .= " to change your password";
		// $strHTML .= "</body>";
		// $strHTML .= "</html>";
		
		$strHTML = "<!doctype html>";
		$strHTML .= "<html>";
		$strHTML .= "<head>";
		$strHTML .= "<meta charset=\"utf-8\">";
		$strHTML .= "<title>Brain Lab Account Reminder</title>";
		$strHTML .= "</head>";
		$strHTML .= "<body >";
		$strHTML .= "    <div style=\"font-family:Arial, Helvetica, sans-serif !important;width:600px;margin:0 auto;line-height:1.1\">";
		$strHTML .= "        <img style=\"margin:5px 0 0 0\" src=\"https://www.thinkfunbrainlab.com/img/logo_email.png\">";
		$strHTML .= "        <h4 style=\"margin:20px 0 0 0\">Dear --firstname-- --lastname--,</h4>";
		$strHTML .= "        <p>You have requested a user name or password reminder for your Brain Lab account. If you did not make the request, you may safely ignore this email.</p>";
		$strHTML .= "        <p>Your user name is: <span style=\"font-weight:bold\">--username--</span></p>";
		$strHTML .= "      <p>If you have forgotten your password, click <a target=\"_blank\" href=\"https://www.thinkfunbrainlab.com/home/reset?code=--securitycode--&username=--username--\">here</a> to change it.</p>";
		$strHTML .= "      <p>If you are still having difficulty signing in please send an email to <a href=\"mailto:support@thinkfunbrainlab.com?=Login Help\">support@thinkfunbrainlab.com</a></p>";
		$strHTML .= "      <p>Have fun!</p><br>";
		$strHTML .= "        With Regards<br>";
		$strHTML .= "        <span style=\"font-weight:bold\">The Brain Lab Team</span>";
		$strHTML .= "        <hr style=\"border:0;background-color:#333;height:2px;\"> ";
		$strHTML .= "    </div>";
		$strHTML .= "</body>";
		$strHTML .= "</html>";

		
		$strHTML = str_replace("--securitycode--", $_securitycode, $strHTML);
		$strHTML = str_replace("--firstname--", $_firstName, $strHTML);
		$strHTML = str_replace("--lastname--", $_lastName, $strHTML);
		$_strHTMLtoSend = str_replace("--username--", $_userName, $strHTML);
		
		$subject = "Your member account reminder";
		
		$qm = new queuemail();
		$qm->sendmail($idInvitation, $_email, 'support@thinkfunbrainlab.com', $subject, $_strHTMLtoSend, 'This is the email plain text');
		
	}

	public function sendInvitationList(&$dom)
	{

		$err = ErrorMgr::ERR_SUCCESS;
		$_idInvitedBy = 0;
		$_emailList = "NULL";
		$_idTeam = "NULL";
		$_idLeague = "NULL";
		$_idInviteeRole = 0;
		
		//print "hello";
		//die();

		// check that user is logged in.
		//$auth = new api_auth();
		if(api_auth::isLoggedIn() == FALSE){return ErrorMgr::ERR_API_LOGGED_OUT;}
				
		if(isset($_POST['idPlayer']) and $_POST['idPlayer'] != "") {$_idInvitedBy = $_POST['idPlayer'];
		}else{
			$this->last_error_text = "idPlayer";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
		if(isset($_POST['emailList']) and $_POST['emailList'] != "") {$_emailList = $_POST['emailList'];
		}else{
			$this->last_error_text = "emailList";
			return ErrorMgr::ERR_API_PARAM_MISSING;
		} 
				
		/*
		 * split the email list then iterate the collection and perform the following steps:
		 * 	- generate a UUID for the registration code to be emailed
		 * 	- queue up an email to be sent with the registration code to the invited member
		 *  - 
		*/

		$arrList = json_decode($_emailList, true);
		
		if($arrList == NULL)
		{
			return ErrorMsg::ERR_API_PARAM_INVALID;				
		}
	
		// get attributes of the Inviter from the database
		$_firstName = "";
		$_lastName = "";
		$idTeam = 0;
		$_teamName = "";
		$idLeague = 0;
		$_leagueName = "";		
		$_emailSubject = "";
		$_emailHTML = "";
		$_emailText = "";
		
		//print $_emailList;
		//die();
		
		$db = new mysql_db();
		$db->logActivity($_SERVER['REMOTE_ADDR'], $_POST['API_VER'], $_POST['API_LIBRARY'], $_POST['API_METHOD'], $_emailList);
		$db->close_connection();
		
		//$db->getInvitationEmailbyInviter($_idInvitedBy, $_firstName, $_lastName, $_teamName, $_leagueName, $_emailSubject, $_emailHTML, $_emailText);
		$db = new mysql_db();
		$db->getInvitationEmailbyInviter($_idInvitedBy, $_firstName, $_lastName, $idTeam, $_teamName, $idLeague, $_leagueName, $_idInviteeRole, $_emailSubject, $_emailHTML, $_emailText);
		
		if($idTeam == null){$idTeam = 0;}
		if($idLeague == null){$idLeague = 0;}
		
		//print $_idInvitedBy . "|" . $_firstName . "|" .  $_lastName . "|" .  $idTeam . "|" .  $_teamName . "|" .  $idLeague . "|" .  $_leagueName . "|" .  $_idInviteeRole . "|" .  $_emailSubject . "|" .  $_emailHTML . "|" . $_emailText . "<br>";
		//die();
		
		// set the email text and subject here for now but should call into database to retrieve it.
		// $strTeamMgrHTML = "<html>";
		// $strTeamMgrHTML .= "<head>";
		// $strTeamMgrHTML .= "<style>a{color:blue;text-decoration:underline;}</style>";
		// $strTeamMgrHTML .= "</head>";
		// $strTeamMgrHTML .= "<body>";
		// $strTeamMgrHTML .= "<p>Dear Team Manager,</p><p>--League Manager-- has invited you to join the --League-- League on ThinkFun Brain Lab as a Team Manager. Welcome!</p><p> ";
		// $strTeamMgrHTML .= "To complete the registration process, please click on the code shown here to go to Brain Lab and create your user account:</p><p> ";
		// $strTeamMgrHTML .= "<h4>Registration Code:</h4> <a style=\"color:blue;text-decoration:underline\" href=\"https://www.thinkfunbrainlab.com/home/create_account?regcode=--regcode--\">--regcode--</a></p>";
		// $strTeamMgrHTML .= "<p>Should the above link fail to work for any reason, simply go to:</p><p>";  
		// $strTeamMgrHTML .= "<a style=\"color:blue;text-decoration:underline\" href=\"https://www.thinkfunbrainlab.com/home/create_account\">https://www.thinkfunbrainlab.com/home/create_account</a></p><p> and copy/paste the registration code in to the required field to continue. </p>";
		// //$strTeamMgrHTML .= "<p>Attached is an instructional PDF designed to help guide you through the registration process.</p>";
		// $strTeamMgrHTML .= "<p>An instruction guide, designed to help guide you through the registration process, is available here <a style=\"color:blue;text-decoration:underline\" href=\"https://www.thinkfunbrainlab.com/pdf/teammanager_instructions.pdf\">Team Manager Instructions</a></p>";
		// $strTeamMgrHTML .= "<p>Thanks for joining us and have fun!</p>";
		// $strTeamMgrHTML .= "<p>The Brain Lab Team</p>";
		// $strTeamMgrHTML .= "</body>";
		// $strTeamMgrHTML .= "</html>";
// 		
		$strTeamMgrHTML = "<!doctype html>";
		$strTeamMgrHTML .= "<html>";
		$strTeamMgrHTML .= "<head>";
		$strTeamMgrHTML .= "<meta charset=\"utf-8\">";
		$strTeamMgrHTML .= "<title>BrainLab Registration</title>";
		$strTeamMgrHTML .= "</head>";
		$strTeamMgrHTML .= "<body>";
		$strTeamMgrHTML .= "<div style=\"font-family:Arial, Helvetica, sans-serif !important;width:600px;margin:0 auto;line-height:1.1\">";
		$strTeamMgrHTML .= "<img style=\"margin:5px 0 0 0\" src=\"https://www.thinkfunbrainlab.com/img/logo_email.png\">";
		$strTeamMgrHTML .= "<h4 style=\"margin:20px 0\">Dear Team Manager,</h4>";
		$strTeamMgrHTML .= "<p>Welcome to ThinkFun Brain Lab! <span style=\"font-weight:bold\">--League Manager--</span> has invited you to join the <span style=\"font-weight:bold\">--League--</span> League as a Team Manager. </p>";
		$strTeamMgrHTML .= "<p> To complete the registration process, please click on the code shown here to  go to Brain Lab and create your user account:</p>";
		$strTeamMgrHTML .= "<h4>Registration Code:</h4> <a style=\"color:#1881C2;text-decoration:underline\" target=\"_blank\" href=\"https://www.thinkfunbrainlab.com/home/create_account?regcode=--regcode--\">--regcode--</a>";
		$strTeamMgrHTML .= "<p>Should the above link fail to work for any reason, simply go to:</p>";
		$strTeamMgrHTML .= "<a style=\"color:#1881C2;text-decoration:underline\" target=\"_blank\" href=\"https://www.thinkfunbrainlab.com/home/create_account\">https://www.thinkfunbrainlab.com/home/create_account</a>";
		$strTeamMgrHTML .= "<p>and copy/paste the registration code in to the required field to continue.</p>";
		$strTeamMgrHTML .= "<p>An instruction guide, designed to help you through the registration process, is available <a style=\"color:#1881C2;text-decoration:underline\" target=\"_blank\" href=\"https://www.thinkfunbrainlab.com/pdf/teammanager_instructions.pdf\">here</a>.</p>";
		$strTeamMgrHTML .= "<p>Thanks for joining us and have fun!</p>";
		$strTeamMgrHTML .= "<p>The Brain Lab Team</p>";
		$strTeamMgrHTML .= "<hr style=\"border:0;background-color:#333;height:2px;\">";
		$strTeamMgrHTML .= "</div>";
		$strTeamMgrHTML .= "</body>";
		$strTeamMgrHTML .= "</html>";
		// $strPlayerHTML = "<html>";
		// $strPlayerHTML .= "<head>";
		// $strPlayerHTML .= "<style>a{color:blue;text-decoration:underline;}</style>";
		// $strPlayerHTML .= "</head>";
		// $strPlayerHTML .= "<body>";
		// $strPlayerHTML .= "<p>Dear Team Player,</p><p>--Team Manager-- has invited you to join the --Team-- Team in the --League-- League on ThinkFun  Brain Lab as a Team Player. Welcome!</p><p> ";
		// $strPlayerHTML .= "To complete the registration process, please click on the code shown here to go to Brain Lab and create your user account:</p><p> ";
		// $strPlayerHTML .= "<h4>Registration Code:</h4>  <a style=\"color:blue;text-decoration:underline\" href=\"https://www.thinkfunbrainlab.com/home/create_account?regcode=--regcode--\">--regcode--</a></p>";
		// $strPlayerHTML .= "<p>Should the above link fail to work for any reason, simply go to:</p><p>";  
		// $strPlayerHTML .= "<a style=\"color:blue;text-decoration:underline\" href=\"https://www.thinkfunbrainlab.com/home/create_account\">https://www.thinkfunbrainlab.com/home/create_account</a></p><p> and copy/paste the registration code in to the required field to continue. </p>";
		// //$strPlayerHTML .= "<p>Attached is an instructional PDF designed to help guide you through the registration process.</p>";
		// $strPlayerHTML .= "<p>An instruction guide, designed to help guide you through the registration process, is available here <a style=\"color:blue;text-decoration:underline\" href=\"https://www.thinkfunbrainlab.com/pdf/teamplayer_instructions.pdf\">Team Player Instructions</a></p>";
		// $strPlayerHTML .= "<p>Thanks for joining us and have fun!</p>";
		// $strPlayerHTML .= "<p>The Brain Lab Team</p>";
		// $strPlayerHTML .= "</body>";
		// $strPlayerHTML .= "</html>";
		$strPlayerHTML = "<!doctype html>";
		$strPlayerHTML .= "<html>";
		$strPlayerHTML .= "<head>";
		$strPlayerHTML .= "<meta charset=\"utf-8\">";
		$strPlayerHTML .= "<title>BrainLab Registration</title>";
		$strPlayerHTML .= "</head>";
		$strPlayerHTML .= "<body>";
		$strPlayerHTML .= "<div style=\"font-family:Arial, Helvetica, sans-serif !important;width:600px;margin:0 auto;line-height:1.1\">";
		$strPlayerHTML .= "<img style=\"margin:5px 0 0 0\" src=\"https://www.thinkfunbrainlab.com/img/logo_email.png\">";
		$strPlayerHTML .= "<h4 style=\"margin:20px 0\">Dear Team Player,</h4>";
		$strPlayerHTML .= "<p>Welcome to ThinkFun Brain Lab! <span style=\"font-weight:bold\">--Team Manager--</span> has invited you to join the <span style=\"font-weight:bold\">--Team--</span> Team in the <span style=\"font-weight:bold\">--League--</span> League as a Team Player.</p>";
		$strPlayerHTML .= "<p> To complete the registration process, please click on the code shown here to  go to Brain Lab and create your user account:</p>";
		$strPlayerHTML .= "<h4>Registration Code:</h4> <a style=\"color:#1881C2;text-decoration:underline\" target=\"_blank\" href=\"https://www.thinkfunbrainlab.com/home/create_account?regcode=--regcode--\">--regcode--</a>";
		$strPlayerHTML .= "<p>Should the above link fail to work for any reason, simply go to:</p>";
		$strPlayerHTML .= "<a style=\"color:#1881C2;text-decoration:underline\" href=\"https://www.thinkfunbrainlab.com/home/create_account\">https://www.thinkfunbrainlab.com/home/create_account</a>";
		$strPlayerHTML .= "<p>and copy/paste the registration code in to the required field to continue.</p>";
		$strPlayerHTML .= "<p>An instruction guide, designed to help you through the registration process, is available <a style=\"color:#1881C2;text-decoration:underline\" target=\"_blank\" href=\"https://www.thinkfunbrainlab.com/pdf/teamplayer_instructions.pdf\">here</a>.</p>";
		$strPlayerHTML .= "<p>Thanks for joining us and have fun!</p>";
		$strPlayerHTML .= "<p>The Brain Lab Team</p>";
		$strPlayerHTML .= "<hr style=\"border:0;background-color:#333;height:2px;\">";
		$strPlayerHTML .= "</div>";
		$strPlayerHTML .= "</body>";
		$strPlayerHTML .= "</html>";
							
		$subject = "ThinkFun Brain Lab Registration Code";
		$strHTML = "";
		

		if($_idInviteeRole == 1)
		{
			// replace Team name and Team Manager name	
			$strPlayerHTML = str_replace('--Team--', $_teamName, $strPlayerHTML);
			$strPlayerHTML = str_replace('--League--', $_leagueName, $strPlayerHTML);
			$strPlayerHTML = str_replace('--Team Manager--', $_firstName, $strPlayerHTML);
			$strHTML = $strPlayerHTML;
		}
		elseif($_idInviteeRole == 2)
		{
			// replace League name and League Manager name	
			$strTeamMgrHTML = str_replace('--League--', $_leagueName, $strTeamMgrHTML);
			$strTeamMgrHTML = str_replace('--League Manager--', $_firstName . " ". $_lastName, $strTeamMgrHTML);
			$strHTML = $strTeamMgrHTML;
		}
		
		// get email text to send
		//$strHTML = getEmailText($_idInviteeRole, $_idTeamManager, $_idLeagueManager);
		if($strHTML == NULL || strlen($strHTML) == 0)
		{
			
		}
		
		foreach($arrList as $node)
		{
			$_note = $node[note];
			$_email = $node[email];
			
			// generate registration code
			$uuid = UUID::mint();
			$_regcode = $uuid->__get("string");
			
			$_strHTMLtoSend = str_replace("--regcode--", $_regcode, $strHTML);
			
			//print $strHTML;
			//die();				
					
			// keep a record of the invitation
			$db = new mysql_db();
			$recordset = null;
			
			$query = "CALL addInvitation(" . $_idInvitedBy . "," . $_idInviteeRole . ",'". $_email . "', '". $_regcode . "', '". $_note . "', " . $idTeam . ", " . $idLeague . ");";
			$result = $db->query($query, $recordset);
			//print $query;
			//die();				
			
			if(	$result != 0)
			{
				$this->last_error_text = $db->get_last_error_text();
				$this->mysql_error_text = $db->get_mysql_error();
	
				$db->close_connection();
				return $result;
			}
			
			// get the result set in XML
			$resultFinal = $db->MYSQL2XML3($recordset, "invitation", $dom);
			$db->close_connection();
		
			// keep a record of the invitation
			$db = new mysql_db();
			$recordset = null;
			
			$query = "SELECT idInvite from invite where regCode = '" . $_regcode . "'";
			$result = $db->query($query, $recordset);
			
			if($db->num_rows($recordset) == 0)
			{
				$db->close_connection();
				return ErrorMgr::ERR_DB_NO_RECORDS;
			}
			
			// get the invitation record id for storing in the mail_queue database 
			while ($row = mysql_fetch_assoc($recordset)) 
			{
				$idInvitation = $row[idInvite];
				//print $idInvitation  . "<br>";
			}
			
			$db->close_connection();
			
			// if there is no id something went wrong .. no point in continuing
			if($idInvitation == null || $idInvitation == 0)
			{
				return ErrorMgr::ERR_DB_NO_RECORDS;
			}
			
			// queue the email for sending
			$qm = new queuemail();
			$qm->sendmail($idInvitation, $_email, 'no-reply@thinkfunbrainlab.com', $subject, $_strHTMLtoSend, 'This is the email plain text');
			
		}

		return $err;
	}

	public function isPlayerRegistered($userName, $email, &$err)
	{
		$db = new mysql_db();
		$recordset = null;
		$result = 0;
		$retVal = FALSE;
		$bFound = FALSE;
		
		$_userName = $this -> clean($userName);
		$_userEmail = $this -> clean($email);
		
		// first check if player is already registered
		$query = "CALL isPlayerRegistered('" . $_userName . "', '" . $_userEmail . "');";
		$result = $db->query($query, $recordset);
		if(	$result != 0)
		{
			$this->last_error_text = $db->get_last_error_text();
			$this->mysql_error_text = $db->get_mysql_error();

			$db->close_connection();
			
			$err = $result;
			return TRUE; // well, not really true but we need to allow the caller to stop processing if there is an error
		}
		
		$debug = "";
		// return with error if record found
		if($db->num_rows($recordset) > 0)
		{
			while ($row = mysql_fetch_assoc($recordset)) 
			{
				$bFound = FALSE;
				while((list($var, $val) = each($row)) && $bFound == FALSE) {
					
					if((strcmp($var, "userName") == 0) && (strcmp($val, $_userName) == 0))
				    {
						$bFound = TRUE;
						$err = ErrorMgr::ERR_DB_UNAME_EXISTS;					
						$retVal = TRUE;
						break;
					}
					else if((strcmp($var, "userEmail") == 0) && (strcmp($val, $_userEmail) == 0))
					{
						$bFound = TRUE;
						$err = ErrorMgr::ERR_DB_EMAIL_EXISTS;					
						$retVal = TRUE;
					}
				}	
			}
		}
		
		$db->close_connection();		
		return $retVal;
	}
	
	private function displayTableData($table, $format = "html") {
		// Creates an object from mysql_db class
		$db = new mysql_db();

		$result = $db -> query("SELECT * FROM {$table}");

		if ($db -> num_rows($result) == 0) {
			header("Content-Type:text/html");
			print "No records found.";
		} elseif ($format == "xml") {
			header("Content-Type:text/xml");
			$xml = $db -> MYSQL2XML2($result, "ThinkFunBrainLab", $table);
			print $xml;
		} elseif ($format == "html") {
			header("Content-Type:text/html");
			print "<HTML>\n";
			print "<BODY>\n";
			$html = $db -> MYSQL2HTML($result, "ThinkFunBrainLab", $table);
			print $html;
			print "</BODY>\n";
			print "</HTML>\n";
		}

		$db -> close_connection();
	}

	public function getLastError() {
		return $this -> last_error_text;
	}

	public function getLastMysqlError() {
		return $this -> mysql_error_text;
	}

	public function getTableList() {
		// Creates an object from mysql_db class
		$db = new mysql_db();

		$result = $db -> query("show tables");

		$db -> fetch_array($result);
		$db -> close_connection();
		return $arr;
	}
	
	private function getPlayerId($dom, &$idPlayer)
	{
		$nodes = $dom->getElementsByTagName('idPlayer');
		$nodeData = null;
	
		foreach ($nodes as $node) {
			$idPlayer = $node->nodeValue;
		}		
	}
	
	private function getXmlValue($dom, $tagName, &$strValue)
	{
		$nodes = $dom->getElementsByTagName($tagName);
		$nodeData = null;
	
		foreach ($nodes as $node) {
			$strValue = $node->nodeValue;
		}		
	}

}
?>