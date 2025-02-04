<?php

require_once 'db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$sql = "SELECT first_name, last_name, email, bio, avatar_url, paypal_email, mpesa_phone FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $userId, PDO::PARAM_INT);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $paypalEmail = $_POST['paypal_email'];
    $mpesaPhone = $_POST['mpesa_phone'];
    
    $avatarUrl = $userData['avatar_url']; 
    if (isset($_FILES['avatar'])) {
        $avatarTmp = $_FILES['avatar']['tmp_name'];
        $avatarName = 'avatars/' . $_FILES['avatar']['name'];
        move_uploaded_file($avatarTmp, $avatarName);
        $avatarUrl = $avatarName;
    }
    
    $updateSql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, bio = ?, avatar_url = ?, paypal_email = ?, mpesa_phone = ? WHERE id = ?";
    $stmt = $pdo->prepare($updateSql);
    $stmt->bindParam(1, $firstName);
    $stmt->bindParam(2, $lastName);
    $stmt->bindParam(3, $email);
    $stmt->bindParam(4, $bio);
    $stmt->bindParam(5, $avatarUrl);
    $stmt->bindParam(6, $paypalEmail);
    $stmt->bindParam(7, $mpesaPhone);
    $stmt->bindParam(8, $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url(b2.jpg);
            color:rgb(6, 6, 6);
        }
        .container {
            background-color:rgb(179, 182, 184);
            border-radius: 40px;
            margin-top: 30px;
            text-align: center;
            padding: 2rem;
            width: 50%;
            height: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .container h1 {
            color:rgb(8, 8, 8);
        }
        .avatar {
            border-radius: 50%;
            border: 4px solid #4CAF50;
            width: 150px;
            height: 150px;
            margin: 0 auto;
            display: block;
        }
        mb4 {
            margin-bottom: 1rem;
            color: transparent;
            border-radius: 40px;
            width: 30%;
        }
        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
            background-color: #f1f1f1;
            border: none;
            color: #000;
            border-radius: 30px;
        }
        label {
            display: block;
            text-align: left;
            margin-top: 1rem;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center text-green-500">Edit Profile</h1>
        <form action="edit-profile.php" method="POST" enctype="multipart/form-data" class="max-w-lg mx-auto mt-8">
            <div class="mb-4">
                <label for="first_name" class="block text-gray-300">First Name</label>
                <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($userData['first_name']); ?>" class="w-full p-2 mt-2 bg-gray-700 text-white rounded" required>
            </div>
            <div class="mb-4">
                <label for="last_name" class="block text-gray-300">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($userData['last_name']); ?>" class="w-full p-2 mt-2 bg-gray-700 text-white rounded" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-300">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($userData['email']); ?>" class="w-full p-2 mt-2 bg-gray-700 text-white rounded" required>
            </div>
            <div class="mb-4">
                <label for="bio" class="block text-black-300">Bio</label>
                <textarea name="bio" id="bio" class="w-full p-2 mt-2 bg-transparent-700 text-white rounded"><?php echo htmlspecialchars($userData['bio']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="avatar" class="block text-black-300">Avatar</label>
                <input type="file" name="avatar" id="avatar" class="w-full p-2 mt-2 bg-gray-700 text-white rounded">
            </div>
            <div class="mb-4">
                <label for="paypal_email" class="block text-black-300">PayPal Email</label>
                <input type="email" name="paypal_email" id="paypal_email" value="<?php echo htmlspecialchars($userData['paypal_email']); ?>" class="w-full p-2 mt-2 bg-gray-700 text-white rounded">
            </div>
            <div class="mb-4">
                <label for="mpesa_phone" class="block text-black-300">M-Pesa Phone Number</label>
                <input type="text" name="mpesa_phone" id="mpesa_phone" value="<?php echo htmlspecialchars($userData['mpesa_phone']); ?>" class="w-full p-2 mt-2 bg-gray-700 text-white rounded">
            </div>
            <div class="text-center mt-6">
                <button type="submit" class="p-2 bg-green-500 text-white rounded">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
// Close the database connection
$pdo = null;
?>
