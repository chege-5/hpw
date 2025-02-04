<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if POST variables are set
    if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['username'], $_POST['password'])) {
        // Sanitize user inputs
        $first_name = htmlspecialchars($_POST['first_name']);
        $last_name = htmlspecialchars($_POST['last_name']);
        $email = htmlspecialchars($_POST['email']);
        $username = htmlspecialchars($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hashing password for security

        // Check if the email or username already exists
        $sql = "SELECT * FROM users WHERE email = :email OR username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email, 'username' => $username]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            echo "Email or Username already exists. Please try again.";
        } else {
            // Insert the new user into the database
            $sql = "INSERT INTO users (first_name, last_name, email, username, password) VALUES (:first_name, :last_name, :email, :username, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'username' => $username,
                'password' => $password
            ]);

            echo "Registration successful! You can now <a href='login.php'>Login</a>.";
        }
    } else {
        echo "Please fill in all the fields!";
    }
}
?>
