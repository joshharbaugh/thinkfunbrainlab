<?php
//print $_SERVER["REQUEST_URI"];

//require_once "Mail.php";
//set_include_path('.:/usr/share/pear/');
//set_include_path('.:/usr/share/pear/Mail/'); 
///include ('Mail.php');
//include ('Queue.php');

require_once "Mail/Queue.php";
require_once "Mail.php";
require_once "db.php";

//require_once ('Mail/Queue.php');
//require_once ('Queue.php');

// options for storing the messages
// type is the container used, currently there are 'creole', 'db', 'mdb' and 'mdb2' available
$db_options['type']       = 'db';
// the others are the options for the used container
// here are some for db
$db_options['dsn']        = 'mysql://brainlab_sandbox:e=mc^2@localhost/brainlab_sandbox';
$db_options['mail_table'] = 'mail_queue';

// here are the options for sending the messages themselves
// these are the options needed for the Mail-Class, especially used for Mail::factory()
$mail_options['driver']    = 'smtp';
$mail_options['host']      = 'localhost';
$mail_options['port']      = 25;
$mail_options['localhost'] = 'localhost'; //optional Mail_smtp parameter
$mail_options['auth']      = false;
$mail_options['username']  = '';
$mail_options['password']  = '';

$max_amount_emails = 50;
$delay_for_secs = 5;
$delete_after_send = true;
$id_invite = 99999;
$reg_code = "20fa6cc0-1cd8-11e2-9a52-f50d9fd44fd2";
$role_name = "league";

$mail_queue =& new Mail_Queue($db_options, $mail_options);
//print 'test';
//die();

$message  = '<html>';
$message .= '<body>';
$message .= '<h3>You are invited to join our '.$role_name.' at Thinkfun Brainlab.</h3>';
$message .= '<p>Here is your secret registration code: <strong>'.$reg_code.'</strong></p>';
$message .= '<p>Simply <a href="https://www.thinkfunbrainlab.com/home/create_account">Click here</a> and enter the above code to sign up!</p>';
$message .= '</body>';
$message .= '</html>';

/* we use Mail_mime() to construct a valid mail */
//$crlf = 'crlf';
$crlf = "\n";
$mime =& new Mail_mime($crlf);
//$mime =& new Mail_mime(array('eol' => $crlf));
//$mime->setTXTBody($message);
$mime->setHTMLBody($message);
$body = $mime->get();

/* Put message to queue */
$from = 'stephen@thinkfunproject.com';
$cc = 'stephen.mkandawire@gmail.com';

$to = "stephen.mkandawire@gmail.com";
//$to = 'rick@pintsizeart.com';
$hdrs = array( 'Content-type' => "text/html; charset=iso-8859-1",
			   'From'    => $from,
               'To'      => $to,
			   'CC'      => $cc,
               'Subject' => "Join Thinkfun Brainlab!");
// the 2nd parameter allows the header to be overwritten
// @see http://pear.php.net/bugs/18256
$hdrs = $mime->headers($hdrs, true); 
$result = $mail_queue->put($from, $to, $hdrs, $body, $delay_for_secs, $delete_after_send, $id_invite);

// $to = "stephen@thinkfunproject.com";
// $hdrs = array( 'Content-type' => "text/html; charset=iso-8859-1",
			   // 'From'    => $from,
               // 'To'      => $to,
		   // 'CC'      => $cc,
              // 'Subject' => "Join Thinkfun Brainlab!");
// $hdrs = $mime->headers($hdrs, true); 
// $result = $mail_queue->put($from, $to, $hdrs, $body, $delay_for_secs, $delete_after_send, $id_invite);

// $to = 'rnuthman@gmail.com';
// $hdrs = array( 'Content-type' => "text/html; charset=iso-8859-1",
			   // 'From'    => $from,
               // 'To'      => $to,
			   // 'CC'      => $cc,
               // 'Subject' => "Join Thinkfun Brainlab!");
// $hdrs = $mime->headers($hdrs, true); 
// $result = $mail_queue->put($from, $to, $hdrs, $body, $delay_for_secs, $delete_after_send, $id_invite);
// 
// $to = "mark@tieaknot.com";
// $hdrs = array( 'Content-type' => "text/html; charset=iso-8859-1",
			   // 'From'    => $from,
               // 'To'      => $to,
			   // 'CC'      => $cc,
               // 'Subject' => "Join Thinkfun Brainlab!");
// $hdrs = $mime->headers($hdrs, true); 
// $result = $mail_queue->put($from, $to, $hdrs, $body, $delay_for_secs, $delete_after_send, $id_invite);
// 
// $to = "tobyjmorgan@yahoo.com";
// $hdrs = array( 'Content-type' => "text/html; charset=iso-8859-1",
			   // 'From'    => $from,
               // 'To'      => $to,
			   // 'CC'      => $cc,
               // 'Subject' => "Join Thinkfun Brainlab!");
// $hdrs = $mime->headers($hdrs, true); 
// $result = $mail_queue->put($from, $to, $hdrs, $body, $delay_for_secs, $delete_after_send, $id_invite);
// 
// $to = 'rick@pintsizeart.com';
// $hdrs = array( 'Content-type' => "text/html; charset=iso-8859-1",
			   // 'From'    => $from,
               // 'To'      => $to,
			   // 'CC'      => $cc,
               // 'Subject' => "Join Thinkfun Brainlab!");
// $hdrs = $mime->headers($hdrs, true); 
// $result = $mail_queue->put($from, $to, $hdrs, $body, $delay_for_secs, $delete_after_send, $id_invite);
// 
// $to = 'amandafturner@hotmail.com';
// $hdrs = array( 'Content-type' => "text/html; charset=iso-8859-1",
			   // 'From'    => $from,
               // 'To'      => $to,
		   // 'CC'      => $cc,
              // 'Subject' => "Join Thinkfun Brainlab!");
// $hdrs = $mime->headers($hdrs, true); 
// $result = $mail_queue->put($from, $to, $hdrs, $body, $delay_for_secs, $delete_after_send, $id_invite);
$result = $mail_queue->sendMailsInQueue(
    MAILQUEUE_ALL,
    MAILQUEUE_START,  
    MAILQUEUE_TRY,
    'mailqueue_callback');
	
	//print $result;


function mailqueue_callback($args) {
 

//    $row = $mail_queue->get_mail_queue_row($args['id']);
    
	$db = new mysql_db();
	$recordset = null;
	$result = 0;
	
	//var_dump($args);

	$query = "SELECT id_user, recipient, sent_time from mail_queue where id = " . $args['id'];
	$result = $db->query($query, $recordset);
	print $query . "<br>";
	if(	$result != 0)
	{
		$this->last_error_text = $db->get_last_error_text();
		$this->mysql_error_text = $db->get_mysql_error();

		$db->close_connection();
	}
	
	while ($row = mysql_fetch_assoc($recordset)) 
	{
		$id_user = $row[id_user]; // this is the record id in 'invite' table
		//print $id_user . "<br>";
		$recipient = $row[recipient];
		//print $recipient . "<br>";
		$sent_time = $row[sent_time];
		//print $sent_time . "<br>"; 
	}	

	$db->close_connection();
	
	$db = new mysql_db();
	$recordset = null;
	$result = 0;

	$query = "UPDATE invite set dtInviteSent = '" . $sent_time . "' where idInvite = " . $id_user;
	$result = $db->query($query, $recordset);
	//print $query . "<br>";
	if(	$result != 0)
	{
		//$this->last_error_text = $db->get_last_error_text();
		//$this->mysql_error_text = $db->get_mysql_error();
	}
	
	$db->close_connection();	
	
}

//$mail_queue->sendMailsInQueue($max_amount_emails);
?>