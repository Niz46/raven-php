<?php
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$smtpUser = getenv('GMAIL_SMTP_USER') ?: 'favournzeh1@gmail.com';
$smtpPass = getenv('GMAIL_SMTP_APP_PASS') ?: 'lmgs ywro yqdp xkat';
$to = $smtpUser;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    // enable verbose debug output on CLI
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // lots of output
    $mail->Debugoutput = function($str, $level) { echo $str . PHP_EOL; };

    $mail->setFrom($smtpUser, 'SMTP Test');
    $mail->addAddress($to);
    $mail->Subject = 'SMTP test';
    $mail->Body = "Test from smtp-test.php at " . date('c');

    $mail->send();
    echo "OK: mail sent\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
