<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if equb_id is provided in the URL
if (!isset($_GET['equb_id'])) {
    echo "Equb ID is missing. Please select an Equb to invite members.";
    exit();
}

$equb_id = $_GET['equb_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_email = $_POST['email'];

    // Find user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$member_email]);
    $member = $stmt->fetch();

    if ($member) {
        // Check if the user is already a member of the Equb
        $stmt = $pdo->prepare("SELECT * FROM members WHERE equb_id = ? AND user_id = ?");
        $stmt->execute([$equb_id, $member['id']]);
        $existing_member = $stmt->fetch();

        if ($existing_member) {
            $message = "User is already a member of this Equb.";
        } else {
            // Add member to Equb
            $stmt = $pdo->prepare("INSERT INTO members (equb_id, user_id) VALUES (?, ?)");
            if ($stmt->execute([$equb_id, $member['id']])) {
                $message = "Member invited successfully!";
            } else {
                $message = "Error: " . $stmt->errorInfo()[2];
            }
        }
    } else {
        $message = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite Member</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            text-align: center;
        }

        form {
            margin-top: 20px;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Invite Member</h1>

        <!-- Display success/error messages -->
        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Invite Member Form -->
        <form method="POST">
            <label for="email">Member Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter member's email" required>
            <input type="hidden" name="equb_id" value="<?php echo $equb_id; ?>">
            <button type="submit">Invite Member</button>
        </form>
    </div>
</body>
</html>