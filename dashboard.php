<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's Equbs for the transaction form
$stmt = $pdo->prepare("SELECT e.id, e.name FROM equbs e
                        JOIN members m ON e.id = m.equb_id
                        WHERE m.user_id = ?");
$stmt->execute([$user_id]);
$equbs = $stmt->fetchAll();

// Fetch user details for profile management
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle profile update form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $payment_details = $_POST['payment_details'];

    // Debugging: Print the values being updated
    echo "Name: $name<br>";
    echo "Contact: $contact<br>";
    echo "Payment Details: $payment_details<br>";

    // Update user details in the database
    $stmt = $pdo->prepare("UPDATE users SET name = ?, contact = ?, payment_details = ? WHERE id = ?");
    if ($stmt->execute([$name, $contact, $payment_details, $user_id])) {
        $profile_message = "Profile updated successfully!";
        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    } else {
        $profile_message = "Error updating profile: " . $stmt->errorInfo()[2];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Custom CSS for a Professional Dashboard */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: rgb(51, 118, 184);
            margin: 0;
            padding: 0;
            color: #333;
        }

        .dashboard {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .username, .email {
            color: #555;
            font-size: 1rem;
            margin: 10px 0;
        }

        h2 {
            color: #34495e;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        select, input[type="number"], input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        select:focus, input[type="number"]:focus, input[type="text"]:focus, input[type="email"]:focus, textarea:focus {
            border-color: #3498db;
            outline: none;
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
            margin: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: #e74c3c;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .footer {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #777;
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
    <div class="dashboard">
        <!-- Welcome Section -->
        <h1>Welcome to Your Dashboard</h1>
        <p class="username">Username: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <p class="email">Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>

        <!-- Profile Management Section -->
        <h2>Profile Management</h2>
        <?php if (isset($profile_message)): ?>
            <div class="message <?php echo strpos($profile_message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo $profile_message; ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="update_profile" value="1">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>

            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($user['contact'] ?? ''); ?>" required>

            <label for="payment_details">Payment Details:</label>
            <textarea id="payment_details" name="payment_details" rows="4"><?php echo htmlspecialchars($user['payment_details'] ?? ''); ?></textarea>

            <button type="submit" class="btn">Update Profile</button>
        </form>

        <!-- Transaction Form -->
        <h2>Add Transaction</h2>
        <form method="POST" action="add_transaction.php">
            <label for="equb_id">Select Equb:</label>
            <select name="equb_id" id="equb_id" required>
                <option value="">Select Equb</option>
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

            <label for="via">Via:</label>
            <select name="via" id="via" required>
                <option value="">Select Payment Method</option>
                <option value="TeleBirr">TeleBirr</option>
                <option value="CBE">CBE</option>
                <option value="Cash">Cash</option>
                <option value="Awash">Awash</option>
                <option value="Other">Other</option>
            </select>

            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" placeholder="Enter amount" required>

            <button type="submit" class="btn">Add Transaction</button>
        </form>

        <!-- Action Buttons -->
<a href="create_equb.php" class="btn btn-secondary">Create Equb</a>
<a href="transactions.php" class="btn btn-secondary">View Transactions</a>
<a href="join_equb.php" class="btn btn-secondary">Join Equb</a>

<!-- Invite Member Link -->
<?php if (!empty($equbs)): ?>
    <a href="invite_member.php?equb_id=<?php echo $equbs[0]['id']; ?>" class="btn btn-secondary">Invite Member</a>
<?php else: ?>
    <p>No Equbs available to invite members.</p>
<?php endif; ?>

<a href="logout.php" class="btn btn-danger">Log Out</a>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2023 Equb Dashboard. All rights reserved.</p>
        </div>
    </div>
</body>
</html>