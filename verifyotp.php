<?php
session_start();
require_once 'db.php'; // Database connection
require 'vendor/autoload.php'; // Include Composer's autoloader for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Email SMTP Configuration
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com'; // SMTP server
$mail->SMTPAuth = true;
$mail->Username = 'shegemanz87@gmail.com'; // Your Gmail address
$mail->Password = 'kmmxIdtzqwwwdspo'; // Your Gmail App Password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->setFrom('shegemanz87@gmail.com', 'OTP Verification');

// Verify OTP
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['phone_number']) && isset($_POST['otp'])) {
    $phone_number = $_POST['phone_number'];
    $otp = $_POST['otp'];

    // Get user details from phone number
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE phone_number = ?");
    $stmt->execute([$phone_number]);
    $user = $stmt->fetch();

    if (!$user) {
        die(json_encode(["error" => "Phone number not found!"]));
    }

    $user_id = $user['id'];
    $user_email = $user['email'];

    // Fetch OTP from database
    $stmt = $pdo->prepare("SELECT * FROM otp_codes WHERE user_id = ? AND phone_number = ? AND otp = ? AND expires_at > NOW()");
    $stmt->execute([$user_id, $phone_number, $otp]);
    $otp_record = $stmt->fetch();

    if (!$otp_record) {
        die(json_encode(["error" => "Invalid or expired OTP!"]));
    }

    // Mark OTP as verified
    $stmt = $pdo->prepare("UPDATE otp_codes SET verified = 1 WHERE id = ?");
    $stmt->execute([$otp_record['id']]);

    // Send verification email
    try {
        $mail->addAddress($user_email);
        $mail->Subject = "OTP Verification Successful";
        $mail->Body = "Your OTP has been successfully verified. You can now proceed with your account access.";
        $mail->send();

        echo json_encode(["success" => "OTP verified successfully! Verification email sent."]);
    } catch (Exception $e) {
        echo json_encode(["success" => "OTP verified, but email could not be sent.", "error" => $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["error" => "Phone number and OTP are required."]);
}
?>