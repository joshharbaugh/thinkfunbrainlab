<?php
require_once "Mail/Queue.php";
require_once "Mail.php";

class queuemail
{
	public function sendmail($idManager, $to, $from, $subject, $html, $text)
	{
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

	//$max_amount_emails = 50;
	$delay_for_secs = 30;
	$delete_after_send = false;
	$id_manager = $idManager;

	$mail_queue = new Mail_Queue($db_options, $mail_options);

	// we use Mail_mime() to construct a valid mail 
	$crlf = "\n";
	$mime = new Mail_mime($crlf);
	$mime->setHTMLBody($html);
	//$mime->setTxtBody($text);
	$body = $mime->get();

	// Put message in queue
	$hdrs = array( 'MIME-Version' => "1.0",
				   'Date' => date("D, d M Y H:i:s"),
				   'Content-Type' => "text/html; charset=iso-8859-1",
				   'From'    => $from,
				   'To'      => $to,
	//			   'CC'      => $cc,
				   'Subject' => $subject);
	// the 2nd parameter allows the header to be overwritten
	// @see http://pear.php.net/bugs/18256
	$hdrs = $mime->headers($hdrs, TRUE); 
	$result = $mail_queue->put($from, $to, $hdrs, $body, $delay_for_secs, $delete_after_send, $id_manager);
	}
}
?>