<?php
session_start();
require_once 'db.php'; // Database connection

// Include Composer's autoload file
require_once 'vendor/autoload.php'; 

// Africa's Talking API Credentials
$username = "sandbox"; // Your Africa's Talking username
$apiKey = "atsk_94f2a31e3a2d3a893f7b05b858c4c1ec3fea3e8196bff429bb7acdffefa350c2b3d988f3"; // API key
$otpExpiry = 300; // OTP validity in seconds (5 minutes)

// Import SDKs
use AfricasTalking\SDK\AfricasTalking;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create Africa's Talking instance
$africasTalking = new AfricasTalking($username, $apiKey);
$sms = $africasTalking->sms();

// PHPMailer SMTP Settings
$emailSender = "shegemanz87@gmail.com"; // Your email
$emailPassword = "kmmxIdtzqwwwdspo"; // App password (Use environment variables instead!)
$emailHost = "smtp.gmail.com";
$emailPort = 587;

// Function to generate OTP
function generateOTP() {
    return rand(100000, 999999);
}

// Function to send OTP via Africa's Talking SMS
function sendOTP_SMS($phone_number, $otp, $sms) {
    try {
        $message = "Your OTP is: $otp. It expires in 5 minutes.";
        $result = $sms->send([
            'to' => $phone_number,
            'message' => $message
        ]);
        return $result ?: ["error" => "Failed to send OTP via SMS."];
    } catch (Exception $e) {
        return ["error" => $e->getMessage()];
    }
}

// Function to send OTP via Email using PHPMailer
function sendOTP_Email($email, $otp, $emailSender, $emailPassword, $emailHost, $emailPort) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = $emailHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $emailSender;
        $mail->Password   = $emailPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $emailPort;

        // Sender & Recipient
        $mail->setFrom($emailSender, 'Your App');
        $mail->addAddress($email);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "Your OTP Code is <b>$otp</b>. It expires in 5 minutes.";

        if ($mail->send()) {
            return ["success" => "OTP sent via Email!"];
        } else {
            return ["error" => "Failed to send OTP via Email."];
        }
    } catch (Exception $e) {
        return ["error" => $mail->ErrorInfo];
    }
}

// Handle OTP Request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['phone_number'])) {
    $phone_number = $_POST['phone_number'];

    // Fetch user details
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE phone_number = ?");
    $stmt->execute([$phone_number]);
    $user = $stmt->fetch();

    if (!$user) {
        die(json_encode(["error" => "Phone number not registered!"]));
    }

    $user_id = $user['id'];
    $user_email = $user['email'];

    // Generate OTP
    $otp = generateOTP();
    $expiry = date('Y-m-d H:i:s', time() + $otpExpiry);

    // Store OTP in the database
    $stmt = $pdo->prepare("INSERT INTO otp_codes (user_id, phone_number, otp, expires_at) 
                           VALUES (?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE otp = ?, expires_at = ?");
    $stmt->execute([$user_id, $phone_number, $otp, $expiry, $otp, $expiry]);

    // Send OTP via SMS and Email
    $smsResponse = sendOTP_SMS($phone_number, $otp, $sms);
    $emailResponse = sendOTP_Email($user_email, $otp, $emailSender, $emailPassword, $emailHost, $emailPort);

    // Return Response
    echo json_encode([
        "sms" => $smsResponse,
        "email" => $emailResponse
    ]);
}
?>
