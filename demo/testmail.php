<?php

//error_reporting(E_ALL);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


// $msg  = "<p></p>";
// $msg .= "<p>Dear " . $_POST['name'] . ",</p>";
// $msg .= "<p>Your login details are as follows.</p>";
// $msg .= "<p></p>";
// $msg .= "<p><b>Email id : </b>" . $_POST['to_mail'] . "</p>";
// $msg .= "<p><b>Password : </b>" . $_POST['password'] . "</p>";
// $msg .= "<p></p>";
// $msg .= "<p>Best Regards</p>";
// $msg .= "<p></p>";
// $msg .= "<p>The Mkrad Team</p>";

$msg  = "<p></p>";
$msg .= "<p>Dear ".$_POST['name'].",</p>";
$msg .= "<p>You are receiving this email because we received a password reset request for your account.</p>";
$msg .= "<p></p>";
$msg .= "<p><b>Your reset password otp is : </b>". $_POST['otp']."</p>";
$msg .= "<p></p>";
$msg .= "<p>If you did not request a password reset, no further action is required.</p>";
$msg .= "<p></p>";
$msg .= "<p>Best Regards</p>";
$msg .= "<p></p>";
$msg .= "<p>The Brand Nue Team</p>";

$mail = new PHPMailer; 
$mail->isHTML(true);
$mail->Host = 'localhost';
$mail->Port = 25;
//$mail->CharSet = PHPMailer::CHARSET_UTF8;
$mail->setFrom('admin@brandnueweightloss.com', 'Brand NUE');
//$mail->CharSet = PHPMailer::CHARSET_UTF8;

$mail->addAddress($_POST['to_mail'], $_POST['to_mail']);
$mail->Subject = $_POST['subject'] ?? 'Reset Password Code';
$mail->Body = $msg ?? $_POST['otp'];
if(!$mail->send()) {
	echo json_encode(array('flag' => false, 'message'=>"Error: ".$mail->ErrorInfo ));
	die;
}else{
	echo json_encode(array('flag' => true,'message'=>"You reset password code has been sent to your email id"));
	die;
}