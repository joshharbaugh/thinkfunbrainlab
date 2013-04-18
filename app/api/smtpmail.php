<?php
require_once "Mail/Queue.php";
require_once "Mail.php";
require_once "db.php";

	// options for storing the messages
	// type is the container used, currently there are 'creole', 'db', 'mdb' and 'mdb2' available
	$db_options['type']       = 'db';
	// the others are the options for the used container
	// here are some for db
	//$db_options['dsn']        = 'mysql://brainlab_sandbox:e=mc^2@localhost/brainlab_sandbox';
	$db_options['dsn']        = 'mysql://brainlab:e=mc^2@localhost/brainlab';
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
	$delay_for_secs = 30;
	$delete_after_send = false;
	$id_invite = 0;
	
	$mail_queue = new Mail_Queue($db_options, $mail_options);
	
	//$result = $mail_queue->sendMailsInQueue($max_amount_emails);
	
	$result = $mail_queue->sendMailsInQueue(
	    $max_amount_emails /*MAILQUEUE_ALL*/,
	    MAILQUEUE_START,  
	    MAILQUEUE_TRY,
	    'mailqueue_callback');

function mailqueue_callback($args) {
 
	$db = new mysql_db();
	$recordset = null;
	$result = 0;
	
	//var_dump($args);

	$query = "SELECT id_user, recipient, sent_time from mail_queue where id = " . $args['id'];
	$result = $db->query($query, $recordset);
	//print $query . "<br>";
	
	if(	$result == 0)
	{
		$db->close_connection();
		return;
	}
	
	while ($row = mysql_fetch_assoc($recordset)) 
	{
		$id_invite = $row[id_user]; // this is the record id in 'invite' table
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

	$query = "UPDATE invite set dtInviteSent = '" . $sent_time . "' where idInvite = " . $id_invite;
	$result = $db->query($query, $recordset);
	//print $query . "<br>";
	if(	$result != 0)
	{
		//$this->last_error_text = $db->get_last_error_text();
		//$this->mysql_error_text = $db->get_mysql_error();
	}
	
	$db->close_connection();	
	
}
?>