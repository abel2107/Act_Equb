<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.html");
    exit();
}

// Fetch all accounts from your users table
$stmt = $pdo->query("SELECT username FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$winnerMessage = '';
if (isset($_POST['pickWinner'])) {
    $winner = $users[array_rand($users)];
    $winnerMessage = "🎉 Winner: " . htmlspecialchars($winner['username']) . " 🎉";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Custom CSS */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
        }

        h2 {
            color: #34495e;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            background-color: #f8f9fa;
            margin: 10px 0;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        li:hover {
            background-color: #e9ecef;
        }

        .winner-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #e8f5e9;
            border-radius: 8px;
            border: 1px solid #c8e6c9;
            text-align: center;
        }

        .winner-message {
            font-size: 1.5rem;
            color: #2e7d32;
            font-weight: bold;
        }

        .btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .header {
            background-color: #3498db;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9rem;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Admin Dashboard</h1>
        </div>

        <!-- All Users Section -->
        <h2>All Users</h2>
        <ul>
            <?php foreach ($users as $user): ?>
                <li><?php echo htmlspecialchars($user['username']); ?></li>
            <?php endforeach; ?>
        </ul>

        <!-- Pick a Winner Section -->
        <h2>Pick a Winner</h2>
        <form method="POST">
            <button type="submit" name="pickWinner" class="btn">Pick a Winner</button>
        </form>

        <!-- Winner Message Section -->
        <?php if ($winnerMessage): ?>
            <div class="winner-section">
                <p class="winner-message"><?php echo htmlspecialchars($winnerMessage); ?></p>
            </div>
        <?php endif; ?>
        
    </div>
</body>
</html>