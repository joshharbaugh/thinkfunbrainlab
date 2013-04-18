<?php
    
class ErrorMgr
{
	const ERR_SUCCESS = 0;
		
	const ERR_API_VER = 100; 
 	const ERR_API_KEY = 101;
	const ERR_API_LIBRARY = 102;
	const ERR_API_METHOD = 103;
	const ERR_API_METHOD_UNKNOWN = 104;
	const ERR_API_PARAM_MISSING = 105;
	const ERR_API_PARAM_INVALID = 106;
	const ERR_API_UNAUTHORIZED = 107;
	const ERR_API_LOGGED_OUT = 108;
		
	const ERR_DB_NO_RECORDS = 200;
	const ERR_DB_CONNECT = 201;
	const ERR_DB_SELECT = 202;
	const ERR_DB_QUERY = 203;
	const ERR_DB_XML_TRANSFORM = 204;
	const ERR_DB_UNAME_EXISTS = 205;
	const ERR_DB_EMAIL_EXISTS = 206;
	const ERR_DB_LOGIN_CRED = 207;
	const ERR_DB_EMAIL_NOT_EXISTS = 208;
	const ERR_DB_PWD_RESET_TOKEN = 209;
			 	
 	private $arrErrorCodeStrings = null;
	
	public function __construct()
	{

		$this->arrErrorCodeStrings = array( 
			array( errorCode => $this::ERR_SUCCESS, 			errorText => "Success", 										displayText => ""),
			array( errorCode => $this::ERR_API_VER, 			errorText => "API_VER expected", 								displayText => ""),
			array( errorCode => $this::ERR_API_KEY, 			errorText => "API_KEY expected", 								displayText => ""),
			array( errorCode => $this::ERR_API_LIBRARY, 		errorText => "API_LIBRARY expected ('Account'|'Admin'|'Game)", 	displayText => ""),
        	array( errorCode => $this::ERR_API_METHOD, 			errorText => "API_METHOD expected", 							displayText => ""),
			array( errorCode => $this::ERR_API_METHOD_UNKNOWN, 	errorText => "API_METHOD unknown", 								displayText => ""),
			array( errorCode => $this::ERR_API_PARAM_INVALID, 	errorText => "API parameter invalid", 							displayText => ""),
			array( errorCode => $this::ERR_API_PARAM_MISSING, 	errorText => "API parameter missing", 							displayText => ""),
			array( errorCode => $this::ERR_API_UNAUTHORIZED, 	errorText => "Unauthorized request", 							displayText => ""),
			array( errorCode => $this::ERR_API_LOGGED_OUT, 		errorText => "User logged out", 								displayText => ""),
			
			array( errorCode => $this::ERR_DB_NO_RECORDS, 		errorText => "No Records", 										displayText => ""),
			array( errorCode => $this::ERR_DB_CONNECT, 			errorText => "Database connection failed", 						displayText => "Database connection failed"),
			array( errorCode => $this::ERR_DB_SELECT, 			errorText => "Database selection failed", 						displayText => "Database selection failed"),
			array( errorCode => $this::ERR_DB_QUERY, 			errorText => "Database query failed", 							displayText => "No Records"),
			array( errorCode => $this::ERR_DB_XML_TRANSFORM, 	errorText => "XML transformation failed", 						displayText => "XML transformation failed"),
			array( errorCode => $this::ERR_DB_UNAME_EXISTS, 	errorText => "Duplicate user name", 							displayText => "The username selected is already taken. Please select a different username."),
			array( errorCode => $this::ERR_DB_EMAIL_EXISTS, 	errorText => "Duplicate email", 								displayText => "An account is already registered with this email address. Please enter a different email address or contact the person that sent you the invitation."),
			array( errorCode => $this::ERR_DB_LOGIN_CRED, 		errorText => "Invalid login credentials", 						displayText => "The username or password entered is not correct. Please try again."),
			array( errorCode => $this::ERR_DB_EMAIL_NOT_EXISTS, errorText => "Email address unknown", 							displayText => "There is no account registered with that email address."),
			array( errorCode => $this::ERR_DB_PWD_RESET_TOKEN, 	errorText => "Invalid reset password token", 					displayText => "Password cannot be changed, please contact our Support Team for help.")
		);			
	}
	
	public function getErrorText($errorCode, &$errorText)
	{
		for ($i = 0; $i < sizeof($this->arrErrorCodeStrings); $i++)
		{
			if($this->arrErrorCodeStrings[$i]["errorCode"] == $errorCode)
			{
				$errorText = $this->arrErrorCodeStrings[$i]["errorText"];
				break;
			}
		}
	}
	
	
	public function getDisplayText($errorCode, &$displayText)
	{
		for ($i = 0; $i < sizeof($this->arrErrorCodeStrings); $i++)
		{
			if($this->arrErrorCodeStrings[$i]["errorCode"] == $errorCode)
			{
				$displayText = $this->arrErrorCodeStrings[$i]["displayText"];
				break;
			}
		}
	}
	
	public function getErrorStrings($errorCode, &$errorText, &$displayText)
	{
		for ($i = 0; $i < sizeof($this->arrErrorCodeStrings); $i++)
		{
			if($this->arrErrorCodeStrings[$i]["errorCode"] == $errorCode)
			{
				$errorText = $this->arrErrorCodeStrings[$i]["errorText"];
				$displayText = $this->arrErrorCodeStrings[$i]["displayText"];
				break;
			}
		}
	}
}    

?>