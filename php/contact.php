<?php

// hide all basic notices from PHP
error_reporting(E_ALL ^ E_NOTICE); 

if( isset($_POST['msg-submitted']) ) {

        $email;$comment;$captcha;
        if(isset($_POST['email'])){
          $email=$_POST['email'];
        }
        if(isset($_POST['g-recaptcha-response'])){
          $captcha=$_POST['g-recaptcha-response'];
        }
        if(!$captcha){
          $message = 'Please check the captcha form.';
 	header("Content-type: application/json");
	echo json_encode( array( 'message' => $message, 'result' => $result ));
	die();          
	exit;
        }
        $secretKey = "6LcNtBkTAAAAANp6P2-OvbDM2BNLod5I4HXCqcA9";
        $ip = $_SERVER['REMOTE_ADDR'];
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
        $responseKeys = json_decode($response,true);

        if(intval($responseKeys["success"]) !== 1) {
          $message = '<h2>No SPAM allowed!!!</h2>';
        } else {
          $message = '<h2>Thanks for posting comment.</h2>';
        }
        
	$name = $_POST['name'];
//	$email = $_POST['email'];
	$subject = $_POST['subject'];
	$message = $_POST['message'];

	// server validation
	if( trim($name) === '' ) {
		$nameError = 'Please provide your name.';
		$hasError = true;
	}

	if( trim($email) === '' ) {
		$emailError = 'Please provide your email address.';
		$hasError = true;
	} else if( !preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($email)) ) {
		$emailError = 'Please provide valid email address.';
		$hasError = true;
	}

	if( trim($message) === '' ) {
		$messageError = "Please provide your message.";
		$hasError = true;
	} else {
		if( function_exists( 'stripslashes' ) ) {
			$message = stripslashes( trim( $message ) );
		}
	}
		
	if(!isset($hasError)) {
		
		$emailTo = 'info@aidappliances.com';
		$subject = 'New Submitted Message From: ' . $name;
		$body = "Name: $name \n\nEmail: $email \n\nSubject: $subject \n\nMessage: $message";
		$headers = 'From: ' .' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;

		mail($emailTo, $subject, $body, $headers);
		
		$message = 'Thank you ' . $name . ', your message has been submitted.';
		$result = true;
	
	} 
	else {

		$arrMessage = array( $nameError, $emailError, $messageError );

		foreach ($arrMessage as $key => $value) {
			if( !isset($value) )
				unset($arrMessage[$key]);
		}

		$message = implode( '<br/>', $arrMessage );
		$result = false;
	}

	header("Content-type: application/json");
	echo json_encode( array( 'message' => $message, 'result' => $result ));
	die();
}


?>