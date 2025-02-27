<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $equb_id = $_POST['equb_id'];
    $amount = $_POST['amount'];

    // Insert transaction
    $stmt = $pdo->prepare("INSERT INTO transactions (equb_id, amount, date) VALUES (?, ?, NOW())");
    $stmt->execute([$equb_id, $amount]);

    header("Location: dashboard.php");
}
?>