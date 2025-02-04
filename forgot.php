<?php
if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    // Validate the email format
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Simulate sending a password reset link (In a real scenario, you would interact with a database and send an email)
        echo "<script>alert('A password reset link has been sent to your email address.'); window.location.href = 'forgot_password.php';</script>";
    } else {
        echo "<script>alert('Invalid email address! Please enter a valid email.'); window.location.href = 'forgot_password.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .forgot-password-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        @media screen and (max-width: 500px) {
            .container {
                padding: 20px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-password-form">
            <h2>Forgot Password</h2>
            <form action="forgot_password.php" method="POST">
                <label for="email">Enter your email address:</label>
                <input type="email" id="email" name="email" required placeholder="Your email address">
                <button type="submit" name="submit">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>
