<?php
session_start();  // Start session for user login

// Include the database connection
include('db.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username and password are provided
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Prepare SQL query to fetch user data based on username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Check if username exists in the database
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data && password_verify($password, $user_data['password'])) {
            // If credentials are correct, store user in session and redirect to dashboard
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['username'] = $user_data['username'];
            header("Location: dashboard.html");  // Redirect to dashboard
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>