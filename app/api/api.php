<?php
header("Access-Control-Allow-Origin: http://localhost:8181");
header('Access-Control-Allow-Headers: X-Requested-With');

require_once('db_api_account.php');
require_once('db_api_game.php');
require_once('db_api_admin.php');
require_once('errormgr.php');
require_once('db.php');
//error_reporting(E_ALL);
error_reporting(E_COMPILE_ERROR|E_USER_ERROR|E_RECOVERABLE_ERROR);
//ini_set ('display_errors', '1'); //PHP 5 and >
	
// Prepare result XML. For now we'll only support XML but will want to extend the API to support JSON in future.
$dom = new DOMDocument();
$dom->formatOutput = false;

$err = new ErrorMgr();
$auth = new api_auth(); // calls session_start()

$errText = "";
$errDisplay = "";
 
$nodeRoot = $dom->createElement("ThinkFunBrainLab");
$dom->appendChild( $nodeRoot );

$nodeResultCode = $dom->createElement("resultcode");
$nodeRoot->appendChild( $nodeResultCode );

$nodeResultText = $dom->createElement("resulttext");
$nodeRoot->appendChild( $nodeResultText );

$nodeDisplayText = $dom->createElement("displaytext");
$nodeRoot->appendChild( $nodeDisplayText );

$nodeData = $dom->createElement("data");
$nodeRoot->appendChild( $nodeData );

$db = new mysql_db();
if (isset($_POST['IP']) and $_POST['IP'] != "") 
{
	$strText = serialize($_POST);	
	$db->logActivity($_POST['IP'], $_POST['API_VER'], $_POST['API_LIBRARY'], $_POST['API_METHOD'], $strText);
}else
{
	$strText = serialize($_POST);
	$db->logActivity($_SERVER['REMOTE_ADDR'], $_POST['API_VER'], $_POST['API_LIBRARY'], $_POST['API_METHOD'], $strText);
}

if (isset($_POST['API_VER']) and $_POST['API_VER'] != "") 
{ 
	$apiKey = "";
	$apiLibrary = "";
	$apiMethod = "";
	$apiFormat = "";

	if(isset($_POST['API_KEY']) and $_POST['API_KEY'] != "") {$apiKey = $_POST['API_KEY'];}
	else {
			$err->getErrorStrings(ErrorMgr::ERR_API_KEY, $errText, $errDisplay);	
			
			$nodeResultCode->appendChild( $dom->createTextNode("101") );
			$nodeResultText->appendChild( $dom->createTextNode($errText) );
			$nodeDisplayText->appendChild( $dom->createTextNode($errDisplay) );
			header ("Content-Type:text/xml");
			print $dom->SaveXML();
			exit();
	}
	if(isset($_POST['API_LIBRARY']) and $_POST['API_LIBRARY'] != "") {$apiLibrary = $_POST['API_LIBRARY'];}
	else {
			$err->getErrorStrings(ErrorMgr::ERR_API_LIBRARY, $errText, $errDisplay);	
				
			$nodeResultCode->appendChild( $dom->createTextNode("102") );
			$nodeResultText->appendChild( $dom->createTextNode($errText) );
			$nodeDisplayText->appendChild( $dom->createTextNode($errDisplay) );
			header ("Content-Type:text/xml");
			print $dom->SaveXML();
			exit();
	}
	if(isset($_POST['API_METHOD']) and $_POST['API_METHOD'] != "") {$apiMethod = $_POST['API_METHOD'];}
	else {
			$err->getErrorStrings(ErrorMgr::ERR_API_METHOD, $errText, $errDisplay);	
			
			$nodeResultCode->appendChild( $dom->createTextNode("103") );
			$nodeResultText->appendChild( $dom->createTextNode($errText) );
			$nodeDisplayText->appendChild( $dom->createTextNode($errDisplay) );
			header ("Content-Type:text/xml");
			print $dom->SaveXML();
			exit();
	}
	if(isset($_POST['API_FORMAT']) and $_POST['API_FORMAT'] != "") {$apiFormat = $_POST['API_FORMAT'];}
//	else {
//			$nodeResultCode->appendChild( $dom->createTextNode("104") );
//			$nodeResultText->appendChild( $dom->createTextNode("POST method expected") );
//	}
	
	$db_api = null;
	$nResult = -1;

		
	switch ($apiLibrary) 
	{
		case "Admin":

			$db_api = new db_api_admin();

			switch ($apiMethod)
			{
			case "loginAdmin":
				$nResult = $db_api->loginAdmin($dom);
				break;
			case "addLeague":
				$nResult = $db_api->addLeague($dom);
				break;
			case "getTournamentList":
				$nResult = $db_api->getTournamentList($dom);
				break;
			case "getCountryList":
				$nResult = $db_api->getCountryList($dom);
				break;
			case "getInvitationList":
				$nResult = $db_api->getInvitationList($dom);
				break;
			case "getTeamManagerList":
				$nResult = $db_api->getTeamManagerList($dom);
				break;
			default:
				$error = $db_api->getLastError();
				$mysql_error = $db_api->getLastMysqlError();
				
				$err->getErrorStrings(ErrorMgr::ERR_API_METHOD_UNKNOWN, $errText, $errDisplay);	
					
				$nodeResultCode->appendChild( $dom->createTextNode(ErrorMgr::ERR_API_METHOD_UNKNOWN) );
				$nodeResultText->appendChild( $dom->createTextNode($errText . " [" . $apiMethod . "]") );
				$nodeDisplayText->appendChild( $dom->createTextNode($errDisplay) );
				break;	
			}			
		break;
		case "Account":
			$db_api = new db_api_account();

			switch ($apiMethod)
			{
			case "loginPlayer":
				$nResult = $db_api->loginPlayer($dom);
				break;
			case "logoutPlayer":
				$nResult = $db_api->logoutPlayer($dom);
				break;
			case "changePassword":
				$nResult = $db_api->changePassword($dom);
				break;
			case "resetPassword":
				$nResult = $db_api->resetPassword($dom);
				break;	
			case "getPlayerList":
				$nResult = $db_api->getPlayerList($dom);
				break;
			case "getPlayerById":
				$nResult = $db_api->getPlayerById($dom);
				break;
			case "setPlayerStatus":
				$nResult = $db_api->setPlayerStatus($dom);
				break;	
			case "addPlayer":
				$nResult = $db_api->addPlayer($dom);
				break;
			case "getTeamById":
				$nResult = $db_api->getTeamById($dom);
				break;
			case "getTeamList":
				$nResult = $db_api->getTeamList($dom);
				break;
			case "getTeamByPlayer":
				$nResult = $db_api->getTeamByPlayer($dom);
				break;
			case "getTeamListByLeague":
				$nResult = $db_api->getTeamListByLeague($dom);
				break;
			case "getPlayerListByTeamId":
				$nResult = $db_api->getPlayerListByTeamId($dom);
				break;	
			case "addTeamAndManager":
				$nResult = $db_api->addTeamAndManager($dom);
				break;
			case "setTeamManagerStatus":
				$nResult = $db_api->setTeamManagerStatus($dom);
				break;	
			case "addLeagueManager":
				$nResult = $db_api->addLeagueManager($dom);
				break;
			case "getLeagueById":
				$nResult = $db_api->getLeagueById($dom);
				break;
			case "getLeagueList":
				$nResult = $db_api->getLeagueList($dom);
				break;
			case "getTeamManagerListByLeagueId":
				$nResult = $db_api->getTeamManagerListByLeagueId($dom);
				break;
			case "validateRegistrationCode":
				$nResult = $db_api->validateRegistrationCode($dom);
				break;
			case "getInvitationListByInviter":
				$nResult = $db_api->getInvitationListByInviter($dom);
				break;
			case "sendInvitationList":
				$nResult = $db_api->sendInvitationList($dom);
				break;
			case "sendAccountReminder":
				$nResult = $db_api->sendAccountReminder($dom);
				break;
			default:
				$error = $db_api->getLastError();
				$mysql_error = $db_api->getLastMysqlError();
				
				$err->getErrorStrings(ErrorMgr::ERR_API_METHOD_UNKNOWN, $errText, $errDisplay);	
					
				$nodeResultCode->appendChild( $dom->createTextNode(ErrorMgr::ERR_API_METHOD_UNKNOWN) );
				$nodeResultText->appendChild( $dom->createTextNode($errText . " [" . $apiMethod . "]") );
				$nodeDisplayText->appendChild( $dom->createTextNode($errDisplay) );
				break;			
			}
		break;
		case "Game":

			$db_api = new db_api_game(); 

			switch ($apiMethod)
			{
			case "getTournamentList":
				$nResult = $db_api->getTournamentList($dom);
				break;
			case "getCurrentTournament":
				$nResult = $db_api->getCurrentTournament($dom);
				break;
			case "getGameGroupList":
				$nResult = $db_api->getGameGroupList($dom);
				break;
			case "getGameById":
				$nResult = $db_api->getGameById($dom);
				break;
			case "getGameList":
				$nResult = $db_api->getGameList($dom);
				break;
			case "getGameListByGameGroup":
				$nResult = $db_api->getGameListByGameGroup($dom);
				break;
			case "getGameCategoryById":
				$nResult = $db_api->getGameCategoryById($dom);
				break;
			case "getGameCategoryList":
				$nResult = $db_api->getGameCategoryList($dom);
				break;
			case "getGameChallengeById":
				$nResult = $db_api->getGameChallengeById($dom);
				break;
			case "getGameChallengeList":
				$nResult = $db_api->getGameChallengeList($dom);
				break;
			case "getGameChallengeListByGame":
				$nResult = $db_api->getGameChallengeListByGame($dom);
				break;
			case "getGameLevelById":
				$nResult = $db_api->getGameLevelById($dom);
				break;
			case "getGameLevelList":
				$nResult = $db_api->getGameLevelList($dom);
				break;
			case "getGameTypeById":
				$nResult = $db_api->getGameTypeById($dom);
				break;
			case "getGameTypeList":
				$nResult = $db_api->getGameTypeList($dom);
				break;
			case "getGameSkillById":
				$nResult = $db_api->getGameSkillById($dom);
				break;
			case "getGameSkillList":
				$nResult = $db_api->getGameSkillList($dom);
				break;
			case "getGameAgeGroupById":
				$nResult = $db_api->getGameAgeGroupById($dom);
				break;
			case "getGameAgeGroupList":
				$nResult = $db_api->getGameAgeGroupList($dom);
				break;
			case "getLeaderBoardPlayersByTournamentId":
				$nResult = $db_api->getLeaderBoardPlayersByTournamentId($dom);
				break;	
			case "getLeaderBoardLeaguesByTournamentId":
				$nResult = $db_api->getLeaderBoardLeaguesByTournamentId($dom);
				break;	
			case "getLeaderBoardPlayersByGame":
				$nResult = $db_api->getLeaderBoardPlayersByGame($dom);
				break;
			case "getLeaderBoardPlayersByGameByPlayerTeam":
				$nResult = $db_api->getLeaderBoardPlayersByGameByPlayerTeam($dom);
				break;
			case "getLeaderBoardPlayersByGameByPlayerLeague":
				$nResult = $db_api->getLeaderBoardPlayersByGameByPlayerLeague($dom);
				break;
			case "getLeaderBoardTeamsByGame":
				$nResult = $db_api->getLeaderBoardTeamsByGame($dom);
				break;
			case "getLeaderBoardLeaguesByGame":
				$nResult = $db_api->getLeaderBoardLeaguesByGame($dom);
				break;
			case "startGameSession":
				$nResult = $db_api->startGameSession($dom);
				break;
			case "endGameSession":
				$nResult = $db_api->endGameSession($dom);
				break;
			case "startChallengeSession":
				$nResult = $db_api->startChallengeSession($dom);
				break;
			case "endChallengeSession":
				$nResult = $db_api->endChallengeSession($dom);
				break;
			case "getTopScoresByPlayerByGameByLevelByType":
				$nResult = $db_api->getTopScoresByPlayerByGameByLevelByType($dom);
				break;
			default:
				$error = $db_api->getLastError();
				$mysql_error = $db_api->getLastMysqlError();
				
				$err->getErrorStrings(ErrorMgr::ERR_API_METHOD_UNKNOWN, $errText, $errDisplay);	
					
				$nodeResultCode->appendChild( $dom->createTextNode(ErrorMgr::ERR_API_METHOD_UNKNOWN) );
				$nodeResultText->appendChild( $dom->createTextNode($errText . " [" . $apiMethod . "]") );
				$nodeDisplayText->appendChild( $dom->createTextNode($errDisplay) );
				break;
			}
		break;
	}
				
	if( $nResult == 0)
	{
		$err->getErrorStrings(ErrorMgr::ERR_SUCCESS, $errText, $errDisplay);	
			
		$nodeResultCode->appendChild( $dom->createTextNode(ErrorMgr::ERR_SUCCESS) );
		$nodeResultText->appendChild( $dom->createTextNode($errText) );
		$nodeDisplayText->appendChild( $dom->createTextNode($errDisplay) );

	}
	else if( $nResult == -1)
	{
	
	}
	else if( $nResult == ErrorMgr::ERR_API_PARAM_MISSING)
	{
		$err->getErrorStrings(ErrorMgr::ERR_API_PARAM_MISSING, $errText, $errDisplay);	
		$error = $db_api->getLastError();
		
		$nodeResultCode->appendChild( $dom->createTextNode($nResult) );
		$nodeResultText->appendChild( $dom->createTextNode( $errText . " [" . $error . "]") );
	}
	else if( $nResult == ErrorMgr::ERR_DB_QUERY)
	{
		$error = $db_api->getLastError();
		$mysql_error = $db_api->getLastMysqlError();
		
		$nodeResultCode->appendChild( $dom->createTextNode($nResult) );
		$nodeResultText->appendChild( $dom->createTextNode( $error . " [" . $mysql_error . "]") );
	}
	else
	{

		$error = $db_api->getLastError();
		$mysql_error = $db_api->getLastMysqlError();
		
		$err->getErrorStrings($nResult, $errText, $errDisplay);	
			
		$nodeResultCode->appendChild( $dom->createTextNode($nResult) );
		$nodeResultText->appendChild( $dom->createTextNode($errText) );
		$nodeDisplayText->appendChild( $dom->createTextNode($errDisplay) );
	}

}
else
{
		$err->getErrorStrings(ErrorMgr::ERR_API_VER, $errText, $errDisplay);	
			
		$nodeResultCode->appendChild( $dom->createTextNode(ErrorMgr::ERR_API_VER) );
		$nodeResultText->appendChild( $dom->createTextNode($errText) );
		$nodeDisplayText->appendChild( $dom->createTextNode($errDisplay) );
}

header ("Content-Type:text/xml");
print $dom->SaveXML();

//		header ("Content-Type:text/html");
//		print $query;
		//exit();


?>

