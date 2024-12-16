<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Total users
$total_users = $conn->query("SELECT COUNT(*) AS count FROM users")->fetchColumn();

// Total books
$total_books = $conn->query("SELECT COUNT(*) AS count FROM books")->fetchColumn();

// Total transactions
$total_transactions = $conn->query("SELECT COUNT(*) AS count FROM transactions")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Advanced Reports</h1>
        <ul>
            <li>Total Users: <?php echo $total_users; ?></li>
            <li>Total Books: <?php echo $total_books; ?></li>
            <li>Total Transactions: <?php echo $total_transactions; ?></li>
        </ul>
    </div>
</body>
</html>
