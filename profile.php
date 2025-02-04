<?php
session_start();

require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

try {
    $sql = "SELECT first_name, last_name, email, username FROM users WHERE id = :userId"; 
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    body {
        background-image: url(b2.jpg);
        background-size: cover;
      color:rgb(6, 6, 6);
    }
    .profile-container {
      background-color:rgb(204, 233, 250);
      border-radius: 40px;
      margin-top: 30px;
      text-align: center;
      padding: 2rem;
      width: 70%;
      height: 100%;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .profile-container h1 {
      color:rgb(8, 8, 8);
    }
    .avatar {
      border-radius: 50%;
      border: 4px solid #4CAF50; 
      width: 150px;
      height: 150px;
      object-fit: cover;
      margin-bottom: 1rem;
    }
    .button {
      background-color:rgb(179, 190, 180);
      color: #000;
      padding: 0.5rem 1.5rem;
      border-radius: 35px;
      border: 2px solid #000;
      transition: background-color 0.3s ease;
    }
    .button:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body class="font-sans">
  <div class="container mx-auto mt-10">
    <div class="profile-container mx-auto max-w-4xl">
      <!-- Profile Header -->
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-green-500">Welcome, <?php echo htmlspecialchars($userData['first_name']); ?>!</h1>
        <p class="text-lg text-gray-400">Your profile details</p>
      </div>
      
      <!-- Profile Info -->
      <div class="space-y-6">
        <div class="details">
          <h2 class="text-2xl font-semibold text-gray-300">Personal Information</h2>
          <p class="text-lg text-gray-400">Name: <?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></p>
          <p class="text-lg text-gray-400">Email: <?php echo htmlspecialchars($userData['email']); ?></p>
          <p class="text-lg text-gray-400">Username: <?php echo htmlspecialchars($userData['username']); ?></p>
        </div>

        <div class="text-center mt-6">
          <a href="edit.php" class="button">Edit Profile</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
