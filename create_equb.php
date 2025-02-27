<?php
session_start();
require 'db.php';

// Initialize the variable
$name = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the name field is set
    if (isset($_POST['name'])) {
        $name = trim($_POST['name']); // Get the name and trim whitespace

        // Check if the name is empty
        if (empty($name)) {
            $message = "Name cannot be empty.";
        } else {
            // Insert logic
            $stmt = $pdo->prepare("INSERT INTO equbs (name) VALUES (?)");
            try {
                $stmt->execute([$name]);
                // Redirect to the same page with success message
                header("Location: create_equb.php?success=1");
                exit();
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        }
    } else {
        $message = "Name field is not set.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Equb</title>
    <style>
        /* Custom CSS for */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color:rgb(51, 118, 184);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #3498db;
            outline: none;
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
        <h1>Create Equb</h1>

        <!-- Display success/error messages -->
        <?php if (!empty($message)): ?>
            <div class="message error"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="message success">Equb created successfully!</div>
        <?php endif; ?>

        <!-- Equb Creation Form -->
        <form method="POST">
            <input type="text" name="name" placeholder="Equb Name" required>
            <button type="submit">Create Equb</button>
        </form>
    </div>
</body>
</html>