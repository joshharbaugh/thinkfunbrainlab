<?php

require_once ('errormgr.php');
	
define("MAX_SESSION_TIME", 3600);

//error_reporting(E_ALL);
//ini_set ('display_errors', '1'); //PHP 5 and >
	
class api_auth
{

//	const MAX_SESSION_TIME = 3600; // # of seconds of inactivity after which a session expires and user must login again. 
	
	private $err = ErrorMgr::ERR_SUCCESS;

	public function __construct()
	{
		//Start session
		
		$id = '';
		
		if (isset($_SERVER['PHPSESSID']) && $_SERVER['PHPSESSID'] != "")
		{
			$id = $_SERVER['PHPSESSID'];
		}
		elseif (isset($_POST['SI']) && $_POST['SI'] != "") 
		{
			$id = $_POST['SI'];
		}
		// var_dump($_REQUEST);
		// print "<br>";
		// print "<br>";
		// var_dump($_SERVER);
		// print "<br>";
		// print "<br>";
		// var_dump($_POST);
		// die(); 		
		
		if($id != NULL) {session_id($id);}
			
//		var_dump($_REQUEST);
//		die();
		
		session_start();
		
		//Check whether the session variable SESS_PLAYER_ID is present or not
		//if(!isset($_SESSION['SESS_PLAYER_ID']) || (trim($_SESSION['SESS_PLAYER_ID']) == '')) {
		//	//$_SESSION['SESS_REQUEST_URI'] = $_SERVER["REQUEST_URI"];
		//	header("location: login.php");
		//	exit();
		//}
		
		// NOTE: using the following abbreviations:
		//	TF_LA = Last Activity
		//  TF_UID = User/Unique ID
 		
		if (isset($_SESSION['TF_LA']) && (time() - $_SESSION['TF_LA'] > MAX_SESSION_TIME)) {
		    	
//		    print "session timeout .. current time [" . time() . "] TF_LA [" . $_SESSION['TF_LA'] . "] MAX [" . $this::MAX_SESSION_TIME . "]";
//			die();

		    // last request was more than 30 minates ago
		    session_unset();     // unset $_SESSION variable for the runtime 
		    session_destroy();   // destroy session data in storage
		}
		
		// update last activity time stamp
		$_SESSION['TF_LA'] = time(); 
	}
	
	static public function isLoggedIn()
	{
		if (isset($_SESSION['TF_LA']) && (time() - $_SESSION['TF_LA'] < MAX_SESSION_TIME) &&
			isset($_SESSION['TF_UID']) && $_SESSION['TF_UID'] > 0) 
			{ 
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	static public function setLoggedIn($idPlayer)
	{
		//session_regenerate_id(true);
		//session_start();
		
		$_SESSION['TF_UID'] = $idPlayer;
		$_SESSION['TF_LA'] = time();
//		print $idPlayer;
//	var_dump($_SESSION);
//	die();	
			
	}

	static public function setLoggedOut($idPlayer)
	{
		 if(isset($_SESSION['TF_UID']) && $_SESSION['TF_UID'] != $idPlayer)
		 {
		 	return ErrorMgr::ERR_API_INVALID_PARAM;
		 } 
		 
		 session_unset();     // unset $_SESSION variable for the runtime 
		 session_destroy();   // destroy session data in storage
		 
		 return ErrorMgr::ERR_SUCCESS;
	}	
}
?>