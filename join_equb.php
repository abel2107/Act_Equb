<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['equb_id'])) {
        $equb_id = $_POST['equb_id'];

        // Check if the user is already a member of the Equb
        $stmt = $pdo->prepare("SELECT * FROM members WHERE equb_id = ? AND user_id = ?");
        $stmt->execute([$equb_id, $user_id]);
        $existing_member = $stmt->fetch();

        if ($existing_member) {
            echo "<p>You are already a member of this Equb.</p>";
        } else {
            // Add the user to the Equb
            $stmt = $pdo->prepare("INSERT INTO members (equb_id, user_id) VALUES (?, ?)");
            if ($stmt->execute([$equb_id, $user_id])) {
                echo "<p>Joined Equb successfully!</p>";
            } else {
                echo "<p>Error: " . $stmt->errorInfo()[2] . "</p>";
            }
        }
    } else {
        echo "<p>Equb ID is not set.</p>";
    }
}

// Fetch all available Equbs
$stmt = $pdo->query("SELECT id, name FROM equbs");
$equbs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Equb</title>
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

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        select {
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Join Equb</h1>

        <!-- Display success/error messages -->
        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Equb Selection Form -->
        <form method="POST">
            <label for="equb_id">Select Equb:</label>
            <select name="equb_id" id="equb_id" required>
                <option value="">Select an Equb</option>
                <?php if (!empty($equbs)): ?>
                    <?php foreach ($equbs as $equb): ?>
                        <option value="<?php echo $equb['id']; ?>">
                            <?php echo htmlspecialchars($equb['name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No Equbs available</option>
                <?php endif; ?>
            </select>
            <button type="submit">Join Equb</button>
        </form>
    </div>
</body>
</html>